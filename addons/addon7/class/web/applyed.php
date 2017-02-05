<?php
  if ( !empty($_GP['type']) ){
      //开始兑换，进行填写物流单号
       $ship = array(
           'shipping' => $_GP['shipping'],
		   'state'    => 4,  //已经兑换
	       'shiptype' =>$_GP['type'],
	       'shipstr' =>$_GP['shipstr']
	   );
       $res = mysqld_update('addon7_award', $ship, array('id'=>$_GP['id']));
       if($res && !empty($_GP['draw_id'])){
           $draw_data = array(
                'realname' => $_GP['draw_name'],
                'mobile'   => $_GP['draw_mobile'],
                'address'  => $_GP['draw_address']
           );
           mysqld_update('addon7_request', $draw_data, array('id'=>$_GP['draw_id']));
           message("兑换成功",refresh(),'success');
       }
  }
  //中奖记录
  $win           = mysqld_select("SELECT * FROM ".table('addon7_award')." where id = {$_GP['id']}");
 $win['name']    = '';
 $win['mobile']  = '';
 $win['address'] = '';
  if($win['state']>2){
      // 中奖者
      $winer          = mysqld_select("SELECT * FROM ".table('addon7_request')." WHERE status = 1 and award_id = ".$_GP['id']);

      $win['name']    = $winer['realname'];
      $win['mobile']  = $winer['mobile'];
      $win['address'] = $winer['address'];
  }

  $pindex = max(1, intval($_GP['page']));
  $psize = 50;
  // 购买记录
  $awardlist = mysqld_selectall("select * FROM " . table('addon7_request')." where award_id=".$_GP['id']." order by id asc limit ".($pindex-1).','.$psize);
  $total = $pager = '';
  if(!empty($awardlist)){
     foreach($awardlist as &$draw){
         //获取用户名字和微信信息
         $member = mysqld_select("select realname,mobile from ".table('member')." where openid='{$draw['openid']}'");
         $weixin = mysqld_select("select nickname from ".table('weixin_wxfans')." where openid='{$draw['openid']}'");
         $draw['pc_name'] = $member['realname'];
         $draw['mobile']  = $member['mobile'];
         $draw['wx_name']  = empty($weixin)? '' : $weixin['nickname'];
     }
      $total     = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('addon7_request') . " WHERE  award_id=".$_GP['id']);
      $pager      = pagination($total, $pindex, $psize);
  }


 //获取物流信息
$dispatchlist = mysqld_selectall("SELECT * FROM " . table('dispatch')." where sendtype=0 order by sort desc" );

  /*foreach ( $awardlist as &$c ){
      //没有购买数量 暂时注释
	  if ($c['count'] > 1){
				$c['sn'] = ($c['star_num'] + '1000000') . '-' .($c['star_num']+$c['count']-1+ '1000000');
		}else{
			   $c['sn'] = $c['star_num'] + '1000000' ;
	  }
  }*/
 include addons_page('applyed');