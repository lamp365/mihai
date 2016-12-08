<?php
  if ( !empty($_GP['type']) ){
       $ship = array(
           'shipping' => $_GP['shipping'],
		   'state' => 3,
	       'shiptype' =>$_GP['type']
	   );
	   mysqld_update('addon7_award', $ship, array('id'=>$_GP['id']));
  }
  //中奖记录
  $win = mysqld_select("SELECT a.*,b.title, b.thumb FROM ".table('addon7_award')." a left join ".table('shop_goods')." b on a.gid = b.id where a.id = ".$_GP['id']);
  // 中奖者
  $winer = mysqld_select("SELECT * FROM ".table('addon7_request')." WHERE status = 1 and award_id = ".$_GP['id']);
  $win['name'] = $winer['realname'];
  $win['mobile'] = $winer['mobile'];
  $win['address'] = $winer['address'];
  $pindex = max(1, intval($_GP['page']));
  $psize = 20;
  // 购买记录
  $awardlist = mysqld_selectall("select * FROM " . table('addon7_request')." where award_id=".$_GP['id']." limit ".($pindex-1).','.$psize);
  $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('addon7_request') . " WHERE  award_id=".$_GP['id']);
 $pager = pagination($total, $pindex, $psize);
  foreach ( $awardlist as &$c ){
	  if ($c['count'] > 1){
				$c['sn'] = ($c['star_num'] + '1000000') . '-' .($c['star_num']+$c['count']-1+ '1000000');
		}else{
			   $c['sn'] = $c['star_num'] + '1000000' ;
	  }
  }
 include addons_page('applyed');