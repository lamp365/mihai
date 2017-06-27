<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/6/16
 * Time: 13:48
 */
namespace wapi\controller;

class member extends base
{
    public function __construct()
    {
        parent::__construct();
        if(!checkIsLogin()){
            ajaxReturnData(0,'请授权登录！');
        }
    }
    /**
     * 获取个人小程序推广二维码
     */
    public function get_qrcode()
    {
        $member = get_member_account();
        $weixin = new \WeixinTool();
        $result = $weixin->get_xcx_erweima($member['openid'],2);
        if($result['errno'] == 0){
            ajaxReturnData(0,$result['message']);
        }else{
            ajaxReturnData(1,$result['message']);
        }
    }

    public function bindRelation()
    {
        $_GP = $this->request;
        $member = get_member_account();
        $openid   = $member['openid'];
        $p_openid = $_GP['openid'];
        if(!empty($r_openid) && !empty($openid)){
            //如果存在过不用插入
            $find = mysqld_select("select id from ".table('member_blong_relation')." where p_opend='{$p_openid}' and m_openid='{$openid}'");
            if(empty($find)){
                $insted_data['p_openid']   = $p_openid;
                $insted_data['m_openid']   = $openid;
                $insted_data['createtime'] = time();
                $insted_data['type']       = 2;
                mysqld_insert('member_blong_relation',$insted_data);
            }
        }
        ajaxReturnData(1,'操作成功');
    }
}