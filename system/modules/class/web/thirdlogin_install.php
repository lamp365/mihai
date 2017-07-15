<?php

$code=$_GP['code'];
require WEB_ROOT.'/system/modules/plugin/thirdlogin/'.$code.'/lang.php';


$item = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE code = :code", array(':code' => $code));

if (empty($item['id'])) {
    $data = array(
        'code' => $code,
        'name' => $_LANG['thirdlogin_'.$code.'_name'],
        'enabled' => '0'
    );
    mysqld_insert('thirdlogin', $data);
} else {
    $data = array(
        'name' => $_LANG['thirdlogin_'.$code.'_name'],
    );
    mysqld_update('thirdlogin',$data , array('code' => $code));
}


$settings = globaSetting();

if (checksubmit('submit')) {
    require WEB_ROOT.'/system/modules/plugin/thirdlogin/'.$code.'/submit.php';
    message('保存成功！',create_url('site', array('name' => 'modules','do' => 'thirdlogin')),'success');
}
$item = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE code = :code", array(':code' => $code));
$configs = unserialize($item['configs']);
include WEB_ROOT.'/system/modules/plugin/thirdlogin/'.$code.'/config.php';


