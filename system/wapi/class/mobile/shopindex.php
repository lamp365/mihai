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
       $list = getCurrentAct();
       if (empty($list)) ajaxReturnData(1,'暂时没有活动');
       
       //取时间段
       $actService = new \service\wapi\activityService();
       $_GP = $this->request;
       $type = intval($_GP['type']) ? intval($_GP['type']) : 0;
       if ($type == 1){
           $return = $actService->getActAreaNoExp($list['ac_area']);
       }else{
           $return = $actService->getActAreaNoExp($list['ac_area'],5);
       }
       if (empty($return)){
           ajaxReturnData(1,'对不起，没有设置时间区域');
       }
       
       //判断该区域是否有商品
       $_GP = $this->request;
       $jd = $_GP['longitude'];//经度
       $wd = $_GP['latitude'];//纬度
       $where = "ac_action_id={$list['ac_id']} and ac_dish_status=1 ";
       if (empty($jd) || empty($wd)){
           $cityCode = getCityidByIp();
           $where .=" and (ac_city='$cityCode' or ac_city=0)";
       }else{
           $return = getAreaid($jd,$wd);
           if (empty($return)) ajaxReturnData(0,'参数错误');
           if ($return['status'] == 0) ajaxReturnData(0,$return['mes']);
           $ac_city = $return['ac_city'];
           $ac_city_area = $return['ac_city_area'];
           $where .= " and IF(ac_city='$ac_city',ac_city_area='$ac_city_area' OR ac_city_area=0,IF(ac_city_area=0,ac_city=0,ac_city_area='$ac_city_area'))";
       }
       $actDishModel = new \model\activity_dish_model();
       foreach ($return as $key=>$val){
           $where1 = $where." and (ac_area_id = {$val['ac_area_id']} or ac_area_id=0) ";
           $info = $actDishModel->getAllActivtyDish($where1,'ac_dish_id');
           if (empty($info)) {
               unset($return[$key]);
               continue;
           }
           $res[]= $val;
       }
       $data['detail'] = $res;
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
       
       if (empty($jd) || empty($wd)){
           $cityCode = getCityidByIp();
           $where .=" and (ac_city='$cityCode' or ac_city=0)";
       }else{
           $return = getAreaid($jd,$wd);
           if (empty($return)) ajaxReturnData(0,'参数错误');
           if ($return['status'] == 0) ajaxReturnData(0,$return['mes']);
           $ac_city = $return['ac_city'];
           $ac_city_area = $return['ac_city_area'];
           $where .= " and IF(ac_city='$ac_city',ac_city_area='$ac_city_area' OR ac_city_area=0,IF(ac_city_area=0,ac_city=0,ac_city_area='$ac_city_area'))";
       }
       /* if (empty($jd) || empty($wd)) {//根据ip取城市
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
           $where .= " and IF(ac_city='$ac_city',ac_city_area='$ac_city_area' OR ac_city_area=0,IF(ac_city_area=0,ac_city=0,ac_city_area='$ac_city_area'))";
       } */
       
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
            $temp['ac_shop_dish'] = $v['ac_shop_dish'];
            $data[] = $temp;
       }
       ajaxReturnData(1,'',$data);
   } 
   

}