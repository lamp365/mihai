<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace shopwap\controller;
use  shopwap\controller;

class account{

    //这个值等价于$_GP
    public $request = '';

    public function __construct()
    {
        if ( !checkIsLogin() ){
            header("location:" . to_member_loginfromurl());
        }

    }


    //没有op默认显示 index
    public function index()
    {
        $service    = new \service\shopwap\accountService();
        $bank_list  = $service->get_bank_list();
        include themePage('member/account');
    }

    public function addbank()
    {
        $memInfo = get_member_account();
        $_GP = $this->request;
        $url = mobile_url('account');
        if(!empty($_GP['action'])){
            //添加账户
            $service = new \service\shopwap\accountService();
            $res     = $service->addBank($_GP);
            if($res){
                message('操作成功！',$url,'success');
            }else{
                message($service->getError(),$url,'error');
            }
        }
        $edit_bank = array();
        if($_GP['id']){
            $edit_bank = mysqld_select("select * from ".table('member_bank')." where id={$_GP['id']} and openid='{$memInfo['openid']}'");
            if(empty($edit_bank)){
                message('账户不存在！',$url,'error');
            }
        }
        include themePage('member/addBank');
    }

    public function delbank()
    {
        $_GP = $this->request;
        $member = get_member_account();
        $res = mysqld_delete('member_bank',array('id'=>$_GP['id'],'openid'=>$member['openid']));
        if($res){
            message('删除成功！',refresh(),'success');
        }else{
            message('删除失败！',refresh(),'success');
        }
    }

    public function setDefault()
    {
        $_GP = $this->request;
        $member = get_member_account();
        set_bank_default($member['openid'],$_GP['id']);
        message('操作成功！',refresh(),'success');
    }

}