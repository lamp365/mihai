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
       $now = time();
       $where = "ac_status=0 and ac_time_end > $now";
       $list = $actListModel->getAllActList($where,'ac_id,ac_title,ac_time_str,ac_time_end');
       if ($list && count($list) == 0){
           ajaxReturnData(0,'暂无活动');
       }elseif ($list && count($list) == 1){
            $actAreaModel = new \model\activity_area_model();
            $list = $list[0];
            $activty_area = $actAreaModel->getAllActArea(array('ac_list_id'=>$list['ac_id'],'ac_area_status'=>0),"ac_area_id,ac_area_time_str,ac_area_time_end");
            if (empty($activty_area)) ajaxReturnData(0,'没有设置时间段');
            foreach ($activty_area as $key=>$v){
                $activty_area[$key]['ac_area_time_str'] = date("H:i",$v['ac_area_time_str']);
                $activty_area[$key]['ac_area_time_end'] = date("H:i",$v['ac_area_time_end']);
                $activty_area[$key]['ac_id'] = $list['ac_id'];
            }
            ajaxReturnData(1,'',$activty_area);
       }else {//同时存在活动列表大于2的话先不考虑
           
       }
   }
   //根据活动区间返回活动商品
   public function active_dish()
   {
       $_GP = $this->request;
       $ac_list_id = intval($_GP['ac_list_id']);//活动id
       $ac_area_id = intval($_GP['ac_area_id']);//区域id
       if (empty($ac_list_id) || empty($ac_area_id)) ajaxReturnData(0,'参数错误');
       $jd = $_GP['longitude'];//经度
       $wd = $_GP['latitude'];//纬度
       if (empty($jd) || empty($wd)) ajaxReturnData(0,'没有打开地里位置,获取不到商品信息');
       
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
       $where = "ac_action_id = '$ac_list_id' and ac_dish_status=0 and (ac_area_id = '$ac_area_id' or ac_area_id=0) and IF(ac_city='$ac_city',ac_city_area='$ac_city_area',IF(ac_city_area=0,ac_city='$ac_city' OR ac_city=0,ac_city=0))";
       
       //分页取数据
       $pindex = max(1, intval($_GP['page']));
       $psize = isset($_GP['limit']) ? $_GP['limit'] : 4;//默认每页4条数据
       $limit= ($pindex-1)*$psize;
       $orderby = " ac_dish_id DESC LIMIT ".$limit.",".$psize;
       $list = $actDishModel->getAllActivtyDish($where,"*",$orderby);
       if (empty($list)) ajaxReturnData(1,'暂无商品信息');

       //shop_dish表取商品详情
       $shopDishModel = new \model\shop_dish_model();
       $data = array();
       foreach ($list as $key=>$v){
            $goods = $shopDishModel->getOneShopDish(array('id'=>$v['ac_shop_dish']),'title,thumb,marketprice');
            if(empty($goods)) continue;
            $data[$key]['title'] = $goods['title'];
            $data[$key]['thumb'] = $goods['thumb'];
            $data[$key]['marketprice'] = $goods['marketprice'];
            $data[$key]['ac_dish_id'] = $v['ac_dish_id'];
            $data[$key]['ac_action_id'] = $v['ac_action_id'];
            $data[$key]['ac_area_id'] = $v['ac_area_id'];
            $data[$key]['ac_shop'] = $v['ac_shop'];
            $data[$key]['ac_dish_price'] = $v['ac_dish_price'];
            $data[$key]['ac_dish_total'] = $v['ac_dish_total'];
            $data[$key]['ac_dish_sell_total'] = $v['ac_dish_sell_total'];
       }
       ajaxReturnData(1,'',$data);
   } 
   

}