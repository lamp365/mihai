<?php
/**
 *模型层:优惠券
 *执行sql
 *Author:严立超 
 *   
 **/
namespace model;
class store_coupon_model extends model
{
    public $table_name;
    public function __construct() {
		$this->table_name = 'store_coupon';
		parent::__construct();
	}
	/**
	 * 获得单条store_coupon表信息
	 *   */
	public function getOneCoupon($where ,$param="*"){
	    if (empty($where)) return false;
	    return $this->getOne($where,$param);
	}
	/**
	 * 获得多条store_coupon表信息
	 *   */
	public function getAllCoupon($where,$param="*",$orderby=false){
	    return $this->getAll($where,$param,$orderby);
	}
}