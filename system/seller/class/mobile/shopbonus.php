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
        
        foreach($storeCouponListData['data'] as $k=>$v)
        {
            switch ($v['payment'])
            {
                case 1:
                    $storeCouponListData['data'][$k]['payment'] = '用户';
                    break;
                case 2:
                    $storeCouponListData['data'][$k]['payment'] = '通用';
                    break;
                case 3:
                    $storeCouponListData['data'][$k]['payment'] = '活动';
                    break;
            }
            
            switch ($v['usage_mode'])
            {
                case 1:
                    $storeCouponListData['data'][$k]['usage_mode'] = '全场';
                    break;
                case 2:
                    $storeCouponListData['data'][$k]['usage_mode']             = '分类';
                    break;
                case 3:
                    $storeCouponListData['data'][$k]['usage_mode'] = '单品';
                    break;
            }
            
            $storeCouponListData['data'][$k]['coupon_amount']               = FormatMoney($v['coupon_amount'],2);
            $storeCouponListData['data'][$k]['amount_of_condition']         = FormatMoney($v['amount_of_condition'],2);
            $storeCouponListData['data'][$k]['receive_start_time']          = date('Y-m-d H:i:s',$v['receive_start_time']);
            $storeCouponListData['data'][$k]['receive_end_time']            = date('Y-m-d H:i:s',$v['receive_end_time']);
            $storeCouponListData['data'][$k]['use_start_time']              = date('Y-m-d H:i:s',$v['use_start_time']);
            $storeCouponListData['data'][$k]['use_end_time']                = date('Y-m-d H:i:s',$v['use_end_time']);
        }
        
        $pager = pagination($storeCouponListData['total'], $pindex, $psize);
        
        include page('shopbonus/coupon');
    }
    
    //优惠券添加
    public function addcoupon(){
        $storyShopClass = array();
        $types          = 'add';
        $Title        = '添加优惠券';
        
        
        //获取一级分类
        $storyShopClass['oneClass'] = getStoreCategoryAllparent($this->memberData['store_sts_id'],'id,name');
        
        //获取全部宝贝
        $dishData = $this->shopDishService->getDishAll('id,title');
        
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
    public function addcouponsub(){
        $_GP = $this->request;
        
        $insertId = $this->shopBonusService->addCoupon($_GP);
        if($insertId > 0)
        {
            ajaxReturnData(0,'优惠券添加成功',mobile_url('shopbonus',array('op'=>'index')));
        }
        else{
            ajaxReturnData(1,'优惠券添加失败','');
        }
    }
    
    //编辑优惠券
    public function upcoupon(){
        $_GP            = $this->request;
        $storyShopClass = array();
        $types          = 'update';
        $Title        = '编辑优惠券';
        
        $coupon = $this->shopBonusService->getOneCoupon($_GP['id']);
        
        $coupon['coupon_amount']        = FormatMoney($coupon['coupon_amount'],2);
        $coupon['amount_of_condition']  = FormatMoney($coupon['amount_of_condition'],2);
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
            $styleCss  = '';
            $dishidStr = '';
            $dishidArr = json_decode($coupon['store_shop_dishid']);
            foreach($dishidArr as $v)
            {
                $dishidStr .= $v.',';
            }
            $dishidStr = rtrim($dishidStr,',');

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
    
    //编辑优惠券提交
    public function upcouponsub(){
        $_GP = $this->request;
        
        $upStatus = $this->shopBonusService->upCoupon($_GP,$_GP['id']);
        if($upStatus > 0)
        {
            ajaxReturnData(0,'优惠券编辑成功',mobile_url('shopbonus',array('op'=>'index')));
        }
        else{
            ajaxReturnData(1,'优惠券编辑失败','');
        }
    }
    
    //查看发放记录
    public function couponmember(){
        //
        $_GP = $this->request;
        $condition = '';
        
        $oneCoupon = $this->shopBonusService->getOneCoupon($_GP['id'],'usage_mode');
        
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
                $couponMemberListData['data'][$k]['order_money']  = FormatMoney($v['order_money'],2);
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
            ajaxReturnData(4,'必要参数不存在','');
        }
        
        //判断手机号是否存在
        $isMobile = isMobileMember($_GP['mobile']);
        if($isMobile['mobile'] == '')
        {
            ajaxReturnData(3,'手机号对应用户不存在','');
        }
        
        //判断优惠券是否足够发放
        $couponData = $this->shopBonusService->getOneCoupon($_GP['id'],'release_quantity');
        
        if($couponData['release_quantity'] <= $_GP['grantnums'])
        {
            ajaxReturnData(2,'优惠券发放失败,库存不足','');
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