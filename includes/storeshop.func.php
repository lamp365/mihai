<?php
/**
 * 通过id或多个 获取系统的分类数
 * @param string $ids
 * @param string $fields
 * @return array
 */
function get_systemCategoryTreeByids($ids='',$fields='id,name,parentid'){
    $data = $cate_ids = array();
    $sql  = "SELECT {$fields} FROM ".table('shop_category')." where id in ({$ids})";
    $one  = mysqld_selectall($sql);
    foreach($one as $v) {
        $data[$v['id']] = $v;
        $cate_ids[] = $v['id'];
    }

    $sql_two = "SELECT {$fields} FROM ".table('shop_category')." where parentid in ({$ids})";
    $two  = mysqld_selectall($sql_two);

    foreach($two as $k=>$vv){
        $data[$vv['parentid']]['twoCategory'][] = $vv;
        $cate_ids[] = $vv['id'];
    }
    return $data;
}

/**
 * 获取店铺的分组  并且分组 第一级 和第二级
 * @return array
 */
function get_storeCategoryGroup(){
    $redata = array('oneCate'=>array(),'twoCate'=>array());
    $memInfo = get_member_account();

    $allCategoryData = mysqld_selectall('select id,name as cat_name,pid as parentid,store_shop_id,status from '.table('store_shop_category').'  where store_shop_id = '.$memInfo['store_sts_id'].' and status = 1 order by sort asc');
    foreach($allCategoryData as $item){
        if($item['parentid'] == 0){
            $redata['oneCate'][] = $item;
        }else{
            $redata['twoCate'][] = $item;
        }
    }
    return $redata;
}