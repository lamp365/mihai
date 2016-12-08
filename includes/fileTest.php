<?php
include "TopSdk.php";
date_default_timezone_set('Asia/Shanghai'); 
$c = new TopClient;
$c->appkey = '23364190';
$c->secretKey = 'f40319d910d5e7cef4811f99f8e4ea17';
//您的${product}验证码：${code}，10分钟内有效，感谢您的支持！
$req = new AlibabaAliqinFcSmsNumSendRequest;
$req->setSmsType("normal");
$req->setSmsFreeSignName("小物社区");
$req->setSmsParam("{\"code\":\"1828\",\"product\":\"小物社区\"}");
$req->setRecNum("13413873366");
$req->setSmsTemplateCode("SMS_9625259");
$resp = $c->execute($req);
print_r($resp);
?>