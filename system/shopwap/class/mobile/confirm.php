<?php
header("Cache-control:no-cache,no-store,must-revalidate");
header("Pragma:no-cache");
header("Expires:0");
		if($_GP["follower"]!="nologinby")
		{
				if(is_login_account()==false)
				{
					if(empty($_SESSION["noneedlogin"]))
					{
					tosaveloginfrom();
					header("location:".create_url('mobile',array('name' => 'shopwap','do' => 'login','from'=>'confirm')));	
					}
				}
		}else
		{
			 $_SESSION["noneedlogin"]=true;
				clearloginfrom();	
		}
	// 控制非登录状态下下单安全
	$member=get_member_account(true);

	// 获取用户的opeinid;
		$openid =$member['openid'] ;
	// 获取链接参数
		$op = $_GP['op']?$_GP['op']:'display';
	// 初始化订单金额
		$totalprice = 0;
	// 初始化订单产品列表
		$allgoods = array();
	// 尝试获取非购物车数据ID
	    $cookie = new LtCookie();
		$good = $cookie->getCookie('goods');
		$id = intval($good['id']);

	// 获取规格项ID
		$optionid = intval($good['optionid']);
	// 获取数量如果为空则数量为1
		$total = intval($good['total']);
		if (empty($total)) {
			$total = 1;
		}
     $direct = false; //是否是直接购买
     $returnurl = ""; //当前连接
	 $issendfree=0;
	 // 设置清关开关
	 $ifcustoms = 0;
	 // 税率总和
	 $taxtot = 0;
	 // 运费设置
	 $ships = 0;
	 // 获取用户的配送地址
	 // 获取用户的配送地址
	if(is_mobile_request()){
		$defaultAddress   =   mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE  deleted = 0 and isdefault =1 and openid = :openid order by isdefault desc ", array(':openid' => $openid));
	}else{
		$addresslist      =   mysqld_selectall("SELECT * FROM " . table('shop_address') . " WHERE  deleted = 0 and openid = :openid order by isdefault desc ", array(':openid' => $openid));
	}
	 // 找出用户的免单金额及余额
    $user_data    = mysqld_select("SELECT * FROM ".table("member")." WHERE openid = :openid limit 1", array(':openid'=>$openid));
    $user_balance = getMemberBalance($user_data['gold'],$user_balance_data['freeorder_gold'],$user_data['freeorder_gold_endtime']);


	 // 直接购买操作代码
     if (!empty($id) && $_GP['op'] != 'cart' ) {
		    update_group_status($id);
		    if ( isAddedTeamBuyGroup($id, $openid) ){
                   message('你已经参加过该产品的团购');
	        }
		    // 获得产品信息ss
		    $item = get_good(array(
					'table'=>'shop_dish',
					'where' => ' a.id='. $id
			));
			if( $item['status'] == 0 ){
				    message('抱歉，该商品已经下架，无法购买了！', refresh(), "error");
			}
			// 获得单品的库存
			$itemStock = $item['total'];
			/*
            if (!empty($optionid)) {
                $option = mysqld_select("select title,marketprice,weight,stock from " . table("shop_goods_option") . " where id=:id", array(":id" => $optionid));
                if ($option) {
                    $item['optionid'] = $optionid;
                    $item['title'] = $item['title'];
                    $item['optionname'] = $option['title'];
                    $item['marketprice'] = $option['marketprice'];
                    $item['weight'] = $option['weight'];
                }
            }
			*/

			// 对购买数量进行处理，如果购买数量大于库存，则将购买数量设置为库存
			if( $total > $item['total']){
                $total = $item['total'];
			}
            $item['buynum'] = $total;
			// 进行促销价格和正常价格的比对
		    if ($item['istime'] != 0 && $item['type'] == 4 ){
                if ( (empty($item['timeend']) || (TIMESTAMP<$item['timeend'])) && ( TIMESTAMP>=$item['timestart'])){
                      $item['marketprice'] = $item['timeprice'];
				}
			}
			// 进行团购价格的处理
            if ( $good['type'] >= 0 ){
				 $item['marketprice'] = $item['timeprice'];
				 $item['shop_type'] = 1;
				 $shop_type = 1;
			}
			
			// 获得单品总价
            $item['totalprice'] = $total * $item['marketprice'];
			// 打包税率费用初始化
			$taxarray = array(
			     array('taxid'=>$item['taxid'],'id'=>$id,'count'=>$total,'price'=>$item['marketprice'])
			);
			$taxprice = get_taxs($taxarray);
			$taxprice = $taxprice['all_sum_tax'];
			$item['taxprice'] = $taxprice;
			$taxtot = $taxprice;
 			// 设置积分  这里的积分设置已经没有意义了。 要删除，请注意，是否会报错。 积分的设置，统一在入数据的时候，处理
            $item['credit'] = $total* $item['credit_cost'];
			$shipname = mysqld_select("select name from ".table('dish_list')." where id =:shipprice ",array(':shipprice'=>$item['pcate']));
			$item['title'] = '<div style="padding-bottom:5px;">'.$item['title']."</div><span style='font-size:10px;border:1px solid #E4393C;color:#E4393C;padding:0px 2px;'>".$shipname['name']."</span>";
			// 将信息增加到产品列表
            $allgoods[] = $item;
			// 获得订单总额
            $totalprice += $item['totalprice'];  
            //========促销活动===============
			$issendfree = $item['issendfree'];
            $direct = true;
            $returnurl = mobile_url("confirm", array("id" => $id, "optionid" => $optionid, "total" => $total));
        }
        if (!$direct) {
            //如果不是直接购买（从购物车购买）
			//可能存在 购物车里的商品只是部分需要支付   设置在mycart.php中
			$cart_ids = $cookie->getCookie('choose_cart');
			if(!empty($cart_ids)){
				$list = array();
				foreach($cart_ids as $cart_id){
					$list[] = mysqld_select("select * from " . table('shop_cart') . " WHERE id={$cart_id}");
				}
			}else{   //否则的话，全部取
				$list = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE  session_id = '".$openid."'");
			}
            if (!empty($list)) {
            	$totalprice=0;
            	$totaltotal=0;
                foreach ($list as &$g) {
					$item = get_good(array(
							 'table'=>'shop_dish',
							 'where' => 'a.id='. $g['goodsid']
					));
					if ( $item['status'] == 0 ) {
                         continue;
					}
					// 初始化税率金额
					$taxprice = 0;
                    //属性
					/*
                    $option = mysqld_select("select * from " . table("shop_goods_option") . " where id=:id ", array(":id" => $g['optionid']));
                    if ($option) {
                    		if($item['issendfree']==1)
                    		{
                    			$issendfree=1;	
                    		}
                        $item['optionid'] = $g['optionid'];
                        $item['title'] = $item['title'];
                        $item['optionname'] = $option['title'];
                        $item['marketprice'] = $option['marketprice'];
                        $item['weight'] = $option['weight'];
                    }
					*/
		    // 对购买数量进行处理，如果购买数量大于库存，则将购买数量设置为库存
			if( $g['total'] > $item['total']){
                $g['total'] = $item['total'];
			}
            $item['buynum'] = $g['total'] ;
			// 进行促销价格和正常价格的比对
		    if ($item['istime'] != 0){
                if ( (empty($item['timeend']) || (TIMESTAMP<$item['timeend'])) && ( TIMESTAMP>=$item['timestart'])){
                      $item['marketprice'] = $item['timeprice'];
				}
			}
			// 获得单品总价
			$shipname = mysqld_select("select name from ".table('dish_list')." where id =:shipprice ",array(':shipprice'=>$item['pcate']));
			$item['title'] = '<div style="padding-bottom:5px;">'.$item['title']."</div>";
			$item['shipname'] = "<span style='font-size:10px;border:1px solid #E4393C;color:#E4393C;padding:1px 3px;'>".$shipname['name']."</span>";
            $item['totalprice'] = $item['buynum'] * $item['marketprice'];
			// 打包税率费用初始化
			$taxarray = array(
			     array('taxid'=>$item['taxid'],'id'=>$id,'count'=>$item['buynum'],'price'=>$item['marketprice'])
			);
			$taxprice = get_taxs($taxarray);
			$taxprice = $taxprice['all_sum_tax'];
			$item['taxprice'] = $taxprice;
			$taxtot += $taxprice;
 			// 设置积分   这里的积分设置已经没有意义了。 要删除，请注意，是否会报错。 积分的设置，统一在入数据的时候，处理
            $item['credit'] = $total* $item['credit_cost'];
			// 将信息增加到产品列表
            $allgoods[] = $item;
			// 获得订单总额
            $totalprice += $item['totalprice'];  
           }     
          //========end===============
                unset($g);
            }
            $returnurl = mobile_url("confirm");
        }

        if (count($allgoods) <= 0) {
            header("location: " . mobile_url('myorder'));
            exit();
        }

        // 获取可获得的换购产品
		$change_goods = get_change_goods($totalprice);
		
		// 获取换购的产品信息  change_id包含了id和num 属于数组
		$change_id    = $cookie->getCookie('change_goods');
		$dish_good_detail = '';
		if (!empty($change_id)){
			if(array_key_exists($change_id['id'],$change_goods)){
				$change_good_detail = mysqld_select("SELECT * FROM ".table('shop_mess')." WHERE id = ".$change_id['id']);
				$dish_good_detail    = mysqld_select("SELECT * FROM ".table('shop_dish')." WHERE id = ".$change_good_detail['gid']);
				if (!empty($dish_good_detail['issendfree'])){
					$issendfree=1;
				}
				$dish_good_detail['marketprice'] = $change_good_detail['marketprice'];
				$dish_good_detail['buynum'] = $change_id['num'];
				$dish_good_detail['shop_type'] = -1;
				$dish_good_detail['taxprice'] = 0 ;
				$totalprice += $change_good_detail['marketprice'] * $change_id['num'] ;
				$change_good = get_change_good($change_id,$change_goods);
			}else{
				$cookie->delCookie('change_goods');
			}

		}
		// 操作换购产品
		// 计算运费
		 $promotion=mysqld_selectall("select * from ".table('shop_pormotions')." where starttime<=:starttime and endtime>=:endtime",array(':starttime'=>TIMESTAMP,':endtime'=>TIMESTAMP));
			if(empty($issendfree)){
				   $promotion=mysqld_selectall("select * from ".table('shop_pormotions')." where starttime<=:starttime and endtime>=:endtime",array(':starttime'=>TIMESTAMP,':endtime'=>TIMESTAMP));
			       //========运费计算===============
						foreach($promotion as $pro){
							if($pro['promoteType']==1){
								if(($totalprice)>=$pro['condition']){
									$issendfree=1;		
								}
							} else if($pro['promoteType']==0){
								if($totaltotal>=$pro['condition']){
									$issendfree=1;	
								}
							}		
					}
            } 
		$dispatchprice = 0;
		$ships = shipcost($allgoods);
		$ifcustoms = $ships['ifcustoms'];
		$ships = $ships['price'];
	    if($issendfree!=1){
             $dispatchprice = $ships;
		}
		$paymentconfig="";
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
			$paymentconfig=" and code!='alipay'";
		}
        $payments = mysqld_selectall("select * from " . table("payment")." where enabled=1 {$paymentconfig} order by `order` desc");
		if ( $_GP['ajax'] == 'ajax' ){
			$change_goods_id = $_GP['change_goods_id'];
			$change_goods_num = $_GP['change_goods_num'];
			//判断个数  result 0超过  0失败 0一个单一个  1 添加成功 2删除成功
			if($change_goods[$change_goods_id]['max_buy'] != 0 && $change_goods_num > $change_goods[$change_goods_id]['max_buy']){
				//0不限制数量
				die(json_encode(array(
					"result" => 0 ,
					"text" => '此商品最多只能换购'.$change_goods[$change_goods_id]['max_buy'].'个'
				)));
			}
			if($change_goods_num > $change_goods[$change_goods_id]['total']){
				die(json_encode(array(
					"result" => 0 ,
					"text" => '对不起库存剩余'.$change_goods[$change_goods_id]['total'].'个'
				)));
			}
			$change_good_value = array('id'=>$change_goods_id, 'num'=>$change_goods_num);
			if (!empty($change_goods)){
			    $change_result = set_change_goods($change_good_value,$_GP['todo'],$change_goods);
			    die($change_result);
			}else{
                die(json_encode(array(
						 "result" => 0 ,
						 "text" => '添加失败'
					)));
			}
		}
		if ( $_GP['ajax'] == 'get_detail' ){
             die(json_encode(array(
						 "ships" => $dispatchprice ,
						 "total" => $totalprice,
				         'tax'   =>  $taxtot
			 )));
		}
        if (checksubmit('submit')) {
			//有验证，先验证，验证不过 就不用往下走数据库
			if (empty($_GP['payment']) ) {
				message('请选择支付方式！');
			}

			$groupbuy = true;
			if ( isset($good['type']) && $good['type'] == 0 ){
				// 开始进行库存的判断然后再进行是否创建团购的操作
                if ( isset($itemStock) && isset($item['team_buy_count']) ){
                     if ( $itemStock > $item['team_buy_count'] ){
                          $groupbuy   = createTeamBuyGroup($id, $openid);
			              $group_member_id =  $groupbuy['team_buy_member_id'];
				          $error = '组团失败';
					 }else{
                          message('商品库存不足,不能开团,请选择参加别人的团购');
					 }
				}else{
                     message('参数错误');
				}
			}elseif(isset($good['type']) &&  $good['type'] > 0 ) {
                $groupbuy =  AddToTeamBuyGroup($good['type'],$openid,$item['team_buy_count']);
				$group_member_id  = mysqld_insertid ();
				$error = '参团失败';
			}
			if ( !$groupbuy ){
                 message($error);
			}
			if ( ($_GP['token'] !== $_SESSION['token']) || empty($_SESSION['token']) || empty($_GP['token']) ){
                 message('请求错误');
			}
            $address = mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE id = :id and openid = :openid", array(':id' => intval($_GP['address']),':openid'=>$openid));

			// 获取身份证默认信息
			$identity = mysqld_select("SELECT * FROM " . table('member_identity') . " WHERE  isdefault = 1  and status =0 and openid = :openid", array(':openid'=>$openid));
//			$_sendtype = mysqld_select("select * from " . table("dispatch")." where  id = {$_GP['dispatch']} ");  不去掉有时候会报错，底下也没用到
            if (empty($address)) {
                message('抱歉，请填写收货地址!',refresh(),'error');
            }
			if(empty($identity['identity_number'])){
				message('请完善地址，填写身份证',refresh(),'error');
			}
			if (!empty($identity['identity_front_image']) && !empty($identity['identity_back_image'])){
                $ifcustoms = 2;  
			}
			/*
             if (empty($_GP['dispatch'])) {
                message('请选择配送方式！');
            }
			*/

             //商品价格
             $goodsprice = 0;
             $goodscredit=0;
             foreach ($allgoods as $row) {
                  $goodsprice+= $row['totalprice'];
                  if($row['issendfree']==1||$row['type']==1){
                        $issendfree=1;	
                  }
                  $goodscredit+= intval($row['credit']);
              }

          //$dispatchid = intval($_GP['dispatch']);
          //$dispatchitem = mysqld_select("select sendtype,express from ".table('shop_dispatch')." where id=:id limit 1",array(":id"=>$dispatchid));
           //
			$ordersns= 'SN'.date('Ymd') . random(6, 1);
			$randomorder = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE  ordersn=:ordersn limit 1", array(':ordersn' =>$ordersns));
          	if(!empty($randomorder['ordersn']))
          	{
          		$ordersns= 'SN'.date('Ymd') . random(6, 1);       		
          	}
          	$payment = mysqld_select("select * from " . table("payment")." where enabled=1 and code=:payment",array(':payment'=>$_GP['payment']));
   			if(empty($payment['id']))
   			{
   				message("没有获取到付款方式");	
   			}
   			$paytype=$this->getPaytypebycode($payment['code']);
			$free_if = $gold_if = 0;
			$pay_price   =  $totalprice + $dispatchprice + $taxtot;

			//先扣除优惠卷 后扣除 余额
			$hasbonus   = $bonusprice = 0;
			if(is_login_account() && !empty($_GP['bonus'])){
				//检测优惠券是否有效
				$bonus_sn  = $_GP['bonus'];
				$use_bonus = mysqld_select("select * from ".table('bonus_user')." where deleted=0 and isuse=0 and bonus_sn=:bonus_sn and openid =:openid limit 1",array(":bonus_sn"=>$bonus_sn,":openid"=>$openid));
				if(empty($use_bonus['bonus_id'])){
					message("未找到相关优惠券",refresh(),'error');
				}
				$bonus_type = mysqld_select("select * from ".table('bonus_type')." where deleted=0 and type_id=:type_id and min_goods_amount<=:min_goods_amount  and use_start_date<=:use_start_date and use_end_date>=:use_end_date",array(":type_id"=>$use_bonus['bonus_type_id'],":min_goods_amount"=>$goodsprice,":use_start_date"=>time(),":use_end_date"=>time()));
				if(empty($bonus_type['type_id'])){
					message("优惠券已过期，请选择'无'可继续购买操作。",refresh(),'error');
				}
				$hasbonus   = 1;
				$bonusprice = $bonus_type['type_money'];
				$pay_price  = $pay_price - $bonusprice;
			}
			//最后扣除余额
			if (isset($_GP['balance'])){
				$balance_result   = operation_member_balance($openid,$pay_price,$user_data);
				$pay_price        = $balance_result['pay_price'];
				$free_use         = $balance_result['free_use'];
				$gold_use         = $balance_result['gold_use'];
				$free_if          = $free_use > 0 ? 1 : 0;
				$gold_if          = $gold_use > 0 ? 1 : 0;
			}else{
				$free_use         =  0 ;
				$gold_use         =  0 ;
			}
            $data = array(
                'openid' => $openid,	
                'ordersn' => $ordersns,
                'price' => $pay_price, // 产品金额+运费
                'dispatchprice' => $dispatchprice,
                'goodsprice' => $goodsprice,
				'ifcustoms'   => $ifcustoms,
				'ordertype' => isset($shop_type)?$shop_type:0,   // 订单类型，默认为一般订单72小时关闭
				'identity_id' => $identity['identity_id'],
				'taxprice'=> $taxtot,
                'credit'=> $goodscredit,
                'status' => 0,
                'paytype'=> $paytype,
                'sendtype' => intval($dispatchitem['sendtype']),
                'dispatchexpress' => $dispatchitem['express'],
                'dispatch' => $dispatchid,
                'paytypecode' => $payment['code'],
                'paytypename' => $payment['name'],
                'remark' => $_GP['remark'],
                'addressid'=> $address['id'],
				'has_balance' => $gold_if,
				'balance_sprice'=>$gold_use,
				'freeorder_price'=>$free_use,
                'address_mobile' => $address['mobile'],
                'address_realname' => $address['realname'],
                'address_province' => $address['province'],
                'address_city' => $address['city'],
                'address_area' => $address['area'],
                'address_address' => $address['address'],
                'source' => get_mobile_type(),
				'hasbonus'     => $hasbonus,
				'bonusprice'   => $bonusprice,
                'createtime' => time()
            );
            mysqld_insert('shop_order', $data);
            $orderid = mysqld_insertid();

            //更新优惠券为已经使用
              if ($hasbonus == 1) {
				   mysqld_update('bonus_user',array('isuse'=>1,'bonus_sn'=>$bonus_sn,'used_time'=>time(),'order_id'=>$orderid),array('bonus_id'=>$use_bonus['bonus_id']));
              }
			//插入订单后，后续动作，如插入paylog
            $data['orderid'] = $orderid;
			after_insert_order($data);

            //如果有换购商品，则进行货存处理
			if(!empty($dish_good_detail)){
				$allgoods[]    = $dish_good_detail;  //并入order_goods表中
				$chage_mess_id = $change_id['id'];
				if($change_id['num'] > $change_good_detail['max_buy'] && $change_good_detail['max_buy'] != 0){
					//等于0不做限制
					message('此商品最多只能换购'.$change_good_detail['max_buy'].'个',refresh(),'error');
				}
				if($change_id['num'] > $change_good_detail['total']){
					message('换购商品库存剩下'.$change_good_detail['total'].'个',refresh(),'error');
				}
				$re_total = $change_good_detail['total'] - $change_id['num'];
				mysqld_update('shop_mess',array('total'=>$re_total),array('id'=>$chage_mess_id));
			}
			set_change_goods(-1,1);
			//修正团购订单成员数据
            if ( $group_member_id ){
                 mysqld_update('team_buy_member',array('order_id'=>$orderid),array('id'=>$group_member_id));
			}

			//获取积分比例以及佣金比例
			$crited_ratio_arr = bankSetting();

			//插入订单商品
            foreach ($allgoods as $row) {
                if (empty($row)) {
                    continue;
                }
				$t_credit = $row['marketprice']*$crited_ratio_arr['credit_ratio']*$row['buynum'];
				$t_credit = ceil($t_credit);
                $d = array(
                    'goodsid' => $row['id'],
		            'shopgoodsid' => $row['gid'],
		            'aid'    =>  $row['id'],
		            'taxprice' => $row['taxprice'],
                    'orderid' => $orderid,
		            'shop_type'=>isset($row['shop_type'])?$row['shop_type']:0,
                    'total' => $row['buynum'],
                    'price' => $row['marketprice'],
                    'createtime' => time(),
                    'optionid'   => $row['optionid'],
					'credit'     => $t_credit
                );
				// 进行库存的操作
				if ( $row['totalcnf'] != 2 ){
					$row['total'] = $row['total'] - $row['buynum'];
					$row['sales'] = $row['sales'] + $row['buynum'];
                    mysqld_update('shop_dish',array('total'=> $row['total'],'sales'=>$row['sales']), array('id'=>$row['id']));
				}

				//有推荐人的话，设置佣金
				if(!empty($member['recommend_openid'])){
					$the_commision      = empty($row['commision']) ? $crited_ratio_arr['com_gold'] : $row['commision'];
					$d['commision']     = $row['buynum']*$row['marketprice']*$the_commision;
					$d['recommend_openid'] = $member['recommend_openid'];
				}
                $o = mysqld_select("select title from ".table('shop_goods_option')." where id=:id limit 1",array(":id"=>$row['optionid']));
                if(!empty($o)){
                    $d['optionname'] = $o['title'];
                }
				//获取商品id
				$ccate = $row['ccate'];
                mysqld_insert('shop_order_goods', $d);
            }
            //清空购物车  不能全部清空，wap端可能是部分商品 进行结算
            if (!$direct) {
				$cart_ids = $cookie->getCookie('choose_cart'); //当只是选择部分产品结算的时候，设置在mycart.php中
				if(!empty($cart_ids)){
					foreach($cart_ids as $cart_id){
						mysqld_delete("shop_cart", array( "id" => $cart_id));
					}
					$cookie->delCookie('choose_cart');
				}else{   //否则的话，全部删除
					mysqld_delete("shop_cart", array( "session_id" => $openid));
				}

            }else{
				$cookie->delCookie('goods');  //设置在mycart.php中
			}
            clearloginfrom();
            header("Location:".mobile_url('pay', array('orderid' => $orderid,'topay'=>'1')) );
        }else{
            $_SESSION['token'] = md5(microtime(true));
		}
  
  if(is_login_account())
  {
        $bonus_list=array();
	    foreach ($allgoods as $row) {
            if (empty($row)) {
                 continue;
            }
            $d_c[] = array(
                  'id'     => $row['gid'],
                  'dishid' => $row['id'],
                  'num'    =>   $row['total']
            );
        }
		$bonus_order = array('price'=>$totalprice, 'openid'=>$openid, 'goods'=>$d_c);
		$bonus_list = get_bonus_list($bonus_order);
  	}	
       include themePage('confirm');

/**
 * @content 计算用户的余额抵扣
 * @param $openid
 * @param $pay_price
 * @param $user_data
 * @return array
 */
function operation_member_balance($openid,$pay_price,$user_data){
	$free_gold   = $user_data['freeorder_gold'];
	if(time() > $user_data['freeorder_gold_endtime']){
		//免单余额有使用期限，过了期限则作废
		$free_gold = 0;
	}
	$user_gold   = $user_data['gold'];
	//免单金额 与  支付总额 取小的 为当前可以免去的金额就为总额
	$free_use    =  min($free_gold,$pay_price);

	if ($free_use == $pay_price){
		//当免单的余额 足够抵用总的价格 就不用抵扣 用户余额
		$gold_use  =  0 ;
	}else{
		if ($user_gold >= $pay_price - $free_use){
			//当用户余额大于剩下的钱
			$gold_use = $pay_price - $free_use;
		}else{
			//当用户余额小于剩下的钱   用户余额全部拿来使用
			$gold_use = $user_gold;
		}
	}
	$pay_price   =  $pay_price - $free_use - $gold_use;

	return array(
		'pay_price' => $pay_price,
		'free_use'  => $free_use,
		'gold_use'  => $gold_use
	);
}

/**
 * 插入订单后后续动作  如paylog记录
 * @param $data
 */
function after_insert_order($data){
	$free_use = $data['freeorder_price'];
	$gold_use = $data['balance_sprice'];
	$openid   = $data['openid'];
	$orderid  = $data['orderid'];
	if ( $free_use > 0 ){
		$remark = PayLogEnum::getLogTip('LOG_FREE_BALANCE_TIP');
		member_freegold($openid,$free_use,'usegold',$remark,$orderid);
	}
	if ( $gold_use > 0 ){
		$remark = PayLogEnum::getLogTip('LOG_BALANCE_TIP');
		member_gold($openid,$gold_use,'usegold',$remark,true,$orderid);
	}
}