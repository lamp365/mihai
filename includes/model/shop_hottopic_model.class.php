<?php
/**
 *模型层:区间设置
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class shop_hottopic_model extends model
{
    public $table_name;
    public function __construct() {
		$this->table_name = 'shop_hottopic';
		parent::__construct();
	}
	/**
	 * 获得多条shop_hottopic表信息
	 *   */
	public function getAllShopHot($where,$param="*",$orderby=false){
	    return $this->getAll($where,$param,$orderby);
	}
    
}