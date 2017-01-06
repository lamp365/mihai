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

      	 	if(empty($returnofmoney_price))
      	{
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
			    	for($dateindex=1;$dateindex<=7;$dateindex++)
			  		{
			  			$time=$nowyear."-".$nowmonth."-".$dateindex;
			  			$datastr=date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$dateindex,date("Y"))); 
			  			$start_time=date("Y-m-d 00:00:01",mktime(0, 0 , 0,date("m"),date("d")-date("w")+$dateindex,date("Y"))); 
						$end_time=date("Y-m-d 23:59:59",mktime(23,59,59,date("m"),date("d")-date("w")+$dateindex,date("Y"))); 
	        		    $chart1data = mysqld_selectcolumn("SELECT sum(price) FROM " . table('shop_order') . " WHERE status>=1 and createtime >=".strtotime($start_time)." and createtime <=".strtotime($end_time));
			      		if(empty($chart1data))
			      	{
			      		$chart1data="0.00";
			      	}else
			      	{
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