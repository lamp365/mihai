<?php
/**
 *模型层:店铺商店模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
use model\model;
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
    
}