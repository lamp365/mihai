<?php

class Autoloader{
  
     /**
     * 类库自动加载，写死路径，确保不加载其他文件。
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
        $name           = $class;
        $namespaceClass = '';
        if(false !== strpos($name,'\\')){
            //命名空间才会走这里
            $name            = strstr($name,'\\',true);
            $namespaceClass  = str_replace('\\','/',$class);
        }
        $filename = TOP_AUTOLOADER_PATH."/top/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = TOP_AUTOLOADER_PATH."/top/request/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = TOP_AUTOLOADER_PATH."/top/domain/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = TOP_AUTOLOADER_PATH."/aliyun/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = TOP_AUTOLOADER_PATH."/aliyun/request/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = TOP_AUTOLOADER_PATH."/aliyun/domain/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }
        //阿里云图片服务器
        $filename = TOP_AUTOLOADER_PATH."/aliyunOSS/{$namespaceClass}.php";
        if(is_file($filename)) {
            include $filename;
            return;
        }
    }
}

spl_autoload_register('Autoloader::autoload');
?>