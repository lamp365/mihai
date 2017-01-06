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
        mkdir($path);
    }
    return is_dir($path);
}

/**
 * @param $file         参数$_FILES['pic']
 * @param bool|true $uploadByQiniu
 * @param string $width
 * @param string $height
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
function file_upload($file, $uploadByQiniu=true, $width="350", $height="350",$type = 'image')
{
    $fileObj = new FileUpload();
    $result  = $fileObj->upload($file,$uploadByQiniu,$width,$height,$type);
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
                //阿里的图片
                $picUrl = download_alipic($imgPath,$width , $height, $scaleType);
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
    ppd($result);
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