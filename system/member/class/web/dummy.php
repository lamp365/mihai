<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/11/16
 * Time: 18:32
 */
$op = empty($_GP['op']) ? 'list' : $_GP['op'];

if($op == 'list'){
    $pindex = max(1, intval($_GP['page']));
    $psize = 30;

    $list = mysqld_selectall('SELECT * FROM '.table('member')." where dummy=1 LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
    $total = $pager = '';
    if(!empty($list)){
        $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('member')." where dummy=1");
        $pager = pagination($total, $pindex, $psize);
    }
    include page('dummy_list');
}else if($op == 'add')
{
    //虚拟用户密码都是hinrc_123456
    if(empty($_GP['realname']) || empty($_GP['mobile']))
        message('用户名以及手机号不能为空！',refresh(),'error');

    if(!is_numeric($_GP['mobile']))
        message('对不起，手机号必须是数字',refresh(),'error');

    $avatar = '';
    if ($_FILES['avatar']['error'] != 4) {
        $upload = file_upload($_FILES['avatar']);
        if (is_error($upload)) {
            message($upload['message'], '', 'error');
        }
        $avatar = $upload['path'];
    }

    $openid    = date("YmdH",time()).rand(100,999);
    $hasmember = mysqld_select("SELECT * FROM " . table('member') . " WHERE openid = :openid ", array(':openid' => $openid));
    if(!empty($hasmember['openid']))
    {
        $openid=date("YmdH",time()).rand(100,999);
    }

    mysqld_insert('member',array(
       'realname' => $_GP['realname'],
       'mobile'   => $_GP['mobile'],
       'email'    => $_GP['email'],
       'pwd'      => md5('hinrc_123456'),
       'avatar'   => $avatar,
       'createtime' => time(),
       'openid'   => $openid,
       'dummy'   => 1
    ));
    message('添加成功！',refresh(),'success');

}else if($op == 'edit')
{
    if(empty($_GP['realname']) || empty($_GP['mobile']))
        message('用户名以及手机号不能为空！',refresh(),'error');

    if(!is_numeric($_GP['mobile']))
        message('对不起，手机号必须是数字',refresh(),'error');

    $avatar = $_GP['hide_avatar'];
    if ($_FILES['avatar']['error'] != 4) {
        $upload = file_upload($_FILES['avatar']);
        if (is_error($upload)) {
            message($upload['message'], '', 'error');
        }
        $avatar = $upload['path'];
    }

    $data = array(
        'realname' => $_GP['realname'],
        'mobile'   => $_GP['mobile'],
        'avatar'   => $avatar,
        'email'   => $_GP['email'],
    );
    mysqld_update('member',$data,array('openid'=>$_GP['openid']));
    message('修改成功！',refresh(),'success');
}else if($op == 'delete')
{
    if(is_array($_GP['openid'])){
        foreach($_GP['openid'] as $openid){
           mysqld_delete('member',array('openid'=>$openid));
        }
    }else{
        mysqld_delete('member',array('openid'=>$_GP['openid']));
    }
    message('删除成功！',refresh(),'success');
}else if($op == 'addbat'){
    for($i=1;$i<=100;$i++){
        $mobile = get_rand_mobile();
        $username = get_rand_username();

         do{
             $openid    = date("YmdH",time()).rand(100,999);
             $hasmember = mysqld_select("SELECT * FROM " . table('member') . " WHERE openid = :openid ", array(':openid' => $openid));
         }while($hasmember);

        mysqld_insert('member',array(
            'realname'   => $username,
            'mobile'     =>$mobile,
            'email'      => '',
            'pwd'        => md5('hinrc_123456'),
            'createtime' => time(),
            'openid'     => $openid,
            'dummy'      => 1
        ));

    }
    message('添加成功！',refresh(),'success');
}