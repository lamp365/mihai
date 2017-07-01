<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/07/01
 * Time: 16:44
 **/
namespace service\shopwap;

class payorderService extends  \service\publicService
{
    /**
     * 插入订单 参数
     * array(
            address_id  => 2
            bonus  => '2_68,3_89'  //表示店铺2 优惠卷 68  店铺3优惠卷89
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

        //获取推荐人openid 以及推荐人从属的店铺  没有返回空
        $recommend = getRecommendOpenidAndStsid($openid);
        $recommend_openid = $recommend['recommend_openid'];
        $recommend_sts_id = $recommend['recommend_sts_id'];
        $earn_rate        = $recommend['earn_rate']; //商家约定给子账户 推广员的提成

        //开始遍历购物车的数据 写入到订单
        foreach($goodslist as $item){
            $total_store_earn_price  = 0;  //一笔订单的总提成
            $total_member_earn_price = 0;  //一笔订单的总提成
            $ordersns    = 'SN'.date('Ymd') . random(6, 1);
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
            $bonus_id    = $bonus[$item['sts_id']];
            $bonus_price = 0;
            if(array_key_exists($item['sts_id'],$bonus)){
                //从库里面取出来的 价格是分
                $bonus_price  = getCouponByMemidOnPay($bonus[$item['sts_id']],$item['sts_id'],$item['dishlist'],'coupon_amount');
                if(empty($bonus_price)){
                    $bonus_id    = 0;
                    $bonus_price = 0;
                }
            }

            $order_data = array();
            $order_data['sts_id']           = $item['sts_id'];
            $order_data['openid']           = $openid;
            $order_data['recommend_sts_id'] = $recommend_sts_id;
            $order_data['recommend_openid'] = $recommend_openid;
            $order_data['ordersn']          = $ordersns;
            $order_data['ordertype']        = 0;                        //普通订单
            $order_data['price']            = $price - $bonus_price;    //需要支付的总金额
            $order_data['goodsprice']       = $goodsprice;              //商品价格
            $order_data['dispatchprice']    = $express_fee;             //运费
            $order_data['status']           = 0;    //状态未付款
            $order_data['source']           = get_mobile_type(1);    //设备来源
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
                    mysqld_update('store_coupon_member',array('status'=>1,'use_time'=>time()),array('scmid'=>$bonus_id));

                $dishlist    = $item['dishlist'];
                $is_action   = 0;
                foreach($dishlist as $one_dish){
                    $pay_title    = str_replace('&','',$one_dish['title']);  //去除带有 & 的字符
                    //获取商品对应的提成的收入价格 商家所得佣金和商家约定给推广员的提成   返回分为单位
                    $earn_price = getStoreAndMemberEarnPrice($one_dish['id'],$item['sts_id'],$one_dish['time_price'],$item['sts_shop_type'],$earn_rate);
                    $store_earn_price  = $earn_price['store_earn_price'];
                    $member_earn_price = $earn_price['member_earn_price'];
                    $o_good = array();
                    $o_good['orderid']               = $orderid;
                    $o_good['sts_id']                = $item['sts_id'];
                    $o_good['dishid']                = $one_dish['id'];
                    $o_good['action_id']             = $one_dish['action_id'];
                    $o_good['shop_type']             = empty($one_dish['action_id']) ? 0 : 4;
                    $o_good['price']                 = FormatMoney($one_dish['time_price'],1);  //商品单价 转为分
                    $o_good['store_earn_price']      = empty($recommend_sts_id) ? 0 : $store_earn_price;   //单个商品 提成 单位 分
                    $o_good['member_earn_price']     = empty($recommend_openid) ? 0 : $member_earn_price;   //单个商品 提成 单位 分
                    $o_good['total']                 = $one_dish['buy_num'];
                    $o_good['createtime']            = time();
                    $res2 = mysqld_insert('shop_order_goods',$o_good);
                    if(!$res2){
                        //如果不成功  把提交给第三方的总额中去除该商品的价格
                        $pay_total_money = $pay_total_money -  $o_good['price'];
                    }else{
                        if($one_dish['action_id']){
                            $is_action = 1;
                        }
                        //单个商品提成乘以个数
                        $total_store_earn_price  += $store_earn_price*$o_good['total'];
                        $total_member_earn_price += $member_earn_price*$o_good['total'];
                        //库存的操作减掉 卖出数量加1
                        operateStoreCount($one_dish['id'],$one_dish['buy_num'],$one_dish['action_id'],1);
                    }

                }

                //跟新该笔订单的总提成
                $order_update = array(
                    'store_earn_price' => $total_store_earn_price,
                    'member_earn_price'=> $total_member_earn_price
                );
                //有一个是限时购的 该订单表示限时购订单
                if($is_action){
                    $order_update['ordertype'] = 4;
                }
                mysqld_update('shop_order',$order_update,array('id'=>$orderid));
            }
        }

        //移除购物车
        mysqld_delete("shop_cart",array('session_id'=>$openid,'to_pay'=>1));
        return array(
            'pay_ordersn'     => $pay_ordersn,  //数组型的 订单号
            'pay_total_money' => $pay_total_money,
            'pay_title'       => $pay_title
        );
    }

    public function getPayOrder($orderid)
    {
        $memInfo = get_member_account();
        if(empty($orderid)){
            $this->error = '参数有误！';
            return false;
        }
        $order = mysqld_select("select id,ordersn,price,status from ".table('shop_order')." where id={$orderid} and openid='{$memInfo['openid']}'");
        if(empty($order)){
            $this->error = '订单不存在！';
            return false;
        }
        if($order['status']!=0){
            $this->error = '订单已经支付！';
            return false;
        }

        $o_sql   = "select h.title from ".table('shop_order_goods')." as g left join ".table('shop_dish')." as h";
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
