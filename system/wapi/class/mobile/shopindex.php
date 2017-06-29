<?php
/**
 *小程序首页接口
 */

namespace wapi\controller;
class shopindex extends base{

    /* //返回活动区间接口
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
           $jdwd = getAreaid($jd,$wd);
           if (empty($jdwd)) ajaxReturnData(0,'参数错误');
           if ($jdwd['status'] == 0) {
               $cityCode = $jdwd['ac_city'];
               $where .=" and (ac_city='$cityCode' or ac_city=0)";
           }else{
               $ac_city = $jdwd['ac_city'];
               $ac_city_area = $jdwd['ac_city_area'];
               $where .= " and IF(ac_city='$ac_city',ac_city_area='$ac_city_area' OR ac_city_area=0,IF(ac_city_area=0,ac_city=0,ac_city_area='$ac_city_area'))";
           }
       }
       //获得当前时间的区域id
       $currentId = $actService->getCurrentArea();
       
       $actDishModel = new \model\activity_dish_model();
       foreach ($return as $key=>$val){
           $whereSql = $where;
           if ($currentId && ($currentId != $val['ac_area_id'])){
               $whereSql .= " and ac_dish_total > 0 ";
           }
           $where1 = $whereSql." and (ac_area_id = {$val['ac_area_id']} or ac_area_id=0) ";
           $info = $actDishModel->getAllActivtyDish($where1,'ac_dish_id');
           if (empty($info)) {
               unset($return[$key]);
               continue;
           }
           $res[]= $val;
       }
       if (empty($res)) ajaxReturnData(0,'暂无数据');
       $data['detail'] = $res;
       $data['ac_id'] = $list['ac_id'];
       ajaxReturnData(1,'',$data);
   } */
    public function active_area(){
       //取出活动列表，理论上是一个，但不排除有多个
       $list = getCurrentAct();
       if (empty($list)) ajaxReturnData(1,'暂时没有活动');
       $actService = new \service\wapi\activityService();
       $area = $actService->getActArea($list['ac_area']);
       if (empty($area)){
           ajaxReturnData(1,'对不起，没有设置时间区域');
       }
       //判断该区域是否有商品
       $_GP = $this->request;
       logg("接收：".var_export($_GP,1),"jieshou");
       $jd = isset($_GP['longitude']) ? $_GP['longitude'] : '';//经度
       $wd = isset($_GP['latitude']) ? $_GP['latitude'] : '';//纬度
       if (empty($area)) ajaxReturnData('1','暂无分配时间区域');
       
       foreach ($area as $key=>$val){
           $flag = $actService->checkIsGoods($list['ac_id'], $val['ac_area_id'],$jd,$wd);
           if (!$flag) {
               unset($area[$key]);
               continue;
           }
       }
       logg("返回1：".var_export($area,1),"fanhui1");
       if (empty($area)) ajaxReturnData('1','暂无数据');
       $return =array();
       if (empty($area)) $area = array();
       $type = intval($_GP['type']) ? intval($_GP['type']) : 0;
       if ($type == 1){
           $data['detail'] = $area;
           $data['ac_id'] = $list['ac_id'];
           ajaxReturnData('1','',$data);
       }else{
           if (count($area) < 5 ){
               $tmdate = date("Y-m-d",strtotime("+1 day"));
               $area1 = $actService->getActArea($list['ac_area'],$tmdate);
               foreach ($area1 as $key=>$val){
                   $flag = $actService->checkIsGoods($list['ac_id'], $val['ac_area_id'],$jd,$wd);
                   if (!$flag) {
                       unset($area1[$key]);
                       continue;
                   }
               }
               if (!empty($area1)) $area = array_merge($area,$area1);
               $return = array_slice($area,0,5,true);
           }else {
               $return = array_slice($area,0,5,true);
           }
           logg("返回2：".var_export($return,1),"fanhui2");
           $data['detail'] = $return;
           $data['ac_id'] = $list['ac_id'];
           //logg("返回：".var_export($data,1),"fanhui");
           ajaxReturnData('1','',$data);
       }
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
       $where = " a.ac_action_id = '$ac_list_id' and a.ac_dish_status=1 and (a.ac_area_id = '$ac_area_id' or a.ac_area_id=0) ";
       
       if (empty($jd) || empty($wd)){
           $cityCode = getCityidByIp();
           $where .=" and (a.ac_city='$cityCode' or a.ac_city=0)";
       }else{
           $return = getAreaid($jd,$wd);
           if (empty($return)) ajaxReturnData(0,'参数错误');
           if ($return['status'] == 0) {
               $cityCode = $return['ac_city'];
               $where .=" and (a.ac_city='$cityCode' or a.ac_city=0)";
           }else {
               $ac_city = $return['ac_city'];
               $ac_city_area = $return['ac_city_area'];
               $where .= " and IF(a.ac_city='$ac_city',a.ac_city_area='$ac_city_area' ,IF(a.ac_city_area=0,a.ac_city=0,a.ac_city_area='$ac_city_area'))";
           }
       }
       //获得当前时间的区域id
       $activityService = new \service\wapi\activityService();
       $currentId = $activityService->getCurrentArea();
       
       $sql = "SELECT a.*,b.title,b.thumb,b.marketprice from ".table('activity_dish')." as a left join ".table('shop_dish')." as b on a.ac_shop_dish = b.id where ";
       $sql .= $where;
       if ($currentId && ($currentId != $ac_area_id)){
           $sql .= " and a.ac_dish_total > 0";
       }
       //分页取数据
       $pindex = max(1, intval($_GP['page']));
       $psize = isset($_GP['limit']) ? $_GP['limit'] : 4;//默认每页4条数据
       $limit= ($pindex-1)*$psize;
       $orderby = " order by a.ac_dish_id DESC LIMIT ".$limit.",".$psize;
       $sql .=$orderby;
       $list = mysqld_selectall($sql);
       if (empty($list)) ajaxReturnData(1,'暂无商品');
       //shop_dish表取商品详情
       $data = array();
       foreach ($list as $key=>$v){
            $temp['title'] = $v['title'];
            $temp['thumb'] = $v['thumb'];
            $temp['marketprice'] = FormatMoney($v['marketprice'],0);
            $temp['ac_dish_id'] = $v['ac_dish_id'];
            $temp['ac_action_id'] = $v['ac_action_id'];
            $temp['ac_area_id'] = $v['ac_area_id'];
            $temp['ac_shop'] = $v['ac_shop'];
            $temp['ac_dish_price'] = FormatMoney($v['ac_dish_price'],0);
            $temp['ac_dish_total'] = $v['ac_dish_total'];
            $temp['flag'] = 1;
            if ($temp['ac_dish_total'] == 0) $temp['flag'] = 0;
            $temp['ac_dish_sell_total'] = $v['ac_dish_sell_total'];
            $temp['ac_shop_dish'] = $v['ac_shop_dish'];
            $data[] = $temp;
       }
       if (empty($data)) ajaxReturnData(1,'暂无商品');
       ajaxReturnData(1,'',$data);
   }
}