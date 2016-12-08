<?php
/*
setting相关
*/

function refreshSetting($arrays)
{
    if (is_array($arrays)) {
        foreach ($arrays as $cid => $cate) {
            $config_data = mysqld_selectcolumn('SELECT `name` FROM ' . table('config') . " where `name`=:name", array(
                ":name" => $cid
            ));
            if (empty($config_data)) {
                mysqld_delete('config', array(
                    'name' => $cid
                ));
                $data = array(
                    'name' => $cid,
                    'value' => $cate
                );
                mysqld_insert('config', $data);
            } else {
                mysqld_update('config', array(
                    'value' => $cate
                ), array(
                    'name' => $cid
                ));
            }
        }
        mysqld_update('config', array(
            'value' => ''
        ), array(
            'name' => 'system_config_cache'
        ));
    }
}

function globaSetting($conditions = array())
{
    $config = array();
    $system_config_cache = mysqld_select('SELECT * FROM ' . table('config') . " where `name`='system_config_cache'");
    if (empty($system_config_cache['value'])) {
        $configdata = mysqld_selectall('SELECT * FROM ' . table('config'));
        foreach ($configdata as $item) {
            $config[$item['name']] = $item['value'];
        }
        if (! empty($system_config_cache['name'])) {
            mysqld_update('config', array(
                'value' => serialize($config)
            ), array(
                'name' => 'system_config_cache'
            ));
        } else {
            mysqld_insert('config', array(
                'name' => 'system_config_cache',
                'value' => serialize($config)
            ));
        }
        return $config;
    } else {
        return unserialize($system_config_cache['value']);
    }
}

/**
 * @param $cfg
 * @return array|string
 * @content qq客服可设置为上班和下班  数据格式发生变化存为json {7918458:1,5468988:0} 1表示上班  0表不上班 返回上班中的qq
 */
function getQQ_onWork($cfg){
    $qqarr = '';
    if(!empty($cfg['shop_kfcode'])){
        $data = json_decode($cfg['shop_kfcode'],true);
        if(is_array($data)){
            foreach($data as $qq => $num){
                if($num == 1)
                    $qqarr[] = $qq;
            }
        }
    }
    return $qqarr;
}