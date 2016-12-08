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
 * @param string $height      高度不给，则为原图
 * @param int $scaleType    比例类型 1为非等比 会被裁减掉 2为等比
 * @return mixed
 * @content 得到图片缩略图  会根据原图图片地址判断是从七牛获取还是从本地获取
 */
function download_pic($imgPath,$width = '', $height = '', $scaleType = 2)
{
    if(empty($width)){
        return $imgPath;
    }else{

        if (strstr($imgPath,'attachment')){
            $web_url = WEBSITE_ROOT;
            $picUrl  = str_replace($web_url,'',$imgPath);
            $thumbPic = imgThumb($picUrl,$width,$height);
            return $thumbPic;
        }else{
            if(empty($height)){
                return $imgPath."?imageView/{$scaleType}/w/{$width}";
            }else{
                return $imgPath."?imageView/{$scaleType}/w/{$width}/h/{$height}";
            }

        }

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