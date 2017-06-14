<?php
/*
error操作
*/

function error($code, $msg = '')
{
    return array(
        'errno' => $code,
        'message' => $msg
    );
}

function is_error($data)
{
    if (empty($data) || ! is_array($data) || ! array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
        return false;
    } else {
        return true;
    }
}

function showAjaxMess($code,$msg = ''){
    $error = array(
        'errno' => $code,
        'message' => $msg
    );
//    return json_encode($error,JSON_UNESCAPED_UNICODE);  防止乱码，5.4以上才可以
    return json_encode($error);
}