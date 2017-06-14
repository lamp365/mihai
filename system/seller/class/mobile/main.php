<?php
namespace seller\controller;
use  seller\controller;

class main extends base
{
    private $shopStore;
    private $memberData;
            
    function __construct() {
       parent::__construct();
       $this->memberData        = get_member_account();
       $this->shopStore         = new \service\seller\shopStoreService();        //分类操作对象
   }
    
    //没有op  默认显示 index
    public function index()
    {
        $_GP = $this->request;
        
        //获取店铺等级信息
        $dataMember = $this->shopStore->getStoreShop('sts_level_valid_time,sts_shop_level,sts_name,recharge_money');
        $days = count_days($dataMember['sts_level_valid_time'], time());

        $dataStore = $this->shopStore->getStoreShopLevel($dataMember['sts_shop_level']);
        
        if($dataStore['is_free'] > 0)
        {
            $store_title = "店铺处于免费试用期还剩<font color='red'>{$days}</font>天";
        }
        else{
            $store_title = "尊贵的{$dataStore['rank_name']},您的店铺距离过期剩余<font color='red'>{$days}</font>天";
        }
        
        include page('main');
    }
    
    public function diamain(){
        $_GP = $this->request;
        
        //获取店铺等级信息
        $dataMember = $this->shopStore->getStoreShop('sts_level_valid_time,sts_shop_level,sts_name,recharge_money');

        $dataStore = $this->shopStore->getStoreShopLevel($dataMember['sts_shop_level']);
        
        include page('diamain');
    }
    
    public function renewal(){
        $_GP = $this->request;
        $url = mobile_url('main',array('op'=>'index'));;
        if($this->memberData['store_is_admin'] <= 0)
        {
            message("只有店铺管理员才允许执行此操作",$url,'error'); 
        }
        
        //判断用户余额
        $dataMember = $this->shopStore->getStoreShop('sts_level_valid_time,sts_shop_level,sts_name,recharge_money');
        $recharge_money = FormatMoney($dataMember['recharge_money'],2);
        
        $dataStore = $this->shopStore->getStoreShopLevel($dataMember['sts_shop_level']);
        
        if($dataStore['money'] > $recharge_money)
        {
            message("店铺余额不足",$url,'error'); 
        }

        $status = store_gold($this->memberData['store_sts_id'], $dataStore['money'], '-1', '店铺租期延长');
         if($status <= 0)
        {
            message("余额扣除失败",$url,'error'); 
        }

        //延长租期
        $times = $dataStore['time_range'] * 31536000;
        $upDateExtendData = array();
        $upDateExtendData['sts_level_valid_time'] = $dataMember['sts_level_valid_time'] + $times;
        $upStatusExtend = $this->shopStore->extendedUserLease($upDateExtendData,$this->memberData['store_sts_id']);
        if($upStatusExtend <= 0)
        {
            message("租期延长失败",$url,'error'); 
        }
        
        message("租期延长成功",$url,'succes'); 
    }

}