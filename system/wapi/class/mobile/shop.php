<?php
/**
 *小程序商品页
 */

namespace wapi\controller;
class shop extends base{
    /**
     * 商品列表页
     *   */
    public function shopList(){
        $_GP = $this->request;
        $type = intval(isset($_GP['type']))? intval($_GP['type']) : 3;//类型,1是一级栏目，2是二级栏目，3是关键词
        if ($type == 3){
            $keyword = $_GP['keyword'];//关键词
            if (empty($keyword)) ajaxReturnData(0,'请填写关键词搜索');
        }else{
            $id = intval($_GP['id']);//栏目id
            if (empty($id)) ajaxReturnData(0,'参数错误');
        }
        $jd = $_GP['longitude'];//经度
        $wd = $_GP['latitude'];//纬度
        $minpriceTemp = $_GP['minprice'];//最小价格
        $maxpriceTemp = $_GP['maxprice'];//最大价格
        $timearea = $_GP['timearea'];//时间区域
        
        if (!empty($minpriceTemp)) $minprice = FormatMoney($minpriceTemp,1);
        if (!empty($maxpriceTemp)) $maxprice = FormatMoney($maxpriceTemp,1);
        //分页
        $pindex = max(1, intval($_GP['page']));
        $psize = isset($_GP['limit']) ? $_GP['limit'] : 4;//默认每页4条数据
        $limit= ($pindex-1)*$psize;
        
        //取出当前活动
        $list = getCurrentAct();
        if (empty($list)) ajaxReturnData(0,'暂时没有活动');
        
        //取当前的时间区域
        $actAreaModel = new \model\activity_area_model();
        $areaList = $actAreaModel->getAllActArea(array('ac_list_id'=>$list['ac_area']));
        if ($areaList){
            foreach ($areaList as $v){
                $startDate = date("Y:m:d")." ".date('H:i:s',$v['ac_area_time_str']);
                $endDate = date("Y:m:d")." ".date('H:i:s',$v['ac_area_time_end']);
                $starttime = strtotime($startDate);
                $endtime = strtotime($endDate);
                if (time() >= $starttime && time() <= $endtime){
                    $areaid = $v['ac_area_id'];
                }else if ($starttime >= time()){
                    $areaidArr[] =$v['ac_area_id']; 
                }
            }
        }
        
        //查询条件拼接
        $where = " a.ac_action_id={$list['ac_id']} and a.ac_dish_status=1 and b.status=1 ";
        //区域和城市id
        if (empty($jd) || empty($wd)) {
            //高德地图根据ip获取城市
            $cityCode = getCityidByIp();
            $where .=" and (a.ac_city='$cityCode' or a.ac_city=0) ";
        }else{
            //高德接口获取区域id
            $return = getAreaid($jd,$wd);
           if (empty($return)) ajaxReturnData(0,'参数错误');
           if ($return['status'] == 0) {
               $cityCode = $return['ac_city'];
               $where .=" and (a.ac_city='$cityCode' or a.ac_city=0) ";
           }else {
               $ac_city = $return['ac_city'];
               $ac_city_area = $return['ac_city_area'];
               $where .= " and IF(a.ac_city='$ac_city',a.ac_city_area='$ac_city_area' OR a.ac_city_area=0,IF(a.ac_city_area=0,a.ac_city=0,a.ac_city_area='$ac_city_area'))";
           }
        }
        //栏目或者关键词
        if ($type == 1){
            $where .=" and a.ac_p1_id = '$id' ";
        }elseif($type == 2){
            $where .=" and a.ac_p2_id = '$id' ";
        }else if ($type == 3){
            if (function_exists('scws_new') ){
                $word = get_word($keyword);
                if ( !empty($word) && is_array($word) ){
                    foreach ($word as $word_value ) {
                        $keys[] = " b.title like '%".$word_value."%' ";
                    }
                    	
                    $keys = implode(' or ' , $keys);
                    $where .= ' and ('.$keys.')';
                }
            }else {
                //$where .=" AND LOCATE('$keyword',b.title) >0";
            }
        }
        //价格
        if ($minprice && $maxprice){
            if ($maxprice < $minprice) ajaxReturnData(0,'抱歉，价格输入不正确');
            $where .= " AND a.ac_dish_price >= '$minprice' AND  a.ac_dish_price <= '$maxprice' ";
        }elseif ($minprice){
            $where .= " AND a.ac_dish_price >= '$minprice' ";
        }elseif ($maxprice) {
            $where .= " AND a.ac_dish_price <= '$maxprice' ";
        }
        $sql_base = "SELECT a.ac_dish_id,a.ac_action_id,a.ac_area_id,a.ac_shop_dish,a.ac_dish_price,a.ac_dish_total,a.ac_dish_sell_total,b.title,b.thumb,b.marketprice";
        
        //搜索的时间区域和自动筛选出来
        if (!empty($timearea)){
            $timeareaArr = explode(",",$timearea);
            $timeareaArr[0] = '0';
            foreach ($timeareaArr as $v){
                $timeWhere[] = " a.ac_area_id='$v' ";
            }
            $timearea = implode(' or ' , $timeWhere);
            $where .= ' and ('.$timearea.')';
            //$timeareaStr = to_sqls($timearea,'','a.ac_area_id');
            $where3 = $where.' and ('.$timearea.') ';
            $sql = $sql_base." FROM ".table('activity_dish')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.ac_shop_dish=b.id  WHERE ".$where3;
        }else{
            if($areaid) {
                $where1 = $where." and (a.ac_area_id = '$areaid' or a.ac_area_id=0) ";
            }else{
                $where1 = $where." and a.ac_area_id=0 ";
            }
            //sql拼接
            $sql1 = $sql_base." FROM ".table('activity_dish')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.ac_shop_dish=b.id WHERE ".$where1;
            
            //未开始的活动的id
            if (!empty($areaidArr)){
                $areaidStr = to_sqls($areaidArr,'','a.ac_area_id');
                $where2 = $where." and $areaidStr ";
                $sql2 = $sql_base." FROM ".table('activity_dish')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.ac_shop_dish=b.id  WHERE ".$where2;
            }
            //$sql1 .= $orderby;
            if ($sql2){
                //$sql2 .= $orderby;
                $sql = "SELECT * FROM ($sql1) as t1 UNION SELECT * FROM ($sql2) as t2";
            }else{
                $sql = $sql1 ;
            }
        }
        //排序
        $status = intval(isset($_GP['status'])) ? intval($_GP['status']) : 1;//1表示综合，2表示价格，3表示销量
        $orderby = ' order by ';
        if ($status == 1){
            $orderby .= " ac_dish_id DESC ";
        }elseif ($status == 2){
            $price_type = $_GP['price_type'] ? $_GP['price_type'] : '1';
            if($price_type == 1){
                $price = ' asc ';
            }else{
                $price = ' desc ';
            }
            $orderby .= " ac_dish_price $price ";
        }else {
            $orderby .= " ac_dish_sell_total DESC ";
        }
        $limit = " limit ".$limit." , ".$psize;
        $sql .= $orderby.$limit;
        $list = mysqld_selectall($sql);

        if (empty($list)) ajaxReturnData(1,'暂时没有商品');
        
        $shopDishModel = new \model\shop_dish_model();
        foreach ($list as $key=>$v){
            if ($v['ac_area_id'] == $areaid){
                $list[$key]['status'] = 1;
            }else{
                $list[$key]['status'] = 0;
            }
            $list[$key]['marketprice'] = FormatMoney($v['marketprice'],0);
            $list[$key]['ac_dish_price'] = FormatMoney($v['ac_dish_price'],0);
        }
        ajaxReturnData(1,'',$list);
    }
    /**
     * 热搜词
     *   */
    public function gethot(){
        $_GP = $this->request;
        $catid = intval($_GP['catid']);
        $actService = new \service\wapi\activityService();
        $info = $actService->gethot($catid);
        if (empty($info)) ajaxReturnData('1','暂无数据');
        ajaxReturnData('1','',$info);
    }
}