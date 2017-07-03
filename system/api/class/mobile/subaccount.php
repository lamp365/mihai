<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace api\controller;

use api\controller;

class subaccount extends base
{
    private $memberInfo     = array();
    private $subAccountObj  = array();
    private $weixin         = array();
    private $shopDish       = array();
    private $shopCate       = array();

    public function __construct()
    {
        //error_reporting(E_ERROR);
        
        $this->memberInfo       = get_member_account();
        $this->subAccountObj    = new \service\seller\subAccountService();
        $this->weixin           = new \WeixinTool();
        $this->shopDish         = new \service\seller\ShopDishService();
        $this->shopCate         = new \service\seller\ShopStoreCategoryService();
        parent::__construct();
        
    }
    
    public function index(){

    }
    
    //子账户信息
    public function subaccountInfo(){
        $data   = $this->request;
        $redata = array();
        $redata['memberInfo'] = $this->subAccountObj->getMemberinfo($this->memberInfo['openid'],'gold,freeze_gold,wait_glod,outgold,realname,nickname,avatar');
        
        $redata['memberInfo']['gold'] = FormatMoney($redata['memberInfo']['gold'],2);
        $redata['memberInfo']['freeze_gold'] = FormatMoney($redata['memberInfo']['freeze_gold'],2);
        $redata['memberInfo']['wait_glod'] = FormatMoney($redata['memberInfo']['wait_glod'],2);
        $redata['memberInfo']['outgold'] = FormatMoney($redata['memberInfo']['outgold'],2);
        
        $result = $this->weixin->get_xcx_erweima($this->memberInfo['openid'],2);
        
        //获取总收益
        $redata['memberInfo']['account_fee'] = $this->subAccountObj->getTotalIncome($this->memberInfo['openid']);
        $redata['memberInfo']['account_fee'] = FormatMoney($redata['memberInfo']['account_fee'],2);
        
        $redata['memberInfo']['qrcode'] = $result['message'];
        
        ajaxReturnData(1,'获取成功',$redata);
    }
    
    //子账户收入列表
    public function subaccountList(){
        $data   = $this->request;
        $redata = array();
        $subaccountList = $this->subAccountObj->getProfitList($this->memberInfo['openid'],$data,'fee,friend_openid,createtime');
        
        //
        $friend_openids = '';
        foreach($subaccountList as $v){
            $friend_openids .= '"'.$v['friend_openid'].'",';
        }
        $friend_openids = rtrim($friend_openids,',');
        
        //获取用户信息
        $memberData = $this->subAccountObj->getMemberInfos($friend_openids, '*');
        $memberArr = array();
        if($memberData != '')
        foreach($memberData as $v){
            $memberArr[$v['openid']]['nickname'] = $v['nickname'];
            $memberArr[$v['openid']]['avatar']   = $v['avatar'];
        }
        
        foreach($subaccountList as $k=>$v){
            $subaccountList[$k]['nickname'] = $memberArr[$v['friend_openid']]['nickname'];
            $subaccountList[$k]['avatar']   = $memberArr[$v['friend_openid']]['avatar'];
            
            $subaccountList[$k]['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
            
            unset($subaccountList[$k]['friend_openid']);
        }
        
        $redata = $subaccountList;
        ajaxReturnData(1,'获取成功',$redata);
        
    }
    
    
    //子账户信息
    public function subaccountInfoList(){
        $data   = $this->request;
        $redata = array();
        $redata['memberInfo'] = $this->subAccountObj->getMemberinfo($this->memberInfo['openid'],'gold,freeze_gold,wait_glod,outgold,realname,nickname,avatar');
        
        $redata['memberInfo']['gold'] = FormatMoney($redata['memberInfo']['gold'],2);
        $redata['memberInfo']['freeze_gold'] = FormatMoney($redata['memberInfo']['freeze_gold'],2);
        $redata['memberInfo']['wait_glod'] = FormatMoney($redata['memberInfo']['wait_glod'],2);
        $redata['memberInfo']['outgold'] = FormatMoney($redata['memberInfo']['outgold'],2);
        
        $result = $this->weixin->get_xcx_erweima($this->memberInfo['openid'],2);
        
        //获取总收益
        $redata['memberInfo']['account_fee'] = $this->subAccountObj->getTotalIncome($this->memberInfo['openid']);
        $redata['memberInfo']['account_fee'] = FormatMoney($redata['memberInfo']['account_fee'],2);
        
        //结算列表
        $redata['memberInfo']['cspList'] = $this->subAccountObj->getCspList($this->memberInfo['openid'],$data,'createtime,fee,remark');
        
        ajaxReturnData(1,'获取成功',$redata);
    }
    
    //获取分销商品分类列表
    public function distributionCate(){
        $data   = $this->request;
        $redata = array();
        
        $dishOneData = $this->shopDish->distributionListDish($data, 'DISTINCT(store_p1)',1);
        
        $dishTwoData = $this->shopDish->distributionListDish($data, 'DISTINCT(store_p2),store_p1',1);

        if($dishTwoData != '')
        {
            $store_p1_str = '';
            foreach($dishOneData as $v){
                $store_p1_str .= $v['store_p1'].',';
            }
            $store_p1_str = rtrim($store_p1_str,',');
            $oneCate = $this->shopCate->getStoreShopCategory($store_p1_str,'id,name');
            foreach($oneCate as $k=>$v){
                $redata[$v['id']] = $v;
            }
            
            $store_p2_str = '';
            foreach($dishTwoData as $v){
                $store_p2_str .= $v['store_p2'].',';
            }
            $store_p2_str = rtrim($store_p2_str,',');
            $twoCate = $this->shopCate->getStoreShopCategory($store_p2_str,'id,name,pid');

            foreach($twoCate as $k=>$v){
                $redata[$v['pid']]['twocate'][] = $v;
            }
            $redata = array_values($redata);
        }   
        
        ajaxReturnData(1,'获取成功',$redata);
    }
    
    //获取列表内容
    
    
    
}
?>