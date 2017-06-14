<?php
  if ( !empty($_GP['type']) ){
      //开始兑换，进行填写物流单号
       $ship = array(
           'shipping' => $_GP['shipping'],
		   'state'    => 4,  //已经兑换
	       'shiptype' =>$_GP['type'],
	       'shipstr' =>$_GP['shipstr']
	   );
      if($_GP['type'] != 'xian_chan' && empty($_GP['shipping'])){
          message("物流单号不能为空！",refresh(),'error');
      }
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


$op = empty($_GP['op']) ? 'xinyuan' : $_GP['op'];

if($op == 'xinyuan'){
    $request_type = empty($_GP['request_type']) ? 1 : $_GP['request_type'];
    $condition = " award_id = {$_GP['id']} and request_type={$request_type}";

    //中奖记录
    $win           = mysqld_select("SELECT * FROM ".table('addon7_award')." where id = {$_GP['id']}");
    if($win['state']>2 && $request_type==1){
        // 中奖者
        $winer          = mysqld_select("SELECT * FROM ".table('addon7_request')." WHERE status = 1 and award_id = ".$_GP['id']);
        $win['name']    = $winer['realname'];
        $win['mobile']  = $winer['mobile'];
        $win['address'] = $winer['address'];
    }

    $pindex = max(1, intval($_GP['page']));
    $psize = 50;
    $limit = ' limit '.($pindex-1)*$psize.','.$psize;
    // 购买记录
    $awardlist = mysqld_selectall("select * FROM " . table('addon7_request')." where  {$condition} order by status desc,id desc {$limit} ");
    $total = $pager = '';
    if(!empty($awardlist)){
        foreach($awardlist as &$draw){
            //获取用户名字和微信信息
            if($draw['realname'] == '' || $draw['mobile'] ==''){
                $member          = mysqld_select("select realname,nickname,mobile from ".table('member')." where openid='{$draw['openid']}'");
                $draw['pc_name'] = getNameByMemberInfo($member);
                $draw['mobile']  = $member['mobile'];
            }else{
                $draw['pc_name'] = $draw['realname'];
            }
            $weixin           = mysqld_select("select nickname from ".table('weixin_wxfans')." where openid='{$draw['openid']}'");
            $draw['wx_name']  = empty($weixin)? '' : $weixin['nickname'];
        }
        $total     = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('addon7_request') . " WHERE  {$condition}");
        $pager      = pagination($total, $pindex, $psize);
    }


    //获取物流信息
    if($request_type==1){
        $dispatchlist = mysqld_selectall("SELECT * FROM " . table('dispatch')." where sendtype=0 order by sort desc" );
    }

    include addons_page('applyed');

}else if($op == 'change'){
    //兑换记录
    $win  = mysqld_select("SELECT * FROM ".table('addon7_award')." where id = {$_GP['id']}");
    $condition = 'where award_id='.$_GP['id'];
    if(!empty($_GP['status'])){
        $condition .= " and status={$_GP['status']}";
    }
    $pindex = max(1, intval($_GP['page']));
    $psize = 50;
    $limit = ' limit '.($pindex-1)*$psize.','.$psize;
    // 购买记录
    $awardlist = mysqld_selectall("select * FROM " . table('addon7_change')."  {$condition} order by id desc {$limit} ");
    $total = $pager = '';
    if(!empty($awardlist)){
        foreach($awardlist as &$draw){
            //获取收货地址
            if(!empty($draw['address_id'])){
                $member           = mysqld_select("select realname,mobile,province,city,area,address from ".table('shop_address')." where id={$draw['address_id']} and openid='{$draw['openid']}'");
                $draw['address']  = $member['province'].$member['city'].$member['area'].$member['address'];
                $draw['realname'] = $member['realname'];
                $draw['mobile']   = $member['mobile'];

                $member           = member_get($draw['openid'],'mobile,credit');
                $draw['m_mobile'] = $member['mobile'];
                $draw['credit']   = $member['credit'] < 0 ? '异常' : '';
            }else{
                $member           = member_get($draw['openid'],'realname,mobile,credit');
                $draw['address']  = '';
                $draw['realname'] = $member['realname'];
                $draw['mobile']   = $member['mobile'];
                $draw['m_mobile'] = $member['mobile'];
                $draw['credit']   = $member['credit'] < 0 ? '异常' : '';
            }
            $weixin           = mysqld_select("select nickname from ".table('weixin_wxfans')." where openid='{$draw['openid']}'");
            $draw['wx_name']  = empty($weixin)? '' : $weixin['nickname'];
        }
        $total     = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('addon7_change') . " {$condition}");
        $pager      = pagination($total, $pindex, $psize);
    }

    include addons_page('applyed');

}else if($op == 'checked'){
    if(empty($_GP['id']) || empty($_GP['status'])){
        message("参数有误！",refresh(),'error');
    }
    if(is_array($_GP['id'])){
        foreach($_GP['id'] as $id){
            if($_GP['status'] == 3) {
                //审核失败了
                $request = mysqld_select("select award_id,openid from ".table('addon7_change')." where id={$id}");
                $award   = mysqld_select('select jifen_change from '.table('addon7_award')." where id = {$request['award_id']}");
                member_credit($request['openid'],$award['jifen_change'],'addcredit',PayLogEnum::getLogTip('LOG_JIFEN_CHANGE_FAIL_TIP'));
            }
            mysqld_update("addon7_change",array('status'=>$_GP['status']),array('id'=>$id));
        }
        die(showAjaxMess(200,'批量审核成功！'));
    }else{
        if($_GP['status'] == 3){
            //审核失败了
            $request = mysqld_select("select award_id,openid from ".table('addon7_change')." where id={$_GP['id']}");
            $award   = mysqld_select('select jifen_change from '.table('addon7_award')." where id = {$request['award_id']}");
            member_credit($request['openid'],$award['jifen_change'],'addcredit',PayLogEnum::getLogTip('LOG_JIFEN_CHANGE_FAIL_TIP'));
        }
        mysqld_update("addon7_change",array('status'=>$_GP['status']),array('id'=>$_GP['id']));
        message("操作成功！",refresh(),'success');
    }

}
