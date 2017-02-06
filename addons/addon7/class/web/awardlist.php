<?php
$pindex = max(1, intval($_GP['page']));
$psize = 24;
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
 if (!empty($_GP['keyword'])) {
      $condition .= " AND title LIKE '%{$_GP['keyword']}%'";
}    
$state = array(
   '-1'=>'全部',
   '0'=>'进行中',
   '1'=>'待锁定',
   '2'=>'可开奖',
   '3'=>'已开奖',
   '4'=>'已兑奖'
);
if ($_GP['state'] != '-1' && isset($_GP['state'])){
    $condition .= " AND state = '{$_GP['state']}'";
}
if (!empty($_GP['cate_2'])) {
	$cid = intval($_GP['cate_2']);
	$condition .= " AND ccate = '{$cid}'";
} elseif (!empty($_GP['cate_1'])) {
	$cid = intval($_GP['cate_1']);
	$condition .= " AND pcate = '{$cid}'";
}
$awardlist = mysqld_selectall("SELECT * FROM " . table('addon7_award') . " WHERE  deleted=0 $condition ORDER BY createtime DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
foreach($awardlist as $key=>$val){
	   if ( empty($val['logo'])){
			$lists = mysqld_select("SELECT thumb FROM " . table('shop_goods') . " WHERE  id = ".$val['gid']);
			$awardlist[$key]['imgs'] = $lists['thumb'];
	   }else{
			$awardlist[$key]['imgs'] = $val['logo'];
	   }

	   if ( empty($val['title'])){
			$lists = mysqld_select("SELECT title FROM " . table('shop_goods') . " WHERE  id = ".$val['gid']);
			$awardlist[$key]['title'] = $lists['title'];
	   }
}
 $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('addon7_award') . " WHERE deleted=0 $condition");
 $pager = pagination($total, $pindex, $psize);
 include addons_page('awardlist');