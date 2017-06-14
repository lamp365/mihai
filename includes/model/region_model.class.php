<?php
/**
 *模型层:店铺商店模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
use model\model;
class region_model extends model
{
    public function __construct() {
		$this->table_name = 'region';
		parent::__construct();
	}
	
    
}