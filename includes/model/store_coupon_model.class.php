<?php
/**
 *模型层:store_coupon
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
use model\model;
class store_coupon_model extends model
{
    public function __construct() {
		$this->table_name = 'store_coupon';
		parent::__construct();
	}
	
    
}