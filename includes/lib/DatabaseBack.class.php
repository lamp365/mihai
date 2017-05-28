<?php
/**
 * 数据库备份还原类
 * @author 刘建凡
 * @date 2016-11-12
 * Class DatabaseBack
 */
class DatabaseBack
{
    private $handler;
    private $config = array(
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'root',
        'password' => '',
        'database' => 'test',
        'charset' => 'utf8',
        'target' => 'sql.sql'
    );
    private $dir = 'databack';
    private $sqlname = '';
    private $tables = array();
    private $error;
    private $begin; //开始时间
    /**
     * 架构方法
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->begin = microtime(true);
        $config = is_array($config) ? $config : array();
        $this->config = array_merge($this->config, $config);
        //启动PDO连接
        $this->handler = mysqldb();
      /*  try
        {
            $this->handler = new PDO("mysql:host={$this->config['host']}:{$this->config['port']};dbname={$this->config['database']}", $this->config['user'], $this->config['password']);
        }
        catch (PDOException $e)
        {
            $this->error = $e->getMessage();
            return false;
        }
        catch (Exception $e)
        {
            $this->error = $e->getMessage();
            return false;
        }*/
    }

    /**
     * 备份
     * @param array $tables
     * @return bool
     * @content 参数不给就是全部的表
     */
    public function backup($tables='')
    {
        $this->sqlname = $tables;
        //存储表定义语句的数组
        $ddl = array();
        //存储数据的数组
        $data = array();
        $this->setTables($tables);
        if (!empty($this->tables))
        {
            foreach ($this->tables as $table)
            {
                $ddl[] = $this->getDDL($table['Name']);
                $data[] = $this->getData($table['Name']);
            }
            //开始写入
            $res = $this->writeToFile($this->tables, $ddl, $data);
        }
        else
        {
           $res = showAjaxMess('1002','数据库中没有该表！');
        }

        return $res;
    }

    /**
     * 设置要备份的表
     * @param array $tables
     */
    private function setTables($tables)
    {
        $this->tables = $this->getTables($tables);
    }

    /**
     * 查询
     * @param string $sql
     * @return mixed
     */
    private function query($sql = '')
    {
        $list = mysqld_query($sql);
        return $list;
    }

    /**
     * 获取全部表
     * @return array
     */
    public function getTables($tables = '')
    {
        $allTables = mysqld_selectall("SHOW TABLE STATUS");
        if(!empty($tables)){
            foreach($allTables as $key=>$row){
                if($row['Name'] != $tables)
                    unset($allTables[$key]);
            }
        }
        return $allTables;
    }

    /**
     * 获取表定义语句
     * @param string $table
     * @return mixed
     */
    private function getDDL($table = '')
    {
        $sql  = "SHOW CREATE TABLE `{$table}`";
        $info = mysqld_selectall($sql);
        $ddl = $info[0]['Create Table'] . ';';
        return $ddl;
    }

    /**
     * 获取表数据
     * @param string $table
     * @return mixed
     */
    private function getData($table = '')
    {
        $sql = "SHOW COLUMNS FROM `{$table}`";
        $list = mysqld_selectall($sql);
        //字段
        $columns = '';
        //需要返回的SQL
        $query = '';
        foreach ($list as $value)
        {
            $columns .= "`{$value['Field']}`,";
        }
        $columns = substr($columns, 0, -1);
        $data = mysqld_selectall("SELECT * FROM `{$table}`");
        foreach ($data as $value)
        {
            $dataSql = '';
            foreach ($value as $v)
            {
                $dataSql .= "'{$v}',";
            }
            $dataSql = substr($dataSql, 0, -1);
            $query .= "INSERT INTO `{$table}` ({$columns}) VALUES ({$dataSql});\r\n";
        }
        return $query;
    }

    /**
     * 写入文件
     * @param array $tables
     * @param array $ddl
     * @param array $data
     */
    private function writeToFile($tables = array(), $ddl = array(), $data = array())
    {
        $str = "/*\r\nMySQL Database Backup Tool Class\r\n";
        $str .= "Auth:刘建凡\r\n";
        $str .= "Data:" . date('Y-m-d H:i:s', time()) . "\r\n*/\r\n";
        $str .= "SET FOREIGN_KEY_CHECKS=0;\r\n";
        $i = 0;
        foreach ($tables as $table)
        {
            $str .= "-- ----------------------------\r\n";
            $str .= "-- Table structure for {$table['Name']}\r\n";
            $str .= "-- ----------------------------\r\n";
            $str .= "DROP TABLE IF EXISTS `{$table['Name']}`;\r\n";
            $str .= $ddl[$i] . "\r\n";
            $str .= "-- ----------------------------\r\n";
            $str .= "-- Records of {$table['Name']}\r\n";
            $str .= "-- ----------------------------\r\n";
            $str .= $data[$i] . "\r\n";
            $i++;
        }
        if(empty($this->sqlname)){
            $sqlname = date("YmdHis",time()).".sql";
        }else{
            $sqlname = date("YmdHis",time())."_{$this->sqlname}.sql";
        }

        $dir = WEB_ROOT . '/' . $this->dir;
        if(!is_dir($dir)){
            mkdirs($dir);
        }
        $sqlurl = $dir.'/'.$sqlname;
        if(file_put_contents($sqlurl, $str)){
            return showAjaxMess(200,'备份成功!花费时间' . round(microtime(true) - $this->begin,3) . 'ms');
        }else{
            return showAjaxMess(1002,'备份失败！');
        }
    }

    /**
     * 错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $path
     * @return bool
     * @content 导入数据库
     */
    public function import($sqlfile = '')
    {
        $path = $this->dir.'/'.$sqlfile;
        if (!file_exists($path))
        {
            return showAjaxMess('1002','SQL文件不存在!');
        }
        else
        {
            $sql = $this->parseSQL($path);
            $res = mysqld_query($sql);
            if($res){
                $msg = '还原成功!花费时间 '. round(microtime(true) - $this->begin,3) . 'ms';
                return showAjaxMess(200,$msg);
            }else{
                return showAjaxMess('1002','导入失败！');
            }
        }
    }

    /**
     * 解析SQL文件为SQL语句数组
     * @param string $path
     * @return array|mixed|string
     */
    private function parseSQL($path = '')
    {
        $sql = file_get_contents($path);
        $sql = explode("\r\n", $sql);

        //先消除--注释
        $sql = array_filter($sql, function ($data)
        {
            if (empty($data) || preg_match('/^--.*/', $data))
            {
                return false;
            }
            else
            {
                return true;
            }
        });

        $sql = implode("\r\n", $sql);
        //删除/**/注释
        $sql = preg_replace('/\/\*.*\*\//', '', $sql);
        ppd($sql);
        $sqlArr = explode(';',$sql);
        ppd($sqlArr);
        return $sqlArr;
    }
}