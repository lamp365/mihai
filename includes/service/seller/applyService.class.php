<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/5/10
 * Time: 14:35
 */
namespace service\seller;

class applyService extends \service\publicService
{
    /**
     * 检测是否可以再次申请店铺
     * @return bool
     */
    public function checkIsCanApply()
    {
        $member = get_member_account();
        //查看该法人 是否有正在申请的店铺
        $apply_store = mysqld_select("select * from ".table('store_shop_apply')." where sts_openid='{$member['openid']}'");

        if(empty($apply_store)){
            //没有申请过  无需操作以下  直接可以申请
            return true;
        }

        if($apply_store['sts_info_status'] == 3){
            $this->error = '您有一个店铺审核未通过，请修改审核信息！';
            return false;
        }else if($apply_store['sts_info_status'] == 2 ){
            $this->error = '您有一个店铺正处于审核中，请等待！';
            return false;
        }else if($apply_store['sts_info_status'] == 1 || $apply_store['sts_info_status'] == 12){
            $this->error = '您有一个店铺信息未完善，请完善信息！';
            return false;
        }
        return true;
    }

}