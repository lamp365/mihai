<?php
/**
 *模型层:行业模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class industry_model extends model
{
    public $table_name;
    public function __construct() {
		$this->table_name = 'industry';
		parent::__construct();
	}
	/**
	 * 获得单条industry表信息
	 *   */
	public function getOneIndustry($where = array(),$param="*"){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param);
	}
	/**
	 * 获得多条industry表信息
	 *   */
	public function getAllIndustry($where = array(),$param="*",$orderby=false){
	    return $this->getAll($where,$param,$orderby);
	}
}