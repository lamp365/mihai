<?php
/**
 * 统计相关公共函数
 */


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
