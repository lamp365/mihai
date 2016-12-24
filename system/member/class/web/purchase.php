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

    $list = mysqld_selectall('SELECT * FROM '.table('member')." where {$condition} LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
    $total = $pager = '';
    if(!empty($list)){
        $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('member')." where {$condition}");
        $pager = pagination($total, $pindex, $psize);
    }
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

    $url  = empty($_GP['platform_url']) ? '' : 'http://'.trim($_GP['platform_url'],'http://');  //不管加没加http. 可先去掉，再加，确保一定有http

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
		'QQ'=>$_GP['QQ'],
		'weixin'=>$_GP['weixin'],
		'wanwan'=>$_GP['wanwan'],
        'platform_url'    => $url
    ));
    message('操作成功！',refresh(),'success');

}else if($op == 'edit')
{

}else if($op == 'delete')
{

}