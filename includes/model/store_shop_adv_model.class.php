<?php
/**
 *模型层:store_shop_adv
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
use model\model;
class store_shop_adv_model extends model
{
    public $table_name;
    public function __construct() {
		$this->table_name = 'store_shop_adv';
		parent::__construct();
	}
	
    
}