<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2017/4/21
 * Time: 14:23
 */
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


//去除反斜线
function stripslashes_deep($value)
{
    if(is_array($value)){
         $value = array_map('stripslashes_deep', $value);
    }else{
        //第一 去除反斜线
        $value  = stripslashes($value);
        //第二 可以继续给value值扩展相关的 操作
    }
    return $value;
}

/**
 * 数据数据组中 key 带有<  和 >字符的转为实体
 * value值 带有<  和 >字符的转为实体  并把实体的 &amp; 换为 &
 * @param $var
 * @return array|mixed
 */
function irequestsplite($var)
{
    if (is_array($var)) {
        foreach ($var as $key => $value) {
            $var[htmlspecialchars($key)] = irequestsplite($value);
        }
    } else {
        //把  < >转为实体
        $var    = str_replace('&amp;', '&', htmlspecialchars($var, ENT_QUOTES));
        //替换一些字符
        $no     = '/%0[0-8bcef]/';
        $var    = preg_replace ( $no, '', $var );
        $no     = '/%1[0-9a-f]/';
        $var    = preg_replace ( $no, '', $var );
        $no     = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';
        $var    = preg_replace ( $no, '', $var );
    }
    return $var;
}

/**
 * 数据安全操作
 */
function init_stripslashes_deep()
{
    if(MAGIC_QUOTES_GPC){
        $_POST     = empty($_POST)    ? $_POST    : array_map('stripslashes_deep', $_POST);
        $_GET      = empty($_GET)     ? $_GET     : array_map('stripslashes_deep', $_GET);
        $_COOKIE   = empty($_COOKIE)  ? $_COOKIE  : array_map('stripslashes_deep', $_COOKIE);
        $_REQUEST  = empty($_REQUEST) ? $_REQUEST : array_map('stripslashes_deep', $_REQUEST);
    }
    $_POST = empty($_POST) ? $_POST : irequestsplite($_POST);
    $_GET  = empty($_GET)  ? $_GET  : irequestsplite($_GET);
}

/**
 * 初始化一些配置
 */
function init_config()
{
    if (DEVELOPMENT) {
        ini_set('display_errors', '1');
        error_reporting(E_ALL ^ E_NOTICE);
    } else {
        error_reporting(0);
    }

    if (! session_id()) {
        $lifeTime = 24 * 3600 * 10;
        session_set_cookie_params($lifeTime);
        session_start();
    }
}


/**
 * 初始化数据库配置
 */
function init_dataconfig()
{
    $BJCMS_CONFIG = array();
    $bjconfigfile = WEB_ROOT . "/config/config.php";
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
    return $bjconfig;
}

/**
 * 启动控制器运行操作
 * @param $_GP
 * @param $_CMS
 */
function init_start_run($_GP,$_CMS)
{
    if(SYSTEM_ACT == 'mobile'){
        //表示前台  index表示后台
        $mobile_web = 'mobile';
    }else{
        $mobile_web = 'web';
    }
    $controller = strtolower($_GP['do']);
    $file = SYSTEM_ROOT.$_CMS['module'].'/class/'.$mobile_web.'/'.$controller.'.php';
    if (is_file($file)){
        include_once  $file;
        $name = "{$_CMS['module']}\\controller\\{$controller}";
        if(!class_exists($name)){

            $base = new \common\controller\basecontroller();
            $base->_baseEmpty("控制器{$name}文件写法有误，不是一个规范的类！");
        }

        $obj       = new $name();
        $function  = $_GP['op'] ?: 'index';
        //把 $_GP 的值全部赋值给基类 request成员
        if($_GET['name'] == 'api'){
            $obj->request  = get_api_parame($_GP);
        }else{
            $obj->request  = $_GP;
        }
        if(!method_exists($obj,$function)){
            
            $base = new \common\controller\basecontroller();
            $base->_baseEmpty("控制器{$name}没有{$function}该方法！");
        }

        call_user_func(array($obj, $function));

    }else{
        $base = new \common\controller\basecontroller();
        $base->_baseEmpty("控制器文件{$file}不存在");
    }
}

function get_api_parame($data){
    //api的数据格式
    /**   array(
            do => shop,
     *      op=>index
     *      name=>seller
     *      data=>{对象串}
        )
     */
    $item = array();
    $item = json_decode(html_entity_decode($data['data']),true);
    unset($data['data']);
    unset($data['name']);
    foreach($data as $key => $value){
        $item[$key] = $value;
    }
    return $item;
}

/**
 * 插件的处理 如 addon 下插件安装或者更新
 */
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

/*
function init_routeCheck()
{
    $allow_module = array('seller','shopwap');

    $url = ltrim($_SERVER['REQUEST_URI'],'/');
    $url = str_replace('index.php/','',$url);
    $url = rtrim($url,'.html');

    //获取模块
    $temp_url_arr    = explode('/',$url);
    $temp_module     = $temp_url_arr[0];
    //不在列表的不用匹配路由
    if(!in_array($temp_module,$allow_module)){
        return true;
    }

    // 路由处理
    $routes =   array(
        'seller/p_sales/:id'         => 'seller/product/sales',
        'seller/p_sales/:id/:named'  => 'seller/product/sales',
        'seller/p_total/:id'         => 'seller/product/total',
        'seller/p_total/:id/:token'  => 'seller/product/sales',
        'seller/sales'               => 'seller/product/sssss',
    );

    $_GET = array();  //这里要定义为空  重新组装
    $is_right = true;

    if(!empty($routes)) {
        //先找静态路由配置
        if(array_key_exists($url,$routes)){
            $pipei_route = $routes[$url];
            $route_arr = explode('/',$pipei_route);
            $_GET['name'] = $route_arr[0];
            $_GET['do']   = $route_arr[1];
            $_GET['op']   = $route_arr[2];

            return true;

        }
        // 没有一致的静态路由 再动态路由处理
        foreach ($routes as $rule=>$route){
            if(!strpos($rule,':')){
                //不匹配静态路由
                continue;
            }

            //前面这一节  属于静态配置  如  'seller/p_sales/:id/:named'   $cut_str = 'seller/p_sales'
            $cut_str = substr($rule,0,strpos($rule,':')-1);
            //匹配到该路由 同时 路由规则跟实际地址 的 斜线位数应该一样多
            if(strstr($url,$cut_str) && count(explode('/',$rule)) == count(explode('/',$url)) ){
                //url和规则各去除前面的静态 配置
                $cut_url  = substr($url,strpos($rule,':'));     //得到 2/one
                $cut_rule = substr($rule,strpos($rule,':'));    //得到 :id/:name
                $res  = checkRouteUrlMatch($cut_url,$cut_rule);
                if($res){
                    $route_arr = explode('/',$route);
                    $_GET['name'] = $route_arr[0];
                    $_GET['do']   = $route_arr[1];
                    $_GET['op']   = $route_arr[2];

                }else{
                    $is_right  = false;
                }
            }
        }
    }
    return $is_right;
}

function checkRouteUrlMatch($url,$rule) {


    $m1 = explode('/',$url);
    $m2 = explode('/',$rule);
    $var = array();

    foreach ($m2 as $key=>$val){

        if(':' == substr($val,0,1)) {// 动态变量
            $name = substr($val, 1);
            $var[$name] = isset($m1[$key])?$m1[$key]:'';
        }else{
            $name       = $val;
            $var[$name] = isset($m1[$key])?$m1[$key]:'';
        }
    }
    // 成功匹配后返回URL中的动态变量数组
    $_GET   =  array_merge($var,$_GET);
    return true;
}*/