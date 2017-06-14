<?php
/*
数据库操作
*/

function mysqldb()
{
    global $_CMS;
    static $db;
    if (empty($db)) {
        $db = new PdoUtil($_CMS['config']['db']);
    }
    return $db;
}

function mysqld_query($sql, $params = array())
{
    return mysqldb()->query($sql, $params);
}

function mysqld_select($sql, $params = array())
{
    return mysqldb()->fetch($sql, $params);
}

function mysqld_selectcolumn($sql, $params = array(), $column = 0)
{
    return mysqldb()->fetchcolumn($sql, $params, $column);
}

function mysqld_selectall($sql, $params = array(), $keyfield = '')
{
    return mysqldb()->fetchall($sql, $params, $keyfield);
}

function mysqld_update($table, $data = array(), $params = array(), $orwith = 'AND')
{
    return mysqldb()->update($table, $data, $params, $orwith);
}

function mysqld_insert($table, $data = array(), $es = FALSE)
{
    return mysqldb()->insert($table, $data, $es);
}

function mysqld_delete($table, $params = array(), $orwith = 'AND')
{
    return mysqldb()->delete($table, $params, $orwith);
}

function mysqld_insertid()
{
    return mysqldb()->insertid();
}

function mysqld_batch($sql)
{
    return mysqldb()->excute($sql);
}

function mysqld_fieldexists($tablename, $fieldname = '')
{
    return mysqldb()->fieldexists($tablename, $fieldname);
}

function mysqld_indexexists($tablename, $indexname = '')
{
    return mysqldb()->indexexists($tablename, $indexname);
}

function table($table)
{
    return "`squdian_{$table}`";
}
function begin(){
    return mysqldb()->begin();
}
function commit(){
    return mysqldb()->commit();
}
function rollback(){
    return mysqldb()->rollback();
}
/**
 * 根据条件获得单条数据
 * $table 表名
 * $condition 条件
 * $param 要取的参数
 * */
function getSingleDate($table,$param="*",$condition,$front="AND"){
    if (empty($table) || empty($condition)) return false;
    if ($condition && is_array($condition)){
        $con = to_sqls($condition,$front);
    }else {
        $con = $condition;
    }
    $sql = "SELECT {$param} FROM ".table($table)." WHERE {$con} ";
    $info = mysqld_select($sql);
    return $info;
}