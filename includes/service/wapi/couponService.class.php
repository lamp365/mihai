<?php
/**
优惠券
 */
namespace service\wapi;

class couponService extends \service\publicService
{
    /**
     * 获得所有优惠券，
     * $openid 
     *   */
    public function getAllCoupon($openid=''){
        $mytime = time();
        $couponModel = new \model\store_coupon_model();
        $couponsList = $couponModel->getAllCoupon(" `payment` != 1 and {$mytime} >=`receive_start_time` and {$mytime} <=`receive_end_time`");
        if ($couponsList){
            if (empty($openid)){//如果没有获得openid则取所有未过期的
                $data['openid'] = $openid;
                foreach ($couponsList as $key=>$v){
                    $data['scid'] = $v['scid'];
                    $data['stsid'] = $v['store_shop_id'];
                    $res = $this->IsCanGetCoupon($data);
                    $list = array();
                    if ($res['status'] == 1){
                        $list[$key] = $v;
                        $list[$key]['coupon_amount'] = FormatMoney($v['coupon_amount'],0);
                        $list[$key]['amount_of_condition'] = FormatMoney($v['amount_of_condition'],0);
                    }
                    return $list;
                } 
                
            }else{//取可以领取的
                foreach ($couponsList as $key=>$v){
                    $couponsList[$key]['coupon_amount'] = FormatMoney($v['coupon_amount'],0);
                    $couponsList[$key]['amount_of_condition'] = FormatMoney($v['amount_of_condition'],0);
                }
                return $couponsList;
            }
        }else {
            return array();
        }
        
   } 
    /**
     * 是否可以领取优惠券
     * $data array
     * return array
     *   */
    public function IsCanGetCoupon($data){
        if (!empty($data) && is_array($data)){
            $now = time();
            $con = "`scid`={$data['scid']} and `store_shop_id`={$data['stsid']}";
            $couponModel = new \model\store_coupon_model();
            $info = $couponModel->getOneCoupon($con,"scid,release_quantity,receive_start_time,receive_end_time,inventory,get_limit");
            $return['status'] = 0;
            /* and `payment`!=1 and {$now} >= `receive_start_time` and {$now} <= `receive_end_time` and `inventory`>0 */
            if (empty($info)) {
                $return['status'] = -1;
                $return['mes'] = '对不起,优惠券不存在';
                return $return;
            }
            if (($now < $info['receive_start_time']) || ($now > $info['receive_end_time'])){
                $return['mes'] = '对不起,不在领取的范围内';
                return $return;
            }
            if (($info['release_quantity']-$info['inventory']) < 1){
                $return['mes'] = '对不起,优惠券数量不足';
                return $return;
            }
            if ($info['get_limit']==0) {
                $return['status'] = 1;
                return $return;//用户可以无限制领取
            }
            $couponMemModel = new \model\store_coupon_member_model();
            $myCoupons = $couponMemModel->getAllMemberCoupon(array('openid'=>$data['openid'],'scid'=>$data['scid']));
            if (empty($myCoupons)){
                $return['status'] = 1;
                return $return;
            }
            if (count($myCoupons) < $info['get_limit']) {
                $return['status'] = 1;
                return $return;
            }else {
                $return['mes'] = '对不起,你已经领取过了';
                return $return;
            }
        }
    }
    /**
     * 用户领取优惠券
     * $data array
     * return array
     *  */
    public function getCoupons($data){
        $member=get_member_account();
        if (!empty($data) && is_array($data)){
            $storeCouponModel = new \model\store_coupon_model();
            $couponInfo = $storeCouponModel->getOneCoupon(array('scid'=>$data['scid']));
            $insertData = array(
                'scid'=>$data['scid'],
                'receive_time'=>time(),
                'status'=>0,
                'openid'=>$member['openid'],
                'coupon_amount'=>$couponInfo['coupon_amount'],
                'amount_of_condition'=>$couponInfo['amount_of_condition'],
                'use_start_time'=>$couponInfo['use_start_time'],
                'use_end_time'=>$couponInfo['use_end_time'],
                'store_shop_dishid'=>$couponInfo['store_shop_dishid'],
                'usage_mode'=>$couponInfo['usage_mode'],
                'store_category_idone'=>$couponInfo['store_category_idone'],
                'store_category_idtwo'=>$couponInfo['store_category_idtwo'],
                'coupon_name'=>$couponInfo['coupon_name'],
                'coupon_img'=>$couponInfo['coupon_img'],
                'store_shop_id'=>$couponInfo['store_shop_id'],
            );
            try {
                begin();
                $row = null;
                mysqld_insert("store_coupon_member",$insertData);
                
                if (!mysqld_insertid()) throw new \PDOException('增加失败');
                $now = time();
                $couponModel = new \model\store_coupon_model();
                $info = $couponModel->getOneCoupon("`scid`={$data['scid']} and {$now} >= `receive_start_time` and {$now} <= `receive_end_time`","release_quantity,inventory");
                if (empty($info)) throw new \PDOException('对不起，不在领取的时间范围内');
                $flag = $info['release_quantity']-$info['inventory'];
                if($flag < 0) throw new \PDOException('对不起，优惠券数量不足');
                $new_inventory = $info['inventory']+1;
                
                $storeCouponModel->update($storeCouponModel->table_name,array('inventory'=>$new_inventory),array('scid'=>$data['scid']));
                commit();
                $return['status'] = 1;
                $return['mes'] = '恭喜你，优惠券领取成功';
                return $return;
            } catch (\PDOException $e) {
                rollback();
                $return['status'] = 0;
                $return['mes'] = $e->getMessage();
                return $return;
            }
        }
    }
}