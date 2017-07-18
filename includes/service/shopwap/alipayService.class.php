<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/07/01
 * Time: 18:29
 **/
namespace service\shopwap;

class alipayService extends \service\publicService
{
    private $alipay_config = array(
        'sign_type'      => 'MD5',
        '_input_charset' => 'utf-8',
        'cacert'         => '',
        'transport'      => 'http',
        'payment_type'   => '1',
        'service'        => 'create_direct_pay_by_user',
        'anti_phishing_key' => '',
        'exter_invoke_ip'   => '',
    );

    /**
     * 构造函数
     * @param type $data
     */
    public function __construct() {
        parent::__construct();
        $payment = mysqld_select("SELECT * FROM " . table('payment') . " WHERE  enabled=1 and code='alipay' limit 1");
        $configs = unserialize($payment['configs']);
        if ( !is_mobile_request()){
            $this->alipay_config['service'] = "create_direct_pay_by_user";
        }else{
            $this->alipay_config['service'] = "alipay.wap.create.direct.pay.by.user";
        }

        $this->alipay_config['partner']     = $configs['alipay_safepid'];  //支付宝partner，2088开头数字
        $this->alipay_config['seller_id']   = $configs['alipay_safepid'];  //卖家支付宝账号（邮箱或手机号码格式）或其对应的支付宝唯一用户号（以2088开头的纯16位数字）。
        $this->alipay_config['key']         = $configs['alipay_safekey'];  //支付宝秘钥
    }

    /**
     * 支付宝支付
     * @param type [] 接口参数
     * @return type []
     */
    public function alipay($data = array())
    {
        if(empty($data['subject'])){
            $this->error = '标题不能为空！';
            return false;
        }
        if(empty($data['out_trade_no']) || empty($data['total_fee'])){
            $this->error = '订单编号和金额不能为空！';
            return false;
        }
        if(empty($data['return_url']) || empty($data['notify_url'])){
            $this->error = '异步和通知地址不能为空！';
            return false;
        }
        $config = $this->alipay_config;
        $parameter = array(
            "service"           => $config['service'],
            "partner"           => $config['partner'],
            "seller_id"         => $config['seller_id'],
            "payment_type"      => 1,
            "notify_url"        => $data['notify_url'],
            "return_url"        => $data['return_url'],
            "anti_phishing_key" => $config['anti_phishing_key'],
            "exter_invoke_ip"   => $config['exter_invoke_ip'],
            "out_trade_no"      => $data['out_trade_no'],  //订单号
            "subject"           => $data['subject'],       //标题
            "total_fee"         => $data['total_fee'],
            "app_pay"	        => "Y",
            "body"              => $data['body'],
            "show_url"          => $data['show_url'],  //需要支付成功后 返回的地址
            "_input_charset"    => 'utf-8'
        );
        $alipaySubmit = new \AlipaySubmit($config);
        return $alipaySubmit->buildRequestForm($parameter, "get", "确认");
    }

    /**
     * 异步回调
     * @return string
     */
    public function notify_alipay()
    {
        $config       = $this->alipay_config;
        $alipayNotify = new \AlipayNotify($config); //计算得出通知验证结果
        if ($result = $alipayNotify->verifyNotify()) {
            //验签成功
            if ($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
                // 订单号   5_6_1204454545   5和6表示订单id  最后一个是为了表示唯一性，加的一个标识  只能32位数以内
                $ordersn     = $_POST['out_trade_no'];
                $ordersn_arr = explode('_',$ordersn);   //多商家导致，可能有多个订单号
                array_pop($ordersn_arr);
                //成功后的后续操作/**
                // 支付完毕 处理账单 佣金提成，卖家所得，平台费率
                $settings = globaSetting();
                foreach($ordersn_arr as $ordersn){
                    paySuccessProcess($ordersn,$settings);
                }
                return true;
            }else{
                $member  = get_member_account();
                $memInfo = member_get($member['openid'],'mobile');
                logRecord("{$memInfo['mobile']}用户支付业务错误",'payError');
                $this->error = '业务错误';
                return false;
            }
        }else{
            //验证失败  记录日志
            $member  = get_member_account();
            $memInfo = member_get($member['openid'],'mobile');
            logRecord("{$memInfo['mobile']}用户支付异步签名验证失败",'payError');
            $this->error = '支付失败';
            return false;
        }
    }

    /**
     * 同步回调  返回的数据
     *Array
    (
    [mod] => mobile
    [name] => shopwap
    [do] => alipay
    [op] => returnurl
    [body] => 测试商品
    [buyer_email] => 791845283@qq.com
    [buyer_id] => 2088802661101009
    [exterface] => create_direct_pay_by_user
    [is_success] => T
    [notify_id] => RqPnCoPT3K9%2Fvwbh3InYwe9UQaecKY9y3krILMLAzIFwEVVFOIAEcfZx4sSZwAlhKQTA
    [notify_time] => 2017-06-22 17:27:33
    [notify_type] => trade_status_sync
    [out_trade_no] => sn099239283879
    [payment_type] => 1
    [seller_email] => 33413434@qq.com
    [seller_id] => 2088321009666241
    [subject] => sn099239283879
    [total_fee] => 0.01
    [trade_no] => 2017062221001004000219681644
    [trade_status] => TRADE_SUCCESS
    [sign] => 280e4387a81f3e7cd2f28aa0f4203a12
    [sign_type] => MD5
    )
     */
    public function return_alipay()
    {
        $config = $this->alipay_config;
        $alipayNotify  = new \AlipayNotify($config); //计算得出通知验证结果
        $verify_result = $alipayNotify->verifyReturn();
        if ($verify_result) {
            //验证成功
            if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                // 订单号   5_6_1204454545   5和6表示订单id  最后一个是为了表示唯一性，加的一个标识  只能32位数以内
                $ordersn     = $_GET['out_trade_no'];
                $ordersn_arr = explode('_',$ordersn);   //多商家导致，可能有多个订单号
                array_pop($ordersn_arr);
                //成功后的后续操作/**
                // 支付完毕 处理账单 佣金提成，卖家所得，平台费率
                $settings = globaSetting();
                foreach($ordersn_arr as $ordersn){
                    paySuccessProcess($ordersn,$settings);
                }
                return true;
            } else {
                $member  = get_member_account();
                $memInfo = member_get($member['openid'],'mobile');
                logRecord("{$memInfo['mobile']}用户支付业务错误",'payError');
                $this->error = '业务错误';
                return false;
            }
        } else {
            //验证失败  记录日志
            $member  = get_member_account();
            $memInfo = member_get($member['openid'],'mobile');
            logRecord("{$memInfo['mobile']}用户支付异步签名验证失败",'payError');
            $this->error = '支付失败';
            return false;
        }
    }


}