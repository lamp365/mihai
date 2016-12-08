<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/22 0021
 * Time: 9:43
 * content: DES对称加密
 */
class DESService
{
    const VERSION_V_1 = 1;
    const VERSION_V_2 = 2;

    private static $version = DESService::VERSION_V_2;

    private $aReplace = array(
        '+' => '**',
        '=' => '!!',
        '/' => '!',
        '%' => '_'
    );

    private $encrypter;

    /**
     * @var DESService
     */
    private static $selfV1 = NULL;

    /**
     * @var DESService
     */
    private static $selfV2 = NULL;

    static public function instance($version = DESService::VERSION_V_2)
    {

        if ($version === self::VERSION_V_1) {
            if (self::$selfV1 == NULL) {
                self::$selfV1 = new self($version);
            }

            return self::$selfV1;
        } else {
            if (self::$selfV2 == NULL) {
                self::$selfV2 = new self($version);
            }

            return self::$selfV2;
        }
    }

    private $key;
    private $iv; //偏移量

    public function __construct($version)
    {
        self::$version = $version;

        $des_key = 'c4bd856163d50d953afca305345642c1';
        $des_iv  = '62680693';

        if (self::$version == $this::VERSION_V_1) {
            $this->key = substr($des_key, 0, 8);
            $this->iv  = $des_iv;
        } else {
            $this->key       = $des_iv;
            $this->encrypter = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_CBC, '');
        }
    }

    //加密
    public function encode($str)
    {
        if (self::$version == $this::VERSION_V_1) {
            return $this->encode_v1($str);
        }

        return $this->encode_v2($str);
    }

    //解密
    public function decode($str)
    {
        if (self::$version == $this::VERSION_V_1) {
            return $this->decode_v1($str);
        }

        return $this->decode_v2($str);
    }

    private function encode_v1($str)
    {
        $size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);
        $str  = $this->pkcs5Pad_v1($str, $size);

        $data = mcrypt_cbc(MCRYPT_DES, $this->key, $str, MCRYPT_ENCRYPT, $this->iv);
        $data = base64_encode($data);
        $data = str_replace(array_keys($this->aReplace), array_values($this->aReplace), $data);
        return $data;
    }

    private function decode_v1($str)
    {
        $str = str_replace(array_values($this->aReplace), array_keys($this->aReplace), $str);
        $str = base64_decode($str);
        $str = mcrypt_cbc(MCRYPT_DES, $this->key, $str, MCRYPT_DECRYPT, $this->iv);
        $str = $this->pkcs5Unpad_v1($str);

        return $str;
    }

    private function encode_v2($str)
    {
        $str = $this->pkcs5Pad_v2($str, mcrypt_enc_get_block_size($this->encrypter));
        mcrypt_generic_init($this->encrypter, $this->key, substr($this->key, 0, 8));
        $ciphertext = mcrypt_generic($this->encrypter, $str);
        mcrypt_generic_deinit($this->encrypter);

        $ciphertext = str_replace(array_keys($this->aReplace), array_values($this->aReplace), base64_encode($ciphertext));

        return $ciphertext;
    }

    private function decode_v2($str)
    {
        $str = str_replace(array_values($this->aReplace), array_keys($this->aReplace), $str);
        $str = base64_decode($str);

        mcrypt_generic_init($this->encrypter, $this->key, substr($this->key, 0, 8));
        $origData = mdecrypt_generic($this->encrypter, $str);
        mcrypt_generic_deinit($this->encrypter);
        return $this->pkcs5Unpad_v2($origData);
    }

    private function pkcs5Pad_v1($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);

        return $text . str_repeat(chr($pad), $pad);
    }

    private function pkcs5Unpad_v1($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text))
            return FALSE;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return FALSE;

        return substr($text, 0, -1 * $pad);
    }

    private function pkcs5Pad_v2($data, $blocksize)
    {
        $padding     = $blocksize - strlen($data) % $blocksize;
        $paddingText = str_repeat(chr($padding), $padding);
        return $data . $paddingText;
    }

    private function pkcs5Unpad_v2($data)
    {
        $length    = strlen($data);
        $unpadding = ord($data[$length - 1]);
        return substr($data, 0, $length - $unpadding);
    }

    public function __descruct()
    {
        mcrypt_module_close($this->encrypter);
    }

    /**
     * 将md5字符串转化为10进制
     * @param $str
     * @return string
     */
    public function md5ToDec($str)
    {
        $str_code = '';
        for ($i = 0; $i < 16; $i++) {
            $str_code .= (string)floor(hexdec($str[$i]) / 16 * 10);
            if($i==0 && $str_code == '0'){
                $str_code = '1'.$str_code;
            }
        }
        return $str_code;
    }
}