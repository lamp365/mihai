<?php

$member = get_member_account(false);
$member = member_get($member['openid']);
if (empty($member['openid'])) {
    $member = get_member_account(false);
    $member['createtime'] = time();
}

$result='';
$messid = $member['mess_id'];



$codition =" where section=:section and DATEDIFF(createtime,NOW())=0 and mess_id=:mess_id";

$morning = mysqld_selectall("select * from ".table('sign_list').$codition ,array(":mess_id"=>$messid,":section"=>1));

$midday = mysqld_selectall("select * from ".table('sign_list'). $codition,array(":mess_id"=>$messid,":section"=>2));

$night = mysqld_selectall("select * from ".table('sign_list'). $codition,array(":mess_id"=>$messid,":section"=>3));

$zao = 0;
if(is_array($morning))
{
   $zao = count($morning);
}
$zhongwu = 0;
if(is_array($midday))
{
   $zhongwu = count($midday);
}
$wang= 0;

if(is_array($night))
{
    $wang = count($night);
}

include themePage('signinlist');