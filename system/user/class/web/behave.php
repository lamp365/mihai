<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/12/23
 * Time: 16:31
 */
defined('SYSTEM_IN') or exit('Access Denied');

$op = $_GP['op'];
if($op == 'list'){
    //删除20天之前的数据
    $del_time = time()-3600*24*20;
    mysqld_query("delete from ".table('admin_behave_log')." where createtime<{$del_time}");

    $condition = ' where 1=1';
    if(!empty($_GP['uid'])){
        $condition .= " and uid={$_GP['uid']}";
    }
    if(!empty($_GP['timestart']) && !empty($_GP['timeend'])){
        $timestart = strtotime($_GP['timestart']);
        $timeend   = strtotime($_GP['timeend']);
        $condition .= " and createtime>{$timestart} and createtime<{$timeend}";
    }
    $psize  =  30;
    $pindex = max(1, intval($_GP["page"]));
    $limit  = ' limit '.($pindex-1)*$psize.','.$psize;
    $all_log = mysqld_selectall("select * from ".table('admin_behave_log')." {$condition} order by id desc ". $limit);
    $total   = mysqld_selectcolumn("select count('id') from ".table('admin_behave_log')." {$condition}");
    $pager   = pagination($total, $pindex, $psize);

    //获取所有管理员
    $all_admin = mysqld_selectall("select id,username from ".table('user'));
    include page('behavelist');
}