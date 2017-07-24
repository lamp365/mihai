<?php
$op = $_GP['op'];
if ($op == 'store_search' && checkIsAjax()){
    $reData = array();
    $sts_name = $_GP['sts_name'];
    $sql = "select sts_id as id,sts_name from ".table('store_shop')." where sts_name like '%{$sts_name}%' ";
    $reData['store'] = mysqld_selectall($sql);
    echo json_encode($reData);
    exit;
}else{
    $pindex = max(1, intval($_GP['page']));
    $psize = 30;
    $condition='';
    $conditiondata=array();
    $mess_list = array();
    /* $_mess    =  mysqld_selectall("SELECT * FROM " . table('shop_mess'));
     if (!empty($_GP['mess'])){
     $condition .= " AND mess_id = ".$_GP['mess'];
    }*/
    if(!empty($_GP['timestart']) && !empty($_GP['timeend'])){
        $timestart = strtotime($_GP['timestart']);
        $timeend   = strtotime($_GP['timeend']);
        $condition .= " AND a.createtime >= {$timestart} And a.createtime<= {$timeend}";
    }
    if(!empty($_GP['realname']))
    {
         
        $condition=$condition.' and a.realname like :realname ';
        $conditiondata[':realname']='%'.trim($_GP['realname']).'%';
    }
    if(!empty($_GP['mobile']))
    {
        $condition=$condition.' and a.mobile like :mobile ';
        $conditiondata[':mobile']= '%'.trim($_GP['mobile']).'%';
    }
    
    $vc = isset($_GP['status'])? $_GP['status']: 1;
    switch ( $vc ){
        case 1:
            $condition=$condition.' and a.parent_roler_id = 0 and a.son_roler_id = 0 ';
            break;
        case 2:
            $condition=$condition.' and a.parent_roler_id > 0 and a.son_roler_id > 0 ';
            break;
        default :
            break;
    }
    
    if(!empty($_GP['weixinname']))
    {
         
        $condition=$condition.' and a.openid in (select wxfans.openid from ' . table('weixin_wxfans').' wxfans where wxfans.nickname like :weixinname)';
        $conditiondata[':weixinname']='%'.$_GP['weixinname'].'%';
    }
    if(!empty($_GP['alipayname']))
    {
         
        $condition=$condition.' and a.openid in (select alifans.openid from ' . table('alipay_alifans').' alifans where alifans.nickname like :alipayname)';
        $conditiondata[':alipayname']='%'.$_GP['alipayname'].'%';
    }
    $status=1;
    if(empty($_GP['showstatus'])||$_GP['showstatus']==1)
    {
         
        $status=1;
    }
     
    if($_GP['showstatus']==-1)
    {
         
        $status=0;
    }
    if(!empty($_GP['rank_level']))
    {
        $rank_model = mysqld_select("SELECT * FROM " . table('rank_model')."where rank_level=".intval($_GP['rank_level']) );
        if(!empty($rank_model['rank_level']))
        {
            $condition=$condition." and a.experience>=".$rank_model['experience'];
            $rank_model2 = mysqld_select("SELECT * FROM " . table('rank_model')."where rank_level>".$rank_model['rank_level'].' order  by rank_level limit 1' );
            if(!empty($rank_model2['rank_level']))
            {
                if(intval($rank_model['experience'])<intval($rank_model2['experience']))
                {
                    $condition=$condition." and a.experience<".$rank_model2['experience'];
                }
            }
        }
    }
    $sts_id = $_GP['sts_id'];
    if (!empty($sts_id))
    {
        $condition=$condition." and b.p_sid = {$sts_id} ";
        $storeInfo = mysqld_select("select sts_name,sts_id as id from ".table('store_shop')." where sts_id=:sts_id",array('sts_id'=>$sts_id));
    }
    $rank_model_list = mysqld_selectall("SELECT * FROM " . table('rank_model')." order by rank_level" );
    $sql = 'SELECT a.*,b.p_sid FROM '.table('member')." as a left join ".table('member_blong_relation')." as b on a.openid=b.m_openid where  a.dummy=0 and a.`istemplate`=0  and a.`status`={$status} {$condition} "." LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
    $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('member')." as a left join ".table('member_blong_relation')." as b on a.openid=b.m_openid where  dummy=0 and `istemplate`=0 {$condition} ",$conditiondata);
    $list = mysqld_selectall($sql,$conditiondata);
    
    $pager = pagination($total, $pindex, $psize);
    
    foreach($list as  $index=>$item){
        $list[$index]['weixin']= mysqld_selectall("SELECT * FROM " . table('weixin_wxfans') . " WHERE openid = :openid", array(':openid' => $item['openid']));
        $list[$index]['alipay'] = mysqld_selectall("SELECT * FROM " . table('alipay_alifans') . " WHERE openid = :openid", array(':openid' => $item['openid']));
        $list[$index]['mess_name'] = mysqld_selectcolumn("SELECT title FROM " . table('shop_mess') . " WHERE id = :id", array(':id' => $item['mess_id']));
        $sql = "SELECT sts_name FROM ".table('store_shop')."   where sts_id = {$item['p_sid']}";
        $list[$index]['sts_name'] = '';
        $store = mysqld_select($sql);
        if ($store && is_array($store)) $list[$index]['sts_name'] = $store['sts_name'];
        	
    }
    
    
    //获取角色
    $rolers   = mysqld_select("select id,name,createtime from ".table('rolers')." where type=1 and isdelete=0");
    
    //获取业务员
    $user_rolers  = '';
    if(!empty($rolers)){
        $sql = "select r.id,r.rolers_id,r.uid,u.username from ".table('rolers_relation')." as r ";
        $sql .= " left join ".table('user')." as u on u.id=r.uid where r.rolers_id={$rolers['id']}";
        $user_rolers = mysqld_selectall($sql);
    }
    
    //获取会员身份 如渠道商    这些信息 用在添加会员时的 下拉选择
    $purchase = mysqld_selectall("select id,pid,name,createtime from ".table('rolers')." where type<>1 order by pid asc");
    if (! empty($purchase)) {
        $childrens = '';
        foreach ($purchase as $key => $item) {
            if (! empty($item['pid'])) {
                $childrens[$item['pid']][$item['id']] = $item;
                unset($purchase[$key]);
            }
        }
    }
    
    include page('list');
}