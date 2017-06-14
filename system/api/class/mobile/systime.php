<?php
/**
 * app 系统时间接口
 */

namespace api\controller;
use api\controller;

class systime extends homebase{

    public function index(){
        $systime = date('Y-m-d H:i:s');
        ajaxReturnData(1,'获取成功',$systime);
    }
}