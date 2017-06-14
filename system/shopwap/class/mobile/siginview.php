<?php

if(!empty($_GP['section']))
{
  $member = get_member_account(false);
$member = member_get($member['openid']);
if (empty($member['openid'])) {
    $member = get_member_account(false);
    $member['createtime'] = time();
}
$result='';
$messid = $member['mess_id'];
$codition =" where section=:section and DATEDIFF(createtime,NOW())=0 and mess_id=:mess_id";
$list = mysqld_selectall("select * from ".table('sign_list').$codition ,array(":mess_id"=>$messid,":section"=>$_GP['section']));

 foreach ( $list as $key=>$value ) {
            $nickname = mysqld_select("select * from ".table('member')." where openid=:openid",array(":openid"=>$member['openid']));
            if(is_array($nickname))
            {
                $list[$key]['realname'] =$nickname['realname'];
                $list[$key]['mobile'] =$nickname['mobile'];
            }
        }

}

include themePage('siginview');