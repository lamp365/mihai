<?php
/**
 *模型层:活动列表
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class activity_list_model extends model
{
    public $table_name;
    public function __construct() {
		$this->table_name = 'activity_list';
		parent::__construct();
	}
	/**
	 * 获得单条activity_list表信息
	 *   */
	public function getOneActList($where = array(),$param="*"){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param);
	}
	/**
	 * 获得多条activity_list表信息
	 *   */
	public function getAllActList($where,$param="*",$orderby=false){
	    return $this->getAll($where,$param,$orderby);
	}
}