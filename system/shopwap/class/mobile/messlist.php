<?php
/*
if (isset($_COOKIE['mess'])) {
    header("location:" . create_url('mobile', array(
        'name' => 'shopwap',
        'do' => 'index'
    )));
}
*/

$messid = intval($_GP['id']);
if(isset($messid) && $messid>0)
{   
    $mess =mysqld_select("SELECT * FROM " . table('shop_mess') . " WHERE id = :id", array(':id' => $messid));
    if(isset($mess))
    {
        $shitan =serialize(array('mess_name'=> $mess['title'], 'mess_id'=>$mess['id']));
       // $_COOKIE['mess'] = array('mess_name'=> $mess['title'], 'mess_id'=>$mess['id']);
        setcookie("mess",$shitan,time()+3600*24*365*10);
         //setcookie("mess['mess_id']",$mess['id']);
        
        if(is_login_account())
        {
            $member=get_member_account();
            mysqld_update('member', array('mess_id' => $messid), array('openid' => $member['openid']));                    
        }        
        
         header("location:" . create_url('mobile', array(
        'name' => 'shopwap',
        'do' => 'shopindex'
        )));
    }
}

$category = mysqld_selectall("SELECT * FROM " . table('mess_list') . " WHERE deleted=0 and enabled=1 ORDER BY parentid ASC, displayorder DESC");
foreach ($category as $index => $row) {
    if (!empty($row['parentid'])) {
        $children[$row['parentid']][$row['id']] = $row;
        unset($category[$index]);
    }
}

$where ="";

$cid = intval($_GP['ccate']);
$pid = intval($_GP['pcate']);

if(!isset($cid) || $cid==0) { 
	$cid=2;
}
if(!isset($pid) || $pid==0) $pid=1;

$where =" WHERE ccate=".$cid." and pcate=".$pid;

$messlist =mysqld_selectall("select * FROM".table('shop_mess') . $where);


	
include themePage('messlist');
