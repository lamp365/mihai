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
 * $where 条件
 * $param 要取的参数
 * return 一维数组
 * */
function getOne($table,$where,$param="*",$front="AND"){
    if (empty($table)) return false;
    if (is_array($where)) $where = to_sqls($where,$front);
    
    $sql = "SELECT {$param} FROM ".table($table);
    $sql .= ($where) ? " WHERE $where" : '';
    return mysqld_select($sql);
}

/**
 * 获得多条数据
 * $table 表名
 * $where 条件
 * $field 要取的参数
 * return 二维数组
 * */
function getAll($table,$where,$field='*',$orderby = false){
    if (empty($table)) return false;
    if (is_array($where)) $where = to_sqls($where);
    $sql = "SELECT {$field} FROM ".table($table);
    $sql .= ($where) ? " WHERE $where" : '';
    $sql .= ($orderby) ? " ORDER BY $orderby" : '';
    return mysqld_selectall($sql);
}