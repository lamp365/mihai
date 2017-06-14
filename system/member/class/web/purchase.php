<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/11/16
 * Time: 18:32
 */
$op = empty($_GP['op']) ? 'list' : $_GP['op'];

if($op == 'list'){
    //获取业务员
    $users  = getAllAgent();
    $pindex = max(1, intval($_GP['page']));
    $psize = 30;

    $condition = "parent_roler_id<>0";
    if(!empty($_GP['uid'])){
        $condition .= " and relation_uid={$_GP['uid']}";
    }
    if(!empty($_GP['keyword'])){
        if(is_numeric($_GP['keyword'])) {
            $condition .= " and mobile={$_GP['keyword']}";
        } else {
            $condition .= " and realname like '%{$_GP['keyword']}%'";
        }
    }

    $list = mysqld_selectall('SELECT * FROM '.table('member')." where {$condition} order by openid desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
    $total = $pager = '';
    if(!empty($list)){
        $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('member')." where {$condition}");
        $pager = pagination($total, $pindex, $psize);
    }
    $agent_ration = globaSetting();
    include page('purchase_list');

}else if($op == 'add')  //会员添加
{
    if(empty($_GP['mobile']))
        message('对不起，手机号码不能为空！',refresh(),'error');
    if(!is_numeric($_GP['mobile']))
        message('对不起，手机号码必须是数字',refresh(),'error');
    if(empty($_GP['pwd']))
        message('对不起，密码不能为空！',refresh(),'error');

    $info = mysqld_select("select openid from ".table('member')." where mobile={$_GP['mobile']}");
    if(!empty($info))
        message('对不起，该手机号已经注册过',refresh(),'error');

    if($_GP['parent_roler_id'] != 0 && $_GP['son_roler_id']==0){
        message('对不起，会员身份选择有误！',refresh(),'error');
    }

    do{
        $openid    = date("YmdH",time()).rand(100,999);
        $hasmember = mysqld_select("SELECT * FROM " . table('member') . " WHERE openid = :openid ", array(':openid' => $openid));
    }while($hasmember);


    mysqld_insert('member',array(
        'realname'   => $_GP['realname'],
        'nickname'   => $_GP['realname'],
        'mobile'     => $_GP['mobile'],
        'email'      => $_GP['email'],
        'pwd'        => md5($_GP['pwd']),
        'createtime' => time(),
        'openid'     => $openid,
        'dummy'      => 0,
        'relation_uid'    => empty($_GP['relation_uid']) ? 0 : $_GP['relation_uid'],
        'parent_roler_id' => empty($_GP['parent_roler_id']) ? 0 : $_GP['parent_roler_id'],
        'son_roler_id'    => empty($_GP['son_roler_id']) ? 0 : $_GP['son_roler_id'],
        'platform_name'   => $_GP['platform_name'],
        'platform_pic'    => empty($_GP['picurl'])? '' : implode(',',$_GP['picurl']),
		'QQ'=>$_GP['QQ'],
		'weixin'=>$_GP['weixin'],
		'wanwan'=>$_GP['wanwan'],
        'platform_url'    => empty($_GP['platform_url']) ? '' : $_GP['platform_url']
    ));
    if(mysqld_insertid() && !empty($_GP['parent_roler_id'])){
        //获取聚到商店铺二维码
        get_weixin_erweima($openid);
    }
    message('操作成功！',refresh(),'success');

}else if($op == 'geterweima'){
    if(!empty($_GP['openid'])){
        $picurl = get_weixin_erweima($_GP['openid']);
        if($picurl)
            die(showAjaxMess(200,$picurl));
        else
            die(showAjaxMess(1002,'获取失败，请再试一下'));
    }else{
        //生成二维码
        $condition = "parent_roler_id<>0";
        $list = mysqld_selectall('SELECT * FROM '.table('member')." where {$condition}");
        foreach($list as $one){
            $picurl = get_weixin_erweima($one['openid']);
            echo "{$openid}二维码已生成：{$picurl}<br/>";
        }
    }

}else if($op == 'set_ratio')   //设置业务员收益比例
{
    $agent_ration = $_GP['agent_ration'];
    if($agent_ration == null){
        message("对不起请设置收益比例",refresh(),'error');
    }
    refreshSetting(array('agent_ration'=>$agent_ration));
    message("设置成功！",refresh(),'success');
}