<?php
/**
 *模型层:店铺等级
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class store_shop_level_model extends model
{
    public $table_name;
    public function __construct() {
		$this->table_name = 'store_shop_level';
		parent::__construct();
	}
	/**
	 * 获得单条store_shop_level表信息
	 *   */
	public function getOneShopLevel($where = array(),$param="*"){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param);
	}
	/**
	 * 获得多条store_shop_level表信息
	 *   */
	public function getAllShopLevel($where = array(),$param="*",$orderby=false){
	    return $this->getAll($where,$param,$orderby);
	}
}