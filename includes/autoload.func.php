<?php
function  hinrcAutoload($className) {
    defined('WEB_ROOT') or define('WEB_ROOT', str_replace("\\", '/', dirname(dirname(__FILE__))));
    $className = $className;
    $filePath = WEB_ROOT."/includes/lib/{$className}.class.php";
    if (is_readable($filePath)) {
        require_once($filePath);
    }
}


spl_autoload_register('hinrcAutoload');