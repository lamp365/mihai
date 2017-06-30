<?php
/**
 *模型层:商品栏目模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class shop_category_model extends model
{
    public function __construct() {
		$this->table_name = 'shop_category';
		parent::__construct();
	}
	/**
	 * 获得单条shop_category表信息
	 *   */
	public function getOneShopCategory($where = array(),$param="*"){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param);
	}
	/**
	 * 获得多条shop_category表信息
	 *   */
	public function getAllShopCategory($where = array(),$param="*"){
	    if (empty($where)) return false;
	    return $this->getAll($where,$param);
	}
}