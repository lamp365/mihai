<?php
/**
 *模型层:store_coupon_member
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
use model\model;
class store_coupon_member_model extends model
{
    public function __construct() {
		$this->table_name = 'store_coupon_member';
		parent::__construct();
	}
	
    
}