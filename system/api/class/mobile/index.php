<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/27
 * Time: 15:28
 */
namespace api\controller;
use api\controller;

class index extends homebase
{
    public function index ()
    {
        $member_info = update_member_info();
        if(empty($member_info)){
            ajaxReturnData(2,'无用户信息！');
        }
        $member_info['seller_roler'] = checkSellerRoler();
        $loginService = new \service\shopwap\loginService();
        $store_data   = $loginService->getStoreData($member_info);
        $data = array(
            'store'          => $store_data['store_info'],
            'store_identity' => $store_data['store_identity'],
            'member'         => $member_info
        );

        ajaxReturnData(1,  LANG('COMMON_OPERATION_SUCCESS'),$data);
    }

}