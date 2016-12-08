<?php
/**
 * RSA 加解密已签名验证相关类
 * 
 */
class Rsa {
	
	private $public_key;
	
	
	/**
	 * 构造函数
	 */
	function __construct()
	{
		$this->public_key	= file_get_contents(WEB_ROOT."/config/api_key/api_public_key.pem");
	}
	
	
	/**
	 * 公钥解密
	 * 
	 * $param $encrypt_data : 密文
	 * $return 解密后的字符
	 */
	public function public_decrypt($encrypt_data)
	{
		openssl_public_decrypt(base64_decode($encrypt_data),$decrypt_data,$this->public_key);

		return $decrypt_data;
	}
	
	/**
	 * 获取返回时的签名验证结果
	 * @param $data 待签名参数字符
	 * @param $sign 返回的签名结果
	 * @return boolean 签名验证结果
	 */
	function getSignVerify($data, $sign) {
		
		return rsaVerify($data, trim($this->public_key), $sign);
	}
}