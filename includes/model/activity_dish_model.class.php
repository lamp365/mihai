<?php
/**
 *模型层:区间设置
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class activity_dish_model extends model
{
    public $table_name;
    public function __construct() {
		$this->table_name = 'activity_dish';
		parent::__construct();
	}
	/**
	 * 获得多条activity_dish表信息
	 *   */
	public function getAllActivtyDish($where,$param="*",$orderby=false){
	    return $this->getAll($where,$param,$orderby);
	}
    
}