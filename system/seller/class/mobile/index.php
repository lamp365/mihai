<?php 

namespace seller\controller;
use  seller\controller;

class index extends base{

    const TABLE_NAME = 'store';
    
    public function __construct() {
        parent::__construct();
      
    }

    //默认没有op  会显示index
    public function index(){
        $_GP = $this->request;
        $leftMenu  = $this->getLeftMenu();
        $member    = get_member_account();
        /*
        $mem_store = member_store_getById($member['store_sts_id']);
        $mem_store['sts_id'] = 1;
        $mem_store['sts_shop_level'] = 2;
        if($mem_store['sts_level_valid_time'] < TIMESTAMP ){//店铺等级有效期到了,进行降级处理
            $RankService = new \service\seller\RankService();
            $RankService->downToFreeLevel($mem_store['sts_id'], $mem_store['sts_shop_level']);
        }
         * 
         */
        
        include page('index');
    }
    

}


