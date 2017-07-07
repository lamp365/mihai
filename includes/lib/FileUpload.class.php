<?php

/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/18 0018
 * Time: 16:24
 */
//use qiniu\QiniuStorage;

class FileUpload
{
    public $dir          = '';  //设置存放在阿里下的目录
    public $save_oldname = '';  //是否保存为图片原名

    /**
     * 图片保存方式：
       年月/xxxxx.jpg
       店铺id/年月/xxxxxxxx.jpg
       店铺id/分组/年月/xxxxxxx.jpg
     * FileUpload constructor.
     * @param string $pic_group
     * $pic_group为空 则        店铺id/年月/xxxxxxxx.jpg
     * $pic_group不为空 则      店铺id/分组/年月/xxxxxxxx.jpg
     */
    public function __construct($pic_group = '')
    {
        if(function_exists('get_member_account')){
            $member   = get_member_account();
            $store_id = $member['store_sts_id'];
        }else{
            $store_id = 0;
        }
        if(!empty($store_id)){
            $this->dir = 'shop'.$store_id.'/';
            if(!empty($pic_group)){
                $this->dir .= $pic_group.'/'.date('Ym');
            }else{
                $this->dir .= date('Ym');
            }
        }
    }

    public function uploadRemotePic($urlpic)
    {
        if(empty($urlpic))
            return $this->error(- 1, "图片文件地址不能为空！");

        $extention = pathinfo($urlpic, PATHINFO_EXTENSION);
        $extention = strtolower($extention);
        $result = $this->uploadByQiniu($urlpic, $extention);
        return $result;

    }
    /**
     *  表单提交上来的文件  如果传到本地则默认生成缩略图
     * @param $file
     * @param bool|true $uploadByQiniu
     * @param string $type
     * @return array
     */
    public function upload($file, $uploadByAli = true, $type = 'image')
    {
        if ($file['error'] == 4) {
            return $this->error(- 1, '没有上传内容');
        }

        // 返回文件后缀
        $extention = pathinfo($file['name'], PATHINFO_EXTENSION);
        $extention = strtolower($extention);
        if (empty($type) || $type == 'image') {
            $extentions = array(
                'gif',
                'jpg',
                'jpeg',
                'png'
            );
            $limit = 10000*1024;   //图片允许10造
        }
        if ($type == 'music') {
            $extentions = array(
                'mp3',
                'mp4'
            );
            $limit = 50000*1024;   //媒体允许15造
        }
        if ($type == 'other') {
            $extentions = array(
                'gif',
                'jpg',
                'jpeg',
                'png',
                'mp3',
                'mp4',
                'doc',
                'docx',
                'apk',
                'xls',
                'csv',
                'xlsx'
            );
            $limit = 80000*1024;   //apk允许25造
        }
        if (! in_array(strtolower($extention), $extentions)) {
            return $this->error(- 1, '不允许上传此类文件');
        }
        if ($limit < filesize($file['tmp_name'])) {
            $daxiao = $this->conversion($limit);
            return $this->error(- 1, "上传的文件超过大小限制，请上传小于 " . $daxiao . " 的文件");
        }

        //设置开关是否启用阿里云服务器存储文件
        if($uploadByAli){
//            $result = $this->uploadByQiniu($file, $extention);
            $result = $this->uploadBylocal($file, $extention);  //全部存本地
//            $result = $this->uploadByAli($file, $extention);
        }else{
            $result = $this->uploadBylocal($file, $extention);
        }

        return $result;
    }

    /**
     * @param $file
     * @param $extention
     * @return array
     * @content 通过七牛上传，无需操作缩略图
     */
    public function uploadByQiniu($file,$extention)
    {
        include_once 'qiniu/QiniuStorage.class.php';
        $config = array(
            'accessKey'  => 'JNJGtit_3bbLcOd8bG-TZLdW-WH1ZfUYDD74aOAF',
            'secrectKey' => 'FBeTd4rkZQyHAdiPyLDs4dD3U5XtgKK0-9vDBtu7',
            'bucket'     => 'mihai',
            'domain'     => 'http://odozak4lg.bkt.clouddn.com',
        );

        $qiniu = new QiniuStorage($config);

        $fileName = date('YmdHi',time()).uniqid(). ".{$extention}";

        if(is_array($file)){
            $file_body = $file['tmp_name'];
        }else{
            $file_body = $file;
        }
        $fileData = array(
            'name'      => 'file',
            'fileName'  => $fileName,
            'fileBody'  => file_get_contents($file_body)
        );
        $config = array();
        $result = $qiniu->upload($config, $fileData);

        $data = array();
        if($result){
            $data['path']    = 'http://odozak4lg.bkt.clouddn.com/'.$fileName;
            $data['success'] = true;
            unset($qiniu);
            return $data;
        }else{
            $msg = $this->error(-1,$qiniu->errorStr);
            unset($qiniu);
            return $msg;
        }

    }

    /**
     * @param $file 表单提交上来的文件
     * @param $extention
     * @return array
     * @content 直接上传到本地
     */
    public function uploadBylocal($file,$extention)
    {
        $result = array();
        if(empty($this->dir)){
            $path = 'attachment/'.date('Ym').'/';
        }else{
            $path = 'attachment/'.$this->dir.'/';
        }

        $result['path'] = $path;
        mkdirs(WEB_ROOT . '/' . $result['path']);
        do {
            $filename = date('YmdHi',time()).uniqid(). ".{$extention}";
        } while (file_exists(SYSTEM_WEBROOT . '/' .$path . $filename));

        $result['path'] .= $filename;
        $filename = WEB_ROOT .'/'. $result['path'];
        $result['extention'] = $extention;
        if (! file_move($file['tmp_name'], $filename)) {
            return $this->error(- 1, '保存上传文件失败');
        }

        $result['path']    = WEBSITE_ROOT.$result['path'];
        $result['success'] = true;
        return $result;

    }

    public function uploadByAli($file,$extention){
        $http_type =  $this->http_type()?'https://':'http://';
        if($this->save_oldname){
            $fileName  = $file['name'];
        }else{
            $fileName  = date('YmdHi',time()).uniqid(). ".{$extention}";
        }

        $dir       = $this->dir;
        $result    = aliyunOSS::uploadFile($file['tmp_name'],$fileName,$dir);
        $data = array();
        if($result){
            $data['path']    = str_replace('http://',$http_type,$result['oss-request-url']);
            $data['success'] = true;
            return $data;
        }else{
            $msg = $this->error(-1,'上传失败！');
            return $msg;
        }
    }
    public function conversion($size) {
        $kb = 1024; // 1KB（Kibibyte，千字节）=1024B，
        $mb = 1024 * $kb; //1MB（Mebibyte，兆字节，简称“兆”）=1024KB，
        $gb = 1024 * $mb; // 1GB（Gigabyte，吉字节，又称“千兆”）=1024MB，
        $tb = 1024 * $gb; // 1TB（Terabyte，万亿字节，太字节）=1024GB，

        if ($size < $kb) {
            return $size . " B";
        } else if ($size < $mb) {
            return round($size / $kb, 2) . " KB";
        } else if ($size < $gb) {
            return round($size / $mb, 2) . " MB";
        } else if ($size < $tb) {
            return round($size / $gb, 2) . " GB";
        } else {
            return round($size / $tb, 2) . " TB";
        }

    }

    public function http_type(){
        if(!isset($_SERVER['HTTPS']))  return FALSE;
        if($_SERVER['HTTPS'] === 1){  //Apache
            return TRUE;
        }elseif($_SERVER['HTTPS'] === 'on'){ //IIS
            return TRUE;
        }elseif($_SERVER['SERVER_PORT'] == 443){ //其他
            return TRUE;
        }
        return FALSE;
    }

    public function error($code, $msg = '')
    {
        return array(
            'errno' => $code,
            'message' => $msg
        );
    }
    /**
     * @return bool
     * 获取七牛所有的图片
     */
    public function getQiniuPicList(){
        include_once 'qiniu/QiniuStorage.class.php';
        $config = array(
            'accessKey'  => 'JNJGtit_3bbLcOd8bG-TZLdW-WH1ZfUYDD74aOAF',
            'secrectKey' => 'FBeTd4rkZQyHAdiPyLDs4dD3U5XtgKK0-9vDBtu7',
            'bucket'     => 'mihai',
            'domain'     => 'http://odozak4lg.bkt.clouddn.com',
        );

        $qiniu = new QiniuStorage($config);
        $res   = $qiniu->getList();
        return $res;
    }

}