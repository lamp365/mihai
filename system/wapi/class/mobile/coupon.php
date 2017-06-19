<?php
/**
 *优惠券接口
 */

namespace wapi\controller;
class coupon extends base{
    //优惠券列表
    public function couponList(){
        $GP = $this->request;
        $act_id = intval($GP['act_id']);
        $actListModel = new \model\activity_dish_model();
        $actStore = $actListModel->getAllActivtyDish(array('ac_action_id'=>$act_id),'ac_shop','ac_dish_id DESC','ac_action_id');
        
        /* $openid = checkIsLogin();
        $return = $couponService->getStoreCoupons($storeid,$openid);
        $couponList = $return['couponList'];
        $coupon_amount = $return['coupon_amount'];
        $conpou_total = $return['total']; */
    }
}