<?php
/**
 *模型层:用户收货地址
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class shop_address_model extends model
{
    public function __construct() {
		$this->table_name = 'shop_address';
		parent::__construct();
	}
	/**
	 * 获得单条shop_address表信息
	 *   */
	public function getOneAddress($where = array(),$param="*"){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param);
	}
	/**
	 * 获得单条shop_address表信息
	 *   */
	public function getAllAddress($where ,$param="*",$orderby=false){
	    return $this->getAll($where,$param,$orderby);
	}
}