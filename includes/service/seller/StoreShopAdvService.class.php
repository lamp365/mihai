<?php

/**
 * Author: 王敬
 */

namespace service\seller;

class StoreShopAdvService extends \service\publicService {

    protected $myTableName = 'store_shop_adv';

    /**
     * 店铺注册信息第2步
     * $param array('')
     */
    public function save($data, $id = '') {
        $arr = array(
            'ssa_shop_id'        => $_SESSION[MOBILE_ACCOUNT]['store_sts_id'] ,
            'ssa_title'          => trim($data['ssa_title']),
            'ssa_sub_title'      => trim($data['ssa_sub_title']),
            'ssa_thumb'          => trim($data['ssa_thumb']),
            'ssa_content'        => htmlspecialchars_decode($data['ssa_content']),
            'ssa_is_require_top' => intval($data['ssa_is_require_top'])==1?1:0,
            'ssa_type'           => intval($data['ssa_type'])==2?2:1,
            'ssa_weixin_url'     => trim($data['ssa_weixin_url']),
            'ssa_start_time'     => strtotime($data['ssa_start_time']),
            'ssa_end_time'       => strtotime($data['ssa_end_time']),
            'ssa_create_time'    => time(),
        );
        if ($id) {
            mysqld_update($this->myTableName, $arr, array('ssa_adv_id' => $id));
        } else {
            $effect = mysqld_insert($this->myTableName, $arr);
        }
        return true;
    }

}
