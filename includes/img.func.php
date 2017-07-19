 <?php
/*
图片处理
*/

/**
 * 生成缩略图
 * @author yangzhiguo0903@163.com
 * @param string     源图绝对完整地址{带文件名及后缀名}
 * @param string     目标图绝对完整地址{带文件名及后缀名}
 * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
 * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
 * @param int        是否裁切{宽,高必须非0}
 * @param int/float  缩放{0:不缩放, 0<this<1:缩放到相应比例(此时宽高限制和裁切均失效)}
 * @return boolean
 */
function img2thumb($src_img, $dst_img, $width = 75, $height = 75, $cut = 0, $proportion = 0)
{
    if(!is_file($src_img))
    {
        return false;
    }
    $ot = fileext($dst_img);
    $otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
    $srcinfo = getimagesize($src_img);
    $src_w = $srcinfo[0];
    $src_h = $srcinfo[1];
    $type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
    $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);
 
    $dst_h = $height;
    $dst_w = $width;
    $x = $y = 0;
 
    /**
     * 缩略图不超过源图尺寸（前提是宽或高只有一个）
     */
    if($width == 0 || $height == 0)
    {
        $proportion = 1;
    }
   /* if($width> $src_w)
    {
        $dst_w = $width = $src_w;
    }
    if($height> $src_h)
    {
        $dst_h = $height = $src_h;
    }*/
 
    if(!$width && !$height && !$proportion)
    {
        return false;
    }
    if(!$proportion)
    {
        if($cut == 0)
        {
            if($dst_w && $dst_h)
            {
                if($dst_w/$src_w> $dst_h/$src_h)
                {
                    $dst_w = $src_w * ($dst_h / $src_h);
                    $x = ($dst_w - $width) / 2;
                    $x = $x<0 ? $x*(-1) : $x;
                }
                else
                {
                    $dst_h = $src_h * ($dst_w / $src_w);
                    $y = ($dst_h - $height) / 2;
                    $y = $y<0 ? $y*(-1) : $y;
                }
            }
            else if($dst_w xor $dst_h)
            {
                if($dst_w && !$dst_h)  //有宽无高
                {
                    $propor = $dst_w / $src_w;
                    $height = $dst_h  = $src_h * $propor;
                }
                else if(!$dst_w && $dst_h)  //有高无宽
                {
                    $propor = $dst_h / $src_h;
                    $width  = $dst_w = $src_w * $propor;
                }
            }
        }
        else
        {
            if(!$dst_h)  //裁剪时无高
            {
                $height = $dst_h = $dst_w;
            }
            if(!$dst_w)  //裁剪时无宽
            {
                $width = $dst_w = $dst_h;
            }
            $propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
            $dst_w = (int)round($src_w * $propor);
            $dst_h = (int)round($src_h * $propor);
            $x = ($width - $dst_w) / 2;
            $y = ($height - $dst_h) / 2;
            $x = $x<0 ? $x*(-1) : $x;
            $y = $y<0 ? $y*(-1) : $y;

        }
    }
    else
    {
        $proportion = min($proportion, 1);
        $height = $dst_h = $src_h * $proportion;
        $width  = $dst_w = $src_w * $proportion;
    }

    $src = $createfun($src_img);
    $dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);

    if(function_exists('imagecopyresampled'))
    {
        imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    }
    else
    {
        imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    }
    $otfunc($dst, $dst_img);
    imagedestroy($dst);
    imagedestroy($src);
    return true;
}

function imgThumb($file,$width=310,$height=310){
    $file_url = $file;
    if ( is_file($file_url) ){
        $ext = strrchr($file, '.');
        $name = ($ext === FALSE) ? $file : substr($file, 0, -strlen($ext));

        if($pos = strpos($name,'_thumb')){
            $name = substr($name,0,$pos);
        }

        $dst_file = $name.'_thumbx'.$width.'x'.$height.$ext;
        if ( !is_file($dst_file) ){
            img2thumb($file_url, $dst_file, $width, $height, $cut = 0, $proportion = 0);
        }
        return $dst_file;
    }else{
        return $file_url;
    }
}

// 下载远程图片函数
function GrabImage($url,$filename="") {  
    if($url==""){
        return false;
    }
    if($filename=="") { 
        $typearr=array("jpg","gif","png","jpeg");
        $ext = trim(strtolower(substr(strrchr($url,'.'),1,10)));
        if(!in_array($ext,$typearr)){
            return false;
        }
        $filename=md5(uniqid(mt_rand(), true)).substr(microtime(),2,8).".".$ext; 
    } 
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $img = curl_exec ($ch);
    curl_close ($ch);
    $size = strlen($img);
    if ($size<1) {
        return false;
    }
    $dir = "attachment/goods/".date("Y")."/".date("m")."/".date("d")."/";
    mkdirs(WEB_ROOT.'/'.$dir);
    $fp2=fopen(SYSTEM_WEBROOT.'/'.$dir.$filename, "a"); 
    if ($fp2) {
        fwrite($fp2,$img); 
        fclose($fp2); 
    }else{
        return false;
    }
    $img_dir = $dir.$filename;
    return $img_dir; 
}


function imgToBase64($image_file){
    $image_info           = getimagesize($image_file);
    $file_content         = file_get_contents($image_file);
    $base64_image_content = "data:{$image_info['mime']};base64," . base64_encode($file_content);
    return $base64_image_content;
}

function base64Toimg($base64_image_content){
    //保存base64字符串为图片
    //匹配出图片的格式
    $res  = '';
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
        $type     = $result[2];
        $name     = date("YmdHi").uniqid();
        $new_file = "./images/{$name}.{$type}";
        if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
            $res = aliyunOSS::putObject($new_file);
        }
    }
    return $res;
}

function getFullPicUrl($picurl){
    return WEBSITE_ROOT.$picurl;
}


 /**
  * 图片合成
  * @param $bigImgPath
  * @param $qCodePath
  * @param int $w_pos
  * @param int $h_pos
  */
 function mergeImgs($bigImgPath,$qCodePath,$w_pos = 200,$h_pos = 300,$qr_w = '',$qr_h=''){
     if(!empty($qr_w) || !empty($qr_h)){
         //二维码裁减一下大小
         $qCodePath  = download_pic($qCodePath,$qr_w,$qr_h);
     }
     $bigImg     = imagecreatefromstring(file_get_contents($bigImgPath));
     $qCodeImg   = imagecreatefromstring(file_get_contents($qCodePath));


     list($qCodeWidth, $qCodeHight, $qCodeType) = getimagesize($qCodePath);

     imagecopy($bigImg, $qCodeImg, $w_pos, $h_pos, 0, 0, $qCodeWidth, $qCodeHight);

     list($bigWidth, $bigHight, $bigType) = getimagesize($bigImgPath);

     if(!is_dir('attachment/qcode')){
         mkdirs('attachment/qcode');
     }
     $path = 'attachment/qcode/'.date('YmdHi',time()).uniqid();
     switch ($bigType) {
         //如果需要输出到页面显示，则加入head   前面不能有任何输出 和head
         case 1: //gif
//             header('Content-Type:image/gif');
             $mergePath = $path.'.gif';
             imagegif($bigImg,$mergePath);
             break;
         case 2: //jpg
//             header('Content-Type:image/jpg');
             $mergePath = $path.'.jpg';
             imagejpeg($bigImg,$mergePath);
             break;
         case 3: //jpg
//             header('Content-Type:image/png');
             $mergePath = $path.'.png';
             imagepng($bigImg,$mergePath);
             break;
         default:
             $mergePath = '';
             # code...
             break;
     }

     imagedestroy($bigImg);
     imagedestroy($qCodeImg);
     $imgPathArr = explode('attachment',$qCodePath);
     if(count($imgPathArr) == 2){
         //说明缩小后的图片在本地上，删除掉无用
         @unlink($qCodePath);
     }
     return WEBSITE_ROOT.$mergePath;
 }