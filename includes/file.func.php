<?php
/*
文件操作
*/

function file_delete($file)
{
    if (empty($file)) {
        return FALSE;
    }
    if (file_exists(SYSTEM_WEBROOT . '/attachment/' . $file)) {
        unlink(SYSTEM_WEBROOT . '/attachment/' . $file);
    }
    return TRUE;
}

function file_move($filename, $dest)
{
    mkdirs(dirname($dest));
    if (is_uploaded_file($filename)) {
        move_uploaded_file($filename, $dest);
    } else {
        rename($filename, $dest);
    }
    return is_file($dest);
}

function mkdirs($path)
{
    if (! is_dir($path)) {
        mkdirs(dirname($path));
        mkdir($path,0777);
        chmod($path,0777);
    }
    return is_dir($path);
}

/**
 * @param $file         参数$_FILES['pic']
 * @param bool|true $uploadByAli    1传到阿里   0传到本地
 * @param string $type
 * @return array
 * @content 图片上传 返回数组
 * 使用如下
 $upload = file_upload($_FILES['thumb']);
 if (is_error($upload)) {
    message($upload['message'], '', 'error');
 }
  $pic = $upload['path'];
 */
function file_upload($file, $uploadByAli=1, $pic_group = '',$type = 'image')
{
    $fileObj = new FileUpload($pic_group);
    $result  = $fileObj->upload($file,$uploadByAli,$type);
    return $result;
}
/**
 * @param $picurl
 * @return array
 * @content 上传远程地址图片到七牛
使用如下
$upload = remote_file_upload($picurl);
if (is_error($upload)) {
 message($upload['message'], '', 'error');
}
$pic = $upload['path'];
 */
function remote_file_upload($picurl)
{
    $fileObj = new FileUpload();
    $result  = $fileObj->uploadRemotePic($picurl);
    return $result;
}


/**
 * @param $imgPath          图片地址
 * @param string $width       宽度不给，则为原图
 * @param string $height      高度
 * @param int $scaleType    比例类型 1为等比缩放，按照宽度等比缩放  或者按照高度等比缩放
 *                                  2按照宽高 固定拉伸
 *                                  3按照宽高 裁减掉
 * @return mixed
 * @content 得到图片缩略图  会根据原图图片地址判断是从阿里获取 七牛获取 还是从本地获取
 * 并返回对应的绝对地址
 */
function download_pic($imgPath,$width = '', $height = '', $scaleType = 1)
{
    if(empty($imgPath)){
        //可以返回默认图片
        $default = "http://nrctest.oss-cn-shanghai.aliyuncs.com/201706/2017060211515930e0d52c645.png";
        return $default;
    }
    $imgPathArr = explode('attachment',$imgPath);
    //获取参数个数
    $numargs    = func_num_args();
    if(count($imgPathArr) == 2){
        //本地图片
        if($numargs < 3){
            //返回原图
            $http_arr = explode('http',$imgPath);
            if(count($http_arr) == 2){
                $picUrl = $imgPath;
            }else{
                //补全全路劲
                $picUrl = WEBSITE_ROOT.$imgPath;
            }

        }else{
            $picUrl  = str_replace(WEBSITE_ROOT,'',$imgPath);
            $thumbPic = imgThumb($picUrl,$width,$height);
            $picUrl   = WEBSITE_ROOT.$thumbPic;
        }
    }else{
        if($numargs == 1){
            //返回原图
            $picUrl = $imgPath;
        }else{
            $img_type = explode('odozak4lg.bkt.clouddn.com',$imgPath);
            if(count($img_type) == 2){
                //七牛的图片
                $picUrl = download_qiniupic($imgPath,$width , $height, $scaleType);
            }else{
                if(strstr($imgPath,"aliyuncs.com")){
                    //阿里的图片
                    $picUrl = download_alipic($imgPath,$width , $height, $scaleType);
                }else{
                    //返回原图
                    $picUrl = $imgPath;
                }
            }

        }
    }

    return $picUrl;
}

function download_alipic($imgPath,$width, $height, $scaleType){
    //阿里云图片
//        $ali_url = WEB_HTTP.aliyunOSS::bucket.'.'.aliyunOSS::endpoint;
    if($scaleType == 1){
        //按照宽度或者高度等比缩放
        if(!empty($width)){
            $picUrl = $imgPath."?x-oss-process=image/resize,w_{$width}";
        }else if(!empty($height)){
            $picUrl = $imgPath."?x-oss-process=image/resize,h_{$height}";
        }
    }else if($scaleType == 2){
        //按照宽高拉伸
        $picUrl = $imgPath."?x-oss-process=image/resize,m_fixed,h_{$height},w_{$width}";
    }else if($scaleType == 3){
        //裁减掉
        $picUrl = $imgPath."?x-oss-process=image/resize,m_fill,h_{$height},w_{$width}";
    }
    return $picUrl;
}

function download_qiniupic($imgPath,$width, $height, $scaleType){
//    比例类型 1为非等比 会被裁减掉 2为等比iden
    if($scaleType == 1){
        //七牛的2为等比 跟阿里的相反
        $scaleType = 2;
    }else{
        $scaleType = 1;
    }
    if(empty($height)){
        //会等比缩放 按照宽度
        return $imgPath."?imageView/{$scaleType}/w/{$width}";
    }else{
        //按照宽高裁减
        return $imgPath."?imageView/{$scaleType}/w/{$width}/h/{$height}";
    }
}
/**
 * @param $table            需要处理的表
 * @param string $field       需要处理的字段
 * @param string $contorl     批量控制的字段
   @param string $regular    是否需要正则处理 0 需要 1 不需要
 * @content 批量处理本地图片导入到图片服务器操作 如果处理的数据非常大，可能会中途宕机，需要重试几篇，控制字段是为了提高速度
 */
 function upload_img_change($limit = 0, $table='shop_goods', $field = 'content', $contorl_value = 0,  $regular = 0, $contorl = 'rechange', $id = 'id'){
	 set_time_limit(3000);
	 $limit = $limit > 0 ?  ' LIMIT '.$limit : '';
	 if ( !empty( $contorl ) ){
         $where = ' WHERE '.$contorl."=".$contorl_value.' ';
	 }else{
         $where = '';
	 }
     $list = mysqld_selectall("SELECT ".$field.','.$id." FROM ".table($table).$where.$limit);
	 foreach ( $list as $value ){
		 if ( $regular == 0 ){
			$contents = htmlspecialchars_decode($value[$field]);
			preg_match_all('<img.*?src=\"(.*?.*?)\".*?>',$contents,$match);
			$oldimg   =  $match[1];
			foreach ( $oldimg as $key=>$oldimg_value ){
				  // 判断是否为千牛的图片，如果不是，则开始执行上传替换
				  if ( !strstr($oldimg_value, 'odozak4lg.bkt.clouddn.com' )){
					   // 上传图片，返回上传的信息，判断图片是否全名，如果不是，增加http://www.hinrc.com/
					   if ( !strstr($oldimg_value,'http') ){
							 $oldimg = 'http://www.hinrc.com/'.$oldimg_value;
					   }
					   $newimg = remote_file_upload($oldimg);
					   $contents = str_replace($oldimg_value, $newimg['path'],$contents);
				   }
			 }
		 }else{
              $contents = $value[$field];
			  if ( !strstr($contents, 'odozak4lg.bkt.clouddn.com' )){
					   // 上传图片，返回上传的信息，判断图片是否全名，如果不是，增加http://www.hinrc.com/
					   if ( !strstr($contents,'http') ){
							 $contents = 'http://www.hinrc.com/'.$contents;
					   }
					   $newimg = remote_file_upload($contents);
					   $contents = $newimg['path'];
		     }
		 }
		$content_data = array($field=>$contents);
		if ( !empty( $contorl ) ){
          $content_data[$contorl] = $contorl_value + 1;
		}
		mysqld_update($table, $content_data, array(
				'id' => $value[$id]
		));
	  }
 }

/**
 * 转移七牛的图片到阿里云
 */
function get_qiniu_allpic()
{
    $fileObj = new FileUpload();
    $result  = $fileObj->getQiniuPicList();
    foreach($result['items'] as $k => $row){
        $picName = $row['key'];
        $picUrl  = "http://odozak4lg.bkt.clouddn.com/".$picName;
        $res = aliyunOSS::putObject($picUrl,$picName);
        if($res){
            echo "当前所在行：".$k.'<br/>';
        }else{
            die($k);
        }
    }
    die('结束');
}

/*
 * 统一图片校验封装
 * @return array(url,error,errno,filename,url,message)    errno   0失败1成功
 * @content 图片上传 返回数组  核心操作#4
 */
function checkImgFileAndUpload($postName='imgFile'){
    $result  = array('errno'=>0);
    $extentions=array('gif', 'jpg', 'jpeg', 'png');
    #1.1
    if(empty($_FILES[$postName]['name'])){
        $result['message'] = '上传失败，请重试！';
        return $result;
    }
    #1.2
	if ($_FILES[$postName]['error'] != 0) {
			$result['message'] = '上传失败，请重试！';
			return $result;
	}
    #1.3
    $extention = pathinfo($_FILES[$postName]['name'], PATHINFO_EXTENSION);
	if(!in_array(strtolower($extention), $extentions)) {
		$result['message'] = '不允许上传此类文件！';
		return $result;
	}
    #2  核心操作上传
    $file = file_upload($_FILES[$postName], 'image');
    if (is_error($file)) {
        $result['message'] = $file['message'];
        return $result;
    }
    #3  返回数据
    $result['error'] = 0;//这个是以前的，有用吗？
    $result['errno'] = 1;//这个是用来判断是否成功
    $result['url'] = $result['filename'] = $file['path'];
    
    return $result;
	
}

/**
 * 将在线编辑器上传的图片 转移到 阿里
 * @param $content
 */
function changeUeditImgToAli($dish_content,$alidir=''){
    if(empty($dish_content)){
        return $dish_content;
    }
    $contents =  htmlspecialchars_decode($dish_content);
    preg_match_all('<img.*?src=\"(.*?.*?)\".*?>',$contents,$match);
    $oldimg   =  $match[1];
    foreach ( $oldimg as $key=>$oldimg_value ){
        // 将本地的图片上传到阿里云上
        if ( strstr($oldimg_value, 'ueditor' )){

            $picurl   = rtrim(WEB_ROOT,'/').$oldimg_value;
            if(file_exists($picurl)){
                $newimg   = aliyunOSS::putObject($picurl,'',$alidir);

                $content = str_replace($oldimg_value, $newimg['oss-request-url'],$dish_content);
                $dish_content = htmlspecialchars_decode($content);
                //删除本地图片
                $unlink_pic = ".".$oldimg_value;  //相对路径
                @unlink ($unlink_pic);
            }

        }
    }
    return $dish_content;
}

//将详情内容里的部分网络图片上传到阿里云
function changeWebImgToAli($content,$alidir=''){
    if(empty($content)){
        return array();
    }
    $return = array();
    $contents =  htmlspecialchars_decode($content);
    preg_match_all('<img.*?src=\"(.*?.*?)\".*?>',$contents,$match);
    $oldimg   =  $match[1];
    $i = 0;
    foreach ( $oldimg as $key=>$oldimg_value ){
       $return['img'][$i] = $oldimg_value;
       $i++;
    }
    $return['content'] = strip_tags($content);

    return $return;
}	