<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/5/6
 * Time: 16:44
 */
function analyzeSpecprice_keyname($key_name,&$item_arr){
    $res_arr = array();
    //内存|^|8G@@硬盘|^|32G
    $key_name_arr = explode('@@',$key_name);
    foreach($key_name_arr as $row){
        //内存|^|8G
        $one_key = explode('|^|',$row);
        $res_arr[$one_key[0]] = $one_key[1];
        $item_arr[$one_key[0]][] = $one_key[1];
    }
    return $res_arr;
}