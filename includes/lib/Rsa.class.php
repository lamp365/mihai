<?php
/**
 * RSA 加解密已签名验证相关类
 *
 */
class Rsa {

	private $public_key;
	private $rsa_private_key;
	public $secretKey;
	/**
	 * 构造函数
	 */
	function __construct($secretKey = '')
	{
		$this->public_key	= file_get_contents(WEB_ROOT."/config/api_key/api_public_key.pem");
		$this->rsa_private_key = file_get_contents(WEB_ROOT."/config/api_key/rsa_private_key.pem");
		$this->rsa_public_key = file_get_contents(WEB_ROOT."/config/api_key/rsa_public_key.pem");
		// 进行$secretKey RSA 解密
		if ( !empty($secretKey) ){
			$this->secretKey = $this->rsaPriDecrypt($secretKey);
		}
	}
	//生产KEY， 并传输给APP
	function pass_key(){
		$_code = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
		$_code = md5($_code);
		$_size  = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
		if (strlen($_code) != $_size){
			$_code = substr($_code, 0, $_size);
		}
		return $_code;
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

	/**
	 * 转换为openssl格式密钥  rsa 私钥加密
	 * @param $data
	 * @return bool|string
	 */
	public function rsaPriEncrypt($data) {
		//转换为openssl格式密钥
		$res = openssl_get_privatekey($this->rsa_private_key);
		if (!openssl_private_encrypt($data, $a, $res)) {
			return false;
		}
		return base64_encode($a);
	}

	/**
	 * 转换为openssl格式密钥  rsa 公钥加密
	 * @param $data
	 * @return bool|string
	 */
	public function rsaPubEncrypt($data) {
		//转换为openssl格式密钥
		$res = openssl_get_publickey($this->rsa_public_key);
		if (!openssl_public_encrypt($data, $a, $res)) {
			return false;
		}
		return base64_encode($a);
	}
	// 公钥解密
	public function rsaPubDecrypt($data, $rsaPubKeyPem) {
		$data   = base64_decode($data);
		//读取私钥文件
		$priKey = file_get_contents($rsaPubKeyPem);
		//转换为openssl格式密钥
		$res = openssl_get_publickey($priKey);
		if (!openssl_public_decrypt($data, $dcyCont, $res)) {
			return false;
		}
		return $dcyCont;
	}
	// 私钥解密
	public function rsaPriDecrypt($data) {
		$data   = base64_decode($data);
		//转换为openssl格式密钥
		$res = openssl_get_privatekey($this->rsa_private_key);
		if (!openssl_private_decrypt($data, $dcyCont, $res)) {
			return false;
		}
		return $dcyCont;
	}
	/**
	 * @param string $encryptedText
	 * @return string
	 */
	public function decrypt($encryptedText)
	{
		if (empty($this->secretKey)){
			return false;
		}
		$key = $this->secretKey;
		$cryptText = base64_decode($encryptedText);
		//$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
		$iv = '';
		$decryptText = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $cryptText, MCRYPT_MODE_ECB, $iv);
		return trim($decryptText);
	}

	/**
	 * @param string $plainText
	 * @return string
	 */
	public function encrypt($plainText)
	{
		if (empty($this->secretKey)){
			return false;
		}
		$key = $this->secretKey;
		//$ivSize = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
		$iv = '';
		$encryptText = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plainText, MCRYPT_MODE_ECB, $iv);
		return trim(base64_encode($encryptText));
	}
}