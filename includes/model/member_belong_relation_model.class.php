<?php
/**
 *模型层:会员归属关系模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class member_belong_relation_model extends model
{
    public function __construct() {
		$this->table_name = 'member_belong_relation';
		parent::__construct();
	}
	/**
	 * 获得多条member_belong_relation表信息
	 *   */
	public function getAllMemberBelong($where = array(),$param="*",$orderby=false){
	    return $this->getAll($where,$param,$orderby);
	}
	
}