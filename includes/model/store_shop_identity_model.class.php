<?php
/**
 *模型层:店铺身份认证模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class store_shop_identity_model extends model
{
    public function __construct() {
		$this->table_name = 'store_shop_identity';
		parent::__construct();
	}
	/**
	 * 获得单条store_shop_identity表信息
	 *   */
	public function getOneStoreShopIdentity($where = array(),$param="*",$orderby=false){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param,$orderby);
	}
    
}