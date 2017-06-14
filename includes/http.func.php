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

function http_post($url, $post_data,$header='')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if(!empty($header)){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    //用于文件上传
    if (class_exists('\CURLFile')) {
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
    } else {
        if (defined('CURLOPT_SAFE_UPLOAD')) {
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        }
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $data = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);
    if ($err) {
        die("cURL Error #:" . $err);
    } else {
        return $data;
    }

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

/**
 * 修改uri中的部分参数，再返回
 * @param $uri
 * @param $key
 * @param $val
 * @return string
 */
function changeParame($uri,$key,$val){
    $uri_arr = convertUrlQuery($uri);
    $uri_arr[$key] = $val;
    $uri = getUrlQuery($uri_arr);
    return $uri;
}