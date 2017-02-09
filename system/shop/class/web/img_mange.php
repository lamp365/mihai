<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/2/9
 * Time: 17:35
 */

$op = empty($_GP['op']) ? 'display' : $_GP['op'];
if($op == 'display'){
    //下一页标记
    $nextMarker = empty($_GP['nextMarker'])? '' : $_GP['nextMarker'];
    //前缀模糊查询
    $prefix     = empty($_GP['prefix'])? '' : $_GP['prefix'];
    //分割符号 得到目录
    $delimiter  = empty($_GP['delimiter'])? '' : $_GP['delimiter'];

    $has_dir   = false;
    if(!empty($nextMarker) || !empty($prefix) || !empty($delimiter)){
        $pic_list_arr = aliyunOSS::listObjects($prefix,$delimiter,$nextMarker);
        $pic_list     = $pic_list_arr['data'];
        $nextMarker   = $pic_list_arr['nextMarker'];
    }else{
        $dir_list_arr = aliyunOSS::listObjects('','/');
        if(empty($dir_list)){
            $pic_list_arr = aliyunOSS::listObjects();
            $pic_list     = $pic_list_arr['data'];
            $nextMarker   = $pic_list_arr['nextMarker'];
        }else{
            $has_dir    = true;
            $dir_list   = $dir_list_arr['data'];
            $nextMarker = $dir_list_arr['nextMarker'];
        }
    }

}