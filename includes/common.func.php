<?php

/*
  清理相关
 */

// 判断请求是否为AJAX请求，可在此处叠加判断
function checkIsAjax() {

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        if ('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
            return true;
    }
    if (isset($_SERVER['HTTP_REQUEST_TYPE']) && $_SERVER['HTTP_REQUEST_TYPE'] == "ajax") {
        return true;
    }
    return false;
}

/***
 * @param string $name_key 消息的key
 * @param string $module  对应消息模块文件
 * @param string $replace  可以替换消息体中的占位符号  如消息模板（你输入的@@@不能为空）   replace可以是   密码  分类名  等等
 * @return string
 */
function LANG($name_key = '',$module = 'common',$replace = '') {
    if (empty($name_key)) { // 若空则直接返回
        return $name_key;
    } 

    $default_lang = 'zh-cn';
    $module       = $module ?: 'common';
    //先加载公用语言包 先写死zh-cn，以后做多国可以用cookie来对应其他国家的文件夹
    $_lang = include_once  WEB_ROOT . '/language/'.$default_lang.'/'.$module.'.lang.php';
    if (isset($_lang[$name_key])) {
        if(empty($replace))
            return $_lang[$name_key];
        else
            return str_replace('@@@',$replace,$_lang[$name_key]);
    }
    //都没有则直接返回
    return $name_key;
}

//金额格式化  存入数据库的时候 type 1  页面展示的时候 type 0
function FormatMoney($money,$type=1){
    if(empty($money)) return sprintf("%.2f",0);
    if($type == 1)
    {
        $money = $money * 100;
    }
    else
    {
        $money = sprintf("%.2f",$money/100);
    }
    return $money;
}

/**
 * 生成sql语句，如果传入$in_cloumn 生成格式为 IN('a', 'b', 'c')
 * @param $data 条件数组或者字符串
 * @param $front 连接符
 * @param $in_column 字段名称
 * @return string
 */
function to_sqls($data, $front = ' AND ', $in_column = false) {
    if($in_column && is_array($data)) {
        $ids = '\''.implode('\',\'', $data).'\'';
        $sql = "$in_column IN ($ids)";
        return $sql;
    } else {
        if ($front == '') {
            $front = ' AND ';
        }
        if(is_array($data) && count($data) > 0) {
            $sql = '';
            foreach ($data as $key => $val) {
                $sql .= $sql ? " $front `$key` = '$val' " : " `$key` = '$val' ";
            }
            return $sql;
        } else {
            return $data;
        }
    }
}
