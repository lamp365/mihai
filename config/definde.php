<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/21
 * Time: 14:29
 * content 定义一些常量
 */

define('WEB_HTTP', is_https()?'https://':'http://');
define('SAPP_NAME', '福州小物网络科技有限公司');
define('CORE_VERSION', 20151019);
defined('SYSTEM_VERSION') or define('SYSTEM_VERSION', CORE_VERSION);
header('Content-type: text/html; charset=UTF-8');
define('SYSTEM_WEBROOT', WEB_ROOT);
define('TIMESTAMP', time());
define('SYSTEM_IN', true);
date_default_timezone_set('PRC');
$document_root = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
define('WEBSITE_ROOT', WEB_HTTP . $_SERVER['HTTP_HOST'] . $document_root . '/');
define('RESOURCE_ROOT', WEBSITE_ROOT . 'assets/');
define('SYSTEM_ROOT', WEB_ROOT . '/system/');
define('COMMON_ROOT', SYSTEM_ROOT.'common/');
define('ADDONS_ROOT', WEB_ROOT . '/addons/');
define('INCLUDES_ROOT', WEB_ROOT . '/includes/');
defined('DEVELOPMENT') or define('DEVELOPMENT', 1);
defined('SQL_DEBUG') or define('SQL_DEBUG', 1);
defined('API_DEBUG') or define('API_DEBUG', 1);
define('MAGIC_QUOTES_GPC', (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) || @ini_get('magic_quotes_sybase'));
define('MOBILE_SESSION_ACCOUNT', "mobile_sessionAccount");
define('MOBILE_ACCOUNT', "mobile_account");
define('VIP_MOBILE_ACCOUNT',"vip_mobile_account");
define('MOBILE_WEIXIN_OPENID', "mobile_weixin_openid");
define('MOBILE_ALIPAY_OPENID', "mobile_alipay_openid");
define('MOBILE_QQ_OPENID', "mobile_qq_openid");
define('MOBILE_QQ_CALLBACK', "mobile_qq_callback");
define('TEAM_BUY_EXPIRY', 1800);							//团购成团有效期限(半个小时)
define('IM_FROM_USER','nrctongyong');						//即时通讯发送方默认userid
define('IM_ORDER_FROM_USER','admin_order');					//发送订单消息时，即时通讯发送方userid
define('IM_WEALTH_FROM_USER','admin_wealth');				//发送财富消息时，即时通讯发送方userid
define('WRITE_LOG',true);									//是否记录日志
define('API_MAINTAIN',false);								//app接口访问暂停配置项；为true时，app接口无法访问
define('IM_ICON_URL','http://odozak4lg.bkt.clouddn.com/QQ%E5%9B%BE%E7%89%8720161125151517.png'); //im客户默认头像
//http://restapi.amap.com/v3/geocode/geo?address=北京市朝阳区阜通东大街6号&output=XML&key=447e9b30f3af97fbb075e55a9863fef2
define('GD_KEY','447e9b30f3af97fbb075e55a9863fef2');//高德地图的key
define('AL_CODE','http://restapi.amap.com/v3/geocode/regeo?');//逆地理编码API服务地址
define('GD_IP','http://restapi.amap.com/v3/ip?');//IP定位API服务地址