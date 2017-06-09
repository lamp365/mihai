<?php
/*
其余杂乱公用方法
*/

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
/**
 * 对数组进行 json_encode编码 可处理为编码的时候不让中文乱码
 * @param mixed value 待编码的 value ，除了resource 类型之外，可以为任何数据类型，该函数只能接受 UTF-8 编码的数据
 * @return string 返回 value 值的 JSON 形式
 */
function json_encode_ex($value)
{
    if (version_compare(PHP_VERSION,'5.4.0','<'))
    {
        $str = json_encode($value);
        $str = preg_replace_callback(
            "#\\\u([0-9a-f]{4})#i",
            function($matchs)
            {
                return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
            },
            $str
        );
        return $str;
    }
    else
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}



/**
 * 二维数组根据字段进行排序
 * @params array $array 需要排序的数组
 * @params string $field 排序的字段
 * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
 */
function arraySequence($array, $field, $sort = 'SORT_DESC')
{
    $arrSort = array();
    if(empty($array)){
        return $arrSort;
    }
	foreach ($array as $uniqid => $row) {
		foreach ($row as $key => $value) {
			$arrSort[$key][$uniqid] = $value;
		}
	}
	array_multisort($arrSort[$field], constant($sort), $array);
	return $array;
}

/**
 * 用于检测请求的次数，防止某个活动请求，被恶意用工具刷
 * 在规定的时间 60秒（1分钟）内，最多只能请求5次。。不然认为是恶意行为
 * @param string $key
 * @param int $limit_time   给一个规定时间间隔 单位秒
 * @param int $limit_num    给一个上限次数
 * @return bool   返回true  or  false  假则说明，不准许请求了，
 * example   if(!check_request_times('miyou')){  die("对不起，您的请求过于频繁！"); }
 */
function check_request_times($key='default',$limit_num=5,$limit_time=60){
    $client_ip  = getClientIP();
    $client_key = $client_ip."_".$key;
    if(class_exists('Memcached')) {
        $memcache = new Mcache();
        $mem_info = $memcache->get($client_key);
        if($mem_info){
            $mem_info    = unserialize($mem_info);
            $request_num = $mem_info['request_num'];
            $first_time  = $mem_info['first_time'];
            $diff_time = time() - $first_time;

            if($diff_time < $limit_time){
                //在规定时间间隔内 请求次数如果超过限制次数，我们认为是 刷的可能
                if($request_num > $limit_num){
                    return false;
                }else{
                    $request_num++;
                    $data = array('request_num'=>$request_num,'first_time'=>$first_time);
                    $memcache->set($client_key,serialize($data),3600*2);
                    return true;
                }
            }else{
                //如果下次请求，已经超过了 规定的间隔时间， 可以再次请求，缓存重置
                $data = array('request_num'=>1,'first_time'=>time());
                $memcache->set($client_key,serialize($data),time()+3600*2);
                return true;
            }
        }else{
            //首次访问
            $data = array('request_num'=>1,'first_time'=>time());
            $memcache->set($client_key,serialize($data),time()+3600*2);
            return true;
        }
    }else{
        $cookie      = new LtCookie();
        $cookie_info = $cookie->getCookie($client_key);
        if($cookie_info){
            $cookie_info = unserialize($cookie_info);
            $request_num = $cookie_info['request_num'];
            $first_time  = $cookie_info['first_time'];
            $diff_time = time() - $first_time;

            if($diff_time < $limit_time){
                //在规定时间间隔内 请求次数如果超过限制次数，我们认为是 刷的可能
                if($request_num > $limit_num){
                    return false;
                }else{
                    $request_num++;
                    $data = array('request_num'=>$request_num,'first_time'=>$first_time);
                    $cookie->setCookie($client_key,serialize($data),time()+3600*2);
                    return true;
                }
            }else{
                //如果下次请求，已经超过了 规定的间隔时间， 可以再次请求，缓存重置
                $data = array('request_num'=>1,'first_time'=>time());
                $cookie->setCookie($client_key,serialize($data),time()+3600*2);
                return true;
            }
        }else{
            //首次访问
            $data = array('request_num'=>1,'first_time'=>time());
            $cookie->setCookie($client_key,serialize($data),time()+3600*2);
            return true;
        }
    }

}



/**
 * 多个数组的笛卡尔积
 * @param unknown_type $data
 */
function combineDika() {
    $data = func_get_args();
    $data = current($data);
    $cnt  = count($data);
    $result = array();
    $arr1   = array_shift($data);
    foreach($arr1 as $key=>$item)
    {
        $result[] = array($item);
    }

    foreach($data as $key=>$item)
    {
        $result = combineArray($result,$item);
    }
    return $result;
}
/**
 * 两个数组的笛卡尔积
 * @param unknown_type $arr1
 * @param unknown_type $arr2
 */
function combineArray($arr1,$arr2) {
    $result = array();
    foreach ($arr1 as $item1)
    {
        foreach ($arr2 as $item2)
        {
            $temp = $item1;
            $temp[] = $item2;
            $result[] = $temp;
        }
    }
    return $result;
}


/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key 加密密钥
 * @param int    $expire 过期时间 单位 秒
 * @return string
 */
function cbd_encrypt($data, $key = '', $expire = 0)
{
    $key = md5($key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace(array('+', '/', '='), array('%', '_', ''), base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是myblog365_encrypt方法加密的字符串）
 * @param  string $key 加密密钥
 * @return string
 */
function cbd_decrypt($data, $key = '')
{
    $key = md5($key);
    $data = str_replace(array('%', '_'), array('+', '/'), $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);

}

/**
 * 获取随机ip
 * @return string
 */
function getRandIp(){
    $ip_long = array(
        array('607649792', '608174079'), //36.56.0.0-36.63.255.255
        array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
        array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
        array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
        array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
        array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
        array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
        array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
        array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
        array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
    );
    $rand_key = mt_rand(0, 9);
    $ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
    return $ip;
}