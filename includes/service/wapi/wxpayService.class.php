<?php
namespace service\wapi;

class wxpayService extends  \service\publicService
{
    protected $appid;
    protected $mch_id;
    protected $key;
    protected $openid;
    function __construct($appid,$openid,$mch_id,$key){
        parent::__construct();
        $this->appid  = $appid;
        $this->openid = $openid;
        $this->mch_id = $mch_id;
        $this->key    = $key;
    }

    //微信小程序接口
    public function pay($pay_ordersn,$pay_money,$pay_title){
        //统一下单接口
        $unifiedorder=$this->unifiedorder($pay_ordersn,$pay_money,$pay_title);
        $parameters=array(
            'appId'     => $this->appid,//小程序ID
            'timeStamp' => ''.time().'',//时间戳
            'nonceStr'  => $this->createNoncestr(),//随机串
            'package'   => 'prepay_id='.$unifiedorder['prepay_id'],//数据包
            'signType'  => 'MD5'//签名方式
        );
        //签名
        $parameters['paySign'] = $this->getSign($parameters);
        return $parameters;
    }

    //统一下单接口
    private function unifiedorder($pay_ordersn,$pay_money,$pay_title){
        //微信接口
        if(is_array($pay_ordersn)){  //如果有多条订单的话  用下划线分隔
            $pay_ordersn = implode('_',$pay_ordersn);
        }
        $url='https://api.mch.weixin.qq.com/pay/unifiedorder';
        $parameters=array(
            'appid'     => $this->appid,//小程序ID
            'mch_id'    => $this->mch_id,//商户号
            'nonce_str' => $this->createNoncestr(),//随机字符串
            'body'      => $pay_title,//商品描述
            'out_trade_no'     => $pay_ordersn,  //商户订单号  如果有多条订单的话  用下划线分隔
            'total_fee'        => $pay_money,//总金额 单位 分
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],//终端IP
            'notify_url'       => WEBSITE_ROOT . 'notify/weixin_notify.php',//通知地址
            'openid'           => $this->openid,//用户id
            'trade_type'       => 'JSAPI'//交易类型
        );
        //统一下单签名
        $parameters['sign'] = $this->getSign($parameters);
        $xmlData            = $this->arrayToXml($parameters);
        $postXmlSSLCurl     = $this->postXmlSSLCurl($xmlData,$url,60);
        $return             = $this->xmlToArray($postXmlSSLCurl);
        if($return['return_code'] == 'FAIL'){
            $member  = get_member_account();
            $memInfo = member_get($member['openid'],'mobile');
            logRecord("{$memInfo['mobile']}用户支付错误---{$return['return_msg']}",'payError');
            ajaxReturnData(0,'出错了,请重新再试!');
        }else if($return['result_code'] == 'FAIL'){
            $member  = get_member_account();
            $memInfo = member_get($member['openid'],'mobile');
            logRecord("{$memInfo['mobile']}用户支付错误---{$return['err_code_des']}",'payError');
            ajaxReturnData(0,'出错了,'.$return['err_code_des']);
        }
        return $return;
    }

    //作用：产生随机字符串，不长于32位
    private function createNoncestr($length = 32 ){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
    //作用：生成签名
    private function getSign($Obj){
        foreach ($Obj as $k => $v){
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".$this->key;
        //签名步骤三：MD5加密
        $String = md5($String);
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        return $result_;
    }
    ///作用：格式化参数，签名过程需要使用
    private function formatBizQueryParaMap($paraMap, $urlencode){
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v){
            if($urlencode)
            {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0){
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    //作用：array转xml
    public function arrayToXml($arr){
        $xml = "<xml>";
        foreach ($arr as $key=>$val){
            if (is_numeric($val))
                $xml.="<".$key.">".$val."</".$key.">";
            else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }
    //作用：将xml转为array
    public function xmlToArray($xml){
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }
    //作用：使用证书，以post方式提交xml到对应的接口url
    public function postXmlSSLCurl($xml,$url,$second=30){
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        //curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        //curl_setopt($ch,CURLOPT_SSLCERT, getcwd() . '/source/class/pay/Weixinnewpay/apiclient_cert.pem');
        //默认格式为PEM，可以注释
        //curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        //curl_setopt($ch,CURLOPT_SSLKEY, getcwd() . '/source/class/pay/Weixinnewpay/apiclient_key.pem');
        //post提交方式
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }else {
            $error = curl_errno($ch);
            $member  = get_member_account();
            $memInfo = member_get($member['openid'],'mobile');
            logRecord("{$memInfo['mobile']}用户支付错误!",'payError');
            curl_close($ch);
            ajaxReturnData(0,'出错了,请重新再试!');
        }
    }

    /**
     * 插入订单 参数
     * array(
            address_id  => 2
            bonus  => ['2_68','3_89']  //表示店铺2 优惠卷 68  店铺3优惠卷89
     * )
     * @param $data
     * @return bool
     */
    public function insertOrder($data)
    {
        $memInfo  = get_member_account();
        $openid   = $memInfo['openid'];
        $pay_ordersn     = array();
        $pay_total_money = 0;
        $pay_title       = '';

        if(empty($data['address_id'])){
            $this->error = '请选择对应的收货地址！';
            return false;
        }
        //获取地址
        $address = mysqld_select("select * from ".table('shop_address')." where id={$data['address_id']} and openid='{$openid}'");
        if(empty($address)){
            $this->error = '收货地址不存在！';
            return false;
        }
        //是否有选择优惠卷  $data['bonus'] = '8_18,38_89';
        $bonus = array();
        if(!empty($data['bonus'])){
            $bonus_list = explode(',',$data['bonus']);
            foreach($bonus_list as $one_item){
                $one_arr = explode('_',$one_item);
                if(count($one_arr) == 2){
                    $bonus[$one_arr[0]] = $one_arr[1];
                }
            }
        }

        $service  = new \service\wapi\mycartService();
        $cart_where = "to_pay=1";
        $cartlist   = $service->cartlist($cart_where,1);
        $goodslist  = $cartlist['goodslist'];
        if(empty($goodslist)){
            $this->error = '没有对应的商品';
            return false;
        }

        //获取推荐人openid  没有的话为空
        $recommend = mysqld_select("select p_openid from ".table('member_blong_relation')." where m_openid='{$openid}' and type=2");
        $recommend_openid = $recommend['p_openid'];

        foreach($goodslist as $item){
            $ordersns = 'SN'.date('Ymd') . random(6, 1);
            $randomorder = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE  ordersn=:ordersn limit 1", array(':ordersn' =>$ordersns));
            if(!empty($randomorder['ordersn'])) {
                $ordersns= 'SN'.date('Ymd') . random(6, 1);
            }
            $pay_ordersn[] = $ordersns;

            $price      = FormatMoney($item['totalprice'],1);  //转为分入库  总金额
            if($item['send_free'] == 1){
                //免邮啦 总价格等于 产品总价格
                $goodsprice  = FormatMoney($item['totalprice'],1);  //转为分入库  产品金额
                $express_fee = 0;
            }else{
                //没有免邮  总价扣掉运费 等于产品价格
                $goodsprice  = FormatMoney($item['totalprice']-$item['express_fee'],1);  //转为分入库  产品金额
                $express_fee = FormatMoney($item['express_fee'],1);
            }

            //优惠卷
            $bonus_id    = $bonus['sts_id'];
            $bonus_price = 0;
            if(array_key_exists($item['sts_id'],$bonus)){
                //从库里面取出来的 价格是分
                $bonus_price  = getCouponByMemidOnPay($bonus['sts_id'],$item['sts_id'],$item['dishlist'],'coupon_amount');
                if(empty($bonus_price)){
                    $bonus_id    = 0;
                    $bonus_price = 0;
                }
            }

            $order_data = array();
            $order_data['sts_id']           = $item['sts_id'];
            $order_data['openid']           = $openid;
            $order_data['recommend_openid'] = $recommend_openid;
            $order_data['ordersn']          = $ordersns;
            $order_data['ordertype']        = 4;                        //限时购
            $order_data['price']            = $price - $bonus_price;    //需要支付的总金额
            $order_data['goodsprice']       = $goodsprice;              //商品价格
            $order_data['dispatchprice']    = $express_fee;             //运费
            $order_data['status']           = 0;    //状态未付款
            $order_data['source']           = get_mobile_type();    //设备来源
            $order_data['sendtype']         = 0;    //快递发货
            $order_data['paytype']          = 2;    //在线付款
            $order_data['paytypecode']      = 1;    //微信支付
            $order_data['addressid']        = $data['address_id'];
            $order_data['createtime']       = time();
            $order_data['address_realname'] = $address['realname'];
            $order_data['address_province'] = $address['province'];
            $order_data['address_city']     = $address['city'];
            $order_data['address_area']     = $address['area'];
            $order_data['address_address']  = $address['address'];
            $order_data['address_mobile']   = $address['mobile'];
            $order_data['hasbonus']         = $bonus_id;
            $order_data['bonusprice']       = $bonus_price;

            mysqld_insert('shop_order',$order_data);
            $orderid = mysqld_insertid();
            if($orderid){
                $pay_total_money = $pay_total_money + $order_data['price'];  //单位是分
                //更新优惠卷为已经使用
                if(!empty($bonus_id))
                    mysqld_update('store_coupon_member',array('status'=>1),array('scmid'=>$bonus_id));

                $dishlist = $item['dishlist'];
                foreach($dishlist as $one_dish){
                    $pay_title    = str_replace('&','',$one_dish['title']);  //去除带有 & 的字符
                    //获取推广价
                    $promot_price = getPromotPrice($one_dish['id'],$item['sts_id'],$one_dish['time_price'],$item['sts_shop_type']);
                    $o_good = array();
                    $o_good['orderid']               = $orderid;
                    $o_good['sts_id']                = $item['sts_id'];
                    $o_good['dishid']                = $one_dish['id'];
                    $o_good['recommend_openid']      = $recommend_openid;
                    $o_good['shop_type']             = 4;
                    $o_good['price']                 = FormatMoney($one_dish['time_price'],1);  //商品单价 转为分
                    $o_good['promot_price']          = $promot_price;   //单位 分
                    $o_good['total']                 = $one_dish['buy_num'];
                    $o_good['createtime']            = time();
                    $res2 = mysqld_insert('shop_order_goods',$o_good);
                    if(!$res2){
                        //如果不成功  把提交给第三方的总额中去除该商品的价格
                        $pay_total_money = $pay_total_money -  $o_good['price'];
                    }
                }
            }
        }

        //移除购物车
        mysqld_delete("shop_cart",array('session_id'=>$openid,'to_pay'=>1));
        return array(
            'pay_ordersn'     => $pay_ordersn,  //数组型的 订单号
            'pay_total_money' => $pay_total_money,
            'pay_title'       => $pay_title,
        );
    }

    public function getPayOrder($orderid)
    {
        $memInfo = get_member_account();
        if(empty($orderid)){
            $this->error = '参数有误！';
            return false;
        }
        $order = mysqld_select("select ordersn,price,status from ".table('shop_order')." where id={$orderid} and openid='{$memInfo['openid']}'");
        if(empty($order)){
            $this->error = '订单不存在！';
            return false;
        }
        if($order['status']!=0){
            $this->error = '订单已经支付！';
            return false;
        }

        $o_sql   = "select d.title from ".table('shop_order_goods')." as g left join ".table('shop_dish')." as h";
        $o_sql  .= " on g.dishid=h.id where g.orderid={$order['id']}";
        $o_goods = mysqld_select($o_sql);
        $pay_title = str_replace('&','',$o_goods['title']);

        return array(
            'pay_ordersn'     => $order['ordersn'],   //单个订单号
            'pay_total_money' => $order['price'],
            'pay_title'       => $pay_title,
        );
    }
}
