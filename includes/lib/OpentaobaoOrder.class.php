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

    public function __construct()
    {
        $this->client = new TopClient;
        $this->client->appkey 	 = $this->appkey;
        $this->client->secretKey = $this->secretKey;
        $this->client->format 	 = 'json';
    }


    public function addGoodsStatus(){
        $req = new TradesSoldGetRequest;
        $req->setNumIid("2100663786375");
        $req->setNum("90");
        $sessionKey = '61014031f5f1824fc3cf2be4f250f217a4f257c16e8cffd2074082786';
        $resp = $this->client->execute($req, $sessionKey);
        ppd($resp);
    }
}