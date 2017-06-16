<?php
/**
 *模型层:商品评论统计模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class shop_goods_comment_total_model extends model
{
    public $table_name;
    public function __construct() {
		$this->table_name = 'shop_goods_comment_total';
		parent::__construct();
	}
	
    
}