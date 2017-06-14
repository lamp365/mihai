<?php
/**
 *模型层:店铺商店模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
use model\model;
class shop_goods_comment_model extends model
{
    public $table_name='';
    public function __construct() {
		$this->table_name = 'shop_goods_comment';
		parent::__construct();
	}
	
    
}