<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace wapi\controller;

class confirm extends base
{
    public function __construct()
    {
        parent::__construct();
        if(!checkIsLogin()){
            ajaxReturnData(0,'请授权登录！');
        }
    }

    /**
     * 清单列表
     */
   public function index()
   {
       $_GP =  $this->request;
       $memInfo  = get_member_account();
       $service  = new \service\wapi\mycartService();
       $cart_where = "to_pay=1";
       $cartlist   = $service->cartlist($cart_where,1);
       //获取优惠卷和默认地址
       $defaultAddress   =   mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE openid ='{$memInfo['openid']}'  and isdefault =1 and  deleted = 0 ");
       $cartlist['default_address'] = $defaultAddress;
       //去除过期商品对象，在清单结算页 不需要
       unset($cartlist['out_gooslist']);
       ajaxReturnData(1,'请求成功',$cartlist);
   }

    /**
     * 从结算页进行提交订单结算
     参数 array(
                 address_id  => 2
                 bonus  => '2_68,3_89'  //表示店铺2 优惠卷 68  店铺3优惠卷89
     * )
     */
    public function topay()
    {
        $_GP =  $this->request;
        $weixinpay    = new \service\shopwap\weixinpayService(1);
        $orderservice = new \service\shopwap\payorderService();
        //插入订单的信息
        $res_data     = $orderservice->insertOrder($_GP);
        if(!$res_data){
              ajaxReturnData(0,$weixinpay->getError());
        }
        //库存操作
        $this->limit_buy_stock($res_data['stock_dishids']);

        $pay_data = array(
            'out_trade_no'  => $res_data['pay_ordersn'], //订单号
            'total_fee'     => $res_data['pay_total_money'], //订单金额，单位为分
            'body'          => $res_data['pay_title'],
        );

        $result = $weixinpay->weixinpay($pay_data);
        if (!$result) {
            ajaxReturnData(0,$weixinpay->getError());
        }else{
            ajaxReturnData(1,'操作成功!',$result);
        }
    }


    /**
     * 从个人中心中继续完成支付   参数orderid
     */
    public function payorder()
    {
        $_GP =  $this->request;
        $weixinpay    = new \service\shopwap\weixinpayService(1);
        $orderservice = new \service\shopwap\payorderService();
        //获取要支付的订单
        $res_data  = $orderservice->getPayOrder($_GP['orderid']);
        $pay_data = array(
            'out_trade_no'  => $res_data['pay_ordersn'], //订单号
            'total_fee'     => $res_data['pay_total_money'], //订单金额，单位为分
            'body'          => $res_data['pay_title'],
        );
        $return    = $weixinpay->weixinpay($pay_data);
        if(!$return){
            ajaxReturnData(0,$weixinpay->getError());
        }else{
            ajaxReturnData(1,'操作成功!',$return);
        }
    }

    /**
     * 插入订单后限时购的 商品对应库存操作
     */
    public function limit_buy_stock($stock_dishids)
    {
        if(empty($stock_dishids)) return '';
        $active = getCurrentAct();
        $ac_id  = $active['ac_id'];
        foreach($stock_dishids as $dishid => $buy_num){
            $sql = "update ".table('activity_dish')." set ac_dish_total=ac_dish_total-{$buy_num},ac_dish_sell_total=ac_dish_sell_total+{$buy_num}";
            $sql .= " where ac_shop_dish={$dishid} and ac_action_id={$ac_id}";
            mysqld_query($sql);
        }
    }

}