<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/6/22
 * Time: 16:10
 */
/**
 * 调试通知返回时，可查看或改写log日志的写入TXT里的数据，来检查通知返回是否正常
 */
class AlipayNotify {
    //HTTPS形式消息验证地址
    var $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
    //HTTP形式消息验证地址
    var $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
    var $alipay_config;

    function __construct($alipay_config) {
        $this->alipay_config = $alipay_config;
    }

    function AlipayNotify($alipay_config) {
        $this->__construct($alipay_config);
    }
    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
    function verifyNotify() {
        if (empty($_POST)) {//判断POST来的数组是否为空
            return false;
        } else {
            $isSign = $this->getSignVeryfy($_POST, $_POST["sign"]);
            $responseTxt = 'false';
            if (!empty($_POST["notify_id"])) {
                $responseTxt = $this->getResponse($_POST["notify_id"]);
            }
            if (preg_match("/true$/i", $responseTxt) && $isSign) {
                return true;
            } else {
                return false;
            }
        }
    }
    /**
     * 针对return_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
    function verifyReturn() {
        if (empty($_GET)) {//判断GET来的数组是否为空
            return false;
        } else {
            $isSign = $this->getSignVeryfy($_GET, $_GET["sign"]);
            $responseTxt = 'false';
            if (!empty($_GET["notify_id"])) {
                $responseTxt = $this->getResponse($_GET["notify_id"]);
            }
            if (preg_match("/true$/i", $responseTxt) && $isSign) {
                return true;
            } else {
                return false;
            }
        }
    }
    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    function getSignVeryfy($para_temp, $sign) {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = alipay_paraFilter($para_temp);
        ksort($para_filter);
        reset($para_filter);
        //对待签名参数数组排序
        $para_sort = $para_filter;
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = alipay_createLinkstring($para_sort);
        $isSgin = false;
        switch (strtoupper(trim($this->alipay_config['sign_type']))) {
            case "MD5" :
                $isSgin = md5($prestr . $this->alipay_config['key']) == $sign ? true : false;
                break;
            default :
                $isSgin = false;
        }
        return $isSgin;
    }
    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
    function getResponse($notify_id) {
        $transport = strtolower(trim($this->alipay_config['transport']));
        $partner = trim($this->alipay_config['partner']);
        $veryfy_url = '';
        if ($transport == 'https') {
            $veryfy_url = $this->https_verify_url;
        } else {
            $veryfy_url = $this->http_verify_url;
        }
        $veryfy_url = $veryfy_url . "partner=" . $partner . "&notify_id=" . $notify_id;
        $responseTxt = alipay_getHttpResponseGET($veryfy_url, $this->alipay_config['cacert']);
        return $responseTxt;
    }

}
