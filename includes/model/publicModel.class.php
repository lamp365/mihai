<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/7
 * Time: 18:14
 */
namespace model;
class publicModel
{
    public $error = null;
    public $db    = null;
    public function __construct()
    {
       $this->db = mysqldb();
    }
    public function getError()
    {
        return $this->error;
    }

}
