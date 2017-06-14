<?php
/**
 *模型层:店铺商店模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
use model\model;
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