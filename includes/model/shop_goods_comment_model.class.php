<?php
/**
 *模型层:商品评论模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class shop_goods_comment_model extends model
{
    public $table_name='';
    public function __construct() {
		$this->table_name = 'shop_goods_comment';
		parent::__construct();
	}
	
    
}