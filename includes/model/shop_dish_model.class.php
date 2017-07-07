<?php
/**
 *模型层:商品模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class shop_dish_model extends model
{
    public $table_name;
    public function __construct() {
		$this->table_name = 'shop_dish';
		parent::__construct();
	}
	/**
	 * 获得单条shop_dish表信息
	 *   */
	public function getOneShopDish($where,$param="*",$orderby=false,$group=false){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param,$orderby=false,$group=false);
	}
	/**
	 * 获得多条shop_dish表信息
	 *   */
	public function getAllShopDish($where,$param="*",$orderby=false,$group=false){
	    return $this->getAll($where,$param,$orderby,$group);
	}
}