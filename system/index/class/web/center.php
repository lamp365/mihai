<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小物社区 <QQ:119006873> <http://www.squdian.com>
// +----------------------------------------------------------------------
		$nowyear=intval(date('Y',time()));
		$nowmonth=intval(date('m',time()));
		$nowdate=intval(date('d',time()));
		$lastmonthday=date('t',strtotime($nowyear."-".$nowmonth."-1"));
		$lastyearday=date('t',strtotime($nowyear."-12-1"));	
		$todayordercount = mysqld_selectcolumn("SELECT count(id) FROM " . table('shop_order') . " WHERE status>=1 and createtime >=".strtotime($nowyear."-".$nowmonth."-".$nowdate." 00:00:01")." and createtime <=".strtotime($nowyear."-".$nowmonth."-".$nowdate." 23:59:59"));
		$todayorderprice = mysqld_selectcolumn("SELECT sum(price) FROM " . table('shop_order') . " WHERE status>=1 and createtime >=".strtotime($nowyear."-".$nowmonth."-".$nowdate." 00:00:01")." and createtime <=".strtotime($nowyear."-".$nowmonth."-".$nowdate." 23:59:59"));
		$monthordercount = mysqld_selectcolumn("SELECT count(id) FROM " . table('shop_order') . " WHERE status>=1 and createtime >=".strtotime($nowyear."-".$nowmonth."-01 00:00:01")." and createtime <=".strtotime($nowyear."-".$nowmonth."-".$lastmonthday." 23:59:59"));
		$monthorderprice = mysqld_selectcolumn("SELECT sum(price) FROM " . table('shop_order') . " WHERE status>=1 and createtime >=".strtotime($nowyear."-".$nowmonth."-01 00:00:01")." and createtime <=".strtotime($nowyear."-".$nowmonth."-".$lastmonthday." 23:59:59"));
		$yearordercount = mysqld_selectcolumn("SELECT count(id) FROM " . table('shop_order') . " WHERE status>=1 and createtime >=".strtotime($nowyear."-01-01 00:00:01")." and createtime <=".strtotime($nowyear."-12-".$lastyearday." 23:59:59"));
		$yearorderprice = mysqld_selectcolumn("SELECT sum(price) FROM " . table('shop_order') . " WHERE status>=1 and createtime >=".strtotime($nowyear."-01-01 00:00:01")." and createtime <=".strtotime($nowyear."-12-".$lastyearday." 23:59:59"));
        //今天的订单金额
        $timestar = strtotime($nowyear."-".$nowmonth."-".$nowdate." 00:00:01");
		$timeend = $timestar;
		$today_arr = array();
        for($today = 1; $today <= 4; $today++){
			  $time_star = $timeend;
              $time_end = $timestar + $today * 6 * 60 * 60; 
			  $order_price = mysqld_selectcolumn("SELECT sum(price) as price FROM ".table('shop_order')." WHERE status >= 1 and createtime >=".$time_star." and createtime <".$time_end);
			  $timeend =  $time_end;
			  $today_arr[$today] = $order_price > 0 ? $order_price : 0 ;
		}
		//昨天的订单金额
        $yestar = strtotime("-1 day 00:00:01");
        $yeend = $yestar;
        $yes_arr = array();
		for($today = 1; $today <= 4; $today++){
			  $time_star = $yeend;
              $time_end = $yestar + $today * 6 * 60 * 60; 
			  $order_price = mysqld_selectcolumn("SELECT sum(price) as price FROM ".table('shop_order')." WHERE status >= 1 and createtime >=".$time_star." and createtime <".$time_end);
			  $yeend = $time_end;
			  $yes_arr[$today] = $order_price > 0 ? $order_price : 0;
		}
		//分组订单金额
		$orderpricegroup = "SELECT source,count(source) as peopler, sum(price) as price FROM ".table('shop_order')." where status >= 1 group by source ";
		$ordergroup = mysqld_selectall($orderpricegroup);
		$group_list = array();
		$all_order_price = 0;
		// 类型 1安卓 2ios 3平板 4pc 5wap 默认值为0 全部归为PC订单
		$peopler = 0;
		foreach ( $ordergroup as $ordergroup_value ){
            $group_list[$ordergroup_value['source']]['price'] = $ordergroup_value['price'];
			$group_list[$ordergroup_value['source']]['peopler'] = $ordergroup_value['peopler'];
			$peopler += $ordergroup_value['peopler'];
			$all_order_price += $ordergroup_value['price'];
		}
		if ( isset( $group_list[0]) ){
			if ( isset( $group_list[4] ) ){
            $group_list[4] += $group_list[0];
			}else{
            $group_list[4] = $group_list[0];
			}
		}

		// 访客数量 和 PV
		$ips_list = array();
		$all_ip = 0;
		$all_pv = 0;
		for ( $i = 1 ; $i <= 5; $i ++ ){
			$ips_data = array();
			$pv = 0;
            $ips = "SELECT count(ip) as ip FROM ".table('traffic_count')." where shop_id >= 0 and system = ".$i." group by ip ";
		    $ips_data = mysqld_selectall($ips);
			$all_ip += count($ips_data);
			$ips_list[$i] = array(
			     'ip'=>count($ips_data)
			);
			foreach($ips_data as $ips_data_value){
                $pv += $ips_data_value['ip'];
			}
			$all_pv += $pv;
			$ips_list[$i]['pv'] = $pv;
		}
        // 注册用户数，根据时间来获取
        // 第一阶段时间：  2017/1/21 - 2017/4/30
		$timefirst = strtotime("2017-1-19");
		$timeend = strtotime("2017-4-30");
		// 找出这个阶段的用户注册数量
        $users = mysqld_selectcolumn("SELECT count(*) FROM " . table('member') . "  WHERE createtime >= {$timefirst} and istemplate = 0 ");
        // 找出这个阶段的销售金额
		$totalprice = mysqld_selectcolumn("SELECT sum(price) FROM ".table('shop_order')." where ordertype <> -2 and status >= 1 and createtime >= {$timefirst}");







        // 找出访问数量最多的产品
        $view_pro = "SELECT count(shop_id) as num, shop_id FROM ".table('traffic_count')." where shop_id > 0 group by shop_id order by num desc limit 5";
        $pro_data = mysqld_selectall($view_pro);
		$pro_list   = array();
        foreach( $pro_data as $pro_data_value ){
             $sql_data = array(
                  'table'=>'shop_dish',
	              'where' => 'a.id = '.$pro_data_value['shop_id']
			 );
			 $goods = get_good($sql_data);
			 $goods['view_num'] = $pro_data_value['num'];
			 $goods['view_ip'] = count(mysqld_selectall("SELECT count(ip) FROM ".table('traffic_count')." WHERE shop_id = ".$pro_data_value['shop_id']." group by ip "));
			 // 开始统计支付金额
			 $pay_sql = "SELECT a.* FROM ".table('shop_order_goods')." AS a LEFT JOIN ".table('shop_order')." AS b ON a.orderid = b.id WHERE b.status >= 1 and a.goodsid = ".$pro_data_value['shop_id'];
			 $pro_pay = mysqld_selectall($pay_sql);
			 $goods['view_peo'] = count($pro_pay);
			 $pay_all_price = 0;
			 foreach($pro_pay as $pro_pay_value){
                    $pay_all_price += $pro_pay_value['price'] * $pro_pay_value['total'];
			 }
			 $goods['view_price'] = $pay_all_price;
			 $goods['view_percent'] = round($goods['view_peo'] / $goods['view_ip'] * 100,2).'%';
			 $pro_list[] = $goods;
		}

		// 购物车数据
        $cart_pro = "SELECT count(goodsid) as num, goodsid FROM ".table('shop_cart')." group by goodsid order by num desc limit 5";
        $cart_pro_data = mysqld_selectall($cart_pro);
		$cart_pro_list   = array();
        foreach( $cart_pro_data as $cart_pro_data_value ){
             $sql_data = array(
                  'table'=>'shop_dish',
	              'where' => 'a.id = '.$cart_pro_data_value['goodsid']
			 );
			 $goods = get_good($sql_data);
			 $goods['cart_num'] = $cart_pro_data_value['num'];
			 $goods['cart_colle'] = mysqld_selectcolumn("SELECT count(*) FROM " . table('goods_collection') . " WHERE dish_id=".$cart_pro_data_value['goodsid']);
			 $cart_pro_list[] = $goods;
		}
		//退货单  退货加退款 已经成功的
		$today_time         = "g.createtime >=".strtotime($nowyear."-".$nowmonth."-".$nowdate." 00:00:01")." and g.createtime <=".strtotime($nowyear."-".$nowmonth."-".$nowdate." 23:59:59");
		$todayordercount_re = mysqld_selectcolumn("SELECT count(id) FROM " . table('shop_order_goods') . " as g WHERE g.status=4  and {$today_time}");
		$sql = "SELECT SUM(price+taxprice)  FROM ".table('shop_order_goods')." AS g  WHERE g.type =4 AND {$today_time}";
		$todayorderprice_re = mysqld_selectcolumn($sql);

		$month_time = "g.createtime >=".strtotime($nowyear."-".$nowmonth."-01 00:00:01")." and g.createtime <=".strtotime($nowyear."-".$nowmonth."-".$lastmonthday." 23:59:59");
		$monthordercount_re = mysqld_selectcolumn("SELECT count(id) FROM " . table('shop_order_goods') . " as g WHERE g.status=4  and {$month_time}");
		$sql = "SELECT SUM(price+taxprice)  FROM ".table('shop_order_goods')." AS g WHERE g.type =4 AND {$month_time}";
		$monthorderprice_re = mysqld_selectcolumn($sql);

		$year_time = "g.createtime >=".strtotime($nowyear."-01-01 00:00:01")." and g.createtime <=".strtotime($nowyear."-12-".$lastyearday." 23:59:59");
		$yearordercount_re = mysqld_selectcolumn("SELECT count(id) FROM " . table('shop_order_goods') . " as g WHERE g.status=4 and {$year_time}");
		$sql = "SELECT SUM(price+taxprice) FROM ".table('shop_order_goods')." AS g  WHERE g.type =4 AND {$year_time}";
		$yearorderprice_re = mysqld_selectcolumn($sql);
		$needsend_count = mysqld_selectcolumn("SELECT count(id) FROM " . table('shop_order') . " WHERE status=1 ");
		$needsend__price = mysqld_selectcolumn("SELECT sum(price) FROM " . table('shop_order') . " WHERE status=1 ");
		//退货单
		$returnofgoods_count = mysqld_selectcolumn("SELECT count(id) FROM " . table('shop_order_goods') . " WHERE type =1 and status in (1,2,3) ");
		$sql = "SELECT SUM(price+taxprice) FROM ".table('shop_order_goods')." AS g  WHERE g.type =1 AND g.status IN (1,2,3)";
		$returnofgoods_price = mysqld_selectcolumn($sql);
		//退款单
		$returnofmoney_count = mysqld_selectcolumn("SELECT count(id) FROM " . table('shop_order_goods') . " WHERE type = 3 and status in (1,2,3) ");
		$sql = "SELECT SUM(price+taxprice) FROM  ".table('shop_order_goods')." AS g WHERE g.type =3 AND g.status IN (1,2,3)";
		$returnofmoney_price = mysqld_selectcolumn($sql);
        if(empty($returnofmoney_price)){
      		$returnofmoney_price="0.00";
      	}else
      	{
      	$returnofmoney_price=round($returnofmoney_price,2);	
      	}
      	 	if(empty($needsend__price))
      	{
      		$needsend__price="0.00";
      	}else
      	{
      	$needsend__price=round($needsend__price,2);	
      	}
      	 	if(empty($returnofgoods_price))
      	{
      		$returnofgoods_price="0.00";
      	}else
      	{
      	$returnofgoods_price=round($returnofgoods_price,2);	
      	}
      	
      	if(empty($todayorderprice))
      	{
      		$todayorderprice="0.00";
      	}else
      	{
      	$todayorderprice=round($todayorderprice,2);	
      	}
      		if(empty($monthorderprice))
      	{
      		$monthorderprice="0.00";
      	}else
      	{
      	$monthorderprice=round($monthorderprice,2);	
      	}
      		if(empty($yearorderprice))
      	{
      		$yearorderprice="0.00";
      	}else
      	{
      	$yearorderprice=round($yearorderprice,2);	
      	}
      	    	if(empty($todayorderprice_re))
      	{
      		$todayorderprice_re="0.00";
      	}else
      	{
      	$todayorderprice_re=round($todayorderprice_re,2);	
      	}
      		if(empty($monthorderprice_re))
      	{
      		$monthorderprice_re="0.00";
      	}else
      	{
      	$monthorderprice_re=round($monthorderprice_re,2);	
      	}
      		if(empty($yearorderprice_re))
      	{
      		$yearorderprice_re="0.00";
      	}else
      	{
      	$yearorderprice_re=round($yearorderprice_re,2);	
      	}
      	$chartdata1=array();
      	$index=0;
		for($dateindex=1;$dateindex<=7;$dateindex++){
				$time=$nowyear."-".$nowmonth."-".$dateindex;
				$datastr=date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$dateindex,date("Y"))); 
				$start_time=date("Y-m-d 00:00:01",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$dateindex,date("Y"))); 
				$end_time=date("Y-m-d 23:59:59",mktime(23,59,59,date("m"),date("d")-date("w")+$dateindex,date("Y"))); 
				$chart1data = mysqld_selectcolumn("SELECT sum(price) FROM " . table('shop_order') . " WHERE status>=1 and createtime >=".strtotime($start_time)." and createtime <=".strtotime($end_time));
				if(empty($chart1data)){
					$chart1data="0.00";
				 }else{
					$chart1data=round($chart1data,2);	
				 }
				$tchartdata=array();
				$tchartdata['counts']=$chart1data;
				$tchartdata['dates']=$datastr;
				$tchartdata['index']=$index;
				$chartdata1[]=$tchartdata;
				$index=$index+1;
	    } 
		include page('center');