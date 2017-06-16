<?php
/**
 *模型层:公共模型
 *Author:严立超 
 *   
 **/
namespace model;
use \PdoUtil;
class model extends PdoUtil{
    //数据表前缀
    protected $tablepre = '';
    //数据表名
    protected $table_name = '';
    
    public function __construct() {
        parent::__construct();
        global $_CMS;
        $this->tablepre = $_CMS['config']['db']['tablepre'];
        $this->table_name = $this->table_name;
        $this->full_table_name = $this->tablepre.$this->table_name;
    }
    /**
     * 获得单条数据
     * $where 条件string
     * $field 要取的参数
     * return 一维数组
     * */
    function getOne($where='',$field='*',$orderby = false,$group = false){
        if (is_array($where)) $where = to_sqls($where);
        $sql = "SELECT {$field} FROM `{$this->full_table_name}`";
        $sql .= ($where) ? " WHERE $where" : '';
        $sql .= ($group) ? " GROUP BY $group" : '';
        $sql .= ($orderby) ? " ORDER BY $orderby" : '';
        return $this->fetch($sql);
    }
    /**
     * 获得多条数据
     * $where 条件string
     * $field 要取的参数
     * return 二维数组
     * */
    function getAll($where='',$field='*',$orderby = false,$group = false){
        if (is_array($where)) $where = to_sqls($where);
        $sql = "SELECT {$field} FROM `{$this->full_table_name}`";
        $sql .= ($where) ? " WHERE $where" : '';
        $sql .= ($group) ? " GROUP BY $group" : '';
        $sql .= ($orderby) ? " ORDER BY $orderby" : '';
        return $this->fetchall($sql);
    }
    
}