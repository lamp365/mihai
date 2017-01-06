<?php
/*
curl
*/
function http_get($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    $result = curl_exec($ch); 
    curl_close($ch);
    return $result;
}

function http_post($url, $post_data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function getAreaByIp($ip){
    $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$ip;
    $location = http_get($url);
    $location = json_decode( $location,true);
    $area = '';
    if(!empty($location)){
        $area = $location['province'].' '.$location['city'];
    }
    return $area;
}

/**
CREATE TABLE IF NOT EXISTS `squdian_traffic_count` (
`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`openid` VARCHAR(50) DEFAULT '',
`refer_url` VARCHAR(255)  DEFAULT '' COMMENT '来源url',
`page_url` VARCHAR(255)  DEFAULT '' COMMENT '当前url',
`system` TINYINT(1)  DEFAULT '1' COMMENT '类型 1安卓 2ios 3平板 4pc 5wap',
`count_type` TINYINT(1)  DEFAULT '1' COMMENT '类型 1宝贝页面访问 2登录 3注册  4下载app',
`shop_id`  INT(10) DEFAULT '0' COMMENT '宝贝id dishid',
`ip`  VARCHAR(20) DEFAULT '' COMMENT 'ip',
`address` VARCHAR(30) DEFAULT '' COMMENT '地区',
`createtime` INT(10) COMMENT '时间',
PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='商城访问量统计';
 * 该表以后数据量会很大 只存登录 注册  下载app宣传页  商品访问时才做插入记录  其他页面不用插入
 */
function trafficCount(){
    $insert_arr  = array('login','regedit','app','shop');
    $insert_mark = '';
    $ip     = getClientIP();
    $address= getAreaByIp($ip);
    $system = get_mobile_type();

    $page_url  = trim(WEBSITE_ROOT,'/').$_SERVER['REQUEST_URI'];
    $refer_url = $_SERVER['HTTP_REFERER'];
    $openid = checkIsLogin();

    //获取访问的商品id
    $shop_id = 0;
    if($_GET['do'] == 'groupbuy' && $_GET['op']=='detail_group' && isset($_GET['id'])){
        $insert_mark = 'shop';
        $shop_id     = $_GET['id'];
        $count_type  = '1';
    }else if($_GET['do'] == 'detail' && $_GET['op']=='dish' && isset($_GET['id'])){
        $insert_mark = 'shop';
        $shop_id     = $_GET['id'];
        $count_type  = '1';
    }

    //获取访问类型
    if($_GET['do']=='regedit'){
        $insert_mark = 'regedit';
        $count_type = '3';
    }else if($_GET['do']== 'login'){
        $insert_mark = 'login';
        $count_type = '2';
    }else if($_GET['do']=='appdown'){
        if(is_mobile_request()){
            if($_GET['op'] == 'get_appversion'){
                //点击了下载
                $insert_mark = 'app';
                $count_type = '4';
            }else{
                //查看 未下载
                $count_type = '1';
            }
        }else{
            $insert_mark = 'app';
            $count_type = '4';
        }
    }
    if(in_array($insert_mark,$insert_arr)){
        mysqld_insert('traffic_count',array(
            'openid'    =>  $openid,
            'refer_url' => empty($refer_url) ? $page_url : $refer_url,
            'page_url'  => $page_url,
            'system'    => $system,
            'count_type'=> $count_type,
            'shop_id'   => $shop_id,
            'ip'        => $ip,
            'address'   => $address,
            'createtime'=> time()
        ));
    }

}

/**
 * 将uri字符串参数变为数组
 * m=content&c=index&a=lists&catid=6&area=0&author=0&h=0®ion=0&s=1&page=1
 * @param $query
 * @return array
 */
function convertUrlQuery($query)
{
    $queryParts = explode('&', $query);
    $params = array();
    foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
    return $params;
}
/**
 * 将参数变为字符串  uri形式
 * @param $array_query
 * @return string string 'm=content&c=index&a=lists&catid=6&area=0&author=0&h=0®ion=0&s=1&page=1'
 */
function getUrlQuery($array_query)
{
    $tmp = array();
    foreach($array_query as $k=>$param)
    {
        $tmp[] = $k.'='.$param;
    }
    $params = implode('&',$tmp);
    return $params;
}