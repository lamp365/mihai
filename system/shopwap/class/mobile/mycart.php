<?php
$member = get_member_account(false);
$openid = $member['openid'] ?: get_sessionid();
$op = $_GP['op'];
if ($op == 'token'){
	 // type -1,正常类型  0，新增团  >0 参与的团购队伍ID
    //团购商品 时间还没开始的 不能开团
//    isCanGoupBuy($_GP['type'],$_GP['id']);
     $goods = array(
             'id'      =>$_GP['id'],
			 'total'   =>$_GP['total'],
		     'type'    =>isset($_GP['type'])?$_GP['type']:-1,
			 'spec_key'=>$_GP['spec_key'],
     );
    $table= array(
        'table'=>'shop_dish',
        'where' => 'a.id = '.$_GP['id']
    );
    $goods_shop = get_good($table);
    if (empty($goods_shop)) {
        $result = array('result' => 1002,'message'=>'抱歉，该商品不存在或是已经被删除！');
        die(json_encode($result));
    }


	 $cookie = new LtCookie();
	 $cookie->setCookie('goods',$goods,time()+3600*2);  //暂时2个小时
	 $result = array(
            'result' => 0
     );
     die(json_encode($result));
     exit();
}
if ($op == 'add') {
    $goodsid = intval($_GP['id']);
    $total = intval($_GP['total']);
    $total = empty($total) ? 1 : $total;
	// 选项
    $spec_key = $_GP['spec_key'] ?: 0;
	$table= array(
         'table'=>'shop_dish',
	     'where' => 'a.id = '.$goodsid
    );
    $goods = get_good($table);
    if (empty($goods)) {
        $result['message'] = '抱歉，该商品不存在或是已经被删除！';
        message($result, '', 'ajax');
    }

	$carttotal = getCartTotal(2);
    $goodsOptionStock = 0;
    $goodsOptionStock = $goods['total'];
	$goodsOptionStock = $goodsOptionStock - $count['nums'];

    if ($goodsOptionStock < ($total+$carttotal) && $goodsOptionStock != - 1) {
        $result = array(
            'result' => 0,
            'maxbuy' => $goodsOptionStock
        );
        die(json_encode($result));
        exit();
    }

    $row = mysqld_select("SELECT id, total FROM " . table('shop_cart') . " WHERE session_id = :session_id  AND goodsid = :goodsid  and spec_key=:spec_key", array(
        ':session_id' =>  $openid,
        ':goodsid'    =>  $goodsid,
        ':spec_key'   =>  $spec_key,
    ));

    if ($row == false) {
        // 不存在
        $data = array(
            'goodsid'       => $goodsid,
            'goodstype'     => $goods['type'],
            'session_id'    => $openid,
            'total'         =>  $total,
            'spec_key'      =>  $spec_key
        );
        mysqld_insert('shop_cart', $data);
    } else {
        // 累加最多限制购买数量
        $t = $total + $row['total'];
        // 存在
        $data = array(
            'total'       => $t,
            'spec_key'    => $spec_key
        );
        mysqld_update('shop_cart', $data, array(
            'id' => $row['id']
        ));
    }
    // 返回数据
    $carttotal = getCartTotal(2);
    $result = array(
        'result' => 1,
        'total' => $carttotal
    );
    die(json_encode($result));
} else if ($op == 'clear') {
        mysqld_delete('shop_cart', array(
            'session_id' => $openid
        ));
		$cookie = new LtCookie();
        $cookie->delCookie('choose_cart');
        die(json_encode(array(
            "result" => 1
        )));
    } else if ($op == 'remove') {
            $id = intval($_GP['id']);
            mysqld_delete('shop_cart', array(
                'session_id' => $openid,
                'id' => $id
            ));
            die(json_encode(array(
                "result" => 1,
                "cartid" => $id
            )));
    } else if($op == 'delbat') {
           if(empty($_GP['ids'])){
               die(showAjaxMess(1002,'参数有误！'));
           }else{
               foreach($_GP['ids'] as $id){
                   mysqld_delete('shop_cart', array(
                       'session_id' => $openid,
                       'id' => $id
                   ));
               }
               die(showAjaxMess(200,'删除成功!'));
           }
    }else if ($op == 'update') {
                $id = intval($_GP['id']);
                $num = intval($_GP['num']);
                mysqld_query("update " . table('shop_cart') . " set total=$num where id=:id", array(
                    ":id" => $id
                ));
                die(json_encode(array(
                    "result" => 1
                )));
    } else if($op == 'choose_cart'){  //在购物车中选择部分商品购买
        if(empty($_GP['cart_ids'])){
            message('对不起你没有选择商品',refresh(),'error');
           die(showAjaxMess(1002,''));
        }else{
            $cookie = new LtCookie();
            $cookie->delCookie('choose_cart');
            $cookie->setCookie('choose_cart',$_GP['cart_ids']);
            $url = mobile_url('confirm',array('op'=>'cart'));
            header("location: " .$url);
        }
    }else {
                $list = array();
                if(!empty($openid)){
                    $list = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE   session_id = '" . $openid . "'");
                }

                $totalprice = 0;
                if (! empty($list)) {
                    foreach ($list as $key=>&$item) {
						$goods = get_good(array(
							 'table'=>'shop_dish',
							 'where' => 'a.id='. $item['goodsid']
						));
						if ($goods['status'] == 0 ){
                              mysqld_delete('shop_cart', array('session_id' => $openid, 'goodsid' => $item['goodsid']));
							  unset($list[$key]);
							  continue;
						}
                        // 属性
						$goods['small'] = download_pic($goods['imgs'], 100,100);
                        $item['goods'] = $goods;
                        $item['totalprice'] = (floatval($goods['marketprice']) * intval($item['total']));
                        $totalprice += $item['totalprice'];
                    }
                    unset($item);
                }
				$jp_goods = empty($goods['p1']) ? array() : cs_goods($goods['p1'], 1, 4, 10);
                include themePage('cart');
            }