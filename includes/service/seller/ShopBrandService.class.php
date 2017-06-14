<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\seller;

class ShopBrandService extends \service\publicService {
    private $memberData;
    private $table;
        
   function __construct() {
       parent::__construct();
       $this->memberData = get_member_account();
       $this->table      = table('shop_brand');
   }
   
   public function getBrandAll($_GP,$fields='id,brand'){
       $reData = array();
       
       $sql = "select {$fields} from {$this->table} where industry_p1_id = {$this->memberData['sts_category_p1_id']} and industry_p2_id = {$this->memberData['sts_category_p2_id']}";
       $data  = mysqld_selectall($sql);
       return $data;
   }
    
	
	   public function getBrandName($brand_id,$fields='id,brand'){
       $reData = array();
       $sql = "select {$fields} from {$this->table} where id = {$brand_id}";
       $data  = mysqld_select($sql);
       return $data;
   }
   
   public function getBidsBrandAll($_GP,$fields='id,brand'){
       $reData = array();
       $sql = "select {$fields} from {$this->table} where id in ({$_GP['ids']})";
       $data  = mysqld_selectall($sql);
       return $data;
   }
   
   public function getBrandTitle($_GP,$fields='id,brand'){
       $reData = array();
       $sql = "select {$fields} from {$this->table} where id in ({$_GP['ids']})";
       $data  = mysqld_selectall($sql);
       return $data;
   }
   
   public function getOneBrandTitle($id,$fields='id,brand'){
       $reData = array();
       $sql = "select {$fields} from {$this->table} where id = {$id}";
       $data  = mysqld_select($sql);
       return $data;
   }
    
   public function getBrandSearch($_GP,$fields='id,brand'){
       $reData = array();
       $sql = "select {$fields} from {$this->table} where brand like '%{$_GP['key']}%'";
       $reData['brand']  = mysqld_selectall($sql);
       return $reData;
   } 
}