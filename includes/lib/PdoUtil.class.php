<?php

class PdoUtil
{

    private $dbo;

    private $cfg;

    public function __construct($cfg)
    {
        global $_CMS;
        if (empty($cfg)) {
            exit("无法读取/config/config.php数据库配置项.");
        }
        $mysqlurl = "mysql:dbname={$cfg['database']};host={$cfg['host']};port={$cfg['port']}";
        try {
            $this->dbo = new PDO($mysqlurl, $cfg['username'], $cfg['password']);
        } catch (PDOException $e) {
            message($e . "数据库连接失败，请检查数据库配置:/config/config.php");
        }
        
        $sql = "SET NAMES '{$cfg['charset']}';";
        $this->dbo->exec($sql);
        $this->dbo->exec("SET sql_mode='';");
        $this->cfg = $cfg;
        if (SQL_DEBUG) {
            $this->debug($this->dbo->errorInfo());
        }
    }

    public function query($sql, $params = array())
    {
        if (empty($params)) {
            $result = $this->dbo->exec($sql);
            if (SQL_DEBUG) {
                $this->debug($this->dbo->errorInfo(),$sql);
            }
            return $result;
        }
        $statement = $this->dbo->prepare($sql);
        $result = $statement->execute($params);
        if (SQL_DEBUG) {
            $this->debug($statement->errorInfo(),$sql);
        }
        if (! $result) {
            return false;
        } else {
            return $statement->rowCount();
        }
    }

    public function fetchcolumn($sql, $params = array(), $column = 0)
    {
        $statement = $this->dbo->prepare($sql);
        $result = $statement->execute($params);
        if (SQL_DEBUG) {
            $this->debug($statement->errorInfo(),$sql);
        }
        if (! $result) {
            return false;
        } else {
            return $statement->fetchColumn($column);
        }
    }

    public function fetch($sql, $params = array())
    {
        $statement = $this->dbo->prepare($sql);
        $result = $statement->execute($params);
        if (SQL_DEBUG) {
            $this->debug($statement->errorInfo(),$sql);
        }
        if (! $result) {
            return false;
        } else {
            return $statement->fetch(pdo::FETCH_ASSOC);
        }
    }

    public function fetchall($sql, $params = array(), $keyfield = '')
    {
        $statement = $this->dbo->prepare($sql);
        $result = $statement->execute($params);
        if (SQL_DEBUG) {
            $this->debug($statement->errorInfo(),$sql);
        }
        if (! $result) {
            return false;
        } else {
            if (empty($keyfield)) {
                return $statement->fetchAll(pdo::FETCH_ASSOC);
            } else {
                $temp = $statement->fetchAll(pdo::FETCH_ASSOC);
                $rs = array();
                if (! empty($temp)) {
                    foreach ($temp as $key => &$row) {
                        if (isset($row[$keyfield])) {
                            $rs[$row[$keyfield]] = $row;
                        } else {
                            $rs[] = $row;
                        }
                    }
                }
                return $rs;
            }
        }
    }

    public function update($table, $data = array(), $params = array(), $orwith = 'AND')
    {
        $fields = $this->splitForSQL($data, ',');
        $condition = $this->splitForSQL($params, $orwith);
        $params = array_merge($fields['params'], $condition['params']);
        $sql = "UPDATE " . $this->table($table) . " SET {$fields['fields']}";
        $sql .= $condition['fields'] ? ' WHERE ' . $condition['fields'] : '';
        return $this->query($sql, $params);
    }

    public function insert($table, $data = array(), $es = FALSE)
    {
        $condition = $this->splitForSQL($data, ',');
        return $this->query("INSERT INTO " . $this->table($table) . " SET {$condition['fields']}", $condition['params']);
    }

    public function insertid()
    {
        return $this->dbo->lastInsertId();
    }

    public function delete($table, $params = array(), $orwith = 'AND')
    {
        $condition = $this->splitForSQL($params, $orwith);
        $sql = "DELETE FROM " . $this->table($table);
        $sql .= $condition['fields'] ? ' WHERE ' . $condition['fields'] : '';
        return $this->query($sql, $condition['params']);
    }

    private function splitForSQL($params, $orwith = ',')
    {
        $result = array(
            'fields' => ' 1 ',
            'params' => array()
        );
        $split = '';
        $suffix = '';
        if (in_array(strtolower($orwith), array(
            'and',
            'or'
        ))) {
            $suffix = '__';
        }
        if (! is_array($params)) {
            $result['fields'] = $params;
            return $result;
        }
        if (is_array($params)) {
            $result['fields'] = '';
            foreach ($params as $fields => $value) {
                $result['fields'] .= $split . "`$fields` =  :{$suffix}$fields";
                $split = ' ' . $orwith . ' ';
                $result['params'][":{$suffix}$fields"] = is_null($value) ? '' : $value;
            }
        }
        return $result;
    }

    public function excute($sql, $stuff = 'squdian_')
    {
        if (! isset($sql) || empty($sql))
            return;
        
        $sql = str_replace("\r", "\n", str_replace(' ' . $stuff, ' squdian_', $sql));
        $sql = str_replace("\r", "\n", str_replace(' `' . $stuff, ' `squdian_', $sql));
        $ret = array();
        $num = 0;
        foreach (explode(";\n", trim($sql)) as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            foreach ($queries as $query) {
                $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0] . $query[1] == '--') ? '' : $query;
            }
            $num ++;
        }
        unset($sql);
        foreach ($ret as $query) {
            $query = trim($query);
            if ($query) {
                $this->query($query);
            }
        }
    }

    public function fieldexists($tablename, $fieldname)
    {
        $isexists = $this->fetch("DESCRIBE " . $this->table($tablename) . " `{$fieldname}`");
        return ! empty($isexists) ? true : false;
    }

    public function indexexists($tablename, $indexname)
    {
        if (! empty($indexname)) {
            $indexs = mysqld_selectall("SHOW INDEX FROM " . $this->table($tablename));
            if (! empty($indexs) && is_array($indexs)) {
                foreach ($indexs as $row) {
                    if ($row['Key_name'] == $indexname) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function table($table)
    {
        return "`squdian_{$table}`";
    }

    public function debug($errors,$sql='')
    {
        //测试环境开启debug，则同时计入log sql错误语句
        if (! empty($errors[1]) && ! empty($errors[1]) && $errors[1] != '00000') {
//             print_r($errors);
            logRecord($sql,"sql_error");
            message($errors[2]);
        }
        return $errors;
    }
}