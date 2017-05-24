<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 17-2-6
 * Time: 下午9:02
 */
require_once WEB_ROOT.'/includes/lib/phpmailer/PHPMailerAutoload.php';

class MailService{
    public $server    = '';
    public $port      = '';
    public $usermail  = '';
    public $username  = '';
    public $pass      = '';
    public $authmode  = '';
    public $sendtype  = '';

    public function __construct(){
        //***************** 配置信息当系统没有录入时暂时采用自己的 ***********************
        $settings           = globaSetting();
        if(empty($settings['smtp_server'])){
            $settings = array(
                'smtp_server'   => 'smtp.163.com',
                'smtp_port' 	=> '25',
                'smtp_mail' 	=> 'lamp365@163.com',
                'smtp_username' => 'lamp365@163.com',
                'smtp_passwd'   => 'lamp7918I',
                'smtp_authmode' => 0,  //是否加密
                'smtp_sendtype' => 0,  //0表示phpmail发送  1表示SOCKET 连接 SMTP 服务器发送
            );
        }
        $this->server 	    = $settings['smtp_server'];//SMTP服务器
        $this->port         = intval($settings['smtp_port']);//SMTP服务器端口
        $this->usermail 	= $settings['smtp_mail'];//SMTP服务器的用户邮箱
        $this->username 	= $settings['smtp_username'];//SMTP服务器的用户帐号
        $this->pass 		= $settings['smtp_passwd'];//SMTP服务器的用户密码
        $this->authmode 	= intval($settings['smtp_authmode']);//是否加密
        $this->sendtype	    = intval($settings['smtp_sendtype']);

    }

    public function sendMail($emailto,$title,$content,$debug=false){
        $mailer = new PHPMailer();
		$mailer->isSMTP();
		$mailer->CharSet = 'utf-8';
		$mailer->Host = $this->server;
		$mailer->Port = $this->port;
		$mailer->SMTPAuth = true;
		$mailer->Username = $this->username;
		$mailer->Password = $this->pass;
		$mailer->do_socket = $this->sendtype;
		if($debug)
		{
			$mailer->SMTPDebug = 1;
		}
        if($this->authmode==1)
        {
            $mailer->SMTPSecure = 'ssl';
        }
        $mailer->From      = $this->usermail ;
        $mailer->FromName  = $this->usermail;
        $mailer->isHTML(true);

        $mailer->Subject = $title;
        $mailer->Body    = $content;
        $mailer->addAddress($emailto);
        $retuenrs        = $mailer->send();
        if($retuenrs == 1)
        {
            return $retuenrs;  //返回的是1
        }else
        {
            //返回错误信息
            return array("errorinfo"=>$mailer->ErrorInfo,"returnrs"=>$retuenrs);
        }
    }
}