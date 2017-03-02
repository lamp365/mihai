<?php
defined('SYSTEM_IN') or exit('Access Denied');
/**
 * @param $experience  目前该参数用的是会员中的积分 注意使用  不要用会员中的经验值来获取等级
 * @return array|bool|mixed
 */
function member_rank_model($experience)
{
		$rank = mysqld_select("SELECT * FROM " . table('rank_model')." where experience<='".intval($experience)."' order by rank_level desc limit 1 " );
		if(empty($rank))
		{
			// 扩展下一级需要
			$rank['rank_level']  = 1;
			$rank['rank_name'] = '普通会员';
			$rank['experience']  = 0;
		}
		$rank = member_rank_next($rank);
		return $rank;		  
}
function member_rank_next($rank=array()){
      if ( empty( $rank ) or empty($rank['rank_level']) ){
           $rank['rank_level'] = 1;
	  }
	  $rank['rank_level']  = intval($rank['rank_level']);
      $rank['rank_next']  = mysqld_select('SELECT * FROM '.table('rank_model')." where rank_level = ".($rank['rank_level'] + 1));
	  return $rank;
}

