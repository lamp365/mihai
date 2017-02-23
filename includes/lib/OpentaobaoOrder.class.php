<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/2/22
 * Time: 11:11
 */
require_once WEB_ROOT . '/includes/TopSdk.php';

class OpentaobaoOrder{
    public $appkey		= '23499623';
    public $secretKey	= 'e2a5c71e4eca9cc7e4d6141ce5c5f0b4';
    public $client      = '';		//TopClient对象

    public $oauthurl         = 'https://oauth.taobao.com/authorize';   //正式环境的
    public $oauthtokenurl    = 'https://oauth.taobao.com/token';   //正式环境的

    public function __construct()
    {
        $this->client = new TopClient;
        $this->client->appkey 	 = $this->appkey;
        $this->client->secretKey = $this->secretKey;
        $this->client->format 	 = 'json';
    }

    /**
     * 获取access_token
     * 获取code
     * https://oauth.taobao.com/authorize?response_type=code&client_id=23499623&redirect_uri=http://dev-hinrc.com/opentaobaoAuth.php&state=1212&view=web
     */
    public function get_access_token(){
        $url = 'https://oauth.taobao.com/token';
        $postfields= array(
            'grant_type'   => 'authorization_code',
            'client_id'    => $this->appkey,
            'client_secret'=> $this->secretKey,
            'code'         => '4zRlqHMkk50qR4iT6Idw6Hsg3923724',
            'redirect_uri' => 'http://dev-hinrc.com/opentaobaoAuth.php'
        );
        $post_data = '';

        foreach($postfields as $key=>$value){
            $post_data .="$key=".urlencode($value)."&";
        }
        $post_data = rtrim($post_data,'&');
        $res = http_post($url,$post_data);
        $res = json_decode($res,true);
        ppd($res);
        /**
        Array
        (
        [taobao_user_nick] => %E5%86%B7%E9%A3%8Et%E5%AF%92%E6%84%8F
        [re_expires_in] => 0
        [expires_in] => 7776000
        [expire_time] => 1495597069018
        [r1_expires_in] => 1800
        [w2_valid] => 1487821069018
        [w2_expires_in] => 0
        [taobao_user_id] => 1061196572
        [w1_expires_in] => 1800
        [r1_valid] => 1487822869018
        [r2_valid] => 1487821069018
        [w1_valid] => 1487822869018
        [r2_expires_in] => 0
        [token_type] => Bearer
        [refresh_token] => 62013269b05df0e4c5a24b1a5bbbd2a5a8ZZ411b77e8e7c1061196572
        [refresh_token_valid_time] => 1487821069018
        [access_token] => 62010267fa6b0a6231fd9835919770623ace29d5e5021581061196572
        )
         */

    }

    public function getTmallOrder(){
        $req = new TradesSoldGetRequest;
        $req->setFields("seller_nick,buyer_nick,title,type,created,sid,tid,seller_rate,buyer_rate,status,payment,discount_fee,adjust_fee,post_fee,total_fee,pay_time,end_time,modified,consign_time,buyer_obtain_point_fee,point_fee,real_point_fee,received_payment,commission_fee,pic_path,num_iid,num_iid,num,price,cod_fee,cod_status,shipping_type,receiver_name,receiver_state,receiver_city,receiver_district,receiver_address,receiver_zip,receiver_mobile,receiver_phone,orders.title,orders.pic_path,orders.price,orders.num,orders.iid,orders.num_iid,orders.sku_id,orders.refund_status,orders.status,orders.oid,orders.total_fee,orders.payment,orders.discount_fee,orders.adjust_fee,orders.sku_properties_name,orders.item_meal_name,orders.buyer_rate,orders.seller_rate,orders.outer_iid,orders.outer_sku_id,orders.refund_id,orders.seller_type");
        $req->setStartCreated("2017-01-01 00:00:00");  //交易创建时间    开始时间
        $req->setEndCreated("2017-02-01 00:00:00");    //交易创建时间    结束时间
        $req->setStatus("TRADE_FINISHED");
        $req->setPageNo(1);
        $resp = $this->client->execute($req, $sessionKey);
    }
}