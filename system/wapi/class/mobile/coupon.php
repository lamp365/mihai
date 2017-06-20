<?php 
/**
 *优惠券接口
 */
namespace wapi\controller;
class coupon extends base{
    //优惠券列表
    public function couponList(){
        $GP = $this->request;
        $act_id = intval($GP['ac_id']);//活动id
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
                $data[$key]['coupon_amount'] = $val['amount_of_condition'];//已经转换过的
                $data[$key]['release_quantity'] = $val['release_quantity'];
                $data[$key]['coupon_name'] = $val['coupon_name'];
                $data[$key]['sts_id'] = $val['store_shop_id'];
                $data[$key]['thumb'] = '';
            }
            ajaxReturnData(1,'',$data);
        }else{
            ajaxReturnData(0,'没有优惠券');
        }
    }
    //领取优惠券
    public function getCoupon(){
        $member = get_member_account();
        $openid = $member['openid'];
        if (empty($openid)) ajaxReturnData(0,'请先登入');
        $_GP = $this->request;
        $stsid = intval($_GP['sts_id']);
        $scid = intval($_GP['scid']);
        if (empty($scid) || empty($stsid)) ajaxReturnData("0","",array('status'=>0,'mes'=>'请重新选择优惠券'));
        $data = array('scid'=>$scid,'stsid'=>$stsid,'openid'=>$openid);
        //判断是否有资格领取优惠券
        $couponService = new \service\shopwap\couponService();
        $flag = $couponService->IsCanGetCoupon($data);
        if ($flag && ($flag['status'] != 1)){
            if ($flag['status'] == -1){
                ajaxReturnData("0",$flag['mes']);//数据库查不到优惠券
            }else{
                $return = array(
                    'mes' => $flag['mes'],
                    'status' => 0
                );
                ajaxReturnData("0",'',$return);//不能领取
            }
        }
        //领取优惠券
        $getcoupons = $couponService->getCoupons($data);
        if ($getcoupons['status'] == 1){
            $flag = $couponService->IsCanGetCoupon($data);//领取完判断能不能再领取
            $return = array(
                'mes' => $getcoupons['mes'],
                'status' => $flag['status'],
            );
            ajaxReturnData("1",'',$return);
        }else {
            ajaxReturnData("0",$getcoupons['mes']);
        }
    }
    //我的优惠券列表
    public function mycoupon(){
        $member = get_member_account();
        $openid = $member['openid'];
        if (empty($openid)) ajaxReturnData(0,'请先登入');
        $couponMemModel = new \model\store_coupon_member_model();
        $couponMemModel->getAllMemberCoupon($where);
    }
}
?>