<?php

/**
 * namespace还有待改进.
 * author: 王敬
 */

namespace seller\controller;

class password extends base {

    const TABLE_NAME = 'member';

    
    private function formValidate($data) {
        if(isset($data['VeryfyCode']) ){
            $isPass = checkSmsCode($data['VeryfyCode'],$data['mobile']) ;
            !$isPass &&  message("验证码不对或已过期，请重新接收验证码！", refresh(), 'error');
        }
        if(isset($data['checkpassword']) ){
            $isPass = encryptPassword($data['checkpassword'])==$_SESSION[MOBILE_ACCOUNT]['pwd'] ?true:false;
            !$isPass &&  message(LANG('旧密码不对，请检查输入'), refresh(), 'error');
        }
        if(isset($data['new_pwd1']) && isset($data['new_pwd2']) ){
            $isPass = $data['new_pwd1']==$data['new_pwd2'] ?true:false;
            !$isPass &&  message(LANG('新密码两次输入不一致'), refresh(), 'error');
        }
        
    }
    //这个接口肯定要用ajax请求
    public function requestCode() {
        #1表单校验
        if ( strlen(trim($this->request['mobile']))!=11 ){
			$result['message'] 	= '请检查手机号码长度是否为11位';
			$result['code'] 	= 0;
            die(showAjaxMess('0','请检查手机号码长度是否为11位！'));
        }
        #2表单校验
        $Recive_Phone_Number= trim($this->request['mobile']);
        $code = set_sms_code($Recive_Phone_Number,0,1);
        $_SESSION['api'][$Recive_Phone_Number] 		= $code;
        $_SESSION['api']['sms_code_expired']        = time()+120;		//短信的有效期,120s
        
        die(showAjaxMess('1','发送成功'));
    }

    public function index() {      
        $info = get_member_account() ;
        include page('password');
    }

    public function post() {
        //ppd(array_filter($this->request['store_category']));
    
        if (checksubmit('submit')) {
            $this->formValidate($this->request);
            
            $data = array(
                'store_pwd'         => encryptPassword($this->request['password'])
            );
         
            mysqld_update(self::TABLE_NAME, $data, array('store_id' => $this->request['id']));
            message(LANG('COMMON_UPDATE_SUCCESS'), web_url(self::TABLE_NAME, array('op' => 'display')), 'success');
        }
    }

    public function rePassword() {
        global  $_GP;        
        
        !$_GP['mobilecode'] && ajaxReturnData(0,'请输入验证码');
        !$_GP['new_pwd1'] && ajaxReturnData(0,'请输入新密码');
        
        $this->formValidate(array('new_pwd1'=>$_GP['new_pwd1'],'new_pwd2'=>$_GP['new_pwd2']));
        
        $self_user_info = get_member_account() ;
        $loginService = new \service\shopwap\loginService();
//        $self_user_info ['mobile'] = '18158205906';
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


}
