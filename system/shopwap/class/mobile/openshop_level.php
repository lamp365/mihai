<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/11/16
 * Time: 15:49
 */
$shopInfo = '';
if(!empty($_GP['shopid'])){
    $shopInfo = mysqld_select("select * from ". table('openshop') . " where id=:id",array(
        'id' => $_GP['shopid']
    ));
}
include themePage('openshop_level');