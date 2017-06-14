<?php
/*
行业表相关操作
*/

function getIndustryNameByID($cat_id) {
    if(!$cat_id){return ;}
    if (extension_loaded('Memcached')) {
        $mcache = new Mcache();
        // 登陆初始化
        $data_cache = $mcache->get('func_getIndustryNameByID');
        if(!$data_cache){
            $all_data = mysqld_selectall("select gc_id,gc_name from ".table('industry') );
            $data_cache =  array_column($all_data, 'gc_name','gc_id');
            $mcache->set('func_getIndustryNameByID',$data_cache,3600);//缓存一小时
        }
        return $data_cache[$cat_id];
    }else{
        $data = mysqld_select("SELECT gc_name FROM " . table('industry') . " WHERE gc_id = ".$cat_id);
        return $data['gc_name'];
    }
}