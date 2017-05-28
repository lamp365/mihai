<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/12/5
 * Time: 16:47
 */
$op = empty($_GP['op']) ? 'display':$_GP['op'];
if($op == 'display'){
    $app_anzhuo = mysqld_select("select url from ".table('app_version')." where app_type='0' order by version_id desc");
    $app_ios    = mysqld_select("select url from ".table('app_version')." where app_type='1' order by version_id desc");
    include themePage('appdown');
}else if($op == 'get_appversion'){
    $app_anzhuo     = mysqld_select("select url from ".table('app_version')." where app_type='0' order by version_id desc");
    $app_ios        = mysqld_select("select url from ".table('app_version')." where app_type='1' order by version_id desc");
    $apply_anzuo    = mysqld_select("select url from ".table('app_version')." where app_type='2' order by version_id desc");
    $apply_ios      = mysqld_select("select url from ".table('app_version')." where app_type='3' order by version_id desc");
    $data = array(
        'iPhoneUrl'         => empty($app_ios['url']) ? WEBSITE_ROOT : $app_ios['url'],
        'AndroidUrl'        => empty($app_anzhuo['url']) ? WEBSITE_ROOT : $app_anzhuo['url'],
        'Apply_iPhoneUrl'   => empty($apply_ios['url']) ? WEBSITE_ROOT : $apply_ios['url'],
        'Apply_AndroidUrl'  => empty($apply_anzuo['url']) ? WEBSITE_ROOT : $apply_anzuo['url']
    );
    die(showAjaxMess(200,$data));
}