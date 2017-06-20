<?php
/**
 *模型层:用户领取的优惠券模型
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class store_coupon_member_model extends model
{
    public function __construct() {
		$this->table_name = 'store_coupon_member';
		parent::__construct();
	}
	/**
	 * 通过查询条件获取用户优惠券表,返回一条数据
	 *@param $where 查询条件
	 *   */
	public function getOneMemberCoupon($where,$param="*",$orderby=false){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param);
	}
	/**
	 * 通过查询条件获取用户优惠券表,返回多条数据
	 *@param $where 查询条件
	 *   */
	public function getAllMemberCoupon($where,$param="*",$orderby=false){
	    return $this->getAll($where,$param,$orderby);
	}
	/**
	 * 获取我的优惠券列表
	 *   */
	public function getAllMyCoupon($where,$param,$orderby=false){
	    if (empty($param)) return false;
	    if (is_array($where)) $where = to_sqls($where);
	    $sql = "SELECT {$param} FROM ".table($this->table_name)." AS a LEFT JOIN ".table('store_coupon')." AS b ON a.scid = b.scid";
	    $sql .= ($where) ? " WHERE $where" : '';
	    $sql .= ($orderby) ? " ORDER BY $orderby" : '';
	    return $this->fetchall($sql);
	}
}