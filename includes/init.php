<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 百家威信 <QQ:2752555327> <http://www.squdian.com>
// +----------------------------------------------------------------------
// 避免加载的顺序错误，暂时先写在这里
function is_https(){  
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
define('WEB_HTTP', is_https()?'https://':'http://');
define('WEB_ROOT', str_replace("\\", '/', dirname(dirname(__FILE__))));
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
define('ADDONS_ROOT', WEB_ROOT . '/addons/');
define('INCLUDES_ROOT', WEB_ROOT . '/includes/');
defined('DEVELOPMENT') or define('DEVELOPMENT', 1);
defined('SQL_DEBUG') or define('SQL_DEBUG', 1);
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
define('API_DEBUG',TRUE);									//为false时，开启app接口的签名验证
define('IM_ICON_URL','http://odozak4lg.bkt.clouddn.com/QQ%E5%9B%BE%E7%89%8720161125151517.png'); //im客户默认头像


if (! session_id()) {
	$lifeTime = 24 * 3600 * 10; 
    session_set_cookie_params($lifeTime);  
    session_start();
}
if (DEVELOPMENT) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL ^ E_NOTICE);
} else {
    error_reporting(0);
}
ob_start();

if (MAGIC_QUOTES_GPC) {

    function stripslashes_deep($value)
    {
        $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
        return $value;
    }
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}
$_GP = $_CMS = array();

$_GP = array_merge($_GET, $_POST, $_GP);

function irequestsplite($var)
{
    if (is_array($var)) {
        foreach ($var as $key => $value) {
            $var[htmlspecialchars($key)] = irequestsplite($value);
        }
    } else {
        $var = str_replace('&amp;', '&', htmlspecialchars($var, ENT_QUOTES));
    }
    return $var;
}
$_GP = irequestsplite($_GP);
$modulename = $_GP['name'];
if (empty($modulename)) {
    if (empty($mname)) {
        if (SYSTEM_ACT == 'mobile') {
            $modulename = 'shopwap';
        } else {
            $modulename = 'public';
        }
    } else {
        $modulename = $mname;
    }
}

if (empty($_GP['do'])) {
    if (empty($do)) {
        $_GP['do'] = 'index';
    } else {
        $_GP['do'] = $do;
    }
}
$pdo = $_CMS['pdo'] = null;
$bjconfigfile = WEB_ROOT . "/config/config.php";
$BJCMS_CONFIG = array();
if (file_exists($bjconfigfile)) {
    require $bjconfigfile;
}
$bjconfig = $BJCMS_CONFIG;
if (empty($bjconfig['db']['host'])) {
    $bjconfig['db']['host'] = '';
}
if (empty($bjconfig['db']['username'])) {
    $bjconfig['db']['username'] = '';
}
if (empty($bjconfig['db']['password'])) {
    $bjconfig['db']['password'] = '';
}
if (empty($bjconfig['db']['port'])) {
    $bjconfig['db']['port'] = '';
}
if (empty($bjconfig['db']['database'])) {
    $bjconfig['db']['database'] = '';
}
$bjconfig['db']['charset'] = 'utf8';
$_CMS['config'] = $bjconfig;
$_CMS['module'] = $modulename;
$_CMS['account'] = $_SESSION["account"];
// 引入公共加载
require_once INCLUDES_ROOT . 'init.func.php';

$system_module = array(
    'common',
    'index',
    'member',
    'modules',
    'public',
    'shop',
    'shopwap',
    'user',
    'weixin',
    'bonus',
    'alipay',
    'promotion',
    'api',
    'job'
);

if (in_array($modulename, $system_module)) {
    $classname = $modulename . "Addons";
    if (! class_exists($classname)) {
        if (SYSTEM_ACT == 'mobile') {
            require (WEB_ROOT . '/system/common/mobile.php');
            $file = SYSTEM_ROOT . $modulename . "/mobile.php";
        } else {
            require (WEB_ROOT . '/system/common/web.php');
            $file = SYSTEM_ROOT . $modulename . "/web.php";
        }
        if (! is_file($file)) {
            exit('ModuleSite Definition File Not Found ' . $file);
        }
        require $file;
    }
    if (! class_exists($classname)) {
        exit('ModuleSite Definition Class Not Found');
    }

    function checkAddons()
    {
        $addons = dir(ADDONS_ROOT);
        while ($file = $addons->read()) {
            if (($file != ".") and ($file != "..")) {
                
                if (file_exists(ADDONS_ROOT . $file . '/key.php')) {
                    $addons_key = file_get_contents(ADDONS_ROOT . $file . '/key.php');
                    if ($file == $addons_key || md5($file) == $addons_key) {
                        $item = mysqld_select("SELECT * FROM " . table('modules') . " where `name`=:name", array(
                            ':name' => $file
                        ));
                        if (empty($item['name'])) {
                            message("发现可用插件，系统将进行安装！", create_url('site', array(
                                'name' => 'modules',
                                'do' => 'addons_update'
                            )), "success");
                        } else {
                            $addons_version = file_get_contents(ADDONS_ROOT . $file . '/version.php');
                            if ($addons_version > $item['version']) {
                                message("发现插件更新，系统将进行更新！", create_url('site', array(
                                    'name' => 'modules',
                                    'do' => 'addons_update'
                                )), "success");
                            }
                        }
                    }
                }
            }
        }
    }
    $class = new $classname();
    $class->module = $modulename;
    $class->inMobile = SYSTEM_ACT == 'mobile';
    if ($class instanceof BjSystemModule) {
        if (! empty($class)) {
            if (isset($_GP['do'])) {
                if (SYSTEM_ACT == 'mobile') {
                    $class->inMobile = true;
                    if ($modulename == "public" && $_GP['do'] == "kernel") {
                        echo md5_file(__FILE__);
                        exit();
                    }
                    
                    if($modulename == 'shopwap'){
                    	
                    	//新增访问记录
                    	insertAccessLog(session_id());
                    	
                        //商城访问量统计
                        trafficCount();
                    }

                } else {
                    
                    if ($modulename != "public") {
                        checklogin();
                    }
                    if (($modulename != "public" && $_GP['do'] != "index") && $modulename != "modules" && $_GP['do'] != "update" && $_GP['act'] != "toupdate") {
                        if (intval(CORE_VERSION) > intval(SYSTEM_VERSION)) {
                            message("发现最新版本，系统将进行更新！", create_url('site', array(
                                'name' => 'modules',
                                'do' => 'update',
                                'act' => 'toupdate'
                            )), "success");
                        }
                    } else {
                        define('LOCK_TO_UPDATE', true);
                    }
                    if ($modulename == "modules" && $_GP['do'] == "addons_update") {
                        define('LOCK_TO_ADDONS_INSTALL', true);
                    }
                    $class->inMobile = false;
                    
                    if ($modulename != "modules" && ! defined('LOCK_TO_UPDATE') && $modulename != "index" && $modulename != "common" && $modulename != "public") {
                        if (checkrule($modulename, $_GP['do'],$_GP['op']) == false) {
                            message("您没有权限操作此功能",refresh(),'error');
                        }
                    }
                }
                $method = 'do_' . $_GP['do'];
            }
            $class->module = $modulename;
            if (method_exists($class, $method)) {
                exit($class->$method());
            } else {
                exit($method . " no this method");
            }
        }
    } else {
        exit('BjSystemModule Class Definition Error');
    }
} else {

    function addons_page($filename)
    {
        global $modulename;
        if (SYSTEM_ACT == 'mobile') {
            $source = ADDONS_ROOT . $modulename . "/template/mobile/{$filename}.php";
        } else {
            $source = ADDONS_ROOT . $modulename . "/template/web/{$filename}.php";
        }
        return $source;
    }

    abstract class BjModule
    {

        public function __web($f_name)
        {
            global $_CMS, $_GP, $modulename;
            include_once ADDONS_ROOT . $modulename . '/class/web/' . strtolower(substr($f_name, 3)) . '.php';
        }

        public function __mobile($f_name)
        {
            global $_CMS, $_GP, $modulename;
            include_once ADDONS_ROOT . $modulename . '/class/mobile/' . strtolower(substr($f_name, 3)) . '.php';
        }
    }
    $tmp_modules = mysqld_select("SELECT *FROM " . table('modules') . "  where `name`=:name", array(
        ':name' => $modulename
    ));
    if (! empty($tmp_modules['isdisable'])) {
        if (SYSTEM_ACT == 'mobile') {
            header("location:" . WEBSITE_ROOT . create_url('mobile', array(
                'name' => 'shopwap',
                'do' => 'shopindex'
            )));
            exit();
        } else {
            message("插件已关闭，页面刷新后该插件菜单将隐藏");
        }
    }
    
    $classname = $modulename . "Addons";
    if (! class_exists($classname)) {
        if (SYSTEM_ACT == 'mobile') {
            $file = ADDONS_ROOT . $modulename . "/mobile.php";
        } else {
            $file = ADDONS_ROOT . $modulename . "/web.php";
        }
        if (! is_file($file)) {
            exit('ModuleSite Definition File Not Found ' . $file);
        }
        require $file;
    }
    if (! class_exists($classname)) {
        exit('ModuleSite Definition Class Not Found ' . $file);
    }
    $class = new $classname();
    $class->module = $name;
    $class->inMobile = SYSTEM_ACT == 'mobile';
    if ($class instanceof BjModule) {
        if (! empty($class)) {
            if (isset($_GP['do'])) {
                if (SYSTEM_ACT == 'mobile') {
                    $class->inMobile = true;
                } else {
                    if ($name != "public") {
                        checklogin();
                    }
                    
                    $class->inMobile = false;
                    
                    if (checkrule($modulename, $_GP['do'], $_GP['op']) == false) {
                        message("您没有权限操作此功能");
                    }
                }
                $method = 'do_' . $_GP['do'];
            }
            $class->module = $modulename;
            if (method_exists($class, $method)) {
                $class->$method();
            } else {
                exit($method . " no this method");
            }
        }
    } else {
        exit('BjModule Class Definition Error');
    }
}

$sets = globaSetting();
