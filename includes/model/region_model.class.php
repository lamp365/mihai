<?php
/**
 *模型层:地区模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class region_model extends model
{
    public function __construct() {
		$this->table_name = 'region';
		parent::__construct();
	}
	/**
	 * 获得单条region表信息
	 *   */
	public function getOneRegion($where = array(),$param="*"){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param);
	}
	/**
	 * 获得多条region表信息
	 *   */
	public function getAllRegion($where ,$param="*",$orderby = false){
	    return $this->getAll($where,$param,$orderby);
	}
    /**
     * 根据子code获取父code
     *   */
	public function getPCodeByCCode($child_code){
	    if (empty($child_code)) return false;
	    $info = $this->getOneRegion(array('region_code'=>$child_code),'parent_id');
	    if ($info){
	       return $this->getOneRegion(array('region_id'=>$info['parent_id']),'region_code');
	    }
	}
}