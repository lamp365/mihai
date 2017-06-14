<?php
namespace api\controller;
use api\controller;

class account extends base
{
	//工作台的数据
	public function index()
	{
		$_GP = $this->request;
		$memInfo = get_member_account();
		$accountService = new \service\seller\accountService();
		//综合评分
		$pingfen_rate   = $accountService->databord_pinfen_rate($memInfo['store_sts_id']);
		//销售额折线图
		$money_lie = $accountService->dashborder_money_line($memInfo['store_sts_id'],'month');
		//工作台一些订单  金额数据
		$dashboder_data = $accountService->dashbord_data_info($memInfo['store_sts_id']);

		$dashboder_data['pinfen']     = $pingfen_rate;
		$dashboder_data['money_line'] = $money_lie;

		ajaxReturnData(1,'请求成功！',$dashboder_data);

	}

	public function moneyline()
	{
		$_GP = $this->request;
		if(empty($_GP['type']) || !in_array($_GP['type'],array('week','month'))){
			ajaxReturnData(0,'类型参数不对！');
		}
		$memInfo = get_member_account();
		$accountService = new \service\seller\accountService();
		//销售额折线图
		$money_lie = $accountService->dashborder_money_line($memInfo['store_sts_id'],$_GP['type']);
		ajaxReturnData(1,'请求成功！',$money_lie);
	}

	//提现详情页
	public function outgold()
	{
		$_GP = $this->request;
		$accountService = new \service\seller\accountService();
		$out_info   = $accountService->outgold();
		ajaxReturnData(1,'请求成功',$out_info);
	}
	//提现提交
	public function do_outgold()
	{
		$_GP = $this->request;
		$accountService = new \service\seller\accountService();
		$res   = $accountService->do_outgold($_GP);
		if($res){
			ajaxReturnData(1,'系统正在审核中');
		}else{
			ajaxReturnData(0,$accountService->getError());
		}
	}
    //登录后的修改密码接口
    public function rePassword() {
        $_GP = $this->request;   
        
        !$_GP['mobilecode'] && ajaxReturnData(0,'请输入验证码');
        !$_GP['new_pwd1'] && ajaxReturnData(0,'请输入新密码');
        
        if(isset($_GP['new_pwd1']) && isset($_GP['new_pwd2']) ){
            $isPass = $_GP['new_pwd1']==$_GP['new_pwd2'] ?true:false;
            !$isPass && ajaxReturnData(0,(LANG('新密码两次输入不一致')));
        }
        
        $self_user_info = get_member_account() ;
        $loginService = new \service\shopwap\loginService();
        $data=array(
            'mobile'=>$self_user_info['mobile'],
            'mobilecode'=>$_GP['mobilecode'],
            'pwd'   =>$_GP['new_pwd1'],
        );
        $result = $loginService->resetPasswordByPhone($data);
        if($result===false){
            ajaxReturnData(0,(LANG('COMMON_OPERATION_FAIL')));  
        }else{
            ajaxReturnData(1,(LANG('COMMON_UPDATE_SUCCESS')));  
        }
    }

	/**
	 * 账单记录
	 */
	public function goldrecord()
	{
		$_GP = $this->request;
		$accountService = new \service\seller\accountService();
		$paylogInfo = $accountService->get_goldrecord($_GP);
		ajaxReturnData(1,'请求成功！',$paylogInfo);
	}

}