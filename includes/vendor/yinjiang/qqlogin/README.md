QQ第三方登录

安装
composer require yinjiang/qqlogin

配置参数在config/config.php

使用步骤
1.调用getCode()函数，跳到QQ登录页面
2.获取URL中带的code参数,调用setCode函数
3.调用getOpenId函数，获取openid
