<?php
/**
* 快递相关
* @author WZW
*/

require_once WEB_ROOT . '/includes/lib/Snoopy.class.php';
// KEY
define('KUAIDI100_KEY', '1303b3f0e4e45635');


// 查询快递状态
function get_expressage($com, $nu) {
    if (empty($com) or empty($nu)) {
        return null;
    }

//    $url = 'http://www.kuaidi100.com/api?id='.KUAIDI100_KEY.'&com='.$com.'&nu='.$nu;  该地址有误查询不到
    $url = "http://www.kuaidi100.com/query?type={$com}&postid={$nu}";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER,0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl, CURLOPT_TIMEOUT,5);
    $get_content = curl_exec($curl);
    curl_close($curl);

    return json_decode($get_content, true);
}

// 直接抓取物流状态
function fetch_expressage($com, $nu) {
    if ($com == 'beihai') {
        return fetch_beihai($nu);
    }
    $cookie = "Hm_lvt_22ea01af58ba2be0fec7c11b25e88e6c=1473822407,1474165404; kd_history=%5B%7B%22code%22%3A%22xlobo%22%2C%22nu%22%3A%22DB733245339US%22%2C%22time%22%3A%222016-11-02T07%3A22%3A43.295Z%22%2C%22ischeck%22%3A0%7D%2C%7B%22code%22%3A%22xlobo%22%2C%22nu%22%3A%22DB693246347US%22%2C%22time%22%3A%222016-11-02T07%3A21%3A51.930Z%22%2C%22ischeck%22%3A0%7D%2C%7B%22code%22%3A%22xlobo%22%2C%22nu%22%3A%22DB693242610US%22%2C%22time%22%3A%222016-11-02T07%3A21%3A40.518Z%22%2C%22ischeck%22%3A0%7D%2C%7B%22code%22%3A%22baishiwuliu%22%2C%22nu%22%3A%22280535485825%22%2C%22time%22%3A%222016-09-18T02%3A23%3A29.866Z%22%2C%22ischeck%22%3A%221%22%7D%5D; WWWID=WWWEA840F563DF023270C9FE24365912E6C; sortStatus=0";
    $url = "http://www.kuaidi100.com/query?type=$com&postid=$nu&id=1";
    $method = "GET";
    $header = "Host: www.kuaidi100.com$
Connection: keep-alive$
Accept: */*$
X-Requested-With: XMLHttpRequest$
User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36$
Referer: http://www.kuaidi100.com/$
Accept-Encoding: gzip, deflate, sdch$
Accept-Language: zh-CN,zh;q=0.8$
Cookie: ".$cookie;
    // 抓取
    $response = fetch($url, $method, array(), $header, $cookie);

    $query = json_decode($response->results, true);

    return $query;
}

//根据物流单号获取物流code
function get_expresscode($nu) {
    
    $cookie = "WWWID=WWW04F16A4BCBCA3788F50FBC8A450C8284; __gads=ID=1495d93e17bc9c4d:T=1495612786:S=ALNI_MY9KPCvc-68CBHlVr_dowLWDD2Ukg; sortStatus=0; Hm_lvt_22ea01af58ba2be0fec7c11b25e88e6c=1495612726,1495613206; Hm_lpvt_22ea01af58ba2be0fec7c11b25e88e6c=1495613206";
    $url = "http://www.kuaidi100.com/autonumber/autoComNum?text=$nu";
    $method = "GET";
    $header = "Host: www.kuaidi100.com$
Connection: keep-alive$
Accept: */*$
X-Requested-With: XMLHttpRequest$
User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36$
Referer: http://www.kuaidi100.com/$
Accept-Encoding: gzip, deflate$
Accept-Language: zh-CN,zh;q=0.8$
Cookie: ".$cookie;
    // 抓取
    $response = fetch($url, $method, array(), $header, $cookie);

    $query = json_decode($response->results, true);

    return $query;
}

// 贝海国际物流信息
function fetch_beihai($nu) {
    $client_id = "868fe395-4e0e-45c5-aad8-bc3a740fb3d7";
    $client_secret = "APvYM8Mt5Xg1QYvker67VplTPQRx28Qt/XPdY9D7TUhaO3vgFWQ71CRZ/sLZYrn97w==";
    $access_token = "AMllKRKndbCzl2BzvxcSg9gQHV+tNT0K5Lp1A3zD4J9F2OPdAVT5sKLBVxeZQk+8bA==";
    
    $msg_param = json_encode(array('BillCodes' => array($nu)));
    $sign = md5(base64_encode(strtolower($client_secret.$msg_param.$client_secret)));

    $method = "method=xlobo.status.get&v=1.0&msg_param=".$msg_param."&client_id=".$client_id."&sign=".$sign."&access_token=".$access_token;
    $url = "http://bill.open.xlobo.com/api/router/rest";

    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $method );
    $query = curl_exec ( $ch );
    curl_close ( $ch );
    
    $ary_query = json_decode($query, true);
    $data = array();
    $data['nu'] = $ary_query['Result'][0]['BillCode'];
    $data['data'] = array();
    $data['company'] = "贝海国际速递";
    foreach ($ary_query['Result'][0]['BillStatusList'] as $vvv) {
        $a = array();
        $a['time'] = $vvv['StartTime'];
        $a['context'] = $vvv['Status']." ".$vvv['StatusDetail'];
        $data['data'][] = $a;
    }

    return $data;
}

// 抓取处理方法
function fetch($url, $method, $data, $header, $cookie) {
    // 创建
    $client = new Snoopy();
//        $client->maxredirs = 0;

    // headers
    $headers = array();
    foreach(explode('$', $header) as $x) {
        $x = trim($x);
        if(strpos($x, ':') != False) {
            $t = explode(':', $x, 2);
            $headers[$t[0]] = $t[1];
        }
    }
    $client->headers = $headers;

    // cookies
    $session = array();
    foreach(explode(';', $cookie) as $x) {
        $x = trim($x);
        if(strpos($x, '=') != False) {
            $t = explode('=', $x, 2);
            $session[$t[0]] = $t[1];
        }
    }

    foreach($session as $k => $v) {
        $client->cookies[$k] = $v;
    }


    $response = '';

    // get post
    if($method == 'GET') {
        $response = $client->fetch($url);
    }
    if($method == 'POST') {
        $client->_submit_type = 'application/json';
        $data = json_encode($data, true);
        $response = $client->submit($url, $data);
    }

    return $response;
}