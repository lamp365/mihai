<?php
/*
清理相关
*/
// 清理主题缓存
function clear_theme_cache($path = '', $isdir = false)
{
    if ($isdir == false) {
        $path = WEB_ROOT . '/cache/' . $path;
    }
    if (is_dir($path)) {
        $file_list = scandir($path);
        foreach ($file_list as $file) {
            if ($file != '.' && $file != '..') {
                
                clear_theme_cache($path . '/' . $file, true);
            }
        }
        
        if ($path != WEB_ROOT . '/cache/') {
            @rmdir($path);
        }
    } else {
        @unlink($path);
    }
}