var verify = {
	//手机号码验证,val表示需要验证的值，dom参数是需要验证的节点
	phone:function(val,dom){
		if( !(/^1[34578]\d{9}$/.test(val)) ){
			layer.tips('请输入正确的手机号码', dom);
			return false;
		}else{
			return true;
		}
	},
	//密码验证,val表示需要验证的值，dom参数是需要验证的节点
	password:function(val,dom){
		if(!(/(.+){6,12}$/.test(val))){
			layer.tips('请输入6~12位数的密码', dom);
			return false;
		}else{
			return true;
		}
	},
	//手机验证码验证,val表示需要验证的值，dom参数是需要验证的节点
	mobilecode:function(val,dom){
		if(!(/^[0-9]{1,}$/.test(val))){
			layer.tips('请输入验证码', dom);
			return false;
		}else{
			return true;
		}
	}
}
