<?php
defined('SYSTEM_IN') or exit('Access Denied');
$_QMXK = array();

class weixinAddons extends BjSystemModule
{

    public function do_control($name=''){
        if ( !empty($name) ){
            $this->__mobile($name);
        }else{
            exit('控制器不存在');
        }
    }
}

