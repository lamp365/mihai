<?php

/**
 * namespace还有待改进.
 * author: 王敬
 */

namespace seller\controller;


class login {

    const TABLE_NAME = 'store';

    //没有op  默认显示index
    public function index() {
        if (checksubmit('submit')) {
            $param = array(
                ':store_mobile' => trim($this->request['userName']),
                ':store_pwd'    => encryptPassword(trim($this->request['password']))
            );
            $account = mysqld_select('SELECT * FROM ' . table(self::TABLE_NAME) . " WHERE  store_mobile = :store_mobile and store_pwd=:store_pwd", $param);
            if (!empty($account['id'])) {
                $_SESSION["account"] = $account;
                header("location:" . create_url('site', array('name' => $this->request['name'], 'do' => 'index')));
            } else {
                message('用户名密码错误！', 'refresh', 'error');
            }
        }
    }
    
    
    public function loginOut() {
        //清空cookie
        foreach($_COOKIE as $key=>$value){
            setCookie($key,"",time()-60);
        }
		unset($_SESSION);
        session_destroy(); 
        session_start(); 
        
		header("location:".create_url('mobile', array('name' => 'shopwap','do' => 'index')));
       
    }

}
