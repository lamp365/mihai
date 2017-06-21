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
        //取出当前活动
        $actListModel = new \model\activity_list_model();
        $list = $actListModel->getCurrentAct();
        if (empty($list)) ajaxReturnData(1,'暂时没有活动');
        //取当前的时间区域
        $actAreaModel = new \model\activity_area_model();
        $areaList = $actAreaModel->getAllActArea(array('ac_list_id'=>$list['ac_area']));
        if ($areaList){
            foreach ($areaList as $v){
                $mydate = date("Y:m:d")." ".date('H:i:s',$v['ac_area_time_end']);
                $endtime = strtotime($mydate);
                if (time() >= $v['ac_area_time_str'] && time() <= $v['ac_area_time_end']){
                    //$id
                }
            }
        } 
        
        
        $actDishModel = new \model\activity_dish_model();
        $_GP = $this->request;
        $type = intval($_GP['type']);//类型
        $id = intval($_GP['id']);//栏目id
        if (empty($type) || empty($id)) ajaxReturnData(1,'参数错误');
        $pindex = max(1, intval($_GP['page']));
        $psize = isset($_GP['limit']) ? $_GP['limit'] : 4;//默认每页4条数据
        $limit= ($pindex-1)*$psize;
        
        $status = intval(isset($_GP['status'])) ? intval($_GP['status']) : 1;//1表示综合，2表示价格，3表示销量
        
        if ($status == 1){
            $orderby = " ac_dish_id DESC ";
        }elseif ($status == 2){
            $price = isset($_GP['desc']) ? $_GP['desc'] : 'asc';
            $orderby = " ac_dish_price DESC ";
        }else {
            $orderby = " ac_dish_sell_total DESC ";
        }
        $orderby .= $limit." , ".$psize;
        $where = array(
            'ac_action_id'=>$list['ac_id'],
            'ac_dish_status'=>1
        );
        if ($type == 1){
            $where['ac_p1_id'] = $id;
        }else {
            $where['ac_p2_id'] = $id;
        }
        $dishInfo = $actDishModel->getAllActivtyDish($where,'ac_shop_dish,ac_dish_price,ac_dish_total,ac_dish_sell_total');
        if (empty($dishInfo)) ajaxReturnData(1,'暂时没有商品');
        $shopDishModel = new \model\shop_dish_model();
        foreach ($dishInfo as $v){
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