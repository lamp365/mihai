<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace api\controller;

use api\controller;

class product extends base
{
    private $ShopSystemCategory   = array();
    private $ShopCategory   = array();
    private $goodsTypeGroup = array();
    private $goodstype      = array();
    private $shopdish       = array();
    private $goodService    = array();
    private $shopPic        = array();
    private $shopBrand      = array();
    private $data           = array();
    private $goods          = array();
    private $ShopSpec       = array();


    public function __construct()
    {
        error_reporting(E_ERROR);
        
        parent::__construct();
        
        $this->ShopCategory         = new \service\seller\ShopCategoryService();        //分类操作对象
        $this->ShopSystemCategory   = new \service\seller\ShopSystemCategoryService();     //
        $this->goodsTypeGroup       = new \service\seller\goodsTypeGroupService();    //分组操作对象
        $this->goodstype            = new \service\seller\goodstypeService();    //规格操作对象
        $this->shopdish             = new \service\seller\ShopDishService();   //宝贝操作对象
        $this->goodService          = new \service\seller\goodsService();
        $this->shopPic              = new \service\seller\ShopPicService();     //宝贝图片
        $this->shopBrand            = new \service\seller\ShopBrandService();     //品牌
        $this->ShopSystemCategory   = new \service\seller\ShopSystemCategoryService();     //
        $this->goods                = new \service\seller\goodsService();     //
        $this->ShopSpec             = new \service\seller\ShopSpecService();     //
    }
    
    //创建店铺宝贝分类
    public function add_cate(){
        $data = $this->request;
        
        //测试数据开始
        /*
        $data = array(
            'oneCategory'=>array("cat_name"=>"aaa"),
            'twoCategory'=>array(
                0=>array("cat_name"=>"bbbb"),
            )
        );
         * 
         */
        /*
        $data = array(
            'oneCategory'=>array(
                'cat_name'=>'测试一级分类31',
                'pid'=>0,
                'status'=>1,
            ),
            'twoCategory'=>array(
                0=>array(
                    'cat_name'=>'测试二级分类1',
                    'status'=>1
                ),
                1=>array(
                    'cat_name'=>'测试二级分类2',
                    'status'=>1
                ),
                2=>array(
                    'cat_name'=>'测试二级分类3',
                    'status'=>1
                ),
                3=>array(
                    'cat_name'=>'测试二级分类',
                    'status'=>1
                ),
                4=>array(
                    'cat_name'=>'测试二级分类',
                    'status'=>1
                )
            )
        );
         * 
         */
        //测试数据结束
        
        if($this->ShopCategory->formValidateBeforeAddCate($data['oneCategory']))
        {
            if(count($data['twoCategory']) > 0)
            {
                $oneId = $this->ShopCategory->do_addCate($data['oneCategory']);
                foreach($data['twoCategory'] as $k=>$v)
                {
                    $data['twoCategory'][$k]['pid'] = $oneId;
                    if($this->ShopCategory->formValidateBeforeAddCate($data['twoCategory'][$k]))
                    {
                        $id = $this->ShopCategory->do_addCate($data['twoCategory'][$k]);
                    }
                    else{
                        continue;
                    }
                }
            }
            else{
                ajaxReturnData(0,'二级分类不能为空');
            }
        }
        else{
            ajaxReturnData(0,'一级分类添加失败');
        }
        
        ajaxReturnData(1,'分类添加成功');
    }
    
    //获取店铺宝贝分类
    public function get_cate_list(){
        $_GP = $this->request;
        $cate_list = $this->ShopCategory->getShopCategoryTree();
        
        if(count($cate_list['oneCategory']) > 0)
        {
            $oneids = '';
            $twoids = '';
            foreach($cate_list['oneCategory'] as $v)
            {
                $oneids .= $v['id'].',';
                if(count($v['twoCategory']) > 0)
                {
                   foreach($v['twoCategory'] as $vv){
                       $twoids .= $vv['id'].',';
                   }
                }
            }
            $oneids = rtrim($oneids,',');
            $twoids = rtrim($twoids,',');

            //统计内容
            $totalOneData = array();
            $totalTwoData = array();
            $totalOneData = $this->shopdish->count_category_one_dish($oneids);
            $totalTwoData = $this->shopdish->count_category_two_dish($twoids);

            foreach($totalOneData as $v)
            {
                $oneData[$v['store_p1']] = $v['total'];
            }

            foreach($totalTwoData as $v)
            {
                $twoData[$v['store_p2']] = $v['total'];
            }

            foreach($cate_list['oneCategory'] as $k=>$v)
            {
                $cate_list['oneCategory'][$k]['dishtotal'] = intval($oneData[$v['id']]);

               if(count($v['twoCategory']) > 0)
               {
                  foreach($v['twoCategory'] as $kk=>$vv){
                      $cate_list['oneCategory'][$k]['twoCategory'][$kk]['dishtotal'] = intval($twoData[$vv['id']]);
                  }
               }   
            }
        }
        ajaxReturnData(1,'分类获取成功',$cate_list);
    }
    
    
    //编辑店铺宝贝分类
    public function enit_cate(){
        $data = $this->request;
        //测试数据开始
        /*
         $data = array(
            'oneCategory'=>array(
                'cat_name'=>'测试一级分类编辑',
                'pid'=>0,
                'status'=>1,
                'id'=>104
            ),
            'twoCategory'=>array(
                0=>array(
                    'cat_name'=>'测试二级分类1',
                    'status'=>1,
                    'id'=>105,
                    'pid'=>104
                ),
                1=>array(
                    'cat_name'=>'测试二级分类2',
                    'status'=>1,
                    'id'=>106,
                    'pid'=>104
                ),
                2=>array(
                    'cat_name'=>'测试二级分类3',
                    'status'=>1,
                    'id'=>107,
                    'pid'=>104
                ),
                3=>array(
                    'cat_name'=>'测试二级分类four',
                    'status'=>1,
                    'id'=>0,
                    'pid'=>104
                )
            )
        );
         * 
         */
        //测试数据结束
        
        if($this->ShopCategory->formValidateBeforeAddCate($data['oneCategory'],2))
        {
            $oneUpStatus = $this->ShopCategory->do_addCate($data['oneCategory']);
        }
        else{
            ajaxReturnData(0,'一级分类编辑失败');
        }
        
        foreach($data['twoCategory'] as $v)
        {
            if($v['id'] > 0)
            {
                //编辑
                if(!$this->ShopCategory->formValidateBeforeAddCate($v,2,$data['oneCategory']))
                {
                    continue;
                }
            }
            else
            {
                //添加
                if(!$this->ShopCategory->formValidateBeforeAddCate($v,1,$data['oneCategory']))
                {
                    continue;
                }
            }
            $twoUpStatus = $this->ShopCategory->do_addCate($v);
        }
        
        ajaxReturnData(1,'分类编辑成功');
    }
    
    //新建规格分组
    public function addSpecGroup(){
        $data = $this->request;
        if($data['group_name'] == '')
        {
            ajaxReturnData(0,'组名不能为空');
        }
        
        if($data['group_name'] == '默认分组')
        {
            ajaxReturnData(0,'系统内建不得再建');
        }
        $reData = array();
        $insertId = $this->goodsTypeGroup->addGoodsTypeGroup($data['group_name']);
        if($insertId > 0)
        {
            $reData['id'] = $insertId;
            ajaxReturnData(1,'分组添加成功',$reData);
        }
        else
        {
            ajaxReturnData(0,'分组添加失败');
        }
    }
    
    //编辑规格分组
    public function editSpecGroup(){
        $data = $this->request;
        
        //测试数据开始
        /*
        $_GP['group_name'] = '测试分组18';
        $_GP['group_id']   = 8;
         * 
         */
        //测试数据结束
        
        if($data['group_name'] == '' || $data['group_id'] <=0)
        {
            ajaxReturnData(0,'组名或者组标识不存在');
        }
        
        $podata = array();
        $podata['group_name'] = $data['group_name'];
        $podata['group_id']   = $data['group_id'];

        $status = $this->goodsTypeGroup->editGoodsTypeGroup($podata);
        
        
        if($status > 0)
        {
            ajaxReturnData(1,'分组编辑成功');
        }
        else
        {
            ajaxReturnData(0,'分组编辑失败');
        }
    }
    
    //变更规格分组状态
    public function changeSpecGroupStatus(){
        $data = $this->request;
        //测试数据开始
        //$_GP['group_id']   = 8;
        //测试数据结束
        
        if($data['group_id'] <=0)
        {
            ajaxReturnData(0,'组标识不存在');
        }
        
        $status = $this->goodsTypeGroup->changeGoodsTypeGroup($data);
        
        if($status > 0)
        {
            //将这个分组下属的所有模型全部更新状态
            $tempStatus = $this->goodstype->changeStoreGroupTemplateSpecItem($data);
        }
        //goods_type
        
        if($status > 0)
        {
            ajaxReturnData(1,'删除成功');
        }
        else
        {
            ajaxReturnData(0,'删除失败');
        }
    }
    
    //获取分组列表
    public function getSpecGroupList(){
        $data = $this->request;
        $redata = array();
        /*
        $_GP['page'] = max(1, intval($_GP['page']));
        $_GP['page'] = ($_GP['page'] - 1) * $_GP['limit'];
        $_GP['limit'] = $_GP['limit']>0?$_GP['limit']:10;
        */
        
        $redata = $this->goodsTypeGroup->getGoodsTypeList($data,'group_id,group_name');
        
        //$redata['total']     = $data['total'];
        //unset($data['total']);
        
        ajaxReturnData(1,'获取分组列表成功',$redata);
    }
    
    //新建商品规格
    public function addSpec(){
       $data = $this->request;
        $reData = array();  //返回数据
        //测试数据开始
        /*
        $_GP = array(
            'group_id'=>0,
            'status'=>1,
            'data'=>array(
                '0'=>array(
                    'name'=>'内存',
                    'item'=>array(
                        0=>'1G',
                        1=>'2G',
                        2=>'4G'
                    )    
                ),
                '1'=>array(
                    'name'=>'硬盘',
                    'item'=>array(
                        0=>'1t',
                        1=>'2t',
                        2=>'4t'
                    )
                )
            )
        );
         * 
         */
        
        //测试数据结束
        
        if($data['group_id'] <= 0)
        {
            $data['group_id'] = $this->goodstype->checkGroupDefault();
        }
        
        //添加模型库
        foreach($data['data'] as $v){
            $gtype_name .= $v['name'].'+';
        }
        $gtype_name = rtrim($gtype_name,'+');        
        
        $redata = array();
        $redata['gtype_name'] = $gtype_name;
        $insertGoodsTypeId = $this->goodstype->add_goodstype($redata,$data['group_id']);
        
        $reData['gtype_name'] = $gtype_name;
        $reData['gtype_id']   = $insertGoodsTypeId;
        
        //添加规格
        $i = 0;
        foreach($data['data'] as $v)
        {
            $addSpecData = array();
            $addSpecData['gtype_id']  = $insertGoodsTypeId;
            $addSpecData['spec_name'] = $v['name'];
            $insertSpecId = $this->goodstype->addspec($addSpecData);
            
            $reData['spec'][$i]['spec_id']  = $insertSpecId;
            $reData['spec'][$i]['spec_name'] = $v['name'];
            
            //添加具体的项
            foreach($v['item'] as $kk=>$vv)
            {
                $addSpecItemData = array();
                $addSpecItemData['spec_id']   = $insertSpecId;
                $addSpecItemData['item_name'] = $vv;
                $insertSpecItemId = $this->goodstype->addspecitem($addSpecItemData);
                
                
                $reData['spec'][$i]['item'][$kk]['id']   = $insertSpecItemId;
                $reData['spec'][$i]['item'][$kk]['item_name'] = $vv;
                
            }
            
            $i++;
        }
        
        
        ajaxReturnData(1,'商品规格创建成功',$reData);
    }
    
    //修改规格项
     public function editSpec(){
        $data = $this->request;

        $reData = array();
        
        //测试数据开始
        //editstatus 0不操作 1表示新增 2表示编辑 3表示删除
        /*
        $data = array(
            'status'=>1,
            'gtypeid'=>2,
            'data'=>array(
                '0'=>array(
                    'name'=>'选档2',
                    'editstatus'=>'3',
                    'id'=>'7'    
                )
            )
        );
         * 
         */
        //测试数据结束
        
        $stgtypeid = $this->ShopSpec->specEdit($data);
        $gtypeid = $data['gtypeid']>0?$data['gtypeid']:$stgtypeid;
        $tempSpecItemData = $this->goodstype->getTemplateSpecItem($gtypeid);
        
        ajaxReturnData(1,'规格项操作成功',$tempSpecItemData);
     }
    
    //批量规格价格修改
    
     //添加产品
     public function addDish(){
       $data = $this->request;
       //$data = json_decode(html_entity_decode(urldecode($_GP['data'])),true);
       
       /*
         //测试数据开始
         $data =  Array
         (
           'xcimg'=>array('1.jpg','2.jpg','3.jpg'),
            'xqimg'=>array('1.jpg','2.jpg','3.jpg'),
            'sts_id' => 1,
            'store_p1' => 71,
            'store_p2' => 76,
            'status' => 1,
            'isreason'=>1,
            'title' => '电脑产品',
            'description' => '电脑产品电脑产品电脑产品电脑产品',
            'content' => '电脑产品详情内容',
            'commision' => 0.05,
            'gtype_id' => 23,
            'data'=>array(
                '0'=>array(
                    'name'=>'内存',
                    'editstatus'=>'0',
                    'id'=>'38',
                    'item'=>array(
                        0=>array(
                            'itemName'=>'1G1G',
                            'editstatus'=>1,
                            'id'=>0
                        ),
                        1=>array(
                            'itemName'=>'2G',
                            'editstatus'=>2,
                            'id'=>89
                        ),
                        2=>array(
                            'itemName'=>'4G',
                            'editstatus'=>3,
                            'id'=>90
                        )
                    )    
                ),
                '1'=>array(
                    'name'=>'硬盘',
                    'editstatus'=>'0',
                    'id'=>'39',
                    'item'=>array(
                        0=>array(
                            'itemName'=>'1T1T',
                            'editstatus'=>1,
                            'id'=>0
                        ),
                        1=>array(
                            'itemName'=>'2T',
                            'editstatus'=>2,
                            'id'=>92
                        ),
                        2=>array(
                            'itemName'=>'4T',
                            'editstatus'=>3,
                            'id'=>93
                        )
                    )    
                )
            ),
            'itemPriceJson'=>array (
                0 => 
                array (
                  '配置' => '不好',
                  '档次' => '标配',
                  'productprice' => '21',
                  'marketprice' => '3',
                  'store_count' => '1',
                  'bar_code' => '2',
                  'spec_key' => '22_24',
                ),
                1 => 
                array (
                  '配置' => '好很好',
                  '档次' => '标配',
                  'productprice' => '21',
                  'marketprice' => '21',
                  'store_count' => '1',
                  'bar_code' => '1',
                  'spec_key' => '21_24',
                ),
              )
         );
        */
        // $_GP['itemPriceJson'] = json_decode('[{"配置":"不好","档次":"标配","productprice":"21","marketprice":"3","store_count":"1","bar_code":"2","spec_key":"22_24"},{"配置":"好很好","档次":"标配","productprice":"21","marketprice":"21","store_count":"1","bar_code":"1","spec_key":"21_24"}]',true);
        //测试数据结束
       
        $data['brand'] = $data['brand_id'];
        $goodsId = $this->goodService->addGoods($data);
        //addSpecOnaddGood

        $specPrice = $this->goodService->addSpecOnaddGood($goodsId,$data);
        
        //配置项
        if(count($data['data']) > 0)
        {
            $status = $this->ShopSpec->specEdit($data);
        }
        ajaxReturnData(1,'宝贝添加成功');
     }
    
     //编辑产品
     public function editDish(){
       $data = $this->request;
       //$data = json_decode(html_entity_decode(urldecode($_GP['data'])),true);
       
        //print_r(json_decode('[{"配置":"不好","档次":"标配","productprice":"21","marketprice":"3","store_count":"1","bar_code":"2","spec_key":"22_24"},{"配置":"好很好","档次":"标配","productprice":"21","marketprice":"21","store_count":"1","bar_code":"1","spec_key":"21_24"}]',true);
         //测试数据开始
         /*
         $data =  Array
         (
            'dish_id' => 693,
            'sts_id' => 1,
            'xcimg'=>array('1.jpg','2.jpg','3.jpg'),
            'xqimg'=>array('1.jpg','2.jpg','3.jpg'),
            'sts_id' => 1,
            'isreason'=>1,
            'store_p1' => 71,
            'store_p2' => 76,
            'status' => 1,
            'title' => '电脑产品693',
            'description' => '高配笔记本电脑',
            'content' => '高配笔记本电脑详情内容',
            'commision' => 0.05,
            'status'=>1,
             
            'gtypeid'=>21,
            'data'=>array(
                '0'=>array(
                    'name'=>'内存',
                    'editstatus'=>'0',
                    'id'=>'38',
                    'item'=>array(
                        0=>array(
                            'itemName'=>'1G1G',
                            'editstatus'=>1,
                            'id'=>0
                        ),
                        1=>array(
                            'itemName'=>'2G',
                            'editstatus'=>2,
                            'id'=>89
                        ),
                        2=>array(
                            'itemName'=>'4G',
                            'editstatus'=>3,
                            'id'=>90
                        )
                    )    
                ),
                '1'=>array(
                    'name'=>'硬盘',
                    'editstatus'=>'0',
                    'id'=>'39',
                    'item'=>array(
                        0=>array(
                            'itemName'=>'1T1T',
                            'editstatus'=>1,
                            'id'=>0
                        ),
                        1=>array(
                            'itemName'=>'2T',
                            'editstatus'=>2,
                            'id'=>92
                        ),
                        2=>array(
                            'itemName'=>'4T',
                            'editstatus'=>3,
                            'id'=>93
                        )
                    )    
                )
            ),
            'itemPriceJson'=>array (
                0 => 
                array (
                  '配置' => '不好',
                  '档次' => '标配',
                  'productprice' => '21',
                  'marketprice' => '3',
                  'store_count' => '1',
                  'bar_code' => '2',
                  'spec_key' => '22_24',
                ),
                1 => 
                array (
                  '配置' => '好很好',
                  '档次' => '标配',
                  'productprice' => '21',
                  'marketprice' => '21',
                  'store_count' => '1',
                  'bar_code' => '1',
                  'spec_key' => '21_24',
                ),
              )
         );
          * 
          */
         //测试数据结束
        $data['brand'] = $data['brand_id'];
        $data['dish_id'] = $data['id'];

        $goodsId = $this->goodService->addGoods($data);

        if(count($data['data']) > 0)
        {
            $status = $this->ShopSpec->specEdit($data);
        }
        //配置价格  
        
        ajaxReturnData(1,'宝贝编辑成功');
     }
     
    //产品库
    
    //产品详情
   public function dishContent(){
       $data = $this->request;

       $redata = array();
       //测试数据开始
       //$data['dish_id'] = 1802;
       //测试数据结束
       if($data['dish_id'] <= 0)
       {
           ajaxReturnData(0,'宝贝id不存在');
       }
       $dishData = $this->shopdish->getDishContent($data);

        $dishData['store_p1_name'] = $this->ShopCategory->getShopCategoryName($dishData['store_p1']);
        $dishData['store_p2_name'] = $this->ShopCategory->getShopCategoryName($dishData['store_p2']);
        $dishData['marketprice']   = FormatMoney($dishData['marketprice'], 2);
        $dishData['productprice']  = FormatMoney($dishData['productprice'], 2);

        $picData                   = $this->shopPic->getDishPic($data['dish_id']);
        
        //通过品牌ID获取品牌名称
        $brand = $this->shopBrand->getBrandName($dishData['brand']);
        $dishData['brand_id'] = intval($brand['id']);
        $dishData['brand_name'] = $brand['brand']!=''?$brand['brand']:'';
        
        if($picData['picurl'] != '')
        {
            $dishData['xcimg']         = explode(',',$picData['picurl']);
        }
        
        if($picData['contentpicurl'] != '')
        {
            $dishData['xqimg']         = explode(',',$picData['contentpicurl']);
        }
        $redata['dish'] = $dishData;
        
        //获取宝贝的配置信息
        
        //通过模型id获取对应的模型信息
        $data['gtype_id'] = $dishData['gtype_id'];
        $goodsStore_id = $this->goodstype->getGoodInfo($data,'store_id,name');
        
        //该模型影响的产品数
        $dish_total = $this->shopdish->getSpecDishCount($data['gtype_id'],$data['dish_id']);

        $redata['goodstype'] = array(
            'store_id'=>intval($goodsStore_id['store_id']),
            'gtype_name'=>$goodsStore_id['name'],
            'total'=>intval($dish_total['total'])
        );
        
        //返回模型的配置项 扩展项 squdian_goodstype_spec squdian_goodstype_spec_item
        $redata['gtype']['gtype_id']   = $data['gtype_id'];
        $redata['gtype']['gtype_name'] = $goodsStore_id['name'];
        $redata['gtype']['spec']       = $this->goodstype->getSpecAndItemByGtypeids($data['gtype_id'],$data['dish_id']);
        
        $redata['dishPrice'] = $this->goodstype->getDishSpecPrice($data);
        foreach($redata['dishPrice'] as $k=>$v){
            $redata['dishPrice'][$k]['marketprice']  = FormatMoney($v['marketprice'],2);
            $redata['dishPrice'][$k]['productprice'] = FormatMoney($v['productprice'],2);
        }
        ajaxReturnData(1,'宝贝详情',$redata);
   }  
    
     
    //宝贝搜索
    public function searchDish(){
        $data = $this->request;
        
        /*
        //测试数据开始
        $data['key']  = '美';
        $data['page'] = '1';
        //测试数据结束
         */
        
        if($data['key'] == '')
        {
            ajaxReturnData(0,'关键字不能为空');
        }
        
        $data['page'] = max(1, intval($data['page']));
        $data['page'] = ($data['page'] - 1) * $data['limit'];
        $data['limit'] = $data['limit']>0?$data['limit']:10;
        
        
        $dishData = $this->shopdish->searchDish($data,'title,marketprice,sales_num,store_count,id,thumb,status');
        $reData['total'] = $dishData['total'];
        unset($dishData['total']);
        $reData['dish'] = $dishData;
        ajaxReturnData(1,'搜索成功',$reData);
    }
    
    //产品库搜索
    public function searchGoods(){
        $data = $this->request;
        
        //测试数据开始
        //$data['key']  = '美';
        //data['page'] = '1';
        //测试数据结束
        
        if($data['key'] == '')
        {
            ajaxReturnData(0,'关键字不能为空');
        }
        
        $data['page'] = max(1, intval($data['page']));
        $data['page'] = ($data['page'] - 1) * $data['limit'];
        $data['limit'] = $data['limit']>0?$data['limit']:10;
        
        $goodsData = $this->goodService->searchGoods($data,'title,thumb,id,productprice,marketprice');
        $reData['total'] = $goodsData['total'];
        unset($goodsData['total']);
        $reData['goods'] = $goodsData;
        ajaxReturnData(1,'列表获取成功',$reData);
    }
    
    public function searchBrand(){
        $data = $this->request;
        if($data['key'] == '')
        {
             ajaxReturnData(0,'关键字必须填写');
        }
        //$this->shopBrand-getBrandSearch()
        $reData = array();
        $reData = $this->shopBrand->getBrandSearch($data);
        
        ajaxReturnData(1,'列表获取成功',$reData);
    }
     
    //历史品牌
    public function historyStoreBrand(){
        $reData = array();
        
        //获取店铺所有的宝贝的品牌ID
        $brandData = $this->shopdish->getDishAll('brand');
        
        foreach($brandData as $k=>$v)
        {
            $brandArrData[$k] = $v['brand'];
        }
        
        $brandArrData = array_unique($brandArrData);
        $brandStrData['ids']  = implode(',', $brandArrData);
        
        if($brandStrData['ids'] != '')
        {
            //通过品牌ID获取品牌信息
            $reData['history'] = $this->shopBrand->getBidsBrandAll($brandStrData);
        }
        else{
            $reData['history'] =  array();
        }
        
        ajaxReturnData(1,'历史品牌获取成功',$reData);
    }
    
    //产品列表
    public function dishListPage(){
        $data = $this->request;
        
        $reData = array();
        //测试数据开始
        /*http://local.otoshop.com:801/api/product/dishListPage.html?data={"store_p1":1,"store_p2":2}
        $_GP = array(
            'sales_num'      => 'asc',
            'store_count'    => 'desc',
            'marketprice_less'    => 100,
            'marketprice_many'    => 1000
        );
         * 
         */
        //价格区间
        //测试数据结束 
        $data['page'] = max(1, intval($data['page']));
        $data['page'] = ($data['page'] - 1) * $data['limit'];
        $data['limit'] = $data['limit']>0?$data['limit']:10;
        
        $reData['dish']  = $this->shopdish->getDishPage($data,'title,marketprice,sales_num,store_count,id,thumb,status');
        
        $reData['total'] = $reData['dish']['total'];
        unset($reData['dish']['total']);
       
        //获取分类信息
        ajaxReturnData(1,'列表获取成功',$reData);
    }
    
    //产品库表表
    public function goodsListGroupPage(){
        $data = $this->request;

        $reData = array();
        
        /*
        //获取用户添加的所有的产品库产品ID
        $storeGidsData = $this->shopdish->getDishGoosIds('gid');
        if(count($storeGidsData) > 0)
        {
            $storeGidsArr = array();
            foreach($storeGidsData as $v){
                $storeGidsArr[] = $v['gid'];
            }
        }
        */
        
        //测试数据开始
        
        /*
        $data = array(
            'store_count'         => 'desc',
            'marketprice_less'    => 0,
            'marketprice_many'    => 100,
            'page'                => 1,
            'limit'               => 10,
            'ccate'               => 202,
            'brands'              => 1,2,3,
            'getGroup'            => 1
        );
         */
        //测试数据结束
        
        //goodstype分类
        if($data['getGroup'] > 0){
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
        
        }
        else{
            $data['page'] = max(1, intval($data['page']));
            $data['page'] = ($data['page'] - 1) * $data['limit'];
            $data['limit'] = $data['limit']>0?$data['limit']:10;

            $reData['goods'] = $this->goodService->goodsListPage($data,'title,marketprice,store_count,id,thumb');
        }
        //获取分类信息
        //$reData['goods']['goodslisttotal']['total']
        
        $reData['total'] = intval($reData['goods']['goodslisttotal']['total']);
        unset($reData['goods']['goodslisttotal']);
  
        ajaxReturnData(1,'列表获取成功',$reData);   
    }
    
    //获取数据
    public function getBrand(){
        $data = $this->request;


        //测试数据开始
        /*
        $_GP = array(
            'store_p1' => 71,
            'store_p2' => 77
        );
         */
        //测试数据结束
        
        //根据
        $dishData = $this->shopdish->getPcontent($data,'distinct(brand)');
        $brand_ids = '';
        if(count($dishData) > 0){
            foreach($dishData as $v){
                $brand_ids .= $v['brand'].',';
            }
        }
        $brand_ids = rtrim($brand_ids,',');
        
        //品牌
        if($brand_ids != '')
        {
            $data['ids'] = $brand_ids;
            $reData['brand'] = $this->shopBrand->getBidsBrandAll($data);
        }
        ajaxReturnData(1,'品牌列表获取成功',$reData);
    }
    
    
    //获取数据
    public function getGoodsBrand(){
        $data = $this->request;


        //测试数据开始
        /*
        $_GP = array(
            'store_p1' => 71,
            'store_p2' => 77
        );
         */
        //测试数据结束
        
        //根据
        $dishData = $this->goods->getPcontent($data,'distinct(brand)');
        $brand_ids = '';
        if(count($dishData) > 0){
            foreach($dishData as $v){
                $brand_ids .= $v['brand'].',';
            }
        }
        $brand_ids = rtrim($brand_ids,',');
        
        //品牌
        if($brand_ids != '')
        {
            $data['ids'] = $brand_ids;
            $reData['brand'] = $this->shopBrand->getBidsBrandAll($data);
        }
        ajaxReturnData(1,'品牌列表获取成功',$reData);
    }
    
    //规格列表
    public function getTemplateSpecItemList(){
        $data = $this->request;
        
        //测试数据开始
        /*
        $_GP = array(
            'limit'=>10,
            'page'=>1
        );
         */
        //测试数据结束
        
        $reData = array();
        
        $reData = $this->goodstype->getStoreTemplateSpecItem($data);
        /*
        if(isset($reData['total']['total']))
        {
            $reData['total'] = $reData['total']['total'];
            unset($reData['total']['total']);
        }
        else{
            $reData['total'] = 0;
        }
         * 
         */
        $reData['total'] = intval($reData['total']['total']);
        ajaxReturnData(1,'规格列表获取成功',$reData);
    }
    
    //规格模板删除 变更状态
    public function delTemplateSpec(){
        $data = $this->request;
        
        //测试数据开始
        //$data['id']  = '2';
        //测试数据结束
        
        $data['gtype_id'] = $data['id'];
        $reData = array();
        
        $status = $this->goodstype->delSpecTemplate($data);

        if($status > 0)
        {
            ajaxReturnData(1,'删除成功');
        }
        else{
            ajaxReturnData(0,'删除失败');
        }
    }
    
    //规格配置移动到指定的分组
    public function moveTemplateSpec(){
        $data = $this->request;
        

        //测试数据开始
        /*
        $data = array(
            'group_id'=>4,
            'goods_type_idstr'=>'26'
        );
         * 
         */
        //测试数据结束

                
        $reData = $this->goodstype->moveStoreTemplateSpecItem($data);
        
        ajaxReturnData(1,'移动成功');
    }
    
    //获取指定的产品库
    public function goodsContent(){
        $data = $this->request;
       //$data = json_decode(html_entity_decode(urldecode($_GP['data'])),true);
       
       //测试数据开始
       //$data['id'] = 1;
       //测试数据结束
       
       $goodData['goodsData'] = $this->goods->getGoodInfo($data);
       
       $goodData['goodsData']['store_p1']    = $goodData['goodsData']['ccate'];
       $goodData['goodsData']['store_p2']    = $goodData['goodsData']['ccate2'];    
       
       
       $contentArr = changeWebImgToAli($goodData['goodsData']['content']);
       $goodData['goodsData']['xqimg']    = $contentArr['img']; 
       $goodData['goodsData']['content']  = $contentArr['content']; 
        $goodData['goodsData']['store_p1_name']         = $this->ShopSystemCategory->getShopSystemCategoryName($goodData['goodsData']['pcate']);
        $goodData['goodsData']['store_p2_name']        = $this->ShopSystemCategory->getShopSystemCategoryName($goodData['goodsData']['ccate']);
        $goodData['goodsData']['marketprice']        = $goodData['goodsData']['marketprice'];
        $goodData['goodsData']['productprice']       = $goodData['goodsData']['productprice'];
        
        if($goodData['goodsData']['brand'] > 0)
        {
            //
            $brand_id = intval($goodData['goodsData']['brand']);
            $brand = $this->shopBrand->getOneBrandTitle($brand_id);
            
            $goodData['goodsData']['brand_id']        = $brand['id'];
            $goodData['goodsData']['brand_name']      = $brand['brand'];
        }
        else{
            $goodData['goodsData']['brand_id']   = '';
            $goodData['goodsData']['brand_name'] = '';
        }
                
        $picData                  = $this->shopPic->getGoodPic($data['id']);
        $goodData['goodsData']['xcimg']        = explode(',',$picData['picurl']);

        ajaxReturnData(1,'产品库详情',$goodData);
    }
    
    //根据模型id返回受影响的产品
    public function getGoodsType(){
         $data       = $this->request;
         $redata     = array();
         if($data['gtype_id'] <= 0)
         {
            ajaxReturnData(0,'必要参数不存在');
         }
         $countGtype = $this->shopdish->getGtypeCount($data);
         
         $redata['total']     = count($countGtype);
         $redata['ids']       = $countGtype;
         
         ajaxReturnData(1,'受影响的宝贝量',$redata);
    }
    
    //店铺分类移动顺序
    public function storeMoveCategorySort(){
        $data       = $this->request;
        
        //测试数据开始
        /*
        $data = array(
            'oneCategory'=>array(
                0=>array(
                    'id'=>128,
                    'twoCategory'=>array(
                        0=>array(
                             'id'=>146
                        ),
                        1=>array(
                             'id'=>147
                        ),
                        2=>array(
                             'id'=>145
                        ),
                        3=>array(
                             'id'=>129
                        ),
                        4=>array(
                             'id'=>361
                        )
                    )
                )
            )
        );
        */
        //测试数据结束

        $onesort = 1;
        foreach($data['oneCategory'] as $v)
        {
            $onedata  = array();
            $onedata['id']   = $v['id'];
            $onedata['sort'] = $onesort;
            $oneUpdateStatus = $this->ShopCategory->upStoreCategorySort($onedata);
            $twosort = 1;
            if(count($v['twoCategory']) > 0)
            {
                foreach($v['twoCategory'] as $vv)
                {
                    $twodata  = array();
                    $twodata['id']   = $vv['id'];
                    $twodata['sort'] = $twosort;
                    $twoUpdateStatus = $this->ShopCategory->upStoreCategorySort($twodata);
                    $twosort = $twosort + 1;
                }
            }
            else{
                $onesort = $onesort + 1;
                continue;
            }
            $onesort = $onesort + 1;
        }
        
        ajaxReturnData(1,'店铺分类移动成功');
    }
    
    //获取店铺分类 和 店铺分类的产品统计信息
    public function categoryBatch(){
         $member = get_member_account();
         $redata = array();
         $redata['store_member'] = $member['store_sts_name'];
         
         //获取店铺分类
         $cate_list = $data = $this->ShopCategory->getShopCategoryTree();
         
         $oneids = '';
         $twoids = '';
         foreach($cate_list['oneCategory'] as $v)
         {
             $oneids .= $v['id'].',';
             if(count($v['twoCategory']) > 0)
             {
                foreach($v['twoCategory'] as $vv){
                    $twoids .= $vv['id'].',';
                }
             }
         }
         $oneids = rtrim($oneids,',');
         $twoids = rtrim($twoids,',');

         //统计内容
         $totalOneData = array();
         $totalTwoData = array();
         $totalOneData = $this->shopdish->count_category_one_dish($oneids);
         $totalTwoData = $this->shopdish->count_category_two_dish($twoids);
         
         foreach($totalOneData as $v)
         {
             $oneData[$v['store_p1']] = $v['total'];
         }
         
         foreach($totalTwoData as $v)
         {
             $twoData[$v['store_p2']] = $v['total'];
         }
         
         foreach($cate_list['oneCategory'] as $k=>$v)
         {
             $cate_list['oneCategory'][$k]['dishtotal'] = intval($oneData[$v['id']]);
             
            if(count($v['twoCategory']) > 0)
            {
               foreach($v['twoCategory'] as $kk=>$vv){
                   $cate_list['oneCategory'][$k]['twoCategory'][$kk]['dishtotal'] = intval($twoData[$vv['id']]);
               }
            }
            else{
                $cate_list['oneCategory'][$k]['twoCategory'] = array();
            }
         }
         
         //系统分类
         $cate_list['systemGroup'] = $this->ShopSystemCategory->ShopCateGroupListTwo();

         $onesysids = '';
         $twosysids = '';
        foreach($cate_list['systemGroup']['oneCategory'] as $v)
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
         
         foreach($cate_list['systemGroup']['oneCategory'] as $k=>$v)
         {
             $cate_list['systemGroup']['oneCategory'][$k]['dishtotal'] = intval($oneSysData[$v['id']]);
             
            if(count($v['twoCategory']) > 0)
            {
               foreach($v['twoCategory'] as $kk=>$vv){
                   $cate_list['systemGroup']['oneCategory'][$k]['twoCategory'][$kk]['dishtotal'] = intval($twoSysData[$vv['id']]);
               }
            }   
         }
         if(count($cate_list['systemGroup']['oneCategory']) <= 0)
         {
            //$cate_list['systemGroup']['oneCategory'] = array();
         }
         ajaxReturnData(1,'列表获取成功',$cate_list);
    }
    
    //将产品库的产品移动到某个分类
    public function moveDish(){
        $data       = $this->request;
        
        /*
        //测试数据开始
        $data = array(
            'store_p1'=>1,
            'store_p2'=>2,
            'goods_ids'=>'1010,1011,1012'
        );
        //测试数据结束
        */
        if($data['store_p1'] <= 0 || $data['store_p2'] <= 0)
        {
            ajaxReturnData(0,'店铺分类ID必须存在');
        }
        
        if($data['goods_ids'] == ''){
             ajaxReturnData(0,'关键参数不存在');
        }
        
        //获取产品库数据
       $goodInfoData = $this->goodService->getGoodInfos($data);
       //获取对应的产品库相册信息
       $goodPicData = $this->shopPic->getGoodPics($data);
       $goodPicArr = array();
       foreach($goodPicData as $k=>$v){
           $goodPicArr[$v['goodid']]['picurl'][$k] = $v['picurl'];
           if($v['contenturl'] != ''){
            $goodPicArr[$v['goodid']]['contenturl'][$k] = $v['contenturl'];
           }
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
           $goodsData['xcimg']        = count($goodPicArr[$v['id']]['picurl'])>0?$goodPicArr[$v['id']]['picurl']:array();
           $goodsData['xqimg']        = count($goodPicArr[$v['id']]['contenturl'])>0?$goodPicArr[$v['id']]['contenturl']:array();
           //宝贝图片
           $dishId = $this->goodService->addGoods($goodsData);
           
           $dishIds .= $dishId.',';
           
           $i++;
       }
       
       //ajaxReturnData(1,'成功导入'.$i.'条记录');
       ajaxReturnData(1,'导入成功');
    }
    
    //分类导入产品
    public function batGroupDish(){
        $data = $this->request;
        
        //测试数据开始
        $data = array(
            'categoryOneIds'=>'701'
        );
        //测试数据结束

        
        if($data['categoryOneIds'] == ''){
             ajaxReturnData(0,'关键参数不存在');
        }
        
        //获取分类信息
        $groupListData = $this->ShopSystemCategory->ShopCateIdsGroupList($data['categoryOneIds']);  //系统分类
        
        $groupStoreListData = $this->ShopCategory->getShopCategoryTree();                           //店铺分类
        
        //获取产品库产品数据
        $goodInfoData = $this->goodService->getGoodInfoGroup($data);
        
        $goodData = array();
        $goodIds = '';
        foreach($goodInfoData as $v){
            //unset($v['content']);
            
            $goodData[$v['ccate']][] = $v;
            $goodIds .= $v['id'].',';
        }

        $goodIds .= rtrim($goodIds, ',');
        
       $data['goods_ids'] = $goodIds;
       $goodPicData = $this->shopPic->getGoodPics($data);
       $goodPicArr = array();
       foreach($goodPicData as $k=>$v){
           $goodPicArr[$v['goodid']]['picurl'][$k]     = $v['picurl'];
           if($v['contenturl'] != '')
           {
            $goodPicArr[$v['goodid']]['contenturl'][$k] = $v['contenturl'];
           }
       }
        
        $p1 = '';
        $p2 = '';

        foreach($groupListData['oneCategory'] as $k=>$v)
        {
            if($v['name'] == $groupStoreListData['oneCategory'][$k]['cat_name']){
                $p1 = $groupStoreListData['oneCategory'][$k]['id'];
            }
            else{
                $insertData             = array();
                $insertData['cat_name'] = $v['name']; 
                $insertData['pid']      = 0; 
                $insertData['p_ccate']      = $v['id']; 
                $p1 = $this->ShopCategory->do_addCate($insertData);
            }
            
            if(count($v['twoCategory']) > 0)
            {
                foreach($v['twoCategory'] as $kk=>$vv){
                    if($vv['name'] == $groupStoreListData['oneCategory'][$k]['twoCategory'][$kk]['cat_name']){
                        $p2 = $groupStoreListData['oneCategory'][$k]['twoCategory'][$kk]['id'];
                    }
                    else{
                        $insertData             = array();
                        $insertData['cat_name'] = $vv['name']; 
                        $insertData['pid']      = $p1; 
                        $insertData['p_ccate']      = $v['id']; 
                        $insertData['p_ccate2']      = $vv['id']; 
                        $p2 = $this->ShopCategory->do_addCate($insertData);
                    }
                    
                    if(count($goodData[$vv['id']]) > 0){
                        foreach($goodData[$vv['id']] as $vvv)
                        {
                            //判断该产品在库里是否已经存在
                            $checkDish = $this->shopdish->checkGoods($vvv['id']);
                            if($checkDish)
                            {
                                continue;
                            }
                            
                            $goodsData                 = array();
                            $goodsData['gid']          = $vvv['id'];
                            $goodsData['store_p1']     = $p1;
                            $goodsData['store_p2']     = $p2;
                            $goodsData['status']       = 0;
                            $goodsData['title']        = $vvv['title'];
                            $goodsData['thumb']        = $vvv['thumb'];
                            $goodsData['description']  = $vvv['description'];
                            $goodsData['content']      = $vvv['content'];
                            $goodsData['marketprice']  = $vvv['marketprice'];
                            $goodsData['productprice'] = $vvv['productprice'];
                            $goodsData['goodssn']      = $vvv['goodssn'];
                            $goodsData['store_count']  = $vvv['store_count'];
                            $goodsData['isnew']        = $vvv['isnew'];
                            $goodsData['xcimg']        = count($goodPicArr[$vvv['id']]['picurl'])>0?$goodPicArr[$vvv['id']]['picurl']:array();
                            $goodsData['xqimg']        = count($goodPicArr[$vvv['id']]['contenturl'])>0?$goodPicArr[$vvv['id']]['contenturl']:array();

                            //宝贝图片
                            $dishId = $this->goodService->addGoods($goodsData);
                        }
                    }
                    
                }
            }
        }
        
        ajaxReturnData(1,'导入成功');
    }
    
    //新增一个接口 dish_id gtype_id
    public function getDishGtype(){
        $data = $this->request;
        $redata = array();
        $data['dish_id']  = intval($data['dish_id']);
        $data['gtype_id'] = intval($data['gtype_id']);
        

        //测试数据开始
        /*
        $data = array(
            'dish_id' =>'640',
            'gtype_id'=>'23'
        );
         * 
         */
        //测试数据结束
        if($data['dish_id'] <= 0 || $data['gtype_id'] <= 0 )
        {
            ajaxReturnData(0,'必要参数不存在');
        }
        
        
       //判断当前模型是系统还是个人
        
        
        //通过模型id获取对应的模型信息
        $goodsStore_id = $this->goodstype->getGoodInfo($data,'store_id');
        
        //该模型影响的产品数
        $dish_total = $this->shopdish->getSpecDishCount($data['gtype_id'],$data['dish_id']);
        
        $redata['goodstype'] = array(
            'store_id'=>intval($goodsStore_id['store_id']),
            'total'=>intval($dish_total['total'])
        );
        
        //返回模型的配置项 扩展项 squdian_goodstype_spec squdian_goodstype_spec_item
        $redata['gtype']['gtype_id'] = $data['gtype_id'];
        $redata['gtype']['spec'] = $this->goodstype->getSpecAndItemByGtypeids($data['gtype_id']);
        
        $redata['dishPrice'] = $this->goodstype->getDishSpecPrice($data);
        
        ajaxReturnData(1,'列表获取成功',$redata);
    }
    
    //上下架宝贝
    public function statusDish(){
        $data = $this->request;
        
        /*
        //测试数据开始
        $data = array(
            'dish_id' =>'1'
        );
        //测试数据结束
         * 
         */
        
        if($data['dish_id'] <= 0)
        {
             ajaxReturnData(0,'必要参数不存在');
        }
        
        $upDishStatus = $this->shopdish->changeDishStatus($data);
        
        ajaxReturnData(1,'操作成功');
    }
    
    
    public function deleteDish(){
        $data = $this->request;
        
        /*
        //测试数据开始
        $data = array(
            'dish_id'  =>'1'
        );
        //测试数据结束
        */
        
        if($data['dish_id'] <= 0)
        {
            ajaxReturnData(0,'必要参数不存在');
        }
        
        $upDishStatus = $this->shopdish->deleteDish($data);
        
        ajaxReturnData(1,'删除成功');
    }
    
}