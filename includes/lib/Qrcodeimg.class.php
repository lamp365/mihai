<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/21 0021
 * Time: 19:43
 * content: 二维码
 */

class Qrcodeimg
{
    public function getImgQcode($url,$logoUrl = '',$name='')
    {
        include_once 'phpqrcode/phpqrcode.php';
        //创建目录 给图片命名
        $qcodeArr = $this->getPicName($name);
        $qcodePic     = $qcodeArr['qcodePic'];
        $qcodePicLogo = $qcodeArr['qcodePicLogo'];
		if ( is_file($qcodePic) ){
            return WEBSITE_ROOT.$qcodePic;
		}
        if (empty($logoUrl)) {
            QRcode::png($url, $qcodePic, 'L', 6,2);
            $img = $qcodePic;
        } else {
            QRcode::png($url, $qcodePic, 'L', 6,2);
            $QR = $qcodePic;        //已经生成的原始二维码图
            $QR             = imagecreatefromstring(file_get_contents($QR));
            $logo           = imagecreatefromstring(file_get_contents($logoUrl));
            $QR_width       = imagesx($QR);//二维码图片宽度
            $QR_height      = imagesy($QR);//二维码图片高度
            $logo_width     = imagesx($logo);//logo图片宽度
            $logo_height    = imagesy($logo);//logo图片高度
            $logo_qr_width  = $QR_width / 6;
            $scale          = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width     = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
            imagepng($QR, $qcodePicLogo);
            $img = $qcodePicLogo;

        }
        return WEBSITE_ROOT.$img;
    }

    public function getPicName($id = '')
    {
        $path = 'attachment/qcode/';
		if ( !empty($id) ){
             $fileName = md5($id).".png";
		}else{
             $fileName = date('YmdHi',time()).uniqid(). ".png";
		}
        $qcodePic = $path.$fileName;
        $qcodePicLogo = $path.'logo_'.$fileName;
        mkdirs(WEB_ROOT . '/' . $path);
        return array('qcodePic'=>$qcodePic, 'qcodePicLogo'=>$qcodePicLogo);
    }

}