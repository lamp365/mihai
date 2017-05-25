<?php

/**
 * 向app端返回数据
 * @param $result Array
 *建议用 ajaxReturnData
 */
function apiReturn($result) {
    return json_encode_ex($result);
}

/**
 * Ajax方式返回数据到客户端
 * @param $code  1 表示成功  0表示失败 2表示app需要重跳转登录
 * @param string $message
 * @param array $arrayData
 * @param string $type
 */
function ajaxReturnData($code, $message='',$arrayData=array(), $type = 'JSON') {
    $data['errno']  = $code;
    $data['message']= $message;
    $data['data']   = $arrayData;

    switch (strtoupper($type)) {
        case 'JSON' :
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode_ex($data));
        case 'XML' :
            // 返回xml格式数据
            header('Content-Type:text/xml; charset=utf-8');
            exit(xml_encode($data));
        case 'JSONP':
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            $handler = isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
            exit($handler . '(' . json_encode($data) . ');');
        case 'EVAL' :
            // 返回可执行的js脚本
            header('Content-Type:text/html; charset=utf-8');
            exit($data);
    }
}


?>