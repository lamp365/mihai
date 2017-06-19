<?php
/**
 *优惠券接口
 */

namespace wapi\controller;
class coupon extends base{
    //优惠券列表
    public function couponList(){
        $GP = $this->request;
        $act_id = intval($GP['ac_list_id']);//活动id
        if (empty($act_id)) ajaxReturnData(0,'参数错误');
        $actListModel = new \model\activity_dish_model();
        $actStore = $actListModel->getAllActivtyDish(array('ac_action_id'=>$act_id),'ac_shop','ac_dish_id DESC','ac_shop');
        if(empty($actStore)) ajaxReturnData(0,'没有该活动');
        $member = get_member_account();
        $openid = $member['openid'];
        $couponService = new \service\shopwap\couponService();
        $couponList = array();
        foreach ($actStore as $key=>$v){
            $return = $couponService->getStoreCoupons($v['ac_shop'],$openid);
            foreach ($return['couponList'] as $val){
                $couponList[] = $val;
            }
        }
        if (!empty($couponList)){
            $data = array();
            foreach ($couponList as $key=>$val){
                $data[$key]['scid'] = $val['scid'];
                $data[$key]['coupon_amount'] = $val['amount_of_condition'];
                $data[$key]['release_quantity'] = $val['release_quantity'];
                $data[$key]['coupon_name'] = $val['coupon_name'];
            }
            ajaxReturnData(1,'',$data);
        }else{
            ajaxReturnData(0,'没有优惠券');
        }
}}

    
