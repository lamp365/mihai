<?php
namespace seller\controller;
use  seller\controller;

class shopbonus extends base
{
    private $memberData;
    private $shopBonusService;
    private $shopDishService;
    
    function __construct() {
       parent::__construct();
       $this->memberData        = get_member_account();                          //用户信息
       
       $this->shopBonusService  = new \service\seller\ShopBonusService();        //优惠券操作对象
       $this->shopDishService   = new \service\seller\ShopDishService();        //宝贝操作对象
    }
    
    //优惠券列表
    public function index()
    {
        $_GP = $this->request;
        $pindex = max(1, intval($_GP['page']));
        $psize = 10;
        
        //usage_mode payment
        $condition = '';
        
        if($_GP['usage_mode'] > 0)
        {
            $condition .= " and usage_mode = {$_GP['usage_mode']}";
        }
        
        if($_GP['payment'] > 0)
        {
            $condition .= " and payment = {$_GP['payment']}";
        }
        
        $storeCouponListData = $this->shopBonusService->couponList($pindex,$psize,$condition);
        $pager = pagination($storeCouponListData['total'], $pindex, $psize);
        
        include page('shopbonus/coupon');
    }
    
    //优惠券添加
    public function showadd(){
        $storyShopClass = array();
        $types          = 'add';
        $Title        = '添加优惠券';
        
        
        //获取一级分类
        $storyShopClass['oneClass'] = getStoreCategoryAllparent($this->memberData['store_sts_id'],'id,name');
        
        //获取全部宝贝
        $dishData = $this->shopDishService->getDishAll('id,title');
        
        $styleCss = 'none';
        
        include page('shopbonus/addcoupon');
    }
    
    //根据分类获取部分宝贝
    public function getDish(){
        $_GP = $this->request;
        
        $dishData = $this->shopDishService->getDishAll('id,title',$_GP['oneCategory'],$_GP['twoCategory']);
        echo json_encode($dishData);
        exit;
    }
    
    //优惠券表单添加
    public function addcoupon(){
        $_GP = $this->request;
        
        $res = $this->shopBonusService->addCoupon($_GP,$_GP['id']);
        if($res) {
            ajaxReturnData(1,'优惠券操作成功',mobile_url('shopbonus',array('op'=>'index')));
        } else{
            ajaxReturnData(0,$this->shopBonusService->getError());
        }
    }
    
    //编辑优惠券
    public function upcoupon(){
        $_GP            = $this->request;
        $storyShopClass = array();
        $types          = 'update';
        $Title        = '编辑优惠券';
        
        $coupon = $this->shopBonusService->getOneCoupon($_GP['id'],'',$this->memberData['store_sts_id']);
        if(!$coupon){
            message($this->shopBonusService->getError(),refresh(),'error');
        }
        $coupon['coupon_amount']        = FormatMoney($coupon['coupon_amount'],1);
        $coupon['amount_of_condition']  = FormatMoney($coupon['amount_of_condition'],1);
        $coupon['receive_start_time']   = date('Y-m-d H:i:s',$coupon['receive_start_time']);
        $coupon['receive_end_time']     = date('Y-m-d H:i:s',$coupon['receive_end_time']);
        $coupon['use_start_time']       = date('Y-m-d H:i:s',$coupon['use_start_time']);
        $coupon['use_end_time']         = date('Y-m-d H:i:s',$coupon['use_end_time']);
        
        //获取一级分类
        $storyShopClass['oneClass'] = getStoreCategoryAllparent($this->memberData['store_sts_id'],'id,name');
        
        //根据一级分类获取二级分类
        $storyShopClass['twoClass'] = getStoreCategoryChild($coupon['store_category_idone']);

        if($coupon['usage_mode']==3)
        {
            //找出单品  目前 store_shop_dishid 是一个逗号分隔的
            $styleCss  = '';
            $dishidStr = $coupon['store_shop_dishid'];
            //left
            $dishData = $this->shopDishService->getDelDishs($coupon['store_category_idone'],$coupon['store_category_idtwo'],$dishidStr);

            //right
            $dishRightData = $this->shopDishService->getDishs($dishidStr);
        }
        else{
            $styleCss = 'none';
        }
        
        include page('shopbonus/addcoupon');
    }
    
    //查看发放记录
    public function couponmember(){
        //
        $_GP = $this->request;
        $condition = '';
        
        $oneCoupon = $this->shopBonusService->getOneCoupon($_GP['id'],'usage_mode',$this->memberData['store_sts_id']);
        if(!$oneCoupon){
            message($this->shopBonusService->getError(),refresh(),'error');
        }
        
        $pindex = max(1, intval($_GP['page']));
        $psize = 10;
        
        if($_GP['searchForm'] == 1)
        {
            $condition .= " and status = {$_GP['search_status']}";
        }
        
        $couponMemberListData = $this->shopBonusService->couponMemberList($_GP['id'],$pindex,$psize,$condition);
        if($couponMemberListData['total'] > 0)
        {
            foreach($couponMemberListData['data'] as $k=>$v){
                $memInfo = member_get($v['openid'],'nickname,mobile');
                $couponMemberListData['data'][$k]['nickname']     = $memInfo['nickname'];
                $couponMemberListData['data'][$k]['mobile']       = $memInfo['mobile'];
                $couponMemberListData['data'][$k]['order_price']  = FormatMoney($v['order_price'],2);
                $couponMemberListData['data'][$k]['receive_time'] = $v['receive_time']>0?date('Y-m-d H:i:s',$v['receive_time']):'';
                $couponMemberListData['data'][$k]['order_time']   = $v['order_time']>0?date('Y-m-d H:i:s',$v['order_time']):'';
                $couponMemberListData['data'][$k]['use_time']     = $v['use_time']>0?date('Y-m-d H:i:s',$v['use_time']):'';
                $couponMemberListData['data'][$k]['status']       = $v['status']==0?'未使用':'已使用';
                
            }
            $pager = pagination($couponMemberListData['total'], $pindex, $psize);
        }
        
        
        include page('shopbonus/couponmember');
    }
    
    
    //发放
    public function grantCoupon(){
        $_GP = $this->request;
        
        include page('shopbonus/grant');
    }
    
    //发放提交
    public function grantCouponSub(){
        $_GP = $this->request;
        
        if($_GP['id'] <= 0)
        {
            ajaxReturnData(4,'必要参数不存在',mobile_url('shopbonus',array('op'=>'index')));
        }
        
        //判断手机号是否存在
        $isMobile = member_get_bymobile($_GP['mobile']);
        if($isMobile['mobile'] == '')
        {
            ajaxReturnData(3,'手机号对应用户不存在,请确认',mobile_url('shopbonus',array('op'=>'grantCoupon','id'=>"{$_GP['id']}")));
        }
        
        //判断优惠券是否足够发放
        $couponData = $this->shopBonusService->getOneCoupon($_GP['id'],'release_quantity',$this->memberData['store_sts_id']);
        if(!$couponData){
            message($this->shopBonusService->getError(),refresh(),'error');
        }
        
        if($couponData['release_quantity'] <= $_GP['grantnums'])
        {
            ajaxReturnData(2,'优惠券发放失败,库存不足',mobile_url('shopbonus',array('op'=>'grantCoupon','id'=>"{$_GP['id']}")));
        }
        
        for($i=0;$i<$_GP['grantnums'];$i++)
        {
            $insertId = $this->shopBonusService->insertCouponMember($_GP);
        }
        if($insertId > 0)
        {
            //扣除优惠券数量
            $couponQuanStatus = $this->shopBonusService->editCoupon($_GP['id'],$_GP['grantnums']);
            ajaxReturnData(0,'优惠券发放成功',mobile_url('shopbonus',array('op'=>'couponmember','id'=>"{$_GP['id']}")));
        }
        else{
            ajaxReturnData(1,'优惠券发放失败','');
        }
        
    }
    
    
}
?>