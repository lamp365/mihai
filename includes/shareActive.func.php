<?php
/**
 * @param $openid
 * 用户每次近来活动主页优先创建 活动主表的记录
 */
function checkIsAddShareActive($openid){
    if(!empty($openid)){
        $info = mysqld_select("select id from ".table('share_active')." where openid='{$openid}'");
        if(empty($info)){
            $data = array(
              'openid'      => $openid,
              'total_num'   => 3,
              'createtime'  => time(),
              'modifytime'  => time()
            );
            mysqld_insert('share_active',$data);
        }
    }
}