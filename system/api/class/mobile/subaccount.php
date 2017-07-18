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
    private $storeShopService = array();

    public function __construct()
    {
        error_reporting(E_ERROR);
        
        $this->memberInfo       = get_member_account();
        $this->subAccountObj    = new \service\seller\subAccountService();
        $this->weixin           = new \WeixinTool();
        $this->shopDish         = new \service\seller\ShopDishService();
        $this->shopCate         = new \service\seller\ShopStoreCategoryService();
        $this->rulerservice     = new \service\seller\shoprulerService();
        $this->storeShopService = new \service\seller\StoreShopService();
        parent::__construct();
        
    }
    
    public function index(){
        
    }
    
    //获取商户信息
    public function shopInfo(){
        $redata = array();
        $redata['memberInfo'] = $this->storeShopService->getShopStore($this->memberInfo['openid'],'sts_avatar as avatar,freeze_money as freeze_gold,recharge_money as gold,sts_name as nickname,sts_openid as openid,totalearn_monry as account_fee');
        
        $redata['memberInfo']['gold']    = FormatMoney($redata['memberInfo']['gold'],2);
        $redata['memberInfo']['freeze_gold']    = FormatMoney($redata['memberInfo']['freeze_gold'],2);
        $redata['memberInfo']['recharge_money'] = FormatMoney($redata['memberInfo']['recharge_money'],2);
        
        //二维码
        $result = $this->weixin->get_xcx_erweima($this->memberInfo['openid'],2);
        $redata['memberInfo']['qrcode'] = $result['message'];
        
        //获取总收益
        $redata['memberInfo']['account_fee'] = FormatMoney($redata['memberInfo']['account_fee'],2);
        
        ajaxReturnData(1,'获取成功',$redata);
    }
    
    //子账户信息
    public function subaccountInfo(){
        $data   = $this->request;
        $redata = array();
        $redata['memberInfo'] = $this->subAccountObj->getMemberinfo($this->memberInfo['openid'],'gold,freeze_gold,wait_glod,realname,nickname,avatar,totalearn_gold as account_fee');
        
        $redata['memberInfo']['openid'] = $this->memberInfo['openid'];
        $redata['memberInfo']['gold'] = FormatMoney($redata['memberInfo']['gold'],2);
        $redata['memberInfo']['freeze_gold'] = FormatMoney($redata['memberInfo']['freeze_gold'],2);
        $redata['memberInfo']['wait_glod'] = FormatMoney($redata['memberInfo']['wait_glod'],2);
        $redata['memberInfo']['outgold'] = FormatMoney($redata['memberInfo']['outgold'],2);
        
        
        //获取总收益
        $redata['memberInfo']['account_fee'] = FormatMoney($redata['memberInfo']['account_fee'],2);
        
        $result = $this->weixin->get_xcx_erweima($this->memberInfo['openid'],2);
        $redata['memberInfo']['qrcode'] = $result['message'];
        
        ajaxReturnData(1,'获取成功',$redata);
    }
    
    //子账户收入列表
    public function subaccountList(){
        $data   = $this->request;
        $redata = array();
        /*
        //通过关系表获取对应的用户数据 1
        $memberInfos = $this->subAccountObj->getChildMember($this->memberInfo['openid'],'openid');
        $openidStr = '';
        foreach($memberInfos as $v){
            $openidStr .= '"'.$v['openid'].'",';
        }
        $openidStr = rtrim($openidStr, ',');
        
        $redata['subaccountList'] = $this->subAccountObj->getMemberInfoPage($openidStr, 'avatar,createtime,fee,nickname');
        */
        
        /*
        //获取下属的所有用户数据
        $subaccountList = $this->subAccountObj->getProfitList($this->memberInfo['openid'],$data,'fee,friend_openid,createtime');
        
        $redata['total'] = $subaccountList['total'];
        unset($subaccountList['total']);
        
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
            @$subaccountList[$k]['fee'] = FormatMoney($v['fee'],2);
            
                //unset($subaccountList[$k]['friend_openid']);
            
        }
        
        $subaccountList = array_values($subaccountList);
        $redata['subaccountList'] = $subaccountList;
        */
        
        //获取下属所有用户数
	if($this->memberInfo['store_is_admin'] == 1){
	    $storeType = 1;
	}
	else{
	    $storeType = 2;
	}
        $memberChildData = $this->subAccountObj->getStoreChildrenMember($this->memberInfo['store_sts_id'],$this->memberInfo['openid'],$storeType,$fields='b.nickname,b.avatar,a.createtime,m_openid');

        foreach($memberChildData['subaccountList'] as $k=>$v){
            //分别统计金额
	    $memberChildData['subaccountList'][$k]['fee'] = FormatMoney($this->subAccountObj->getMemberCommMoney(1,$v['m_openid']),2);
        }
	$redata = $memberChildData;
        ajaxReturnData(1,'获取成功',$redata);
        
    }
    
    
    //子账户信息
    public function subaccountInfoList(){
        $data   = $this->request;
        $redata = array();
        $openid = $data['openid']!=''?$data['openid']:$this->memberInfo['openid'];
        $redata['memberInfo'] = $this->subAccountObj->getMemberinfo($openid,'gold,freeze_gold,wait_glod,realname,nickname,avatar,is_sub_status,openid,totalearn_gold as account_fee');
        $redata['memberInfo']['openid'] = $redata['memberInfo']['openid'];
        $redata['memberInfo']['is_sub_status'] = intval($redata['memberInfo']['is_sub_status']);
        $redata['memberInfo']['gold'] = FormatMoney($redata['memberInfo']['gold'],2);
        $redata['memberInfo']['freeze_gold'] = FormatMoney($redata['memberInfo']['freeze_gold'],2);
        $redata['memberInfo']['wait_glod'] = FormatMoney($redata['memberInfo']['wait_glod'],2);
        $redata['memberInfo']['outgold'] = FormatMoney($redata['memberInfo']['outgold'],2);
        
        $result = $this->weixin->get_xcx_erweima($openid,2);
        
        //获取总收益
        $redata['memberInfo']['account_fee'] = FormatMoney($redata['memberInfo']['account_fee'],2);
        
        //结算列表
        $redata['memberInfo']['cspList'] = $this->subAccountObj->getCspList($openid,$data,'createtime,fee,remark');
        foreach($redata['memberInfo']['cspList'] as $k=>$v){
            $redata['memberInfo']['cspList'][$k]['fee'] = FormatMoney($v['fee'],2);
        }
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
        //
        $redata['memberInfo'] = $this->storeShopService->getShopStore($this->memberInfo['openid'],'sts_avatar as avatar,freeze_money as freeze_gold,recharge_money as gold,sts_name as nickname,sts_openid as openid,totalearn_monry as account_fee');
        
        $redata['memberInfo']['freeze_gold']    = FormatMoney($redata['memberInfo']['freeze_gold'],2);
        $redata['memberInfo']['gold'] = FormatMoney($redata['memberInfo']['gold'],2);
        
        //获取总收益
        $redata['memberInfo']['account_fee'] = FormatMoney($redata['memberInfo']['account_fee'],2);
        
        //判断下属用户
        $rsData = $this->subAccountObj->getParentIdAccount($data,$this->memberInfo['openid']);

        $rsTotalData = $this->subAccountObj->getStoreChildrenCount($this->memberInfo['store_sts_id'],'count(0) as countnum');
        $redata['memberInfo']['total'] = $rsTotalData['countnum'];
        
        $i = 0;
        $redata['memberInfo']['memberData'] = array();
        foreach($rsData as $k=>$v){
            $redata['memberInfo']['memberData'][$i]           = $this->subAccountObj->getMemberinfo($v['openid'],'wait_glod,nickname,realname,avatar,is_sub_status,totalearn_gold as account_fee');
            $redata['memberInfo']['memberData'][$i]['openid']       = $v['openid'];
            $redata['memberInfo']['memberData'][$i]['nickname']     = $redata['memberInfo']['memberData'][$i]['nickname'];
            $redata['memberInfo']['memberData'][$i]['realname']     = $redata['memberInfo']['memberData'][$i]['realname'];
            $redata['memberInfo']['memberData'][$i]['avatar']       = $redata['memberInfo']['memberData'][$i]['avatar'];

            //获取总收益 //
            $redata['memberInfo']['memberData'][$i]['account_fee'] = FormatMoney($redata['memberInfo']['memberData'][$i]['account_fee'],2);
            $redata['memberInfo']['memberData'][$i]['wait_glod']   = FormatMoney($redata['memberInfo']['memberData'][$i]['wait_glod'],2);
            
            //统计所属用户数
            $memberCount = array();
            $memberCount = $this->subAccountObj->getChildrenCount($v['openid'], $v['sts_id'],'count(0) as total');
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
        
        //测试数据开始
        //$data['receivablesOpenid'] = '2017070716755';
        //$data['money']             = '100';
        //$data['remark']            = '微信付款';
        //测试数据结束
        
        $data['money']             = FormatMoney($data['money']);
        
        //判断两者是否存在对应关系
        $isCheck = $this->subAccountObj->getMemberStoreRelation($data['receivablesOpenid'],$this->memberInfo['store_sts_id'],'id',$this->memberInfo['openid']);
        if($isCheck['id'] <= 0)
        {
            ajaxReturnData(0,'该用户不是您的子账户');
        }
        
        $memberReceivablesInfo = $this->subAccountObj->getMemberInfos($data['receivablesOpenid'], 'wait_glod');
        if($data['money'] > $memberReceivablesInfo[0]['wait_glod'])
        {
          ajaxReturnData(0,'可结算资金大于当前资金');  
        }
        
        //payment 付 Receivables 收
        //扣除付款金额
        
        $upReceivablesStatus = $this->subAccountObj->upReceivablesMemberInfos($data['receivablesOpenid'], $data['money']);

        //日志记录
        $insertData = array();
        $insertData['payer_openid']         = $this->memberInfo['openid'];
        $insertData['payee_openid']         = $data['receivablesOpenid'];
        $insertData['fee']                  = $data['money'];
        $insertData['sts_id']               = $this->memberInfo['store_sts_id'];
        $insertData['remark']               = $data['remark'];
        $insertData = $this->subAccountObj->addPayLog($insertData);
        
        ajaxReturnData(1,'扣款成功');
    }
    
    //发送验证码
    public function sendSms(){
        $data   = $this->request;
        $redata = array();
        //测试数据开始 1
        //$data['mobile'] = '18650393557';
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
                    if($checkData['sts_id'] == $this->memberInfo['store_sts_id'])
                    {
                        ajaxReturnData(0,'您已属于该店无需再次绑定');
                    }
                    
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
        //$data['checkcode'] = 59650;             //验证码
        //$data['mobile'] = '15806015164';                //手机号码
        //$data['earn_rate'] = 16;            //佣金设置
        //$data['openid'] = '2017070716755';
        //测试数据结束

	if($data['mobile'] == '' || $data['earn_rate'] == ''|| $data['openid'] == ''){
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
        $data   = $this->request;
        $redata = array();
	//测试数据开始
        //$data['openid'] = '2017070716755';
        //测试数据结束
        
	if($data['openid'] == ''){
	    ajaxReturnData(0,'必要参数不存在');
	}
	
	$memberInfo['memberInfo'] = $this->subAccountObj->getMemberinfo($data['openid'],'nickname,mobile,avatar,openid');
	if($memberInfo['memberInfo']['mobile'] == ''){
	    ajaxReturnData(0,'用户信息不存在');
	}
        
        //获取用户佣金比率
        $ruleData = $this->subAccountObj->getRuleMember($data['openid']);
        $memberInfo['memberInfo']['earn_rate'] = intval($ruleData['earn_rate']);
	ajaxReturnData(1,'获取成功！',$memberInfo);
    }
    
    //手机号码验证
    public function checkMobile(){
        $data   = $this->request;
        $redata = array();
        
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
    
   //手机号码验证
    public function checkMobileSms(){
	$data   = $this->request;
        $redata = array();
        //测试数据开始 1
        //$data['mobile'] = '15806015161';
        //$data['isCheckMobile'] = 1;
        //$data['isType'] = 1;
        //$data['checkcode'] = 94319;             //验证码
        //测试数据结束
        if($data['mobile']  == '' || $data['checkcode'] == '')
        {
            ajaxReturnData(0,'必要参数不存在');
        }
       
	//判断手机号码是否已经存在绑定关系
	$memberInfo = member_get_bymobile($data['mobile']);
	if($memberInfo['mobile'] != '')
	{
	    //判断该手机是否已经绑定过其他店铺
            $checkData = $this->subAccountObj->checkShop($memberInfo['openid'], '*');
	    
	    if($this->memberInfo['store_sts_id'] == $checkData['sts_id']){
		ajaxReturnData(0,'已经绑定该店铺无需再次绑定');
	    }
	    elseif($this->memberInfo['store_sts_id'] != $checkData['sts_id']){
		ajaxReturnData(0,'已经绑定其他店铺无法再次绑定');
	    }
	}
	//判断该手机号码是否已经绑定过其他店铺
	
       if(strtolower($_SESSION["addUser"][$data['mobile']]) == strtolower($data['checkcode'])) {
            ajaxReturnData(1,'验证成功');
        }else{
           //验证码有误
           ajaxReturnData(0,'验证失败');
        }
    } 
   
}
?>