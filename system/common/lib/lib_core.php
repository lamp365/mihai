<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小物社区 <QQ:119006873> <http://www.squdian.com>
// +----------------------------------------------------------------------
defined('SYSTEM_IN') or exit('Access Denied');
function hidtel($phone){
    $IsWhat = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i',$phone); //固定电话
    if($IsWhat == 1){
        return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i','$1****$2',$phone);
    }else{
        return  preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$phone);
    }
}

//记录了 paylog  该方法随着业务改变  已经没有价值了 不要再使用，  剩下一条paylog记录可以在外面做，
//true卖出  false退回来
//id为order  id
function updateOrderStock($id , $minus = true) {
        $order      = mysqld_select("select * from ". table('shop_order') ." where id={$id}");
        $ordergoods = mysqld_selectall("SELECT * FROM " . table('shop_order_goods') . " WHERE orderid='{$id}'");
        $usermoney  = array();
        $str_link   = '';
        foreach ($ordergoods as $item) {

            //记录卖家得到的佣金账单  bill每个产品对应一个记录
            if(!empty($item['seller_openid']) && !empty($item['commision'])){  //如果有卖家openid则，则计算佣金

                if(array_key_exists($item['seller_openid'], $usermoney)){
                    $usermoney[$item['seller_openid']][] = $item;
                    $usermoney[$item['seller_openid']]['seller_commision'] += $item['commision'];
                }else{
                    $usermoney[$item['seller_openid']][] = $item;
                    $usermoney[$item['seller_openid']]['seller_commision'] = $item['commision'];
                }
            }

            $url = WEBSITE_ROOT.mobile_url('detail',array('name'=>'shopwap','op'=>'dish','id'=>$item['goodsid']));
            $str_link .= "<a href='{$url}' target='_blank'>商品</a>、";
        }

        $str_link = rtrim($str_link,'、');
        //记录paylog
        if($minus) {
            $mark  = "订单:{$order['ordersn']}购买 {$str_link} 消费费用";
            member_gold($order['openid'],$order['price'],'usegold',$mark,false);  //paylog
        }else{
            $mark  = "{$order['openid']}@订单:{$order['ordersn']} {$str_link}发生退款费用";
             member_gold($order['openid'],$order['price'],'addgold',$mark,false);  //paylog
        }


        //记录卖家得到的佣金账单 paylog
        //确认收货后，直接把佣金打入对方账户  不用支付后转入冻结资金
       /* if(!empty($usermoney)) {
            $str_link = '';
            foreach ($usermoney as $openid => $data) {
                foreach($data as $row){
                    $url = WEBSITE_ROOT.mobile_url('detail',array('name'=>'shopwap','op'=>'dish','id'=>$row['goodsid']));
                    $str_link .= "<a href='{$url}' target='_blank'>商品</a>、";
                }
                $str_link = rtrim($str_link,'、');
                if($minus) {
                    $type2 = 'addgold_byorder';
                    $mark = "订单:{$order['ordersn']}购买 {$str_link} 得到佣金";
                }else {
                    $type2 = 'usegold_byorder';
                    $mark = "订单:{$order['ordersn']} {$str_link} 发生退款扣除佣金";
                }
                member_commisiongold($openid,$order['openid'],$data['seller_commision'],$type2,$mark);
            }
        }*/

}

//记录了 paylog  该方法随着业务改变  已经没有价值了 不要再使用，  剩下一条paylog记录可以在外面做，
//单个单个退款  卖出去时用true  退款用false id是order_goods中的id
function oneUpdateOrderStock($id , $minus = true) {
    $item  = mysqld_select("SELECT * FROM " . table('shop_order_goods') . " WHERE id='{$id}'");
    $order = mysqld_select("select * from " .table('shop_order'). " where id={$item['orderid']}");
    $aftersale = mysqld_select("select refund_price from ".table('aftersales')." where order_goods_id={$id}");

    $url = WEBSITE_ROOT.mobile_url('detail',array('name'=>'shopwap','op'=>'dish','id'=>$item['goodsid']));
    $str_link = "<a href='{$url}' target='_blank'>商品</a>、";

    //记录账单买家出入资金钱
    if($minus) {
        $type2 = 'usegold';
        $mark  = "订单:{$order['ordersn']}购买 {$str_link} 消费费用";
        $money = $item['price'];
    }else{
        $type2 = 'addgold';
        $money = empty($aftersale['refund_price']) ? $item['price'] : $aftersale['refund_price'];
        $mark  = "订单:{$order['ordersn']} {$str_link}发生退款费用";
    }

    member_gold($order['openid'],$money,$type2,$mark,false);  //paylog

    //记录卖家得到的佣金账单
    if($minus) {
        $type2 = 'addgold_byorder';
        $mark = "订单:{$order['ordersn']}购买 {$str_link} 得到佣金";
    }else {
        $type2 = 'usegold_byorder';
        $mark = "订单:{$order['ordersn']} {$str_link} 发生退款扣除佣金";
    }
    //确认收货后，直接把佣金打入对方账户  不用支付后转入冻结资金
   /* if(!empty($item['seller_openid']) && !empty($item['commision'])){
        member_commisiongold($item['seller_openid'],$order['openid'],$item['commision'],$type2,$mark);
    }*/

}