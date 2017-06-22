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
        $id = intval($_GP['id']);//栏目id
        if (empty($id)) ajaxReturnData(0,'参数错误');
        if ($type == 3){
            $keyword = $_GP['keyword'];//关键词
            if (empty($keyword)) ajaxReturnData(0,'请填写关键词搜索');
        }
        $jd = $_GP['longitude'];//经度
        $wd = $_GP['latitude'];//纬度
        
        //分页
        $pindex = max(1, intval($_GP['page']));
        $psize = isset($_GP['limit']) ? $_GP['limit'] : 4;//默认每页4条数据
        $limit= ($pindex-1)*$psize;
        
        //取出当前活动
        $actListModel = new \model\activity_list_model();
        $list = $actListModel->getCurrentAct();
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
        $where = " a.ac_action_id={$list['ac_id']} and a.ac_dish_status=1 ";
        if (empty($jd) || empty($wd)) {
            //高德地图根据ip获取城市
            $ip = getClientIP();
            $info = getCodeByIP($ip);
            if ($info){
                $info = json_decode($info,1);
                $cityCode = $info['adcode'];
            }
            if (empty($cityCode)) $cityCode = '350100';//如果未取到ip，则取福州
            $where .=" and (a.ac_city='$cityCode' or a.ac_city=0) ";
        }else{
            //高德接口获取区域id
            $return = json_decode(getCodeByLttAndLgt($jd,$wd),1);
            if ($return['status'] == 0) ajaxReturnData(0,'抱歉，获取地里位置信息失败，请刷新一下');
            $ac_city_area = isset($return['regeocode']['addressComponent']['adcode'])?$return['regeocode']['addressComponent']['adcode']:'';
            $actDishModel = new \model\activity_dish_model();
        
            //取市id
            $regionModel = new \model\region_model();
            $info = $regionModel->getPCodeByCCode($ac_city_area);
        
            $ac_city = !empty($info) ? $info['region_code']:'';
            if (empty($ac_city) || empty($ac_city_area)) ajaxReturnData(0,'抱歉，不存在这个地区，请重新刷新一下');
            $where .= " and IF(a.ac_city='$ac_city',a.ac_city_area='$ac_city_area' OR a.ac_city_area=0,IF(a.ac_city_area=0,a.ac_city=0,a.ac_city_area='$ac_city_area'))";
        }
        if ($type == 1){
            $where .=" and a.ac_p1_id = '$id' ";
        }elseif($type == 2){
            $where .=" and a.ac_p2_id = '$id' ";
        }else if ($type == 3){
            $where .=" AND LOCATE('$keyword',b.title) >0";
        }
        if($areaid) {
            $where1 = $where." and (a.ac_area_id = '$areaid' or a.ac_area_id=0) ";
        }else{
            $where1 = $where." and a.ac_area_id=0 ";
        }
        //sql拼接
        $sql_base = "SELECT a.ac_dish_id,a.ac_action_id,a.ac_area_id,a.ac_shop_dish,a.ac_dish_price,a.ac_dish_total,a.ac_dish_sell_total,b.title,b.thumb,b.marketprice";
        $sql1 = $sql_base." FROM ".table('activity_dish')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.ac_shop_dish=b.id WHERE ".$where1;
        
        //未开始的活动的id
        if (!empty($areaidArr)) $areaidStr = to_sqls($areaidArr,'','a.ac_area_id');
        if ($areaidStr) {
            $where2 = $where." and $areaidStr ";
            $sql2 = $sql_base." FROM ".table('activity_dish')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.ac_shop_dish=b.id  WHERE ".$where2;
        }
        
        //排序
        $status = intval(isset($_GP['status'])) ? intval($_GP['status']) : 1;//1表示综合，2表示价格，3表示销量
        $orderby = 'order by ';
        if ($status == 1){
            $orderby .= " a.ac_dish_id DESC ";
        }elseif ($status == 2){
            $price_type = isset($_GP['price_type']) ? $_GP['price_type'] : '1';
            if($price_type == 1){
                $price = 'asc';
            }else{
                $price = 'desc';
            }
            $orderby .= " a.ac_dish_price $price ";
        }else {
            $orderby .= " a.ac_dish_sell_total DESC ";
        }
        $limit = " limit ".$limit." , ".$psize;
        $sql1 .= $orderby.$limit;
        if ($sql2){
            $sql2 .= $orderby;
            $sql = "SELECT * FROM ($sql1) as t1 UNION SELECT * FROM ($sql2) as t2 $limit";
        }else{
            $sql1 .= $orderby;
            $sql = $sql1 ;
        }
        echo $sql;
        $list = mysqld_selectall($sql);
        if (empty($list)) ajaxReturnData(1,'暂时没有商品');
        
        $shopDishModel = new \model\shop_dish_model();
        foreach ($list as $key=>$v){
            if ($v['ac_area_id'] == $areaid){
                $list[$key]['status'] = 1;
            }
        }
        ajaxReturnData(1,'',$list);
    }    
}