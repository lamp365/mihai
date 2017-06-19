<?php
/**
 *模型层:区间设置
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class activity_area_model extends model
{
    public $table_name;
    public function __construct() {
		$this->table_name = 'activity_area';
		parent::__construct();
	}
	/**
	 * 获得单条activity_area表信息
	 *   */
	public function getOneActArea($where = array(),$param="*"){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param);
	}
	/**
	 * 获得多条activity_area表信息
	 *   */
	public function getAllActArea($where,$param="*",$orderby=false){
	    return $this->getAll($where,$param,$orderby);
	}
    
}