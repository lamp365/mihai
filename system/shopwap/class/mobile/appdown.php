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
}