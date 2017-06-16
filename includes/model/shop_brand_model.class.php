<?php
/**
 *模型层:商品品牌模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class shop_brand_model extends model
{
    public function __construct() {
		$this->table_name = 'shop_brand';
		parent::__construct();
	}
	/**
	 * 获得单条shop_brand表信息
	 *   */
	public function getOneShopBrand($where = array(),$param="*"){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param);
	}
    
}