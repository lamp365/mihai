<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\seller;

class ShopPicService extends \service\publicService {
    private $table;
    
   function __construct() {
       parent::__construct();
       $this->table      = table('shop_dish_piclist');
       $this->goodsTable     = table('shop_goods_piclist');
   }
   
   //获取
   public function getDishPic($dish_id,$fields='*'){
       if($dish_id <= 0)
        {
            return false;
        }
        $sql = "select {$fields} from {$this->table} where goodid={$dish_id}";
        $dishPicContent = mysqld_select($sql);
        return $dishPicContent;
   }
   
   //获取
   public function getGoodPic($goodid,$fields='*'){
       if($goodid <= 0)
        {
            return false;
        }
        $sql = "select {$fields} from {$this->goodsTable} where goodid={$goodid}";
        $dishPicContent = mysqld_select($sql);
        return $dishPicContent;
   }
   
   //获取
   public function getGoodPics($data,$fields='*'){
       if($data['goods_ids'] == '')
        {
            return false;
        }
        $sql = "select {$fields} from {$this->goodsTable} where goodid in ({$data['goods_ids']})";
        $dishPicContent = mysqld_selectAll($sql);
        return $dishPicContent;
   }
   
   //图片操作
   public function upPic($data){
       //$this->table
       $sql = "update {$this->table} set picurl = '{$data['picurl']}' where goodid = {$data['dish_id']}";
       $rs  = mysqld_query($sql);
       
       return $rs;
   }
   
}