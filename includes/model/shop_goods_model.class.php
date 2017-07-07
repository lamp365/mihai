<?php
/**
 *模型层:shop_goods
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class shop_goods_model extends model
{
    public $table_name='';
    public function __construct() {
		$this->table_name = 'shop_goods';
		parent::__construct();
	}
	/**
	 * 获得单条shop_goods表信息
	 *   */
	public function getOneShopGoods($where = array(),$param="*"){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param);
	}
	/**
	 * 获得多条shop_goods表信息
	 *   */
	public function getAllShopGoods($where = array(),$param="*",$orderby=false){
	    return $this->getAll($where,$param,$orderby);
	}
    
}