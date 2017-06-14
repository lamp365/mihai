<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/7
 * Time: 18:14
 */
namespace service;
class publicService
{
    public $error = null;
    public function __construct()
    {
       # what to do
    }
    public function getError()
    {
        return $this->error;
    }

}
