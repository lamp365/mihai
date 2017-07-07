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
        error_reporting(E_ERROR);
        
        $this->memberInfo       = get_member_account();
        $this->subAccountObj    = new \service\seller\subAccountService();
        $this->weixin           = new \WeixinTool();
        $this->shopDish         = new \service\seller\ShopDishService();
        $this->shopCate         = new \service\seller\ShopStoreCategoryService();
        $this->rulerservice     = new \service\seller\shoprulerService();
        parent::__construct();
        
    }
    
    public function index(){

    }
    
    //子账户信息
    public function subaccountInfo(){
        $data   = $this->request;
        $redata = array();
        $redata['memberInfo'] = $this->subAccountObj->getMemberinfo($this->memberInfo['openid'],'gold,freeze_gold,wait_glod,outgold,realname,nickname,avatar');
        
        $redata['memberInfo']['openid'] = $this->memberInfo['openid'];
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
            @$subaccountList[$k]['nickname'] = $memberArr[$v['friend_openid']]['nickname']!=''?$memberArr[$v['friend_openid']]['nickname']:'';
            @$subaccountList[$k]['avatar']   = $memberArr[$v['friend_openid']]['avatar']!=''?$memberArr[$v['friend_openid']]['avatar']:'';
            
            @$subaccountList[$k]['createtime'] = $v['createtime'];
            
            unset($subaccountList[$k]['friend_openid']);
        }
        $redata['total'] = $subaccountList['total'];
        unset($subaccountList['total']);
        $subaccountList = array_values($subaccountList);
        $redata['subaccountList'] = $subaccountList;
        ajaxReturnData(1,'获取成功',$redata);
        
    }
    
    
    //子账户信息
    public function subaccountInfoList(){
        $data   = $this->request;
        $redata = array();
        $redata['memberInfo'] = $this->subAccountObj->getMemberinfo($this->memberInfo['openid'],'gold,freeze_gold,wait_glod,outgold,realname,nickname,avatar,is_sub_status,openid');
        
        $redata['memberInfo']['openid'] = $redata['memberInfo']['openid'];
        $redata['memberInfo']['is_sub_status'] = intval($redata['memberInfo']['is_sub_status']);
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
        $redata['memberInfo']['total'] = $redata['memberInfo']['cspList']['total'];
        unset($redata['memberInfo']['cspList']['total']);
        ajaxReturnData(1,'获取成功',$redata);
    }
    
    //获取分销商品分类列表
    public function distributionCate(){
        $data   = $this->request;
        $redata = array();
        
        $dishOneData = $this->shopDish->distributionListDish($data, 'DISTINCT(store_p1)',1);
        
        $dishTwoData = $this->shopDish->distributionListDish($data, 'DISTINCT(store_p2),store_p1',1);
        
        if(count($dishTwoData) > 0)
        {
            $store_p1_str = '';
            foreach($dishOneData as $v){
                $store_p1_str .= $v['store_p1'].',';
            }
            $store_p1_str = rtrim($store_p1_str,',');
            $oneCate = $this->shopCate->getStoreShopCategory($store_p1_str,'id,name');
            foreach($oneCate as $k=>$v){
                $redata['distributionCate'][$v['id']] = $v;
            }
            
            $store_p2_str = '';
            foreach($dishTwoData as $v){
                $store_p2_str .= $v['store_p2'].',';
            }
            $store_p2_str = rtrim($store_p2_str,',');
            $twoCate = $this->shopCate->getStoreShopCategory($store_p2_str,'id,name,pid');

            foreach($twoCate as $k=>$v){
                $redata['distributionCate'][$v['pid']]['twocate'][] = $v;
            }
            $redata['distributionCate'] = array_values($redata['distributionCate']);
        }   
        
        ajaxReturnData(1,'获取成功',$redata);
    }
    
    //Merchant 
    public function subAccountAdmin(){
        $data   = $this->request;
        $redata = array();
        $redata['memberInfo']           = $this->subAccountObj->getMemberinfo($this->memberInfo['openid'],'gold,freeze_gold,wait_glod,outgold,realname,nickname,avatar');
        $redata['memberInfo']['gold']   = FormatMoney($redata['memberInfo']['gold'],2);
        $redata['memberInfo']['freeze_gold'] = FormatMoney($redata['memberInfo']['freeze_gold'],2);
        $redata['memberInfo']['wait_glod']   = FormatMoney($redata['memberInfo']['wait_glod'],2);
        $redata['memberInfo']['outgold']     = FormatMoney($redata['memberInfo']['outgold'],2);
        
        //获取总收益
        $redata['memberInfo']['account_fee'] = $this->subAccountObj->getTotalIncome($this->memberInfo['openid']);
        $redata['memberInfo']['account_fee'] = FormatMoney($redata['memberInfo']['account_fee'],2);
        
        //判断下属用户
        $rsData = $this->subAccountObj->getParentIdAccount($data,$this->memberInfo['openid']);

        $rsTotalData = $this->subAccountObj->getParentIdAccountCount($this->memberInfo['openid'],'count(0) as countnum');
        $redata['memberInfo']['total'] = $rsTotalData['countnum'];
        $i = 0;
        $redata['memberInfo']['memberData'] = array();
        foreach($rsData as $k=>$v){
            $redata['memberInfo']['memberData'][$i]           = $this->subAccountObj->getMemberinfo($v['openid'],'wait_glod,nickname,realname,avatar,is_sub_status');
            $redata['memberInfo']['memberData'][$i]['openid']     = $v['openid'];
            $redata['memberInfo']['memberData'][$i]['nickname']     = $redata['memberInfo']['memberData'][$i]['nickname'];
            $redata['memberInfo']['memberData'][$i]['realname']     = $redata['memberInfo']['memberData'][$i]['realname'];
            $redata['memberInfo']['memberData'][$i]['avatar']     = $redata['memberInfo']['memberData'][$i]['avatar'];

            //获取总收益 //
            $redata['memberInfo']['memberData'][$i]['account_fee'] = $this->subAccountObj->getTotalIncome($v['openid']);
            $redata['memberInfo']['memberData'][$i]['account_fee'] = FormatMoney($redata['memberInfo']['memberData'][$i]['account_fee'],2);
            
            //统计所属用户数
            $memberCount = array();
            $memberCount = $this->subAccountObj->getChildrenCount($openid, $sts_id,'count(0) as total');
            $redata['memberInfo']['memberData'][$i]['memberTotal'] = intval($memberCount['total']);
            
            $i++;
        }
        ajaxReturnData(1,'获取成功',$redata);
    }
    
    //变更用户状态
    public function upMemberStatus(){
        $data   = $this->request;
        $redata = array();
        if($data['openid'] == ''){
             ajaxReturnData(0,'必要参数不存在');
        }
        $data['is_sub_status'] = intval($data['is_sub_status']);
        //判断该子账户是否属于该店家
        $isCheck = $this->subAccountObj->getMemberStoreRelation($data['openid'],$this->memberInfo['store_sts_id']);
        if($isCheck['id'] <= 0)
        {
            ajaxReturnData(0,'该用户不是您的子账户');
        }
        $upStatus = $this->subAccountObj->upMemberSubStatus($data['openid'],$data['is_sub_status']);
        ajaxReturnData(1,'更新成功',$upStatus);
    }
    
    //结算功能
    public function settlementMoney(){
        $data   = $this->request;
        
        /*
        //测试数据开始
        $data['receivablesOpenid'] = '2017060116264';
        $data['money']             = '1000';
        $data['remark']            = '微信付款';
        //测试数据结束
        */
        
        $data['money']             = FormatMoney($data['money']);
        
        //判断两者是否存在对应关系
        $isCheck = $this->subAccountObj->getMemberStoreRelation($data['receivablesOpenid'],$this->memberInfo['store_sts_id'],'id',$this->memberInfo['openid']);
        if($isCheck['id'] <= 0)
        {
            ajaxReturnData(0,'该用户不是您的子账户');
        }
        
        //要结算的金额 付款账户 收款账户
        //获取用户信息
        $memberInfo = $this->subAccountObj->getMemberInfos($this->memberInfo['openid'], 'gold');
        
        if($memberInfo[0]['gold'] < $data['money'])
        {
            ajaxReturnData(0,'账户金额不足');
        }
        
        $memberReceivablesInfo = $this->subAccountObj->getMemberInfos($data['receivablesOpenid'], 'wait_glod');
        if($data['money'] > $memberReceivablesInfo[0]['wait_glod'])
        {
          ajaxReturnData(0,'可结算资金大于当前资金');  
        }
        
        //payment 付 Receivables 收
        //扣除付款金额
        $upPaymentStatus = $this->subAccountObj->upPaymentMemberInfos($this->memberInfo['openid'], $data['money']);
        
        $upReceivablesStatus = $this->subAccountObj->upReceivablesMemberInfos($data['receivablesOpenid'], $data['money']);
        
        $receivablesMemberInfo = $this->subAccountObj->getMemberinfo($data['receivablesOpenid'],'gold');
        
        //日志记录
        $insertData = array();
        $insertData['payer_openid']         = $this->memberInfo['openid'];
        $insertData['payee_openid']         = $data['receivablesOpenid'];
        $insertData['fee']                  = FormatMoney($data['money']);
        $insertData['sts_id']               = $this->memberInfo['store_sts_id'];
        $insertData['account_fee']          = $receivablesMemberInfo['account_fee'];
        $insertData['remark']               = $data['remark'];
        $insertData = $this->subAccountObj->addPayLog($insertData);
        
        ajaxReturnData(1,'扣款成功');
    }
    
    //发送验证码
    public function sendSms(){
        $data   = $this->request;
        $redata = array();
        //测试数据开始 1
        //$data['mobile'] = '15806015161';
        //$data['isCheckMobile'] = 1;
        //$data['isType'] = 1;
        //测试数据结束
        if($data['mobile']  == '')
        {
            ajaxReturnData(0,'必要参数不存在');
        }
        
        //
        
        $redata['ischeck'] = 0;
        //判断手机号码是否已经存在 $this->subAccountObj
        if($data['isCheckMobile'] > 0){
            $memberInfo = member_get_bymobile($data['mobile']);
            if($memberInfo['mobile'] != '')
            { 
                //
                $redata['ischeck'] = 1;
                //判断该手机是否已经绑定过其他店铺
                $checkData = $this->subAccountObj->checkShop($memberInfo['openid'], '*');
                
                if($data['isType'] == 1)
                {
                    if($checkData['sts_id'] == $this->memberInfo['store_sts_id'])
                    {
                        ajaxReturnData(0,'您已属于该店无需再次绑定');
                    }
                    
                    //添加
                    if($checkData['id'] > 0)
                    {
                        ajaxReturnData(0,'该手机已绑定过其他店铺无法再次绑定');
                    }
                }
                else{
                    //编辑
                    if($checkData['sts_id'] != $this->memberInfo['store_sts_id'])
                    {
                        ajaxReturnData(0,'该手机已绑定过其他店铺无法再次绑定');
                    }
                }
            }
        }
        
        $res   = $this->rulerservice->send_mobile_code($data['mobile']);
        if($res){
            ajaxReturnData(1,'发送成功',$redata);
        }else{
            ajaxReturnData(0,$this->rulerservice->getError());
        }
    }
    
    public function addSubaccountMember(){
        $data   = $this->request;
        $redata = array();
        
        //测试数据开始
        //$data['checkcode'] = 94319;             //验证码
        //$data['mobile'] = '18650393557';                //手机号码
        //$data['earn_reate'] = 8;            //佣金设置
        //$data['pwd'] = 'zyy123456';
        //测试数据结束
	if($data['checkcode'] == '' || $data['mobile'] == '' || $data['earn_rate'] == ''){
	    ajaxReturnData(0,'必要参数不存在');
	}
        $data['group_id'] = 2;
        
        if (!empty($_FILES['avatar']['tmp_name'])) {
            $upload = file_upload($_FILES['avatar']);
            is_error($upload) &&  ajaxReturnData(0, $upload['message']);
            $data['avatar'] = $upload['path'];
        }
        
        //提交表单
        $res = $this->rulerservice->do_adduser($data);
        if($res){
            ajaxReturnData(1,'添加成功！');
        }else{
            ajaxReturnData(0,$this->rulerservice->getError());
        }
    }
    
    //编辑
    public function editSubaccountMember(){
        $data   = $this->request;
        $redata = array();
	
	//测试数据开始
        //$data['nickname'] = '林军师';             //验证码
        //$data['checkcode'] = 10510;             //验证码
        //$data['mobile'] = '18650393557';                //手机号码
        //$data['earn_reate'] = 10;            //佣金设置
        //$data['openid'] = '2017070621665';
        //测试数据结束

	if($data['checkcode'] == '' || $data['mobile'] == '' || $data['earn_rate'] == ''|| $data['openid'] == ''){
	    ajaxReturnData(0,'必要参数不存在');
	}
	
        if (!empty($_FILES['avatar']['tmp_name'])) {
            $upload = file_upload($_FILES['avatar']);
            is_error($upload) &&  ajaxReturnData(0, $upload['message']);
            $data['avatar'] = $upload['path'];
        }
        
	//判断两者是否存在对应关系
        $isCheck = $this->subAccountObj->getMemberStoreRelation($data['openid'],$this->memberInfo['store_sts_id'],'id',$this->memberInfo['openid']);
        if($isCheck['id'] <= 0)
        {
            ajaxReturnData(0,'该用户不是您的子账户');
        }

	if($data['pwd'] != ''){
	    $data['pwd'] = encryptPassword($data['pwd']);
	}
	
	$upstatus = $this->rulerservice->editMember($data);
	
	ajaxReturnData(1,'更新成功！');
    }
    
    
    //获取子账户信息
    public function getMemberInfo(){
	//测试数据开始
        //$data['openid'] = '2017070621665';
        //测试数据结束
	if($data['openid'] == ''){
	    ajaxReturnData(0,'必要参数不存在');
	}
	
	$memberInfo = $this->subAccountObj->getMemberinfo($data['openid'],'nickname,mobile,earn_rate,avatar');
	if($memberInfo['mobile'] == ''){
	    ajaxReturnData(0,'用户信息不存在');
	}
	ajaxReturnData(1,'获取成功！',$memberInfo);
    }
    
}
?>