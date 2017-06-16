<?php
/**
 *模型层:购物车模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class shop_cart_model extends model
{
    public $table_name;
    public function __construct() {
		$this->table_name = 'shop_cart';
		parent::__construct();
	}
}