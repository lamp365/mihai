<?php
/**
优惠券的service层
 */
namespace service\shopwap;
use model\store_coupon_model;
use model\store_coupon_member_model;
class couponService extends \service\publicService
{
    public $error_location = 0;

    public function getErrorLocation()
    {
        return $this->error_location;
    }
    /**
     * 获得店铺优惠券
     *   */
    public function getStoreCoupons($storeid,$openid){
        if (empty($storeid)) return ;
        $mytime = time();
        $couponModel = new \model\store_coupon_model();
        $couponsList = $couponModel->getAllCoupon("`store_shop_id`={$storeid} and `payment` != 1 and {$mytime} >=`receive_start_time` and {$mytime} <=`receive_end_time`");
        if (!empty($couponsList)){
            $coupon_amount = 0;
            if ($openid){//用户已经登入，则返回可以领取的优惠券
                $data = array(
                    'openid' => $openid,
                    'stsid' => $storeid,
                );
                foreach ($couponsList as $key=>$v){
                    $data['scid'] = $v['scid'];
                    $res = $this->IsCanGetCoupon($data);
                    if ($res['status'] == 1){
                        $list[$key] = $v;
                        $coupon_amount += $v['coupon_amount'];
                        $list[$key]['coupon_amount'] = FormatMoney($v['coupon_amount'],0);
                        $list[$key]['amount_of_condition'] = FormatMoney($v['amount_of_condition'],0);
                    }
                }
            }else{
                foreach ($couponsList as $key=>$v){
                    $list[$key] = $v;
                    $coupon_amount += $v['coupon_amount'];
                    $list[$key]['coupon_amount'] = FormatMoney($v['coupon_amount'],0);
                    $list[$key]['amount_of_condition'] = FormatMoney($v['amount_of_condition'],0);
                }
            }
            
            $return = array(
               'couponList' => $list, 
               'coupon_amount' => floor(FormatMoney($coupon_amount,0)), 
               'total' => count($list), 
            );
            return $return;
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
            $insertData = array(
                'scid'=>$data['scid'],
                'receive_time'=>time(),
                'status'=>0,
                'nickname'=>$member['nickname'],
                'mobile'=>$member['mobile'],
                'openid'=>$member['openid'],
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
                $storeCouponModel = new store_coupon_model();
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
    /**
     * 根据该商品选择一张最好的优惠券使用
     * @param array $data  */
    public function getBestCouponByDishid($data = array()){
        if (!empty($data)){
            $member=get_member_account();
            $return['status'] = 0;
            $dishid = intval($data['dishid']);//商品id
            $stsid = intval($data['stsid']);//店铺id
            $price = $data['price'];//店铺id
            $now = time();
            //查看自己领取的优惠券
            $couponMemModel = new \model\store_coupon_member_model();
            $myCoupon = $couponMemModel->getAllMemberCoupons(array('openid'=>$member['openid'],'status'=>0),"scmid,scid");
            if(empty($myCoupon)) {
                $return['mes'] = '抱歉，暂无优惠券可以使用';
                return $return;
            }
            $canUseCoupon = array();
            //查找可以使用的优惠券
            foreach ($myCoupon as $key=>$v){
                $where = "`scid`={$v['scid']} and `store_shop_id`={$stsid} and `usage_mode`=3 and `use_start_time`<={$now} and `use_end_time` >= {$now} and {$price}>=`amount_of_condition`";
                $coupon = $this->getCouponsByCondition($where,"use_start_time,use_end_time,store_shop_dishid,amount_of_condition,coupon_amount");
                if(!empty($coupon)) {
                    $store_shop_dishid = json_decode($coupon['store_shop_dishid']);
                    if (in_array($dishid, $store_shop_dishid)){
                        //该商品可以使用
                        $canUseCoupon[$key]['scmid'] = $v['scmid'];
                        $canUseCoupon[$key]['scid'] = $v['scid'];
                        $canUseCoupon[$key]['coupon_amount'] = $coupon['coupon_amount'];//优惠金额
                        $canUseCoupon[$key]['use_end_time'] = $coupon['use_end_time'];//优惠金额
                    }
                }
            }
            if (empty($canUseCoupon)) {
                $return['mes'] = '抱歉，暂无优惠券可以使用';
                return $return;
            }else{
                return $this->checkBestConpus($canUseCoupon);
            }
        }
    }
    /**
     * 根据该栏目选择一张最好的优惠券使用
     * @param array $data
     * */
    public function getBestCouponByCategory($data){
        if (!empty($data)){
            $member=get_member_account();
            $return['status'] = 0;
            $dishid = intval($data['dishid']);//商品id
            $store_category_idone = intval($data['store_category_idone']);//一级分类
            $store_category_idtwo = intval($data['store_category_idtwo']);//二级分类
            $stsid = intval($data['stsid']);//店铺id
            $price = $data['price'];//店铺id
            $now = time();
            //查看自己领取的优惠券
            $couponMemModel = new \model\store_coupon_member_model();
            $myCoupon = $couponMemModel->getAllMemberCoupons(array('openid'=>$member['openid'],'status'=>0),"scmid,scid");
            if(empty($myCoupon)) {
                $return['mes'] = '抱歉，暂无优惠券可以使用';
                return $return;
            }
            $canUseCoupon = array();
            //查找可以使用的优惠券
            foreach ($myCoupon as $key=>$v){
                $where = "`scid`={$v['scid']} and `store_shop_id`={$stsid} and `usage_mode`=2 and `use_start_time`<={$now} and `use_end_time` >= {$now} and {$price}>=`amount_of_condition`";
                $coupon = $this->getCouponsByCondition($where,"use_start_time,use_end_time,store_category_idone,store_category_idtwo,amount_of_condition,coupon_amount");
                if(!empty($coupon)) {
                    if ($coupon['store_category_idtwo'] ==0){
                        if ($store_category_idone == $coupon['store_category_idone']){
                            //该商品可以使用
                            $canUseCoupon[$key]['scmid'] = $v['scmid'];
                            $canUseCoupon[$key]['scid'] = $v['scid'];
                            $canUseCoupon[$key]['coupon_amount'] = $coupon['coupon_amount'];//优惠金额
                            $canUseCoupon[$key]['use_end_time'] = $coupon['use_end_time'];//优惠金额
                        }
                    }else {
                        if ($store_category_idone == $coupon['store_category_idone'] && $store_category_idtwo == $coupon['store_category_idtwo']){
                            //该商品可以使用
                            $canUseCoupon[$key]['scmid'] = $v['scmid'];
                            $canUseCoupon[$key]['scid'] = $v['scid'];
                            $canUseCoupon[$key]['coupon_amount'] = $coupon['coupon_amount'];//优惠金额
                            $canUseCoupon[$key]['use_end_time'] = $coupon['use_end_time'];//优惠金额
                        }
                    }
                    
                }
            }
            if (empty($canUseCoupon)) {
                $return['mes'] = '抱歉，暂无优惠券可以使用';
                return $return;
            }else{
                return $this->checkBestConpus($canUseCoupon);
            }
        }
    }
    /**
     * 选择店铺通用的优惠券使用
     * @param array $data
     * */
    public function getCouponByStore($data){
        if (!empty($data)){
            $member=get_member_account();
            $return['status'] = 0;
            $stsid = intval($data['stsid']);//店铺id
            $price = $data['price'];//店铺id
            $now = time();
            //查看自己领取的优惠券
            $couponMemModel = new \model\store_coupon_member_model();
            $myCoupon = $couponMemModel->getAllMemberCoupons(array('openid'=>$member['openid'],'status'=>0),"scmid,scid");
            if(empty($myCoupon)) {
                $return['mes'] = '抱歉，暂无优惠券可以使用';
                return $return;
            }
            $canUseCoupon = array();
            //查找可以使用的优惠券
            foreach ($myCoupon as $key=>$v){
                $where = "`scid`={$v['scid']} and `store_shop_id`={$stsid} and `usage_mode`=1 and `use_start_time`<={$now} and `use_end_time` >= {$now} and {$price}>=`amount_of_condition`";
                $coupon = $this->getCouponsByCondition($where,"use_start_time,use_end_time,store_category_idone,store_category_idtwo,amount_of_condition,coupon_amount");
                if(!empty($coupon)) {
                    //该商品可以使用
                    $canUseCoupon[$key]['scmid'] = $v['scmid'];
                    $canUseCoupon[$key]['scid'] = $v['scid'];
                    $canUseCoupon[$key]['coupon_amount'] = $coupon['coupon_amount'];//优惠金额
                    $canUseCoupon[$key]['use_end_time'] = $coupon['use_end_time'];//优惠金额
    
                }
            }
            if (empty($canUseCoupon)) {
                $return['mes'] = '抱歉，暂无优惠券可以使用';
                return $return;
            }else{
                return $canUseCoupon;
            }
        }
    }
    /**
     * 根据优惠券列表，选出最优的优惠券
     * @param array $data  */
    public function checkBestConpus($data = array()){
        if (!empty($data)){
            $max = 0;
            $bestCoupon = array();
            //选最优的优惠券
            foreach ($data as $v){
                if ($v['coupon_amount'] > $max){
                    //选择优惠最多的优惠券
                    $max = $v['coupon_amount'];
                    $bestCoupon = $v;
                }elseif ($v['coupon_amount']==$max){
                    //当优惠金额一样时，选择最近过期的优惠券
                    if ($bestCoupon['use_end_time'] >= $v['use_end_time']){
                        $bestCoupon = $v;
                    }
                }
            }
            return $bestCoupon;
        }
    }
}