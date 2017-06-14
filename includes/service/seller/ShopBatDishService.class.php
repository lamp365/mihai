<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace service\seller;

class ShopBatDishService extends \service\publicService {
    private $memberData;
    private $table;
    private $goodService    = array();
    private $shopPic        = array();
    private $shopdish       = array();
    
    function __construct() {
        parent::__construct();
       $this->memberData = get_member_account();
       $this->table      = table('shop_dish');
       
       $this->goodService          = new \service\seller\goodsService();
       $this->shopPic              = new \service\seller\ShopPicService();     //宝贝图片
       $this->shopdish             = new \service\seller\ShopDishService();   //宝贝操作对象
    }
    
    public function batDish($data){
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
           $goodsData['status']       = 1;
           $goodsData['title']        = $v['title'];
           $goodsData['thumb']        = $v['thumb'];
           $goodsData['description']  = $v['description'];
           $goodsData['content']      = $v['content'];
           $goodsData['marketprice']  = $v['marketprice'];
           $goodsData['productprice'] = $v['productprice'];
           $goodsData['goodssn']      = $v['goodssn'];
           $goodsData['store_count']  = $v['store_count'];
           $goodsData['isnew']        = $v['isnew'];
           $goodsData['xcimg']        = count($goodPicArr[$v['id']])>0?$goodPicArr[$v['id']]:array();

           //宝贝图片
           $dishId = $this->goodService->addGoods($goodsData);
           
           $dishIds .= $dishId.',';
           
           $i++;
       } 
       
       return $i;
    }
}