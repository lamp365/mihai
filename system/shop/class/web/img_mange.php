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
        if(empty($dir_list_arr)){
            $pic_list_arr = aliyunOSS::listObjects();
            $pic_list     = $pic_list_arr['data'];
            $nextMarker   = $pic_list_arr['nextMarker'];
        }else{
            $has_dir    = true;
            $dir_list   = $dir_list_arr['data'];
            $nextMarker = $dir_list_arr['nextMarker'];
        }
    }

    include page('img_mange');
}else if($op == 'addDir'){
    if(empty($_GP['dirname'])){
        message("对不起，没有输入目录名",refresh(),'error');
    }
    if(strlen($_GP['dirname']) != mb_strlen($_GP['dirname'])){
        message("对不起，不能带有中文！",refresh(),'error');
    }
    $doesExist = aliyunOSS::doesDirExist($_GP['dirname']);
    if($doesExist){
        message("对不起，该目录已经存在！",refresh(),'error');
    }
    $res = aliyunOSS::createDir($_GP['dirname']);
    message("操作成功",refresh(),'success');
}else if($op == 'del_dir'){
    if(empty($_GP['dirname'])){
        message("对不起，目录名有误！",refresh(),'error');
    }
    //删除刚创建的目录
    $prefix = $_GP['dirname']."/";
    $pic_list_arr = aliyunOSS::listObjects($prefix);
    $pic_list     = $pic_list_arr['data'];

    if(count($pic_list) > 1){
        message("该目录下，不为空不能删除",refresh(),'error');
    }else {
        $file = $pic_list[0];
        if($file == $prefix){
            //说明是空目录
            $res = aliyunOSS::deleteObject($prefix);
        }else{
            //否则的话有一个 readme.txt文件 先删除该文件在删除该目录
            aliyunOSS::deleteObject($file);
            //删除空目录
            $res = aliyunOSS::deleteObject($prefix);
        }
        message("已经删除",refresh(),'success');
    }
}