<?php
$config = mysqld_select("SELECT * FROM " . table('addon7_config') );
$pindex = max(1, intval($_GP['page']));
$psize = 18;
$condition= '';
$awardlist = array();
if ( !empty($_GP['c']) && ($_GP['c'] == 'update') ){
     $awardlist = mysqld_selectall("SELECT * FROM " . table('addon7_award'));
	 foreach ( $awardlist as $value ){
	  $c_p = mysqld_select("SELECT * FROM ".table("shop_goods")." WHERE id = ".$value['gid']);
      $data['p1'] = $c_p['pcate'];
	  $data['p2'] = $c_p['ccate'];
	  $data['p3'] = $c_p['ccate2'];
	   mysqld_update('addon7_award', $data ,array("id"=>intval($value['id'])));	
	 }
	 exit;
}
if ( !empty($_GP['op']) && ($_GP['op'] == 'pay' )){
	$udate = array(
		   'state' => 3
	);
	mysqld_update('addon7_award', $udate, array("id"=>$_GP['id']));
}

$op = empty($_GP['op']) ? 'awardlist' : $_GP['op'];

if($op == 'awardlist'){
	//许愿列表
	$state = array(
		'-1'=>'全部商品',
		'0'=>'进行中',
		'1'=>'待锁定',
		'2'=>'可开奖',
		'3'=>'已开奖',
		'4'=>'已兑奖'
	);

	if (isset($_GP['state']) && $_GP['state'] != '-1'){
		$condition .= " where state = '{$_GP['state']}'";
	}
	if(isset($_GP['isrecommand']) && $_GP['isrecommand']!=-1){
		$condition .= " where isrecommand = '{$_GP['isrecommand']}'";
	}
	if(isset($_GP['deleted']) && $_GP['deleted']!=-1){
			$condition .= " where deleted = '{$_GP['deleted']}'";
	}


	$awardlist = mysqld_selectall("SELECT * FROM " . table('addon7_award') . "  {$condition} ORDER BY endtime DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	$total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('addon7_award') . "  {$condition}");
	$pager = pagination($total, $pindex, $psize);
	include addons_page('awardlist');

}else if($op == 'change'){
	//积分兑换列表
	$condition = "where add_jifen_change = 1";
	$awardlist = mysqld_selectall("SELECT * FROM " . table('addon7_award') . "  {$condition} ORDER BY endtime DESC, sort DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	$total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('addon7_award') . "  {$condition}");
	$pager = pagination($total, $pindex, $psize);
	include addons_page('changelist');

}else if($op == 'set_order'){
	//设置排序
	if(empty($_GP['id']) || empty($_GP['sort'])){
		die(showAjaxMess(1002,'参数有误！'));
	}
	mysqld_update('addon7_award',array('sort'=>$_GP['sort']),array('id'=>$_GP['id']));
	die(showAjaxMess(200,'排序成功！'));
}


/**
 * 检查是否有没有新申请的积分换购用户，有的话 显示小提示，便于管理员知道及时处理
 * @param $award_id
 * @return bool|mixed|string
 */
function checkHasNewJifenChange($award_id){
	//如果有新申请的积分换购的用户
	$sql = "select id from ".table('addon7_change')." where award_id={$award_id} and status=1";
	$res = mysqld_select($sql);
	if(empty($res)){
		return '';
	}else{
		$res = '<img src="images/tag.png" title="有新的积分换购" style="cursor: pointer">';
		return $res;
	}
}

function getBonusSendTime($bonus_id){
	$bonus = mysqld_select("select send_end_date from ".table('bonus_type')." where type_id={$bonus_id}");
	if(!empty($bonus)){
		return date("Y-m-d H:i",$bonus['send_end_date']);
	}else{
		return '';
	}
}