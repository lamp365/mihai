<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\seller;

class ShopStoreCategoryService extends \service\publicService {
    private $memberData;
    private $table;
    
    function __construct() {
       parent::__construct();
       $this->memberData = get_member_account();
       $this->table      = table('store_shop_category');
       
   }
   
   public function getStoreShopCategory($shopCategoryIds,$fields='*'){
       //$this->memberData['sts_category_p1_id'] sts_category_p2_id
       $sql = "select {$fields} from " . $this->table . " where id in ({$shopCategoryIds})";
       $data = mysqld_selectall($sql);
       return $data;
   }
   
   
}   