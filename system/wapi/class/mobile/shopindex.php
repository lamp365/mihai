<?php
/**
 *小程序首页接口
 */

namespace wapi\controller;
class shopindex extends base{
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
       $jd = isset($_GP['longitude']) ? $_GP['longitude'] : '';//经度
       $wd = isset($_GP['latitude']) ? $_GP['latitude'] : '';//纬度
       logRecord(var_export($_GP,1), 'shopIndexarea');
       if (empty($area)) ajaxReturnData('1','暂无分配时间区域');
       
       foreach ($area as $key=>$val){
           $flag = $actService->checkIsGoods($list['ac_id'], $val['ac_area_id'],$jd,$wd);
           if (!$flag) {
               unset($area[$key]);
               continue;
           }
       }
       logRecord(var_export($area,1), 'shopIndexarea1');
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
           $data['detail'] = $return;
           $data['ac_id'] = $list['ac_id'];
           $data['ac_title'] = $list['ac_title'];
           ajaxReturnData('1','',$data);
       }
   }
   //根据活动区间返回活动商品
   public function active_dish()
   {
       $_GP = $this->request;
       $ac_list_id = intval($_GP['ac_id']);//活动id
       $ac_area_id = intval($_GP['ac_area_id']);//区域id
       logRecord(var_export($_GP,1), 'shopIndexdish');
       if (empty($ac_list_id) || empty($ac_area_id)) ajaxReturnData(0,'参数错误');
       $jd = $_GP['longitude'];//经度
       $wd = $_GP['latitude'];//纬度
       $where = " a.ac_action_id = '$ac_list_id' and a.ac_dish_status=1 and b.status=1 and (a.ac_area_id = '$ac_area_id' or a.ac_area_id=0) ";
       
        $sql_where = get_area_condition_sql($jd,$wd);
        if ($sql_where){
            $where .= $sql_where;
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
       logRecord($sql, 'shopindexDishSql');
       
       if (empty($list)){ 
           $returndata = array('status'=>0,'data'=>$list);
           ajaxReturnData(1,'暂无商品',$returndata);
       }
       //shop_dish表取商品详情
       $data = array();
       $storeShopModel = new \model\store_shop_model();
       $regionM = new \model\region_model();
       foreach ($list as $key=>$v){
            $temp['title'] = $v['title'];
            $temp['thumb'] = $v['thumb'];
            $temp['marketprice'] = FormatMoney($v['marketprice'],0);
            $temp['ac_dish_id'] = $v['ac_dish_id'];
            $temp['ac_action_id'] = $v['ac_action_id'];
            $temp['ac_area_id'] = $v['ac_area_id'];
            $temp['ac_shop'] = $v['ac_shop'];
            $store = $storeShopModel->getOneStoreShop(array('sts_id'=>$v['ac_shop']),'sts_id,sts_name,sts_avatar');
            $temp['storeInfo'] = $store;
            $temp['ac_dish_price'] = FormatMoney($v['ac_dish_price'],0);
            $temp['ac_dish_total'] = $v['ac_dish_total'];
            $temp['flag'] = 1;
            if ($temp['ac_dish_total'] == 0) $temp['flag'] = 0;
            $temp['ac_dish_sell_total'] = $v['ac_dish_sell_total'];
            $temp['ac_shop_dish'] = $v['ac_shop_dish'];
            $temp['peisong'] = '';
            if ($v['ac_city'] == 0){
                $temp['peisong'] = "全国";
            }elseif ($v['ac_city_area'] == 0){
                $qu = $regionM->getOneRegion(array('region_code'=>$v['ac_city']),'region_name');
                if ($qu) $temp['peisong'] = $qu['region_name'];
            }else{
                $qu = $regionM->getOneRegion(array('region_code'=>$v['ac_city_area']),'region_name');
                if ($qu) $temp['peisong'] = $qu['region_name'];
            }
            $data[] = $temp;
       }
       $status = 1;
       if (count($data) < $psize){
           $status = 0;
       }else {
           if (!$this->checkIsData($ac_list_id,$ac_area_id,$jd,$wd,$pindex+1,$psize)) $status = 0;
       }
       $returndata = array('status'=>$status,'data'=>$data);
       ajaxReturnData(1,'',$returndata);
   }
   //是否还有数据
   public function checkIsData($ac_id,$area_id,$longitude,$latitude,$page,$limit){
       $ac_list_id = $ac_id;//活动id
       $ac_area_id = $area_id;//区域id
       if (empty($ac_list_id) || empty($ac_area_id)) return false;
       $jd = $longitude;//经度
       $wd = $latitude;//纬度
       $where = " a.ac_action_id = '$ac_list_id' and a.ac_dish_status=1 and b.status=1 and (a.ac_area_id = '$ac_area_id' or a.ac_area_id=0) ";
        
       $sql_where = get_area_condition_sql($jd,$wd);
       if ($sql_where){
           $where .= $sql_where;
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
       $pindex = $page;
       $psize = $limit;//默认每页4条数据
       $limit= ($pindex-1)*$psize;
       $orderby = " order by a.ac_dish_id DESC LIMIT ".$limit.",".$psize;
       $sql .=$orderby;
       $list = mysqld_selectall($sql);
       if (!empty($list)){
           return true;
       }   
   }
}