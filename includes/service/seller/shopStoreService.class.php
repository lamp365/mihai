<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\seller;

class shopStoreService extends \service\publicService {
    private $memberData;
    private $table_store_shop;
    private $table_store_shop_level;
    
    function __construct() {
       parent::__construct();
       $this->memberData              = get_member_account();
       $this->table_store_shop        = table('store_shop');
       $this->table_store_shop_level  = table('store_shop_level');
   }
   
   function getStoreShopLevel($rank_level=0,$fields='*'){
       $where = 'where 1';
       if($rank_level > 0)
       {
           $where .= " and rank_level = {$rank_level}";
       }
       $sql = "SELECT {$fields} FROM {$this->table_store_shop_level} {$where}";
       $data  = mysqld_select($sql);
       return $data;
   }
   
   function getStoreShop($fields='*'){
       $sql = "SELECT {$fields} FROM {$this->table_store_shop} where sts_id = {$this->memberData['store_sts_id']}";
       $data  = mysqld_select($sql);
       return $data;
   }
   
   
   function getStoreShopInfo($sts_id,$fields='*'){
       $sql = "SELECT {$fields} FROM {$this->table_store_shop} where sts_id = {$sts_id}";
       $data  = mysqld_select($sql);
       return $data;
   }
   
   //更新用户余额
   function updateStoreMoney($data,$sts_id){
       $upStatus = mysqld_update('store_shop',$data,array('sts_id'=>$sts_id ));
       return $upStatus;
   }
   
   //延长用户租期
   function extendedUserLease($data,$sts_id){
       $upStatus = mysqld_update('store_shop',$data,array('sts_id'=>$sts_id ));
       return $upStatus;
   }
   
}