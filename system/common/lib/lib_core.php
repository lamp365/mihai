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

//记录了账单bill  和 paylog  库存处理 以及 卖出去多少件处理
//true卖出  false退回来
//id为order  id
function updateOrderStock($id , $minus = true) {
        $order      = mysqld_select("select * from ". table('shop_order') ." where id={$id}");
        $ordergoods = mysqld_selectall("SELECT * FROM " . table('shop_order_goods') . " WHERE orderid='{$id}'");
        $usermoney  = array();
        $str_link   = '';
        foreach ($ordergoods as $item) {
        	$goods = mysqld_select("SELECT * FROM " . table('shop_dish') . "  WHERE id='".$item['goodsid']."'");
            if ($minus) {  //卖出
                $data = array();
                 if($goods['totalcnf']!=1)
                 {
                     $data['total'] = $goods['total'] - $item['total'];
                 }
                $data['sales'] = $goods['sales'] + $item['total'];
                mysqld_update('shop_dish', $data, array('id' => $item['goodsid']));

            } else {  //退回来
                $data = array();
                 if($goods['totalcnf']!=1)
                 {
                     $data['total'] = $goods['total'] + $item['total'];
                 }
                $data['sales'] = $goods['sales'] - $item['total'];
                mysqld_update('shop_dish', $data, array('id' => $item['goodsid']));

            }

            //记录卖家得到的佣金账单  bill每个产品对应一个记录
            if(!empty($item['seller_openid']) && !empty($item['commision'])){  //如果有卖家openid则，则计算佣金
                if($minus) {
                    $type = 1;  //收入佣金
                    $money = $item['commision'];
                }else{
                    $type = -1; //佣金退回
                    $money = $item['commision']*-1;
                }
                $bill_data = array(
                    'order_id'    => $id,
                    'order_goods_id'=>$item['id'],
                    'type'		  => $type,
                    'openid'	  => $item['seller_openid'],
                    'money'		  => $money,
                    'modifiedtime'=> time(),
                    'createtime'  => time()
                );
                mysqld_insert('bill',$bill_data);

                if(array_key_exists($item['seller_openid'], $usermoney)){
                    $usermoney[$item['seller_openid']][] = $item;
                    $usermoney[$item['seller_openid']]['seller_commision'] += $item['commision'];
                }else{
                    $usermoney[$item['seller_openid']][] = $item;
                    $usermoney[$item['seller_openid']]['seller_commision'] = $item['commision'];
                }
            }

            //记录账单买家花了多少钱  bill每个产品对应一个记录
            if($minus) {
                $type       = 0;  //购买
                $money = $order['price']*-1;
            }else{
                $type      = 3;  //获得退款
                $money = $order['price'];
            }
            $bill_data = array(
                'order_id'    => $id,
                'type'		  => $type,
                'openid'	  => $order['openid'],
                'order_goods_id'=>$item['id'],
                'money'		  => $money,
                'modifiedtime'=> time(),
                'createtime'  => time()
            );
            mysqld_insert('bill',$bill_data);  //账单

            $url = WEBSITE_ROOT.mobile_url('detail',array('name'=>'shopwap','op'=>'dish','id'=>$item['goodsid']));
            $str_link .= "<a href='{$url}' target='_blank'>商品</a>、";
        }

        $str_link = rtrim($str_link,'、');
        //记录paylog
        if($minus) {
            $mark  = "{$order['openid']}@订单:{$order['ordersn']}购买 {$str_link} 消费费用";
            member_goldinfo($order['openid'],$order['price'],'usegold',$mark);  //paylog
        }else{
            $mark  = "{$order['openid']}@订单:{$order['ordersn']} {$str_link}发生退款费用";
             member_goldinfo($order['openid'],$order['price'],'addgold',$mark);  //paylog
        }


        //记录卖家得到的佣金账单 paylog
        if(!empty($usermoney)) {
            $str_link = '';
            foreach ($usermoney as $openid => $data) {
                foreach($data as $row){
                    $url = WEBSITE_ROOT.mobile_url('detail',array('name'=>'shopwap','op'=>'dish','id'=>$row['goodsid']));
                    $str_link .= "<a href='{$url}' target='_blank'>商品</a>、";
                }
                $str_link = rtrim($str_link,'、');
                if($minus) {
                    $type2 = 'addgold';
                    $mark = "{$order['openid']}@订单:{$order['ordersn']}购买 {$str_link} 得到佣金";
                }else {
                    $type2 = 'usegold';
                    $mark = "{$order['openid']}@订单:{$order['ordersn']} {$str_link} 发生退款扣除佣金";
                }
                member_goldinfo($openid,$data['seller_commision'],$type2,$mark,'freeze_gold',true);
            }
        }

}

//单个单个退款  卖出去时用true  退款用false
function oneUpdateOrderStock($id , $minus = true) {
    $item  = mysqld_select("SELECT * FROM " . table('shop_order_goods') . " WHERE id='{$id}'");
    $order = mysqld_select("select * from " .table('shop_order'). " where id={$item['orderid']}");
    $goods = mysqld_select("SELECT * FROM " . table('shop_dish') . "  WHERE id='".$item['goodsid']."'");
    $aftersale = mysqld_select("select refund_price from ".table('aftersales')." where order_goods_id={$id}");

    if ($minus) {
        //属性
        $data = array();
        if($goods['totalcnf']!=1)
        {
            $data['total'] = $goods['total'] - $item['total'];
        }
        $data['sales'] = $goods['sales'] + $item['total'];
        mysqld_update('shop_dish', $data, array('id' => $item['goodsid']));

    } else {
        $data = array();
        if($goods['totalcnf']!=1)
        {
            $data['total'] = $goods['total'] + $item['total'];
        }
        $data['sales'] = $goods['sales'] - $item['total'];
        mysqld_update('shop_dish', $data, array('id' => $item['goodsid']));

    }

    $url = WEBSITE_ROOT.mobile_url('detail',array('name'=>'shopwap','op'=>'dish','id'=>$item['goodsid']));
    $str_link = "<a href='{$url}' target='_blank'>商品</a>、";

    //记录账单买家出入资金钱
    if($minus) {
        $type = 0;  //购买
        $type2 = 'usegold';
        $mark  = "{$order['openid']}@订单:{$order['ordersn']}购买 {$str_link} 消费费用";
        $price = $item['price'];
        $money = $price*-1;
    }else{
        $type = 3;  //获得退款
        $type2 = 'addgold';
        $price = empty($aftersale['refund_price']) ? $item['price'] : $aftersale['refund_price'];
        $mark  = "{$order['openid']}@订单:{$order['ordersn']} {$str_link}发生退款费用";
        $money = $price;
    }
    $bill_data = array(
        'order_id'    => $item['orderid'],
        'order_goods_id'=>$item['id'],
        'type'		  => $type,
        'openid'	  => $order['openid'],
        'money'		  => $money,
        'modifiedtime'=> time(),
        'createtime'  => time()
    );
    mysqld_insert('bill',$bill_data);  //账单
    member_goldinfo($order['openid'],$price,$type2,$mark);  //paylog

    //记录卖家得到的佣金账单
    if($minus) {
        $type = 1;  //收入佣金
        $type2 = 'addgold';
        $mark = "{$item['seller_openid']}@订单:{$order['ordersn']}购买 {$str_link} 得到佣金";
    }else {
        $type = -1; //佣金退回
        $type2 = 'usegold';
        $mark = "{$item['seller_openid']}@订单:{$order['ordersn']} {$str_link} 发生退款扣除佣金";
    }
    if(!empty($item['seller_openid']) && !empty($item['commision'])){
        if($minus)
            $money = $item['commision'];
        else{
            $money = $item['commision']*-1;
        }
        $bill_data = array(
            'order_id'    => $item['orderid'],
            'order_goods_id'=>$item['id'],
            'type'		  => $type,
            'openid'	  => $item['seller_openid'],
            'money'		  => $money,
            'modifiedtime'=> time(),
            'createtime'  => time()
        );
        mysqld_insert('bill',$bill_data);
        member_goldinfo($item['seller_openid'],$item['commision'],$type2,$mark,'freeze_gold',true);
    }

}