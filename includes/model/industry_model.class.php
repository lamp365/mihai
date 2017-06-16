<?php
/**
 *模型层:行业模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class  industry_model extends model
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
	/**
	 * 根据行业id（多个），获取行业数据
	 */
	private function getIndustry()
	{
	    $industryModel = new \model\industry_model();
	    //"SELECT gc_id,gc_name from squdian_industry as a where EXISTS (select * from squdian_shop_category as b WHERE a.gc_id=b.industry_p2_id);"
	    $sql = "SELECT gc_id,gc_name from ".table($industryModel->table_name)." AS a WHERE EXISTS (SELECT * FROM ".table('shop_category') ." AS b WHERE a.gc_id=b.industry_p2_id)";
	    $info = $industryModel->fetchall($sql);
	    return $info;
	}
}