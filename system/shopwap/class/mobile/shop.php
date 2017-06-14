<?php 
namespace shopwap\controller;
use service\shopwap\storeShopService;
use service\shopwap\regionService;
use service\shopwap\commentService;
use service\shopwap\couponService;
class shop extends base{
    //店铺商品页
    public function index(){
        $cfg = globaSetting();
        $title = $cfg['shop_title'];
        $storeShopService = new storeShopService();
        $regionService = new regionService();
        $commentService = new commentService();
        $couponService = new couponService();
        $_GP = $this->request;
        $storeid = intval($_GP['storeid']);
        if (empty($storeid)) message('查不到该店铺',refresh(),'error');
        //取出店铺信息
        $storeInfo = $storeShopService->StoreDetailByStoreid($storeid);
        if (empty($storeInfo)) message('查不到该店铺',refresh(),'error');
        count_store_visted($storeid);
        $province = $regionService->getOneRegion(array('region_code'=>$storeInfo['sts_locate_add_1']),'region_name');
        $city = $regionService->getOneRegion(array('region_code'=>$storeInfo['sts_locate_add_2']),'region_name');
        $area = $regionService->getOneRegion(array('region_code'=>$storeInfo['sts_locate_add_3']),'region_name');
        $address = $province['region_name'].$city['region_name'].$area['region_name'].$storeInfo['sts_address'];
        //店铺粉丝数量
        $count = $storeShopService->getFunCount($storeid);
        //店铺配送地址
        $region_name = $regionService->getOneRegion(array('region_code'=>$storeInfo['sts_region']),'region_name');
        //综合评分
        $total = $commentService->getstoreAvePingfen($storeid);
        //取特卖商品
        $shopDiscount = $storeShopService->getStoreTMshop($storeid);
        //取推荐商品
        $shopRecommand = $storeShopService->getRecShopByStoreid($_GP);
        //取得综合排名的商品
        $shopList = $storeShopService->getShopByStoreid($_GP);
        //取得活动的文章
        $storeAdv = $storeShopService->getShopadByStoreid($_GP);
        //优惠券
        //是否登入标识，$openid不为空表示登入,如果登入后则返回可以领取的优惠券
        $openid = checkIsLogin();
        $return = $couponService->getStoreCoupons($storeid,$openid);
        $couponList = $return['couponList'];
        $coupon_amount = $return['coupon_amount'];
        $conpou_total = $return['total'];
        include themePage('shop');
    }
    //ajax获取推荐商品
    public function ajaxShopRec(){
        $_GP = $this->request;
        if (checkIsAjax()){
            $storeShopService = new storeShopService();
            $shopRecommand = $storeShopService->getRecShopByStoreid($_GP);
            ajaxReturnData(1,'',$shopRecommand);
        }
    }
    
    //ajax获取活动列表
    public function ajaxAdvList(){
        $_GP = $this->request;
        if (checkIsAjax()){
            $storeShopService = new storeShopService();
            $storeAdv = $storeShopService->getShopadByStoreid($_GP);
            ajaxReturnData(1,'',$storeAdv);
        }
    }
    //ajax获取商品列表
    public function ajaxShopList(){
        $_GP = $this->request;
        if (checkIsAjax()){
            $storeShopService = new storeShopService();
            $shopList = $storeShopService->getShopByStoreid($_GP);
            ajaxReturnData(1,'',$shopList);
        }
    }
    //ajax领取优惠券
    public function ajaxGetCoupon(){
        if (!$openid=checkIsLogin()) ajaxReturnData("0","",array('status'=>0,'mes'=>'请先登入，再领取优惠券'));
         
        $_GP = $this->request;
        $stsid = intval($_GP['storeid']);
        $scid = intval($_GP['scid']);
        if (empty($scid) || empty($stsid)) ajaxReturnData("0","",array('status'=>0,'mes'=>'请重新选择优惠券'));
        $data = array('scid'=>$scid,'stsid'=>$stsid,'openid'=>$openid);
        //判断是否有资格领取优惠券
        $couponsService = new couponService();
        $flag = $couponsService->IsCanGetCoupon($data);
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
        $getcoupons = $couponsService->getCoupons($data);
        if ($getcoupons['status'] == 1){
            $flag = $couponsService->IsCanGetCoupon($data);//领取完判断能不能再领取
            $return = array(
                'mes' => $getcoupons['mes'],
                'status' => $flag['status'],
            );
            ajaxReturnData("1",'',$return);
        }else {
            ajaxReturnData("0",$getcoupons['mes']);
        }
    }
    //活动详情
    public function advDetail(){
        $cfg = globaSetting();
        $title = $cfg['shop_title'];
        $_GP = $this->request;
        $advid = intval($_GP['advid']);
        if (empty($advid)) message('请重试','refresh','success');
        //取活动详情
        $storeShopService = new storeShopService();
        $info = $storeShopService->getStoreAdvInfo($advid);
        if (empty($info)) message('请重试','refresh','success');
        //活动详情的点击数加1
        $storeShopService->addStoreAdvHits($advid);
        include themePage('advDetail');
    }
    //专题页面
    public function special(){
        $cfg = globaSetting();
        $title = $cfg['shop_title'];
        $storeShopService = new storeShopService();
        $_GP = $this->request;
        //取得活动的文章
        $storeAdv = $storeShopService->getAllAds($_GP);
        if (checkIsAjax()){
            if (empty($storeAdv)){
                ajaxReturnData("0",'没有数据了');
            }
            ajaxReturnData("1",'',$storeAdv);
        }else {
            include themePage('special');
        }
    }
    //商品详情
    public function goodsDetail(){
        $cfg = globaSetting();
        $title = $cfg['shop_title'];
        $_GP = $this->request;
        $dishid = intval($_GP['dishid']);
        count_dish_visted($dishid);
        $storeShopService = new storeShopService();
        $goods = $storeShopService->getGoodsInfoByDishid($dishid);
        if ($goods){
            $goods['marketprice'] = FormatMoney($goods['marketprice'],0);
            $goods['productprice'] = FormatMoney($goods['productprice'],0);
        }
        //取出店铺信息
        $storeInfo = $storeShopService->getOneStoreShop(array('sts_id'=>$goods['sts_id']));
        $regionService = new regionService();
        $area = $regionService->getOneRegion(array('region_code'=>$storeInfo['sts_locate_add_3']),'region_name');
        $area = $area['region_name'];
        //取出评论
        $commentService = new commentService();
        $data = array(
            'storeid'=>$goods['sts_id'],
            'dishid'=>$dishid
        );
        $commentsAll = $commentService->getGoodsComment($data);
        $total = $commentsAll['num'];
        $comments = $commentsAll['list'];
        include themePage('detail');
    }
}
?>
