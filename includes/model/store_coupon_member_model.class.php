<?php
/**
 *模型层:用户领取的优惠券模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class store_coupon_member_model extends model
{
    public function __construct() {
		$this->table_name = 'store_coupon_member';
		parent::__construct();
	}
	
    
}