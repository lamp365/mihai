<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/27
 * Time: 15:28
 */
namespace api\controller;

use api\controller;

class shopruler extends base
{
    public function userlist()
    {
        $_GP = $this->request;
        $rulerservice = new \service\seller\shoprulerService();
        $userlist = $rulerservice->getUserlist();
        ajaxReturnData(1,'',$userlist);
    }

    /**
     * 添加用户
     */
    public function adduser()
    {
        $_GP    = $this->request;
        $rulerservice = new \service\seller\shoprulerService();

        if(!empty($_GP['do_add'])){
            //提交表单
            $res = $rulerservice->do_adduser($_GP);
            if($res){
                ajaxReturnData(1,'添加成功！');
            }else{
                ajaxReturnData(0,$rulerservice->getError());
            }
        }
        //获取分组
        $sellergroup = $rulerservice->getSellerGroup('group_id,group_name');

        if(empty($sellergroup)){
            ajaxReturnData(0,mobile_url('api',array('op'=>'addgroup')));
        }
        ajaxReturnData(1,'',$sellergroup);
    }

    /**
     * 检查手机号是否已经存在
     */
    public function checkmobile()
    {
        $_GP    = $this->request;
        $rulerservice = new \service\seller\shoprulerService();
        $res = $rulerservice->mobile_isreget($_GP['mobile']);
        if(!$res){
            ajaxReturnData(0,$rulerservice->getError());
        }else{
            ajaxReturnData(1,'',$res);
        }
    }

    /**
     * 发送短信验证码
     */
    public function adduser_code()
    {
        $_GP    = $this->request;
        $rulerservice = new \service\seller\shoprulerService();
        $res   = $rulerservice->send_mobile_code($_GP['mobile']);
        if($res){
            ajaxReturnData(1,LANG('COMMON_SMS_SEND_SUCCESS'));
        }else{
            ajaxReturnData(1,$rulerservice->getError());
        }
    }
    /**
     * 添加角色分组
     */
    public function addgroup()
    {
        $_GP    = $this->request;
        $rulerservice = new \service\seller\shoprulerService();
        if(!empty($_GP['do_add'])){
            $res = $rulerservice->do_addgroup($_GP);
            if($res){
                ajaxReturnData(1,LANG('COMMON_ADD_SUCCESS'));
            }else{
                ajaxReturnData(0,$rulerservice->getError());
            }
        }

        //获取商家总的菜单节点
        $menulist      = $rulerservice->getSystemMenuRule();
        //其他权限
        $sellerActRule = \MenuEnum::$sellerActRule;
        $sellergroup   = array('rule'=>array(),'other_rule'=>array());
        ajaxReturnData(1,'',$sellergroup);
    }

    /**
     * 编辑角色分组
     */
    public function editgroup()
    {
        $_GP    = $this->request;
        $rulerservice = new \service\seller\shoprulerService();
        if(!empty($_GP['do_add'])){
            $res = $rulerservice->do_addgroup($_GP);
            if($res){
                ajaxReturnData(1,LANG('COMMON_OPERATION_SUCCESS'));
            }else{
                ajaxReturnData(0,$rulerservice->getError());
            }
        }

        //获取商家总的菜单节点
        $menulist      = $rulerservice->getSystemMenuRule();
        //其他权限
        $sellerActRule = \MenuEnum::$sellerActRule;
        //获取改组的信息
        $sellergroup   = $rulerservice->getSellerGroup('*',$_GP['group_id']);
        $data = array(
            'menulist'        => $menulist,
            'sellerActRule'   => $sellerActRule,
            'sellergroup'     => $sellergroup,
        );
        ajaxReturnData(1,'',$data);
    }

    public function usergroup()
    {
        $rulerservice = new \service\seller\shoprulerService();
        //获取分组
        $sql_field   = 'group_id,group_name,sts_id,description,createtime';
        $sellergroup = $rulerservice->getSellerGroup($sql_field);
        ajaxReturnData(1,'',$sellergroup);
    }

    /**
     * 删除分组
     */
    public function delgroup()
    {
        $_GP    = $this->request;
        if(empty($_GP['group_id'])){
            ajaxReturnData(0,'参数有误！','');
        }
        //获取该分组下的用户
        $rulerservice = new \service\seller\shoprulerService();
        $sellergroup  = $rulerservice->getSellerGroup('group_id',$_GP['group_id']);
        if(empty($sellergroup)){
            ajaxReturnData(0,'该组不存在！','');
        }
        $res  = $rulerservice->delSellerGroup($_GP['group_id']);
        if($res){
            ajaxReturnData(1,'删除成功！','');
        }else{
            ajaxReturnData(0,$rulerservice->getError(),'');
        }
    }
}