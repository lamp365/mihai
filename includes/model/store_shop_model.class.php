<?php
/**
 *模型层:店铺商店模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class store_shop_model extends model
{
    public function __construct() {
		$this->table_name = 'store_shop';
		parent::__construct();
	}
	
    
}