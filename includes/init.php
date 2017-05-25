<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------

define('WEB_ROOT', str_replace("\\", '/', dirname(dirname(__FILE__))));
include_once WEB_ROOT . "/config/common.php";  //一些加载init 所需要的函数
include_once WEB_ROOT . "/config/definde.php"; //一些定义的常量
ob_start();
init_config();             //初始化一些 环境配置
init_stripslashes_deep();  //数据安全操作

$_GP = $_CMS = array();
$_GP = array_merge($_GET, $_POST, $_GP);

/************获取操作模块***************/
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

/****************获取操作的控制器****************/
if (empty($_GP['do'])) {
    if (empty($do)) {
        $_GP['do'] = 'index';
    } else {
        $_GP['do'] = $do;
    }
}
$pdo = $_CMS['pdo'] = null;
$bjconfig       = init_dataconfig();   //初始化数据库
$_CMS['config'] = $bjconfig;
$_CMS['module'] = $modulename;
$_CMS['account'] = $_SESSION["account"];


/********************** 引入公共加载 **************************/
require_once INCLUDES_ROOT . 'init.func.php';
$allow_module = array(
    //模块使用控制器方式
    'controller'  => array(
        'seller',
        'api',
    ),
    //模块不是使用控制器方式操作
    'files'     => array(
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
        'seller'
    ),
);
// 命名空间的使用，后期修改为映射，现在不考虑，先用数组
if(in_array($modulename,$allow_module['controller'])){
    //开始运行
    init_start_run($_GP,$_CMS);
}else if (in_array($modulename, $allow_module['files'])) {
    $classname = $modulename . "Addons";
    if (!class_exists($classname)) {
        if (SYSTEM_ACT == 'mobile') {
            require (COMMON_ROOT . 'mobile.php');
            $file = SYSTEM_ROOT . $modulename . "/mobile.php";
        } else {
            require (COMMON_ROOT . 'web.php');
            $file = SYSTEM_ROOT . $modulename . "/web.php";
        }
        if (! is_file($file)) {
            exit('The ModuleSite Definition File Not Found ' . $file);
        }
        require $file;
    }else{
        exit('ModuleSite Definition Class Not Found');
    }
	// 进入子类控制器
    $class = new $classname();
    if ($class instanceof BjSystemModule) {
			if (SYSTEM_ACT == 'mobile') {
				if($modulename == 'shopwap'){
					//可以用来操作一些 访问前台的信息
					formCheckToken();   //表单令牌，防止重复提交
				}
			} else {
				if (!in_array($modulename, array('public','seller'))) {
					checklogin();
				}                    
				if ($modulename != "modules" && !in_array($modulename, array('index','common','public','seller'))) {
					if (checkrule($modulename, $_GP['do'],$_GP['op']) == false) {
						message("您没有权限操作此功能",refresh(),'error');
					}
				}
			}
            if (method_exists($class, 'do_control')) {
                exit($class->do_control($_GP['do']));
            } else {
                exit("do_control no this method");
            }
    }
} else {
	// 插件扩展
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
                'do' => 'index'
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
            exit('This ModuleSite Definition File Not Found ' . $file);
        }
        require $file;
    }
    if (! class_exists($classname)) {
        exit('ModuleSite Definition Class Not Found ' . $file);
    }
    $class = new $classname();
    if ($class instanceof BjModule) {
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
		exit($method . " no this method");}
}

