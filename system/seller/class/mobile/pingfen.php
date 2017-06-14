<?php
namespace seller\controller;
use service\seller\pingfenService;
class pingfen extends base
{
    public function __construct(){
        $this->pingfenService = new pingfenService();
    }
	//商品评分
	public function index()
	{
	    $_GP = $this->request;
	    $shopCategoryService = new \service\seller\ShopCategoryService();
	    $category = $shopCategoryService->getStoreCategoryName();//店铺父栏目
	    $total = $this->pingfenService->getStoreAllGoodsGrade();//店铺所有商品的总评价
	    include page('pingfen/pingfen');
	}
	//ajax获得二级菜单名称
	public function getSecondMenu(){
	    $_GP = $this->request;
	    if (isset($_GP['id']) && !empty($_GP['id']) && checkIsAjax()){
	        $shopCategoryService = new \service\seller\ShopCategoryService();
	        $return = $shopCategoryService->getStoreCategoryName($_GP['id']);
	        if ($return){
	            ajaxReturnData(1,'',$return);
	        }else{
	            ajaxReturnData(2,'暂无数据');
	        }
	    }else{
	        ajaxReturnData(0,'参数错误');
	    }
	    
	}
	//ajax根据二级栏目id获得该栏目下的所有商品信息
	public function getdishList(){
	    $_GP = $this->request;
	    if (isset($_GP['p1']) && isset($_GP['p2']) && !empty($_GP['p1']) && !empty($_GP['p2']) && checkIsAjax()){
	        $ShopDishService = new \service\seller\ShopDishService();
	        $data = array('store_p1'=>$_GP['p1'],'store_p2'=>$_GP['p2']);
	        $return = $ShopDishService->getPcontent($data,'id,title');
	        if ($return){
	            ajaxReturnData(1,'',$return);
	        }else{
	            ajaxReturnData(2,'暂无数据');
	        }
	    }else{
	        ajaxReturnData(0,'参数错误');
	    }
	}
	//ajax根据筛选条件获得评分
	public function search(){
	    $_GP = $this->request;
	    $ShopDishService = new \service\seller\ShopDishService();
	    if (($_GP['p1'] != 0) && ($_GP['p2'] != 0) && ($_GP['dishid'] != 0)){
	        //获得单个商品的评分
	        $total = $this->pingfenService->getSingleGoodsGrade($_GP['dishid']);
	    }elseif (($_GP['p1'] != 0) && ($_GP['dishid'] == 0)){
	        if($_GP['p2'] != 0){
	            //获取二级栏目下所有商品的评分
	            $data = array('store_p1'=>$_GP['p1'],'store_p2'=>$_GP['p2']);
	            $return = $ShopDishService->getPcontent($data,'id,title');
	        }else{
	            //获取一级栏目下所有商品的评分
	            $data = array('store_p1'=>$_GP['p1']);
	            $return = $ShopDishService->getcontentByP1($data,'id,title');
	        }
	        if (!empty($return)){
                //商品id组成一维数组
	            $dishIdArr = array();
	            foreach ($return as $v){
	                $dishIdArr[] = $v['id'];
	            }
	            $total = $this->pingfenService->getMoreGoodsGrade($dishIdArr);
	        }
	    }else {
	        ajaxReturnData(0,'请选择条件');
	    }
	    if ($total){
	        ajaxReturnData(1,'',$total);
	    }else{
	        ajaxReturnData(2,'暂无数据');
	    }
	    
	}
	//综合评分
    public function synthetic(){
        $storeShopService = new \service\seller\StoreShopService();
        $member = get_member_account(1);
        $total = $this->pingfenService->getStoreAllGoodsGrade();//店铺所有商品的总评价
        $comment = $total['all_wl_rate'] + $total['all_fw_rate'] + $total['all_cp_rate'];//评价总得分
        $store_shop = $storeShopService->getMydefaultShop($member['store_sts_id']);
        //$recharge_money = $store_shop['recharge_money'];//竞价
        //入驻人数
        $enter = $store_shop['friend_count'];
        
        $settings=globaSetting();
        $comment_rate = $comment * $settings['comment_exchange'];
        //$recharge_rate = $recharge_money * $settings['bid_exchange'];
        $enter_rate = $enter * $settings['enter_exchange'];//入驻分
        
        include page('pingfen/synthetic');
    }
    
    
    //综合评分
    public function cbd(){
        $_GP = $this->request;
        $member = get_member_account(1);
        
        #核心查询
        $result = $this->pingfenService->rankCBD(  $_GP['region_code'] );//如果有区code则查询地区排名，如果没有则查询全部
        $store_shop= $result[ $member['store_sts_id'] ];
        
        #其他数据查询
        $rService=new  \service\seller\regionService();
        $same_region_list= $rService->getSubDatas( $store_shop['sts_city'] );//同城市下的区数据查询输出，PC页面输出
//        ppd($store_shop);
        include page('pingfen/cbd');exit();
    }
    
       
}