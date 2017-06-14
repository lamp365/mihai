<?php

$messid=7;
$member = get_member_account(false);
$member = member_get($member['openid']);
if (empty($member['openid'])) {
    $member = get_member_account(false);
    $member['createtime'] = time();
}
$is_login = is_login_account();

if($is_login)
{    
    $messid=$member['mess_id'];   
}
else 
{   
    $openid = $member['openid'];    
    $mess = mysqld_select("SELECT * FROM " . table('weixin_mess') . " WHERE openid = :openid", array(
        ':openid' => $openid)); 
   
	if(isset($mess) && !empty($mess))
	{	   
    	$messid =$mess["mess_id"];   
	}else
	{
	 
		$shitan=unserialize($_COOKIE["mess"]);		
		//$strmess = unserialize($_COOKIE["mess"]);
		$messid = $shitan["mess_id"];		
	}

}


    $sql ="select * from ".table("shop_mess")." where id=".$messid;   
	//echo $sql; 
    $messinfo = mysqld_select($sql);    

include themePage('shitang');