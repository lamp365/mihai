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
        $actDishModel = new \model\activity_dish_model();
        $_GP = $this->request;
        $pindex = max(1, intval($_GP['page']));
        $psize = isset($_GP['limit']) ? $_GP['limit'] : 4;//默认每页4条数据
        $limit= ($pindex-1)*$psize;
        
        $type = intval(isset($_GP['type'])) ? intval($_GP['type']) : 1;//1表示综合，2表示价格，3表示销量
        
        if ($type == 1){
            $orderby = " ac_dish_id DESC ";
        }elseif ($type == 2){
            $price = !empty((isset($_GP['desc'])) ? $_GP['desc'] : 'asc' ;
            $orderby = " ac_dish_price DESC ";
        }else {
            $orderby = " ac_dish_sell_total DESC ";
        }
        $orderby .= $limit." , ".$psize;
        $actDishModel->getAllActivtyDish(array('ac_action_id'=>$list['ac_id'],'ac_dish_status'=>0),'ac_shop_dish,ac_dish_price,ac_dish_total,ac_dish_sell_total');
    }    
    
    
}