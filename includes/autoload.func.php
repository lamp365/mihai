<?php
function  hinrcAutoload($className) {
    defined('WEB_ROOT') or define('WEB_ROOT', str_replace("\\", '/', dirname(dirname(__FILE__))));
    $className = $className;
    $classArr  = explode('\\',$className);


    if(count($classArr) == 2){

        if($classArr[0] == 'service'){
            //service  实例化是  new \service\test()
            $filePath = WEB_ROOT."/includes/service/{$classArr[1]}.class.php";
        }

    }else if(count($classArr) == 3){

        if($classArr[0] == 'service'){
            //service  实例化是  new \service\api\test()
            $filePath = WEB_ROOT."/includes/service/{$classArr[1]}/{$classArr[2]}.class.php";

        }else if($classArr[1] == 'controller'){

            //controller  实例化是  new \seller\controller\base()
            if(SYSTEM_ACT == 'index'){
                //表示后台
                $controller_file = $classArr[0].'/class/web/'.$classArr[2].'.php';
            }else{
                //mobile表示前台
                $controller_file = $classArr[0].'/class/mobile/'.$classArr[2].'.php';

            }
            //如 system\seller\class\web\base.php
            $filePath = WEB_ROOT."/system/{$controller_file}";
        }
    }else{
        $filePath = WEB_ROOT."/includes/lib/{$className}.class.php";
    }

    if (is_readable($filePath)) {
        require_once($filePath);
    }
}


spl_autoload_register('hinrcAutoload');