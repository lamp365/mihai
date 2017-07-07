<?php 
/**
 *优惠券接口
 */
namespace wapi\controller;
class coupon extends base{
    //优惠券列表
    public function couponList(){
        $GP = $this->request;
        /* $act_id = intval($GP['ac_id']);//活动id
        if (empty($act_id)) ajaxReturnData(0,'参数错误');*/
        /* $list = getCurrentAct(); 
        $act_id = $list['ac_id'];
        //获取活动店铺
        $actListModel = new \model\activity_dish_model();
        $actStore = $actListModel->getAllActivtyDish(array('ac_action_id'=>$act_id),'ac_shop','ac_dish_id DESC','ac_shop');
        if(empty($actStore)) ajaxReturnData(0,'没有该活动');
        
        
        $couponService = new \service\shopwap\couponService();
        $couponList = array();
        foreach ($actStore as $key=>$v){
            $return = $couponService->getStoreCoupons($v['ac_shop'],$openid);
            if ($return['couponList']){
                foreach ($return['couponList'] as $val){
                    $couponList[] = $val;
                }
            }
        } */
        $openid = checkIsLogin();
        $couponService = new \service\wapi\couponService();
        $couponList = $couponService->getAllCoupon($openid);
        if (!empty($couponList)){
            $data = array();
            $storeShopModel = new \model\store_shop_model();
            $shopCategoryModel = new \model\shop_category_model();
            $shopDishModel = new \model\shop_dish_model();
            foreach ($couponList as $key=>$val){
                $data[$key]['scid'] = $val['scid'];
                $data[$key]['coupon_amount'] = $val['coupon_amount'];//已经转换过的
                $data[$key]['amount_of_condition'] = $val['amount_of_condition'];//已经转换过的
                $data[$key]['coupon_name'] = $val['coupon_name'];
                $store = $storeShopModel->getOneStoreShop(array('sts_id'=>$val['store_shop_id']),'sts_name');
                $data[$key]['sts_name'] = $store['sts_name'];
                $data[$key]['sts_id'] = $val['store_shop_id'];
                $data[$key]['usage_mode'] = $val['usage_mode'];
                /* 要判断是否有图片，没有则取商品的或者店铺的或者栏目的 */
                $data[$key]['thumb'] = '';
                if (empty($val['coupon_img'])){
                    switch ($val['usage_mode']){
                        case 1:
                            $store = $storeShopModel->getOneStoreShop(array('sts_id'=>$val['store_shop_id']),'sts_id,sts_name,sts_avatar');
                            if (is_array($store)) $data[$key]['thumb'] = $store['sts_avatar'];
                            break;
                        case 2:
                            if ($val['store_category_idtwo'] != 0){
                                $catid = $val['store_category_idtwo'];
                            }else{
                                $catid = $val['store_category_idone'];
                            }
                            $category = $shopCategoryModel->getOneShopCategory(array('id'=>$catid),'id,name,thumb');
                            if (is_array($category) && !empty($category)) $data[$key]['thumb'] = $category['thumb'];
                            break;
                        case 3:
                            $store_shop_dishid = json_decode($val['store_shop_dishid'],true);
                            $dishid = $store_shop_dishid[0];
                            $shop_dish = $shopDishModel->getOneShopDish(array('id'=>$dishid),'id,title,thumb');
                            if (is_array($shop_dish) && !empty($category)) $data[$key]['thumb'] = $shop_dish['thumb'];
                            break;
                    }
                    if (empty($data[$key]['thumb'])){
                        $store = $storeShopModel->getOneStoreShop(array('sts_id'=>$val['store_shop_id']),'sts_id,sts_name,sts_avatar');
                        if (is_array($store)) $data[$key]['thumb'] = $store['sts_avatar'];
                    }
                }else {
                    $data[$key]['thumb'] = $val['coupon_img'];
                }
            }
            ajaxReturnData(1,'',$data);
        }else{
            ajaxReturnData(1,'没有优惠券');
        }
    }
    //领取优惠券
    public function getCoupon(){
        $openid = checkIsLogin();
        if (empty($openid)) ajaxReturnData(0,'请先登入');
        $_GP = $this->request;
        $stsid = intval($_GP['sts_id']);
        $scid = intval($_GP['scid']);
        if (empty($scid) || empty($stsid)) ajaxReturnData("0","",array('status'=>0,'mes'=>'请重新选择优惠券'));
        $data = array('scid'=>$scid,'stsid'=>$stsid,'openid'=>$openid);
        //判断是否有资格领取优惠券
        $couponService = new \service\wapi\couponService();
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
        $openid = checkIsLogin();
        $_GP = $this->request;
        $type = intval(isset($_GP['type'])) ? intval($_GP['type']) : '0';//type 0表示未使用，1已使用，3已过期
        if (empty($openid)) ajaxReturnData(0,'请先登入');
        $couponMemModel = new \model\store_coupon_member_model();
        $now = time();
        $endtime = $now + 7*86400;
        $where .=" openid='$openid' and ";
        
        if ($type == 0){
            $where .= " status=0 and '$now' < use_end_time ";//未使用且没有过期
        }elseif ($type == 1){
            $where .= " status=1 and (use_time <= '$endtime' OR use_time is NULL) ";
        }else {
            $where .= " status=0 and '$now' > use_end_time and use_end_time <= '$endtime' ";//未使用且已过期
        }
        //未使用的数量
        $noUse = 0;
        $wsyData = $couponMemModel->getAllMemberCoupon("openid='$openid' and status=0 and '$now' < use_end_time ", 'scmid');
        if ($wsyData) $noUse = count($wsyData);
        //已使用的数量
        $isUse = 0;
        $ysyData = $couponMemModel->getAllMemberCoupon("openid='$openid' and status=1 and (use_time <= '$endtime' OR use_time is NULL) ", 'scmid');
        if ($ysyData) $isUse = count($ysyData);
        //未使用已过期的数量
        $overdue = 0;
        $overdueData = $couponMemModel->getAllMemberCoupon("openid='$openid' and status=0 and '$now' > use_end_time and use_end_time <= '$endtime' ", 'scmid');
        if ($overdueData) $overdue = count($overdueData);
        $total = array(
            'noUse'=>$noUse,
            'isUse'=>$isUse,
            'overdue'=>$overdue,
        );
        //分页取数据
        //$pindex = max(1, intval($_GP['page']));
        //$psize = isset($_GP['limit']) ? $_GP['limit'] : 4;//默认每页4条数据
        //$limit= ($pindex-1)*$psize;
        //$orderby = " a.scmid DESC LIMIT ".$limit.",".$psize;
        $orderby = " scmid DESC ";
        $mycoupon = $couponMemModel->getAllMemberCoupon($where,"*",$orderby);
        $data = array();
        if (!empty($mycoupon)){
            $storeShopModel = new \model\store_shop_model();
            $shopCategoryModel = new \model\shop_category_model();
            $shopDishModel = new \model\shop_dish_model();
            foreach ($mycoupon as $key=>$v){
                $temp['coupon_name'] = $v['coupon_name'];
                $temp['scid'] = $v['scid'];
                $temp['usage_mode'] = $v['usage_mode'];
                $temp['coupon_amount'] = FormatMoney($v['coupon_amount'],0);
                $temp['amount_of_condition'] = FormatMoney($v['amount_of_condition'],0);
                $store = $storeShopModel->getOneStoreShop(array('sts_id'=>$v['store_shop_id']),'sts_name');
                $temp['sts_name'] = $store['sts_name'];
                $temp['use_start_time'] = date("Y.m.d",$v['use_start_time']);
                $temp['use_end_time'] = date("Y.m.d",$v['use_end_time']);
                $temp['use_time'] = '';
                if (!empty($v['use_time'])) $temp['use_time'] = date("Y.m.d",$v['use_time']);
                $temp['thumb'] = '';
                if (empty($v['coupon_img'])){
                    switch ($v['usage_mode']){
                        case 1:
                            $store = $storeShopModel->getOneStoreShop(array('sts_id'=>$v['store_shop_id']),'sts_id,sts_name,sts_avatar');
                            if (is_array($store)) $temp['thumb'] = $store['sts_avatar'];
                            break;
                        case 2:
                            if ($v['store_category_idtwo'] != 0){
                                $catid = $v['store_category_idtwo'];
                            }else{
                                $catid = $v['store_category_idone'];
                            }
                            $category = $shopCategoryModel->getOneShopCategory(array('id'=>$catid),'id,name,thumb');
                            if (is_array($category)) $temp['thumb'] = $category['thumb'];
                            break;
                        case 3:
                            $store_shop_dishid = json_decode($v['store_shop_dishid'],true);
                            $dishid = $store_shop_dishid[0];
                            $shop_dish = $shopDishModel->getOneShopDish(array('id'=>$dishid),'id,title,thumb');
                            if (is_array($shop_dish)) $temp['thumb'] = $shop_dish['thumb'];
                            break;
                    }
                
                }else {
                    $temp['thumb'] = $v['coupon_img'];
                }
                $data[] = $temp;
            }
        }
        
        $return = array('data'=>$data,'total'=>$total);
        
        ajaxReturnData(1,'',$return);
    }
}
?>