<?php

$data = array(
    'code' => 'weixin',
    'name' => 'weixin',
    'enabled' => '1'
);
$item = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE code = :code", array(':code' => 'weixin'));

if (empty($item['id'])) {
    mysqld_insert('thirdlogin', $data);
} else {
    mysqld_update('thirdlogin',$data , array('code' => 'weixin'));
}
$seting = globaSetting();
$thirdlogin_submit_data=array(
    'thirdlogin_weixin_appid'  => $seting['weixin_appId'],
    'thirdlogin_weixin_appkey' => $seting['weixin_appSecret']
);
mysqld_update('thirdlogin',array('configs'=> serialize($thirdlogin_submit_data)) , array('code' => 'weixin'));
?>