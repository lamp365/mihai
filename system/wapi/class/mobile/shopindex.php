<?php
/**
 *小程序首页接口
 */

namespace wapi\controller;
class shopindex extends base{

    //返回活动区间接口
   public function active_area()
   {
       //取出活动列表，理论上是一个，但不排除有多个
       $actListModel = new \model\activity_list_model();
       $list = $actListModel->getCurrentAct();
       if (empty($list)) ajaxReturnData(1,'暂时没有活动');
       $actAreaModel = new \model\activity_area_model();
       //取时间段
       $activty_area = $actAreaModel->getAll(array('ac_list_id'=>$list['ac_area']));
       if (empty($activty_area)) ajaxReturnData(0,'没有设置时间段');
       foreach ($activty_area as $key=>$val){
           $startDate = date("Y:m:d")." ".date('H:i:s',$val['ac_area_time_str']);
           $endDate = date("Y:m:d")." ".date('H:i:s',$val['ac_area_time_end']);
           $starttime = strtotime($startDate);
           $endtime = strtotime($endDate);
           if ($endtime <= time()) continue;
           $temp['ac_area_id'] = $val['ac_area_id'];
           $temp['ac_area_time_str'] = date("H:i",$val['ac_area_time_str']);
           $temp['ac_area_time_end'] = date("H:i",$val['ac_area_time_end']);
           $temp['status'] = 0;
           if (time() >= $starttime && time() <= $endtime){
               $temp['status'] = 1;
               $temp['section'] = $endtime-time();
           }else{
               $temp['section'] = $starttime-time();
           }
           $tempAll[] = $temp;
       }
       if (count($tempAll) > 5){
           for ($i=0;$i<5;$i++){
               $trueAll[$i] = $tempAll[$i];
           }
       }else {
           $trueAll = $tempAll;
       }
       $data['detail'] = $trueAll;
       $data['ac_id'] = $list['ac_id'];
       ajaxReturnData(1,'',$data);
   }
   //根据活动区间返回活动商品
   public function active_dish()
   {
       $_GP = $this->request;
       $ac_list_id = intval($_GP['ac_id']);//活动id
       $ac_area_id = intval($_GP['ac_area_id']);//区域id
       if (empty($ac_list_id) || empty($ac_area_id)) ajaxReturnData(0,'参数错误');
       $jd = $_GP['longitude'];//经度
       $wd = $_GP['latitude'];//纬度
       $where = " ac_action_id = '$ac_list_id' and ac_dish_status=1 and (ac_area_id = '$ac_area_id' or ac_area_id=0) ";
       
       if (empty($jd) || empty($wd)) {//根据ip取城市
           $ip = getClientIP();
           $info = getCodeByIP($ip);
           if ($info){
               $info = json_decode($info,1);
               $cityCode = $info['adcode'];
           }
           if (empty($cityCode)) $cityCode = '350100';//如果未取到ip，则取福州
           $where .=" and (ac_city='$cityCode' or ac_city=0)";
       }else{
           //高德接口获取区域id
           $return = json_decode(getCodeByLttAndLgt($jd,$wd),1);
           if ($return['status'] == 0) ajaxReturnData(0,'抱歉，获取地里位置信息失败，请刷新一下');
           $ac_city_area = isset($return['regeocode']['addressComponent']['adcode'])?$return['regeocode']['addressComponent']['adcode']:'';
           //取市id
           $regionModel = new \model\region_model();
           $info = $regionModel->getPCodeByCCode($ac_city_area);
           $ac_city = !empty($info) ? $info['region_code']:'';
           if (empty($ac_city) || empty($ac_city_area)) ajaxReturnData(0,'抱歉，不存在这个地区，请重新刷新一下');
           $where .= " and IF(ac_city='$ac_city',ac_city_area='$ac_city_area',IF(ac_city_area=0,ac_city='$ac_city' OR ac_city=0,ac_city=0))";
       }
       
       //分页取数据
       $pindex = max(1, intval($_GP['page']));
       $psize = isset($_GP['limit']) ? $_GP['limit'] : 4;//默认每页4条数据
       $limit= ($pindex-1)*$psize;
       $orderby = " ac_dish_id DESC LIMIT ".$limit.",".$psize;
       $actDishModel = new \model\activity_dish_model();
       $list = $actDishModel->getAllActivtyDish($where,"*",$orderby);
       if (empty($list)) ajaxReturnData(1,'暂无商品信息');

       //shop_dish表取商品详情
       $shopDishModel = new \model\shop_dish_model();
       $data = array();
       foreach ($list as $key=>$v){
            $goods = $shopDishModel->getOneShopDish(array('id'=>$v['ac_shop_dish']),'title,thumb,marketprice');
            if(empty($goods)) continue;
            $temp['title'] = $goods['title'];
            $temp['thumb'] = $goods['thumb'];
            $temp['marketprice'] = FormatMoney($goods['marketprice'],0);
            $temp['ac_dish_id'] = $v['ac_dish_id'];
            $temp['ac_action_id'] = $v['ac_action_id'];
            $temp['ac_area_id'] = $v['ac_area_id'];
            $temp['ac_shop'] = $v['ac_shop'];
            $temp['ac_dish_price'] = FormatMoney($v['ac_dish_price'],0);
            $temp['ac_dish_total'] = $v['ac_dish_total'];
            $temp['ac_dish_sell_total'] = $v['ac_dish_sell_total'];
            $data[] = $temp;
       }
       ajaxReturnData(1,'',$data);
   } 
   

}