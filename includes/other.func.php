<?php
/*
其余杂乱公用方法
*/

function noises(){
     echo 'noises';
}

function fileext($file)
{
    return pathinfo($file, PATHINFO_EXTENSION);
}

function dump($varVal, $isExit = FALSE){
    ob_start();
    var_dump($varVal);
    $varVal = ob_get_clean();
    $varVal = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $varVal);
    echo '<pre>'.$varVal.'</pre>';
    $isExit && exit();
}

// 根据字段排序数组
function array_order($arrUsers, $field, $direction='SORT_ASC') {
    $sort = array(  
        'direction' => $direction, //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
        'field'     => $field,       //排序字段  
    );
    $arrSort = array();
    foreach($arrUsers AS $uniqid => $row) {  
        foreach($row AS $key=>$value) {  
            $arrSort[$key][$uniqid] = $value;  
        }
    }
    if($sort['direction']) {  
        array_multisort($arrSort[$sort['field']], constant($sort['direction']), $arrUsers);  
    }

    return $arrUsers;
}

function getClientIP()
{
    static $ip = '';
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_CDN_SRC_IP'])) {
        $ip = $_SERVER['HTTP_CDN_SRC_IP'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] as $xip) {
            if (! preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    }
    return $ip;
}

function is_mobile_request()
{
    $mobile = new MobileDetect();
    if($mobile->isMobile()){
        return true;
    }else{
        return false;
    }
    /*$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
    $mobile_browser = '0';
    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
        $mobile_browser ++;
    if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
        $mobile_browser ++;
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
        $mobile_browser ++;
    if (isset($_SERVER['HTTP_PROFILE']))
        $mobile_browser ++;
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ',
        'acs-',
        'alav',
        'alca',
        'amoi',
        'audi',
        'avan',
        'benq',
        'bird',
        'blac',
        'blaz',
        'brew',
        'cell',
        'cldc',
        'cmd-',
        'dang',
        'doco',
        'eric',
        'hipt',
        'inno',
        'ipaq',
        'java',
        'jigs',
        'kddi',
        'keji',
        'leno',
        'lg-c',
        'lg-d',
        'lg-g',
        'lge-',
        'maui',
        'maxo',
        'midp',
        'mits',
        'mmef',
        'mobi',
        'mot-',
        'moto',
        'mwbp',
        'nec-',
        'newt',
        'noki',
        'oper',
        'palm',
        'pana',
        'pant',
        'phil',
        'play',
        'port',
        'prox',
        'qwap',
        'sage',
        'sams',
        'sany',
        'sch-',
        'sec-',
        'send',
        'seri',
        'sgh-',
        'shar',
        'sie-',
        'siem',
        'smal',
        'smar',
        'sony',
        'sph-',
        'symb',
        't-mo',
        'teli',
        'tim-',
        'tosh',
        'tsm-',
        'upg1',
        'upsi',
        'vk-v',
        'voda',
        'wap-',
        'wapa',
        'wapi',
        'wapp',
        'wapr',
        'webc',
        'winw',
        'winw',
        'xda',
        'xda-'
    );
    if (in_array($mobile_ua, $mobile_agents))
        $mobile_browser ++;
    if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
        $mobile_browser ++;
        // Pre-final check to reset everything if the user is on Windows
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
        $mobile_browser = 0;
        // But WP7 is also Windows, with a slightly different characteristic
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
        $mobile_browser ++;
    if ($mobile_browser > 0)
        return true;
    else
        return false;*/
}
/**
 * 获取访问设备的类型
 * 最好有涉及到数据存储是 1代表安卓 2代表ios 3代表平板 4代表PC 5代表wap
 * @param string $is_app  如果app调用的则 is_app要有值 任意值
 * @param string $show_str  $show_str如果入库用 不给值，如果需要页面展示具体文字信息 则给值
 * @return string
 * 更多方法查看 http://demo.mobiledetect.net/
 */
function get_mobile_type($is_app='',$show_str = ''){
    if($show_str){
        $typeArr = array('安卓','IOS','平板','PC','WAP');
    }else{
        $typeArr = array(1,2,3,4,5);
    }
    $mobile = new MobileDetect();
    if($mobile->isMobile()){
        //手机端分ios  安卓  wap
        if($is_app){
            if($mobile->isAndroidOS()){
                return $typeArr[0];
            }else if($mobile->isiOS()){
                return $typeArr[1];
            }
        }else{
            //手机端wap
            return $typeArr[4];
        }
    }else if($mobile->isTablet()){
        return $typeArr[2];
    }else{
        return $typeArr[3];
    }
}

/**
 * 判断是否是该设备类型的
 * @param $type
 * @return bool|MobileDetect
 * 返回true or false
 */
function check_mobile_type($type){
    $mobile = new MobileDetect();
    switch($type){
        case 'ios':
            $res = $mobile->isiOS();
            break;
        case 'Android':
            $res = $mobile->isAndroidOS();
            break;
        case 'tablet':
            $res = $mobile->isTablet();
            break;
        case 'safari':
            $res = $mobile->isSafari();
            break;
        case 'uc':
            $res = $mobile->isUCBrowser();
            break;
    }
    return $res;
}

function is_had_mess()
{
    // 先判断是否登录
    $login = is_login_account();
    $urls = $_SERVER['PHP_SELF'];
    $url = explode('/', $urls);
    $end = end($url);
    if (! $login) {
        if (empty($_COOKIE['mess']) && $_GET['do'] != 'messlist' && $_GET['mod'] != 'site' && $end != 'admin.php') {
            message("", WEBSITE_ROOT . 'index.php?mod=mobile&name=shopwap&do=messlist');
        }
    }
    // 如果没登录，再判断是否有食堂信息
    // 最后得出结论跳出
}
//is_had_mess();

function random($length, $nc = 0)
{
    $random = rand(1, 9);
    for ($index = 1; $index < $length; $index ++) {
        $random = $random . rand(1, 9);
    }
    return $random;
}

/**
 * 生成包含数字字母的随机数
 * 
 * @param $len:int 随机数长度
 * 
 */
function randString($len)
{
	$string = md5(uniqid('',true).rand(1,100000000));

	return substr($string,0,$len);
}

if (file_exists(WEB_ROOT . '/config/config.php') && file_exists(WEB_ROOT . '/config/install.link')) {
    require (WEB_ROOT . '/system/common/lib/lib.php');
}


/**
 * 方便打印输出查看数据
 * 使用方式：pp($val1,$arr,$val2)等等可以连续打印多个
 */
function pp()
{
    $arr = func_get_args();
    echo '<pre>';
    foreach($arr as $val) {
        print_r($val);
        echo '</pre>';
        echo '<pre>';
    }
    echo '</pre>';
}

function ppd()
{
    $arr = func_get_args();
    echo '<pre>';
    foreach($arr as $val) {
        print_r($val);
        echo '</pre>';
        echo '<pre>';
    }
    echo '</pre>';
    die();
}

function getOpenshopAccessKey($openid){
    $openid.="@@@kevin";
    return DESService::instance()->encode($openid);
}

function decodeOpenshopAccessKey($accesskey){
    $code = DESService::instance()->decode($accesskey);
    $codeArr = explode('@@@',$code);
    if($codeArr['1']!= 'kevin'){
        return false;
    }else{
        $openid = $codeArr['0'];
        return $openid;
    }
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $slice;
//    return $suffix ? $slice.'...' : $slice;
}


/**
 * 记录日志
 *
 * @param $logMsg 日志信息
 * @param $logFile 日志文件
 *
 */
function logRecord($logMsg,$logFile) {

	if(WRITE_LOG)
	{
		error_log(date('[c]')."{$logMsg}\r\n", 3, WEB_ROOT.'/logs/'.$logFile.date('Y-m-d'));
	}
}

/**
 * @param string $type
 * @return int
 * @content返回一个请求时某种设备访问的
 */
function getSystemType($type = ''){
    if(empty($type)){
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
             return 3;   //ios
        }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
            return 2;  //android
        }else{
            return 1; //pc
        }
    }else{
        $system = array(1=>'PC',2=>'Android','3'=>'ios');
        return $system[$type];
    }

}

/**
 * @return bool
 * @content限制一分钟内 退货协商时只能回复10下 防止刷
 */
function setAfterSaleDialogNum(){
    if(isset($_COOKIE['dialog']) && !empty($_COOKIE['dialog'])){
        $num = $_COOKIE['dialog'];
        $diff_time = time() - $_COOKIE['firstTime'];

        if($diff_time < 60){
            if($num >5){
                return false;
            }else{
                $num++;
                setcookie('dialog',$num,time()+3600);
                return true;
            }
        }else{
            setcookie('dialog',1,time()+3600);
            setcookie('firstTime',time(),time()+3600);
            return true;
        }
    }else{
        setcookie('dialog',1,time()+3600);
        setcookie('firstTime',time(),time()+3600);
        return true;
    }
}

/**
 * wap页面控制新手礼弹窗2次
 * @return bool
 */
function showNewMemberBonus(){
    if(isset($_COOKIE['show_bonus_num']) && !empty($_COOKIE['show_bonus_num'])){
        $num = $_COOKIE['show_bonus_num'];
        if($num >= 2){
            return false;
        }else{
            $num += 1;
            setcookie('show_bonus_num',$num);
            return true;
        }
    }else{
        setcookie('show_bonus_num',1);   //不设置过期时间，随着关闭浏览器而关闭
        return true;
    }
}

function tosize($size) {
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