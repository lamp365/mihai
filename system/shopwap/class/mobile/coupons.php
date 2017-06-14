<?php
/* $op     = empty($_GP['op']) ? 'list' : $_GP['op'];
$openid = checkIsLogin();

if($op == 'list'){  //领券列表
    $sql = "SELECT type_id,type_name,type_money,use_start_date,use_end_date,min_goods_amount,send_max FROM " . table('bonus_type');
    $sql.= " where (send_type =1 or send_type =2) and deleted = 0 ";		//非新手礼优惠券
    $sql.= " and send_start_date<=".time()." and send_end_date >=".time();

    //显示新手礼之外的可领优惠券
    $bonus = mysqld_selectall($sql);

    //已登录用户
    if ($openid && !empty($bonus)) {
        //已领过的优惠劵  以及领取过的次数
        $arrBonusUser = mysqld_selectall('SELECT bonus_type_id,count(openid) as cnt FROM ' . table('bonus_user') . " where openid={$openid} group by bonus_type_id");

        foreach($bonus as $bk => $bv){
            //如果该优惠卷已经在积分兑换中存在，则去除掉
            $award = mysqld_select("select id from ".table('addon7_award')." where award_type=2 and gid={$bv['type_id']}");
            if($award){
                unset($bonus[$bk]);
            }

            if(!empty($arrBonusUser)) {
                if($bv['send_max']==0){
                    continue;
                }else {
                    foreach($arrBonusUser as $uk=>$uv) {
                        //次数已经上限了
                        if($uv['bonus_type_id'] == $bv['type_id'] && $uv['cnt']>=$bv['send_max']) {
                            unset($bonus[$bk]);
                        }
                    }
                }
            }
        }

    }
    //记住当前地址
    tosaveloginfrom();
    include themePage('coupons');
} */
namespace shopwap\controller;
use  service\shopwap\couponService; 
class coupons
{
    public function __construct(){
        $this->couponsService = new couponService();
    }
    //展示该店铺的优惠券
	public function index()
	{
	    $_GP = $this->request;
	    //$stsid = intval($_GP['stsid']);
	    $stsid = 1;
	    if (!$stsid) message("请重新选择店铺","refresh");
	    $mytime = time();
	    $couponsList = $this->couponsService->getAllCouponsByCondition("`store_shop_id`={$stsid} and `payment` != 1 and {$mytime} >=`receive_start_time` and {$mytime} <=`receive_end_time`");
	    if (!empty($couponsList)){
	        foreach ($couponsList as $v){
	            //var_dump(json_decode($v['store_shop_dishid']));
	        }
	    }
	    include page('coupons/lists');
	}
	//用户获得优惠券
	public function getCoupons(){
	    
	    if (!$openid=checkIsLogin()) message("请先登入，再领取优惠券");
	    
	    $_GP = $this->request;
	    $stsid = intval($_GP['stsid']);
	    $scid = intval($_GP['scid']);
	    if (empty($scid) || empty($stsid)) message("请重新选择优惠券");
	    $data = array('scid'=>$scid,'stsid'=>$stsid,'openid'=>$openid);
	    //判断是否有资格领取优惠券
	    $flag = $this->couponsService->IsCanGetCoupon($data);
	    if ($flag && ($flag['status'] == 0)){
	        message($flag['mes']);//不能领取优惠券
	    }
	    //领取优惠券
	    $getcoupons = $this->couponsService->getCoupons($data);
	    if ($getcoupons['status'] == 1){
	        echo $getcoupons['mes'];exit;
	        message($getcoupons['mes'],"refresh");
	    }else {
	        echo $getcoupons['mes'];exit;
	    }
	}
	//根据该商品选择一张最好的优惠券使用
	public function test(){
	    $data = array(
	        'dishid'=>2,
	        'stsid'=>1,
	        'price'=>FormatMoney(1000,1) ,
	    );
	    $info = $this->couponsService->getBestCouponByDishid($data);
	    var_dump($info);
	}
	//根据分类选择一张最好的优惠券使用
	public function test2(){
	    $data = array(
	        'dishid'=>2,
	        'store_category_idone'=>71,//一级栏目id
	        'store_category_idtwo'=>77,//二级栏目id
	        'stsid'=>1,
	        'price'=>FormatMoney(1000,1) ,
	    );
	    $info = $this->couponsService->getBestCouponByCategory($data);
	    var_dump($info);
	}
	//选择店铺通用的优惠券
	public function test3(){
	    $data = array(
	        'stsid'=>1,
	        'price'=>FormatMoney(1000,1) ,
	    );
	    $info = $this->couponsService->getCouponByStore($data);
	    var_dump($info);
	}
}
