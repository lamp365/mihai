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
	/**
	 * 取出当前活动
	 *   */
	public function getCurrentAct(){
	    $now = time();
	    $where = "ac_status=1 and ac_time_end > $now";
	    $list = $this->getAllActList($where,'ac_id,ac_title,ac_time_str,ac_time_end,ac_area');
	    if($list){
	        return $list[0];//默认只有一个
	    }
	}
    
}