<?php

/**
* 数据验证类
*/
class Validator {
	
	/**
	 * 验证字段值是否为有效格式
	 * @access protected
	 * @param mixed    $value  字段值
	 * @param mixed    $rule  验证规则
	 * @return bool
	 */
	public function is($value, $rule)
	{
		switch ($rule) {
			case 'require':
				// 必须
				$result = !empty($value) || '0' == $value;
				break;
			case 'date':
				// 是否是一个有效日期
				$result = false !== strtotime($value);
				break;
			case 'alpha':
				// 只允许字母
				$result = $this->regex($value, '/^[A-Za-z]+$/');
				break;
            case 'num':
                // 只允许数字
                $result = $this->regex($value, '/^[0-9]+$/');
                break;
			case 'alphaNum':
				// 只允许字母和数字
				$result = $this->regex($value, '/^[A-Za-z0-9]+$/');
				break;
			case 'alphaDash':
				// 只允许字母、数字和下划线 破折号
				$result = $this->regex($value, '/^[A-Za-z0-9\-\_]+$/');
				break;
			case 'chs':
				// 只允许汉字
				$result = $this->regex($value, '/^[\x{4e00}-\x{9fa5}]+$/u');
				break;
			case 'chsAlpha':
				// 只允许汉字、字母
				$result = $this->regex($value, '/^[\x{4e00}-\x{9fa5}a-zA-Z]+$/u');
				break;
			case 'chsAlphaNum':
				// 只允许汉字、字母和数字
				$result = $this->regex($value, '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u');
				break;
			case 'chsDash':
				// 只允许汉字、字母、数字和下划线_及破折号-
				$result = $this->regex($value, '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\_\-]+$/u');
				break;
			case 'mobile':
				// 手机号码验证
				$result = $this->regex($value, '/^(13[0-9]|14[0-9]|15[0-9]|153|156|17[0-9]|18[0-9])[0-9]{8}$/');
				break;
				
			case 'activeUrl':
				// 是否为有效的网址
				$result = checkdnsrr($value);
				break;
			case 'ip':
				// 是否为IP地址
				$result = $this->ip($value, $rule);
				break;
			case 'url':
				// 是否为一个URL地址
				$result = $this->filter($value, FILTER_VALIDATE_URL);
				break;
			case 'float':
				// 是否为float
				$result = $this->filter($value, FILTER_VALIDATE_FLOAT);
				break;
			case 'number':
				$result = is_numeric($value);
				break;
			case 'integer':
				// 是否为整形
				$result = $this->filter($value, FILTER_VALIDATE_INT);
				break;
			case 'email':
				// 是否为邮箱地址
				$result = $this->filter($value, FILTER_VALIDATE_EMAIL);
				break;
			
			default:
				// 正则验证
				$result = $this->regex($value, $rule);
		}
		return $result;
	}
	
	/**
	 * 使用filter_var方式验证
	 * @access protected
	 * @param mixed     $value  字段值
	 * @param mixed     $rule  验证规则
	 * @return bool
	 */
	protected function filter($value, $rule)
	{
		if (is_string($rule) && strpos($rule, ',')) {
			list($rule, $param) = explode(',', $rule);
		} elseif (is_array($rule)) {
			$param = isset($rule[1]) ? $rule[1] : null;
		} else {
			$param = null;
		}
		return false !== filter_var($value, is_int($rule) ? $rule : filter_id($rule), $param);
	}
	
	
	/**
	 * 验证是否有效IP
	 * @access protected
	 * @param mixed     $value  字段值
	 * @param mixed     $rule  验证规则 ipv4 ipv6
	 * @return bool
	 */
	protected function ip($value, $rule)
	{
		if (!in_array($rule, array('ipv4', 'ipv6'))) {
			$rule = 'ipv4';
		}
		
		return false !== filter_var($value, FILTER_VALIDATE_IP, 'ipv6' == $rule ? FILTER_FLAG_IPV6 : FILTER_FLAG_IPV4);
	}
	
	/**
     * 使用正则验证数据
     * @access protected
     * @param mixed     $value  字段值
     * @param mixed     $rule  验证规则 正则规则或者预定义正则名
     * @return mixed
     */
    protected function regex($value, $rule)
    {
        if (isset($this->regex[$rule])) {
            $rule = $this->regex[$rule];
        }
        if (0 !== strpos($rule, '/') && !preg_match('/\/[imsU]{0,4}$/', $rule)) {
            // 不是正则表达式则两端补上/
            $rule = '/^' . $rule . '$/';
        }
        return 1 === preg_match($rule, (string) $value);
    }
    
    /**
     * 长度验证数据
     *
     * @param string $value  要验证的数据
     * @param string $range 长度范围 (例:'6,10')
     * @return boolean
     */
    public function lengthValidator($value,$range)
    {
    	$length  =  mb_strlen($value,'utf-8'); // 当前数据长度
    	
    	list($min,$max)   =  explode(',',$range);
    	
    	return $length >= $min && $length <= $max;
    }
    
    /**
     * 小数点位数判断
     * @param string $value  要验证的数据
     * @param int $len 小数点位数
     * @return boolean
     */
    public function decimalPlacesValidator($value,$len)
    {
    	$reg = '/^[-\+]?\d+(\.\d{1,'.$len.'})?$/';
    	
    	return preg_match($reg,$value)===1;
    }
    
    
    /**
     * 身份证验证
     *
     * @param unknown $idNum:身份证号码
     *
     * @return boolean
     */
    public function identityNumberValidator($idNum) {
    
    	$idNum = strtoupper($idNum);
    	$idLength = strlen($idNum);
    
    	$area = array(
    			11 =>"北京",
    			12 =>"天津",
    			13 =>"河北",
    			14 =>"山西",
    			15 =>"内蒙古",
    			21 =>"辽宁",
    			22 =>"吉林",
    			23 =>"黑龙江",
    			31 =>"上海",
    			32 =>"江苏",
    			33 =>"浙江",
    			34 =>"安徽",
    			35 =>"福建",
    			36 =>"江西",
    			37 =>"山东",
    			41 =>"河南",
    			42 =>"湖北",
    			43 =>"湖南",
    			44 =>"广东",
    			45 =>"广西",
    			46 =>"海南",
    			50 =>"重庆",
    			51 =>"四川",
    			52 =>"贵州",
    			53 =>"云南",
    			54 =>"西藏",
    			61 =>"陕西",
    			62 =>"甘肃",
    			63 =>"青海",
    			64 =>"宁夏",
    			65 =>"新疆",
    			71 =>"台湾",
    			81 =>"香港",
    			82 =>"澳门",
    			91 =>"国外"
    	);
    
    	//身份证号码不正确(地区非法)
    	if (!isset($area[(int)substr($idNum,0,2)])) {
    		return false;
    	}
    
    	if (!preg_match('/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', $idNum)) {
    		return false;
    	}
    	if($idLength == 18){
    		$xs = array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
    		$lst = '10X98765432';
    		$sumOfId = 0;
    		for ($i = 0; $i < 17; $i++) {
    			$sumOfId += intval($idNum[$i]) * $xs[$i];
    		}
    		$checkNum = $lst[$sumOfId % 11];
    		if ($idNum[17] != $checkNum) {
    			return false;
    		}
    	}
    
    	if($idLength == 18){
    		$birthDate = array (
    				substr($idNum, 6, 4),
    				substr($idNum, 10, 2),
    				substr($idNum, 12, 2)
    		);
    	}else{
    		$birthDate = array (
    				'19'.substr($idNum, 6, 2),
    				substr($idNum, 8, 2),
    				substr($idNum, 10, 2)
    		);
    	}
    
    	if(!checkdate($birthDate[1],$birthDate[2],$birthDate[0]))
    	{
    		return false;
    	}
    
    	return true;
    }
    
    /**
     * 密码强度检测
     * Password规则调整：至少$length位，密码至少要包含字母、数字、符号中的两种，
     * ******************规则结果：密码强度大于等于2的通过
     * 
     * @param $pwd 密码
     * @param $length 最少长度
     * 
     * @return integer 密码强度
     * 
     */
    public function passwordStrongValidator($pwd, $length = 6){
   
    	$length = (int)$length;
    	 
    	if($pwd == '' || mb_strlen($pwd, 'UTF-8') < $length){
    		return 0;
    	}
    	 
    	$strong = 0;
    	if(preg_match('/[0-9]+/', $pwd)){
    		$strong++;
    	}
    	if(preg_match('/[a-zA-z]+/', $pwd)){
    		if(strpos($pwd, '_') !== false){
    			$strong++;
    		}
    		$strong++;
    	}
    	if(preg_match('/[^\w]+/', $pwd)){
    		$strong++;
    	}

    	return $strong;
    }
}

