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
    public function uploadRemotePic($urlpic)
    {
        if(empty($urlpic))
            return error(- 1, "图片文件地址不能为空！");

        $extention = pathinfo($urlpic, PATHINFO_EXTENSION);
        $extention = strtolower($extention);
        $result = $this->uploadByQiniu($urlpic, $extention);
        return $result;

    }
    /**
     *  表单提交上来的文件  如果传到本地则默认生成缩略图
     * @param $file
     * @param bool|true $uploadByQiniu
     * @param string $width
     * @param string $height
     * @param string $type
     * @return array
     */
    public function upload($file, $uploadByQiniu = true, $width, $height, $type = 'image')
    {
        if ($file['error'] == 4) {
            return error(- 1, '没有上传内容');
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
            $limit = 15000*1024;   //媒体允许10造
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
            $limit = 25000*1024;   //apk允许25造
        }
        if (! in_array(strtolower($extention), $extentions)) {
            return error(- 1, '不允许上传此类文件');
        }
        if ($limit < filesize($file['tmp_name'])) {
            $daxiao = $this->conversion($limit);
            return error(- 1, "上传的文件超过大小限制，请上传小于 " . $daxiao . " 的文件");
        }

        //设置开关是否启用七牛服务器存储文件
        if($uploadByQiniu){
            $result = $this->uploadByQiniu($file, $extention);
//            $result = $this->uploadByAli($file, $extention);
        }else{
            $result = $this->uploadBylocal($file, $extention, $width, $height, $type);
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
            $msg = error(-1,$qiniu->errorStr);
            unset($qiniu);
            return $msg;
        }

    }

    /**
     * @param $file 表单提交上来的文件
     * @param $extention
     * @param $width
     * @param $height
     * @return array
     * @content 直接上传到本地
     */
    public function uploadBylocal($file,$extention,$width, $height, $type='')
    {
        $result = array();
        $path = 'attachment/';
        $result['path'] = $path."{$extention}/" . date('Y/m/');
        mkdirs(WEB_ROOT . '/' . $result['path']);
        do {
            $filename = random(15) . ".{$extention}";
        } while (file_exists(SYSTEM_WEBROOT . '/' .$path . $filename));

        $result['path'] .= $filename;
        $filename = WEB_ROOT .'/'. $result['path'];
        $result['extention'] = $extention;
        if (! file_move($file['tmp_name'], $filename)) {
            return error(- 1, '保存上传文件失败');
        }

        if ($type=='image') {
            //产生缩略图
            $thumb = imgThumb($result['path'],$width,$height);
            $result['path'] = WEBSITE_ROOT.$result['path'];
            $result['thumb'] = WEBSITE_ROOT.$thumb;
            $result['success'] = true;
        }

        return $result;

    }

    public function uploadByAli($file,$extention){
        $fileName = date('YmdHi',time()).uniqid(). ".{$extention}";
        $result   = aliyunOSS::uploadFile($file['tmp_name'],$fileName);
        $data = array();
        if($result){
            $data['path']    = $fileName;
            $data['success'] = true;
            return $data;
        }else{
            $msg = error(-1,'上传失败！');
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