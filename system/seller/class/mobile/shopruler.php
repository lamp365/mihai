<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/27
 * Time: 15:28
 */
namespace seller\controller;

use seller\controller;

class shopruler extends base
{
    public function userlist()
    {
        $_GP = $this->request;
        $rulerservice = new \service\seller\shoprulerService();
        $userlist = $rulerservice->getUserlist();
        include page('shopruler/userlist');
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
                message('添加成功！',refresh(),'success');
            }else{
                message($rulerservice->getError(),refresh(),'error');
            }
        }
        //获取分组
        $sellergroup = $rulerservice->getSellerGroup('group_id,group_name');

        if(empty($sellergroup)){
            message('请先创建角色分组！',mobile_url('shopruler',array('op'=>'addgroup')),'error');
        }
        include page('shopruler/adduser');
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
            ppd(json_encode($_GP));
            $res = $rulerservice->do_addgroup($_GP);
            if($res){
                message(LANG('COMMON_ADD_SUCCESS'),refresh(),'success');
            }else{
                message($rulerservice->getError(),refresh(),'error');
            }
        }

        //获取商家总的菜单节点
        $menulist      = $rulerservice->getSystemMenuRule();
        //其他权限
        $sellerActRule = \MenuEnum::$sellerActRule;
        $sellergroup   = array('rule'=>array(),'other_rule'=>array());
        include page('shopruler/addgroup');
    }

    public function edituser()
    {
        $_GP    = $this->request;
        if(empty($_GP['id'])){
            message('参数有误！',refresh(),'error');
        }
        $memInfo = get_member_account();
        $sql = "select * from ".table('seller_rule_relation')."  where id={$_GP['id']}";
        $the_user = mysqld_select($sql);
        if(empty($the_user) || $the_user['sts_id'] != $memInfo['store_sts_id']){
            message('该用户不存在！',refresh(),'error');
        }else{
            $mem = member_get($the_user['openid'],'mobile');
            $the_user['mobile'] = $mem['mobile'];
        }

        //验证完毕后 在提交修改数据
        if(!empty($_GP['is_edit'])){
            mysqld_update('seller_rule_relation',array('group_id'=>$_GP['group_id'],'earn_rate'=>$_GP['earn_rate']),array('id'=>$_GP['id']));
            message('操作成功！',refresh(),'success');
        }

        $rulerservice = new \service\seller\shoprulerService();
        //获取分组
        $sql_field   = 'group_id,group_name,sts_id,description,createtime';
        $sellergroup = $rulerservice->getSellerGroup($sql_field);
        
        //获取佣金比例
        
        
        include page('shopruler/edituser');
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
                message(LANG('COMMON_OPERATION_SUCCESS'),refresh(),'success');
            }else{
                message($rulerservice->getError(),refresh(),'error');
            }
        }

        //获取商家总的菜单节点
        $menulist      = $rulerservice->getSystemMenuRule();
        //其他权限
        $sellerActRule = \MenuEnum::$sellerActRule;
        //获取改组的信息
        $sellergroup   = $rulerservice->getSellerGroup('*',$_GP['group_id']);
        include page('shopruler/addgroup');
    }

    /**
     * 删除用户
     */
    public function deluser()
    {
        $_GP = $this->request;
        $rulerservice = new \service\seller\shoprulerService();
        $res = $rulerservice->deluser($_GP);
        if($res){
            message('删除成功！',refresh(),'success');
        }else{
            message($rulerservice->getError(),refresh(),'error');
        }

    }

    public function usergroup()
    {
        $rulerservice = new \service\seller\shoprulerService();
        //获取分组
        $sql_field   = 'group_id,group_name,description,createtime';
        $sellergroup = $rulerservice->getSellerGroup($sql_field);
        include page('shopruler/usergroup');
    }

    /**
     * 删除分组
     */
    public function delgroup()
    {
        $_GP    = $this->request;
        if(empty($_GP['group_id'])){
            message('参数有误！',refresh(),'error');
        }

        //获取该分组下的用户
        $rulerservice = new \service\seller\shoprulerService();
        $sellergroup  = $rulerservice->getSellerGroup('group_id',$_GP['group_id']);
        if(empty($sellergroup)){
            message('该组不存在',refresh(),'error');
        }
        $res  = $rulerservice->delSellerGroup($_GP['group_id']);
        if($res){
            message('删除成功！',refresh(),'success');
        }else{
            message($rulerservice->getError(),refresh(),'success');
        }
    }
    
    //通过用户ID获取对应的佣金信息
    
}