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
        $type = intval($_GP['type']);//类型
        $id = intval($_GP['id']);//栏目id
        if (empty($type) || empty($id)) ajaxReturnData(1,'参数错误');
        $jd = $_GP['longitude'];//经度
        $wd = $_GP['latitude'];//纬度
        
        //分页
        $pindex = max(1, intval($_GP['page']));
        $psize = isset($_GP['limit']) ? $_GP['limit'] : 10;//默认每页4条数据
        $limit= ($pindex-1)*$psize;
        
        //取出当前活动
        $actListModel = new \model\activity_list_model();
        $list = $actListModel->getCurrentAct();
        if (empty($list)) ajaxReturnData(1,'暂时没有活动');
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
        $where = " ac_action_id={$list['ac_id']} and ac_dish_status=1 ";
        if ($type == 1){
            $where .=" and ac_p1_id = '$id' ";
        }else {
            $where .=" and ac_p2_id = '$id' ";
        }
        
        if (empty($jd) || empty($wd)) {
            //高德地图根据ip获取城市
            $ip = getClientIP();
            $info = getCodeByIP($ip);
            if ($info){
                $info = json_decode($info,1);
                $cityCode = $info['adcode'];
            }
            if (empty($cityCode)) $cityCode = '350100';//如果未取到ip，则取福州
            $where .=" and (ac_city='$cityCode' or ac_city=0) ";
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
            $where .= " and IF(ac_city='$ac_city',ac_city_area='$ac_city_area' OR ac_city_area=0,IF(ac_city_area=0,ac_city=0,ac_city_area='$ac_city_area'))";
        }
        
        //sql拼接
        $sql_base = "SELECT ac_dish_id,ac_action_id,ac_area_id,ac_shop_dish,ac_dish_price,ac_dish_total,ac_dish_sell_total";
        if($areaid) {
            $where1 = $where." and (ac_area_id = '$areaid' or ac_area_id=0) ";
        }else{
            $where1 = $where." and ac_area_id=0 ";
        }
        $sql1 = $sql_base." FROM ".table('activity_dish')." WHERE ".$where1;
        
        //未开始的活动的id
        if (!empty($areaidArr)) $areaidStr = to_sqls($areaidArr,'','ac_area_id');
        if ($areaidStr) {
            $where2 = $where." and $areaidStr ";
            $sql2 = $sql_base." FROM ".table('activity_dish')." WHERE ".$where2;
        }
        
        //排序
        $status = intval(isset($_GP['status'])) ? intval($_GP['status']) : 1;//1表示综合，2表示价格，3表示销量
        $orderby = 'order by ';
        if ($status == 1){
            $orderby .= " ac_dish_id DESC ";
        }elseif ($status == 2){
            $price_type = isset($_GP['price_type']) ? $_GP['price_type'] : '1';
            if($price_type == 1){
                $price = 'asc';
            }else{
                $price = 'desc';
            }
            $orderby .= " ac_dish_price $price ";
        }else {
            $orderby .= " ac_dish_sell_total DESC ";
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
        $list = mysqld_selectall($sql);
        if (empty($list)) ajaxReturnData(1,'暂时没有商品');
        
        $shopDishModel = new \model\shop_dish_model();
        foreach ($list as $v){
            $goods = $shopDishModel->getOneShopDish(array('id'=>$v['ac_shop_dish']),'title,thumb,marketprice');
            if(empty($goods)) continue;
            $temp['title'] = $goods['title'];
            $temp['thumb'] = $goods['thumb'];
            $temp['marketprice'] = FormatMoney($goods['marketprice'],0);
            $temp['ac_dish_id'] = $v['ac_dish_id'];
            $temp['ac_action_id'] = $v['ac_action_id'];
            $temp['ac_area_id'] = $v['ac_area_id'];
            $temp['ac_dish_price'] = FormatMoney($v['ac_dish_price'],0);
            $temp['ac_dish_total'] = $v['ac_dish_total'];
            $temp['ac_dish_sell_total'] = $v['ac_dish_sell_total'];
            $temp['status'] = 0;
            if ($v['ac_area_id'] == $areaid){
                $temp['status'] = 1;
            }
            $data[] = $temp;
        }
        ajaxReturnData(1,'',$data);
    }    
}