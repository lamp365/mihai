<?php
/**
 * 统计相关公共函数
 */



/**
 * 新增访问记录
 * 
 * @param $flag_code:string  标识码
 */
function insertAccessLog($flag_code)
{
	//访问记录
	$accessInfo = mysqld_select("SELECT access_id FROM " . table('access_log') . " where flag_code='".$flag_code."' and createtime='".date('Y-m-d')."' ");
	
	//是否有当日的访问记录
	if(empty($accessInfo))
	{
		$openid = '';
		
		// 账户验证
		$openid = checkIsLogin();
		
		$data = array(	'ip'    		=> getClientIP(),
						'device_type' 	=> get_mobile_type(),
						'flag_code'  	=> $flag_code,
						'openid'    	=> $openid,
						'createtime'	=> date('Y-m-d'));
	
		mysqld_insert('access_log',$data);
	}
}

/**
 * 获得指定期间内的平均访问量
 *
 * @param $startdate: date 开始时间
 * @param $enddate: date 结束时间
 * 
 * @return float 平均访问量
 */
function getAvgAccessCount($startdate,$enddate)
{
	$d1 = strtotime($startdate);
	$d2 = strtotime($enddate);
	$days = round(($d2-$d1)/3600/24)+1;
	
	$accessCount = mysqld_select("SELECT count(access_id) as cnt FROM " . table('access_log') . " where createtime>='".$startdate."' and createtime<='".$enddate."'");
	
	return $accessCount['cnt']/$days;
}
