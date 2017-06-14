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
    if($nextMarker){
        $url_arr    = parse_url(WEBSITE_ROOT.$_SERVER['REQUEST_URI']);
        $url_query  = changeParame($url_arr['query'],'nextMarker',$nextMarker);
        $nextMarker = WEBSITE_ROOT."index.php?".$url_query;
    }

    if($prefix){
        $prefix_explode = explode('/',$prefix);
        if(count($prefix_explode) == 1){
            $pre_dir    = '';
            $search_key = $prefix_explode[0];
        }else{
            $pre_dir    = $prefix_explode[0];
            $search_key = $prefix_explode[1];
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
}else if($op == 'getImgSize'){
    //获取图片大小
    if(empty($_GP['type'])){
        $img_url =  $_GP['img_url'];
    }else{
        $img_url = download_pic( $_GP['img_url'],$_GP['width'],$_GP['height'],$_GP['type']);
    }
    die(showAjaxMess(200,$img_url));
}else if($op == 'getDir'){
    //获取目录
    $pic_list_arr = aliyunOSS::listObjects('','/','');
    $pic_list     = $pic_list_arr['data'];
    die(showAjaxMess(200,$pic_list));
}else if($op == 'uploadPic'){
    //上传图片
    if($_GP['sel_dir'] == -1){
        message("请选择目录",refresh(),'error');
    }
    if(empty($_GP['sel_dir'])){
        $dir = date("Ym",time());
    }else{
        $dir = $_GP['sel_dir'];
    }
    $file = $_FILES['picture'];

    if($file['error'] != 0){
        message("对不起，你没有上传文件！",refresh(),'error');
    }
    //允许15M
    $limit = 15000*1024;   //媒体允许15造
    if ($limit < filesize($file['tmp_name'])) {
        $daxiao = tosize($limit);
        message("上传的文件超过大小限制，请上传小于 " . $daxiao . " 的文件",refresh(),'error');
    }
    $http_type =  WEB_HTTP;

    if($_GP['rename_type'] == 2){
        //按照图片原名
        $fileName  = $file['name'];
        $picname   = pathinfo($fileName, PATHINFO_FILENAME);
    }else{
        //系统命名
        $extention = pathinfo($file['name'], PATHINFO_EXTENSION);
        $picname   = date('YmdHi',time()).uniqid();
        $fileName  = $picname. ".{$extention}";
    }

    $result    = aliyunOSS::uploadFile($file['tmp_name'],$fileName,$dir);
    $data = array();
    if($result){
        $url     = str_replace('http://',$http_type,$result['oss-request-url']);
        $prefix  = $dir.'/'.$picname;
        $backUrl = web_url('img_mange',array("prefix"=>$prefix));
        message("上传成功！",$backUrl,'success');
    }else{
        message("上传失败，稍后再试！",refresh(),'error');
    }
}else if($op == 'fugai_pic'){
    //覆盖原来图片
    $hide_old_pic = $_GP['hide_old_pic'];
    $old_pic_arr  = explode('/',$hide_old_pic);
    if(count($old_pic_arr) != 2){
        message("对不起，文件地址有误！",refresh(),'error');
    }
    $file = $_FILES['fugai_pic'];
    if($file['error'] != 0){
        message("对不起，你没有上传文件！",refresh(),'error');
    }
    //允许15M
    $limit = 15000*1024;   //媒体允许15造
    if ($limit < filesize($file['tmp_name'])) {
        $daxiao = tosize($limit);
        message("上传的文件超过大小限制，请上传小于 " . $daxiao . " 的文件",refresh(),'error');
    }
    $http_type =  WEB_HTTP;

    $picname   = pathinfo($old_pic_arr[1], PATHINFO_FILENAME);
    $fileName  = $old_pic_arr[1];
    $dir       = $old_pic_arr[0];

    //先删除原图，在上传此图
    $res       = aliyunOSS::deleteObject($hide_old_pic);
    $result    = aliyunOSS::uploadFile($file['tmp_name'],$fileName,$dir);
    $data = array();
    if($result){
        $url     = str_replace('http://',$http_type,$result['oss-request-url']);
        $prefix  = $dir.'/'.$picname;
        $backUrl = web_url('img_mange',array("prefix"=>$prefix));
        message("更改成功！",$backUrl,'success');
    }else{
        message("上传失败，稍后再试！",refresh(),'error');
    }
}else if($op == 'batupload'){
    //批量上传
    $dir_list_arr = aliyunOSS::listObjects('','/');
    $dir_arr      = $dir_list_arr['data'];
    include page('img_batupload');
}