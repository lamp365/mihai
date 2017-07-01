<?php
namespace seller\controller;
use  seller\controller;

class product_bat extends base
{
    private $ShopSystemCategory       = array();
    private $ShopCategory       = array();
    private $goodService       = array();
    private $shopPic       = array();
    private $shopdish       = array();
    private $ShopBrand = array();


    public function __construct()
    {
        parent::__construct();
//        error_reporting(0);
        $this->ShopSystemCategory   = new \service\seller\ShopSystemCategoryService();     //
        $this->ShopCategory         = new \service\seller\ShopCategoryService();        //分类操作对象
        $this->goodService          = new \service\seller\goodsService();
        $this->shopPic              = new \service\seller\ShopPicService();     //宝贝图片
        $this->shopdish             = new \service\seller\ShopDishService();   //宝贝操作对象
        $this->ShopBrand            = new \service\seller\ShopBrandService();     //品牌
    }
    
    //从csv 导入
    public function index()
    {
        $_GP = $this->request;
        $reData['systemGroup'] = $this->ShopSystemCategory->ShopCateGroupListTwo();
        $onesysids = '';
        $twosysids = '';

       foreach($reData['systemGroup']['oneCategory'] as $v)
        {
            $onesysids .= $v['id'].',';
            if(count($v['twoCategory']) > 0)
            {
               foreach($v['twoCategory'] as $vv){
                   $twosysids .= $vv['id'].',';
               }
            }
        }
        
        $onesysids = rtrim($onesysids,',');
        $twosysids = rtrim($twosysids,',');
        $totalOneSysData = array();
        $totalTwoSysData = array();
        $totalOneSysData = $this->ShopSystemCategory->count_category_one_goods($onesysids);
        $totalTwoSysData = $this->ShopSystemCategory->count_category_two_goods($twosysids);
        
        foreach($totalOneSysData as $v)
        {
            $oneSysData[$v['pcate']] = $v['total'];
        }

        foreach($totalTwoSysData as $v)
        {
            $twoSysData[$v['ccate']] = $v['total'];
        }

        foreach($reData['systemGroup']['oneCategory'] as $k=>$v)
        {
            $reData['systemGroup']['oneCategory'][$k]['dishtotal'] = intval($oneSysData[$v['id']]);

           if(count($v['twoCategory']) > 0)
           {
              foreach($v['twoCategory'] as $kk=>$vv){
                  $reData['systemGroup']['oneCategory'][$k]['twoCategory'][$kk]['dishtotal'] = intval($twoSysData[$vv['id']]);
              }
           }   
        }

        foreach($reData['systemGroup']['oneCategory'] as $k=>$v)
        {
            if($v['dishtotal'] <= 0)
            {
                unset($reData['systemGroup']['oneCategory'][$k]);
                continue;
            }
            foreach($v['twoCategory'] as $kk=>$vv){
                if($vv['dishtotal'] <= 0)
                {
                    unset($reData['systemGroup']['oneCategory'][$k]['twoCategory'][$kk]);
                }
            }
        }

        $reData['systemGroup']['oneCategory'] = array_values($reData['systemGroup']['oneCategory']);
        foreach($reData['systemGroup']['oneCategory'] as $k=>$v)
        {
            $reData['systemGroup']['oneCategory'][$k]['twoCategory'] = array_values($reData['systemGroup']['oneCategory'][$k]['twoCategory']);
        }

        include page("dish/csv_add");
    }
    
    //从产品库 列表
    public function bat_add()
    {
        $_GP = $this->request;
        
        $_GP['brands'] = $_GP['brand'];
        //获取行业一级分类 shop_category
        $ShopCategory = new \service\seller\ShopCateService();
        $oneShopCate = $ShopCategory->oneShopCategory('id,name');

        if($_GP['pcate']> 0){
            $ShopCategory    = new \service\seller\ShopCateService();
            $twoShopCate     = $ShopCategory->twoShopCategory(intval($_GP['pcate']),'id,name');
        }

        //获取店铺分类1
        $ShopStoreCategory = new \service\seller\ShopCategoryService(); 
        $shopStoreCateData = $ShopStoreCategory->oneStoreCategory();

        //获取宝贝分页列表
        $ShopGoods     = new \service\seller\goodsService();
        $_GP['page']  = max(1, intval($_GP['page']));
        $_GP['limit'] = 20;
        
        //通过品牌ID获取品牌名称和ID $product['brand']
        $brandData = '';
        $brandData['ids'] = intval($_GP['brand']);
        $brand = $this->ShopBrand->getBrandTitle($brandData);
        $brand = $brand[0];
        unset($brand[0]);
        
        $goodsPage     = $ShopGoods->goodsListPage($_GP);

        $pager = pagination($goodsPage['goodslisttotal']['total'], $_GP['page'], $_GP['limit']);
        
        //获取店铺一级二级分类
        $shopStoreCateData = $this->ShopCategory->getShopCategoryTree();
        
        //include page("dish/bat_add");
        include page("dish/bat_add2");
    }
    
        //从产品库 列表
    public function bat_goods_add()
    {
        $_GP = $this->request;
        
        $_GP['brands'] = $_GP['brand'];
        //获取行业一级分类 shop_category
        $ShopCategory = new \service\seller\ShopCateService();
        $oneShopCate = $ShopCategory->oneShopCategory('id,name');

        if($_GP['pcate']> 0){
            $ShopCategory    = new \service\seller\ShopCateService();
            $twoShopCate     = $ShopCategory->twoShopCategory(intval($_GP['pcate']),'id,name');
        }

        //获取店铺分类1
        $ShopStoreCategory = new \service\seller\ShopCategoryService(); 
        $shopStoreCateData = $ShopStoreCategory->oneStoreCategory();

        //获取宝贝分页列表
        $ShopGoods     = new \service\seller\goodsService();
        $_GP['page']  = max(1, intval($_GP['page']));
        $_GP['limit'] = 20;
        
        //通过品牌ID获取品牌名称和ID $product['brand']
        $brandData = '';
        $brandData['ids'] = intval($_GP['brand']);
        $brand = $this->ShopBrand->getBrandTitle($brandData);
        $brand = $brand[0];
        unset($brand[0]);
        
        $goodsPage     = $ShopGoods->goodsListPage($_GP);

        $pager = pagination($goodsPage['goodslisttotal']['total'], $_GP['page'], $_GP['limit']);
        
        //获取店铺一级二级分类
        $shopStoreCateData = $this->ShopCategory->getShopCategoryTree();
        
        //include page("dish/bat_add");
        include page("dish/bat_add3");
    }
    
    //获取行业二级
    public function cate_tow(){
        $_GP = $this->request;

        //获取行业二级分类 shop_category
        $ShopCategory    = new \service\seller\ShopCateService();
        $twoShopCate     = $ShopCategory->twoShopCategory(intval($_GP['pid']),'id,name');

        echo json_encode($twoShopCate);
        exit;
    }

    //获取分页列表
    public function bat_list()
    {
        $_GP = $this->request;

        //获取宝贝分页列表
        $ShopGoods     = new \service\seller\goodsService();
        $_GP['page']   = max(1, intval($_GP['page']));
        $_GP['limit']  = 20;
        $goodsPage     = $ShopGoods->goodsListPage($_GP);
        
        
        
        echo json_encode($goodsPage);
        exit;
    }

    public function store_cate_two(){
        $_GP = $this->request;
        
        $ShopStoreCategory = new \service\seller\ShopCategoryService(); 
        $shopStoreCateData = $ShopStoreCategory->twoStoreCategory($_GP);
        
        echo json_encode($shopStoreCateData);
        exit;
    }
    
    public function bat_dish_add(){
        $data = $this->request;
        //goodid
        /*
         *  $data = array(
            'store_p1'=>71,
            'store_p2'=>77,
            'goods_id'=>'1,2,3,4,7,8,9,22,12,13,14,15,16,17,19,20,21,23'
            );
         * 
         */
        
        if($data['store_p1'] <= 0 || $data['store_p2'] <= 0)
        {
            ajaxReturnData(0,'店铺分类ID必须存在');
        }
        
        if($data['goods_ids'] == ''){
             ajaxReturnData(0,'关键参数不存在');
        }
        $data['goods_ids'] = rtrim($data['goods_ids'],',');
        
        //获取产品库数据
       $goodInfoData = $this->goodService->getGoodInfos($data);
       //获取对应的产品库相册信息
       $goodPicData = $this->shopPic->getGoodPics($data);
       $goodPicArr = array();
       foreach($goodPicData as $v){
           $goodPicArr[$v['goodid']][] = $v['picurl'];
       }
       
       //添加到宝贝库
       $i = 0;
       
       $dishIds = '';

       foreach($goodInfoData as $v){

           //判断该产品在库里是否已经存在
           $checkDish = $this->shopdish->checkGoods($v['id']);
           if($checkDish)
           {
               continue;
           }
           
           $goodsData                 = array();
           $goodsData['gid']          = $v['id'];
           $goodsData['store_p1']     = $data['store_p1'];
           $goodsData['store_p2']     = $data['store_p2'];
           $goodsData['status']       = 0;
           $goodsData['title']        = $v['title'];
           $goodsData['thumb']        = $v['thumb'];
           $goodsData['description']  = $v['description'];
           $goodsData['content']      = $v['content'];
           $goodsData['marketprice']  = FormatMoney($v['marketprice'],2);
           $goodsData['productprice'] = FormatMoney($v['productprice'],2);
           $goodsData['goodssn']      = $v['goodssn'];
           $goodsData['store_count']  = $v['store_count'];
           $goodsData['isnew']        = $v['isnew'];
           $goodsData['xcimg']        = count($goodPicArr[$v['id']])>0?$goodPicArr[$v['id']]:array();
           //宝贝图片
           $dishId = $this->goodService->addGoods($goodsData);
           
           $dishIds .= $dishId.',';
           
           $i++;
       }
       
       ajaxReturnData(1,'成功导入'.$i.'条记录');
       //ajaxReturnData(1,'导入成功');
    }
    
    public function category_bat(){
        $_GP = $this->request;
        $data['ids'] = $_GP['ids'];
        $res = $this->ShopCategory->batAddCategory($data);
        if(!$res){
            ajaxReturnData(0,$this->ShopCategory->getError());
        }else{
            ajaxReturnData(1,'导入成功');
        }
    }
}