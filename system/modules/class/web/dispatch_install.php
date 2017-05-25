<?php

if ($_GP['op'] == 'all'){
    $codeStr=$_GP['code'];
    if (isset($codeStr) && !empty($codeStr)){
        $codeArr = explode(",", trim($codeStr,","));
    }
    if (!empty($codeArr) && is_array($codeArr)){
        foreach ($codeArr as $key=>$code){
            require WEB_ROOT.'/system/modules/plugin/dispatch/'.$code.'/lang.php';
            
            if ( isset( $_LANG['dispatch_'.$code.'_dispatch_web'] ) && !empty($_LANG['dispatch_'.$code.'_dispatch_web']) ){
                $dispatch_web = $_LANG['dispatch_'.$code.'_dispatch_web'];
            }else{
                $dispatch_web = '';
            }
            $item = mysqld_select("SELECT * FROM " . table('dispatch') . " WHERE code = :code", array(':code' => $code));
            if (empty($item['id'])) {
                $data = array(
                    'code' => $code,
                    'name' => $_LANG['dispatch_'.$code.'_name'],
                    'desc' => $_LANG['dispatch_'.$code.'_desc'],
                    'dispatch_web' => $dispatch_web,
                    'enabled' => '1',
                    'sendtype' => $_LANG['dispatch_'.$code.'_sendtype']
                );
                mysqld_insert('dispatch', $data);
            } else {
                $data = array(
                    'name' => $_LANG['dispatch_'.$code.'_name'],
                    'desc' => $_LANG['dispatch_'.$code.'_desc'],
                    'dispatch_web' => $dispatch_web,
                    'enabled' => '1',
                    'sendtype' => $_LANG['dispatch_'.$code.'_sendtype']
                );
                mysqld_update('dispatch',$data , array('code' => $code));
            }
        }
    }
    message("操作成功",create_url('site', array('name' => 'modules','do' => 'dispatch','op'=>'display')));
}else{
    $code=$_GP['code'];
    require WEB_ROOT.'/system/modules/plugin/dispatch/'.$code.'/lang.php';
    
    if ( isset( $_LANG['dispatch_'.$code.'_dispatch_web'] ) && !empty($_LANG['dispatch_'.$code.'_dispatch_web']) ){
        $dispatch_web = $_LANG['dispatch_'.$code.'_dispatch_web'];
    }else{
        $dispatch_web = '';
    }
    $item = mysqld_select("SELECT * FROM " . table('dispatch') . " WHERE code = :code", array(':code' => $code));
    if (empty($item['id'])) {
        $data = array(
            'code' => $code,
            'name' => $_LANG['dispatch_'.$code.'_name'],
            'desc' => $_LANG['dispatch_'.$code.'_desc'],
            'dispatch_web' => $dispatch_web,
            'enabled' => '1',
            'sendtype' => $_LANG['dispatch_'.$code.'_sendtype']
        );
        mysqld_insert('dispatch', $data);
    } else {
        $data = array(
            'name' => $_LANG['dispatch_'.$code.'_name'],
            'desc' => $_LANG['dispatch_'.$code.'_desc'],
            'dispatch_web' => $dispatch_web,
            'enabled' => '1',
            'sendtype' => $_LANG['dispatch_'.$code.'_sendtype']
        );
        mysqld_update('dispatch',$data , array('code' => $code));
    }
    message("操作成功",create_url('site', array('name' => 'modules','do' => 'dispatch','op'=>'display')));
}