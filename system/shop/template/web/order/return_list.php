<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>

<!-- <link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/css/datetimepicker.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/datetimepicker.js"></script> -->
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<style type="text/css">
.dummy-table-list{
width:100%;margin-bottom: 0;border: 1px solid #ddd;
}
	.dummy-table-list li{
			margin:7px 5px;
			float: left;
			list-style: none;
		}
		.dummy-table-list li select{
			height:30px;
		}
		.panel.with-nav-tabs .panel-heading{
    padding: 5px 5px 0 5px;
}
.hide-tr{
	display: none;
}
.panel.with-nav-tabs .nav-tabs{
	border-bottom: none;
}
.panel.with-nav-tabs .nav-justified{
	margin-bottom: -1px;
}
/********************************************************************/
/*** PANEL DEFAULT ***/
.with-nav-tabs.panel-default .nav-tabs > li > a,
.with-nav-tabs.panel-default .nav-tabs > li > a:hover,
.with-nav-tabs.panel-default .nav-tabs > li > a:focus {
    color: #777;
}
.with-nav-tabs.panel-default .nav-tabs > .open > a,
.with-nav-tabs.panel-default .nav-tabs > .open > a:hover,
.with-nav-tabs.panel-default .nav-tabs > .open > a:focus,
.with-nav-tabs.panel-default .nav-tabs > li > a:hover,
.with-nav-tabs.panel-default .nav-tabs > li > a:focus {
    color: #777;
	background-color: #ddd;
	border-color: transparent;
}
.with-nav-tabs.panel-default .nav-tabs > li.active > a,
.with-nav-tabs.panel-default .nav-tabs > li.active > a:hover,
.with-nav-tabs.panel-default .nav-tabs > li.active > a:focus {
	color: #555;
	background-color: #fff;
	border-color: #ddd;
	border-bottom-color: transparent;
}
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu {
    background-color: #f5f5f5;
    border-color: #ddd;
}
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a {
    color: #777;   
}
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
    background-color: #ddd;
}
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a,
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
.with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
    color: #fff;
    background-color: #555;
}
/********************************************************************/
/*** PANEL PRIMARY ***/
.with-nav-tabs.panel-primary .nav-tabs > li > a,
.with-nav-tabs.panel-primary .nav-tabs > li > a:hover,
.with-nav-tabs.panel-primary .nav-tabs > li > a:focus {
    color: #fff;
}
.with-nav-tabs.panel-primary .nav-tabs > .open > a,
.with-nav-tabs.panel-primary .nav-tabs > .open > a:hover,
.with-nav-tabs.panel-primary .nav-tabs > .open > a:focus,
.with-nav-tabs.panel-primary .nav-tabs > li > a:hover,
.with-nav-tabs.panel-primary .nav-tabs > li > a:focus {
	color: #fff;
	background-color: #3071a9;
	border-color: transparent;
}
.with-nav-tabs.panel-primary .nav-tabs > li.active > a,
.with-nav-tabs.panel-primary .nav-tabs > li.active > a:hover,
.with-nav-tabs.panel-primary .nav-tabs > li.active > a:focus {
	color: #428bca;
	background-color: #fff;
	border-color: #428bca;
	border-bottom-color: transparent;
}
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu {
    background-color: #428bca;
    border-color: #3071a9;
}
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > li > a {
    color: #fff;   
}
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
    background-color: #3071a9;
}
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > .active > a,
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
.with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
    background-color: #4a9fe9;
}
/********************************************************************/
/*** PANEL SUCCESS ***/
.with-nav-tabs.panel-success .nav-tabs > li > a,
.with-nav-tabs.panel-success .nav-tabs > li > a:hover,
.with-nav-tabs.panel-success .nav-tabs > li > a:focus {
	color: #3c763d;
}
.with-nav-tabs.panel-success .nav-tabs > .open > a,
.with-nav-tabs.panel-success .nav-tabs > .open > a:hover,
.with-nav-tabs.panel-success .nav-tabs > .open > a:focus,
.with-nav-tabs.panel-success .nav-tabs > li > a:hover,
.with-nav-tabs.panel-success .nav-tabs > li > a:focus {
	color: #3c763d;
	background-color: #d6e9c6;
	border-color: transparent;
}
.with-nav-tabs.panel-success .nav-tabs > li.active > a,
.with-nav-tabs.panel-success .nav-tabs > li.active > a:hover,
.with-nav-tabs.panel-success .nav-tabs > li.active > a:focus {
	color: #3c763d;
	background-color: #fff;
	border-color: #d6e9c6;
	border-bottom-color: transparent;
}
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu {
    background-color: #dff0d8;
    border-color: #d6e9c6;
}
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a {
    color: #3c763d;   
}
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
    background-color: #d6e9c6;
}
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a,
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
.with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
    color: #fff;
    background-color: #3c763d;
}
/********************************************************************/
/*** PANEL INFO ***/
.with-nav-tabs.panel-info .nav-tabs > li > a,
.with-nav-tabs.panel-info .nav-tabs > li > a:hover,
.with-nav-tabs.panel-info .nav-tabs > li > a:focus {
	color: #31708f;
}
.with-nav-tabs.panel-info .nav-tabs > .open > a,
.with-nav-tabs.panel-info .nav-tabs > .open > a:hover,
.with-nav-tabs.panel-info .nav-tabs > .open > a:focus,
.with-nav-tabs.panel-info .nav-tabs > li > a:hover,
.with-nav-tabs.panel-info .nav-tabs > li > a:focus {
	color: #31708f;
	background-color: #bce8f1;
	border-color: transparent;
}
.with-nav-tabs.panel-info .nav-tabs > li.active > a,
.with-nav-tabs.panel-info .nav-tabs > li.active > a:hover,
.with-nav-tabs.panel-info .nav-tabs > li.active > a:focus {
	color: #31708f;
	background-color: #fff;
	border-color: #bce8f1;
	border-bottom-color: transparent;
}
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu {
    background-color: #d9edf7;
    border-color: #bce8f1;
}
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > li > a {
    color: #31708f;   
}
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
    background-color: #bce8f1;
}
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > .active > a,
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
.with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
    color: #fff;
    background-color: #31708f;
}
/********************************************************************/
/*** PANEL WARNING ***/
.with-nav-tabs.panel-warning .nav-tabs > li > a,
.with-nav-tabs.panel-warning .nav-tabs > li > a:hover,
.with-nav-tabs.panel-warning .nav-tabs > li > a:focus {
	color: #8a6d3b;
}
.with-nav-tabs.panel-warning .nav-tabs > .open > a,
.with-nav-tabs.panel-warning .nav-tabs > .open > a:hover,
.with-nav-tabs.panel-warning .nav-tabs > .open > a:focus,
.with-nav-tabs.panel-warning .nav-tabs > li > a:hover,
.with-nav-tabs.panel-warning .nav-tabs > li > a:focus {
	color: #8a6d3b;
	background-color: #faebcc;
	border-color: transparent;
}
.with-nav-tabs.panel-warning .nav-tabs > li.active > a,
.with-nav-tabs.panel-warning .nav-tabs > li.active > a:hover,
.with-nav-tabs.panel-warning .nav-tabs > li.active > a:focus {
	color: #8a6d3b;
	background-color: #fff;
	border-color: #faebcc;
	border-bottom-color: transparent;
}
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu {
    background-color: #fcf8e3;
    border-color: #faebcc;
}
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > li > a {
    color: #8a6d3b; 
}
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
    background-color: #faebcc;
}
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > .active > a,
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
.with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
    color: #fff;
    background-color: #8a6d3b;
}
/********************************************************************/
/*** PANEL DANGER ***/
.with-nav-tabs.panel-danger .nav-tabs > li > a,
.with-nav-tabs.panel-danger .nav-tabs > li > a:hover,
.with-nav-tabs.panel-danger .nav-tabs > li > a:focus {
	color: #a94442;
}
.with-nav-tabs.panel-danger .nav-tabs > .open > a,
.with-nav-tabs.panel-danger .nav-tabs > .open > a:hover,
.with-nav-tabs.panel-danger .nav-tabs > .open > a:focus,
.with-nav-tabs.panel-danger .nav-tabs > li > a:hover,
.with-nav-tabs.panel-danger .nav-tabs > li > a:focus {
	color: #a94442;
	background-color: #ebccd1;
	border-color: transparent;
}
.with-nav-tabs.panel-danger .nav-tabs > li.active > a,
.with-nav-tabs.panel-danger .nav-tabs > li.active > a:hover,
.with-nav-tabs.panel-danger .nav-tabs > li.active > a:focus {
	color: #a94442;
	background-color: #fff;
	border-color: #ebccd1;
	border-bottom-color: transparent;
}
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu {
    background-color: #f2dede; /* bg color */
    border-color: #ebccd1; /* border color */
}
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > li > a {
    color: #a94442; /* normal text color */  
}
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
    background-color: #ebccd1; /* hover bg color */
}
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > .active > a,
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
.with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
    color: #fff; /* active text color */
    background-color: #a94442; /* active bg color */
}
.dummy-table-list li .left-span{
	float: left;
	height: 30px;
    line-height: 30px;
    background-color: #ededed;
    padding: 0 5px;
    border: 1px solid #cdcdcd;
    border-right: 0;
    font-size: 12px;
}
.dummy-table-list .li-height{
    height: 30px;
    padding-left: 5px;
}
.dummy-table-list td{
	padding: 0!important;
}
#paytype{
	height: 30px
}
.remark-btn-div{
	position: relative;
}
.remark-detail{
	display: none;
    background: #fff;
    border-radius: 4px;
    padding: 15px;
    width: 250px;
    position: absolute;
    top: 24px;
    left: -42px;
    border: 1px solid #ddd;
    text-align: left;
}
.tag-remark-detail{
	display: none;
	background: #fff;
    border-radius: 4px;
    padding: 15px;
    position: absolute;
    width: 250px;
    top: 60px;
    right: 0;
    border: 1px solid #ddd;
    text-align: left;
}
.nav-tabs li a{
	padding: 6px 22px;
}
</style>

<h3 class="header smaller lighter blue">订单管理</h3>

<h3 class="blue">	<span style="font-size:18px;"><strong>订单总数：<?php echo $total ?></strong></span></h3>
<ul class="nav nav-tabs" >
	<li style="" <?php  if($status == 1) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'returnDish','status' => 1))?>">退换申请</a></li>
	<li style="" <?php  if($status == 23) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'returnDish','status'=>23))?>">退换处理中</a></li>
	<li style="" <?php  if($status == 4) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'returnDish', 'status' => 4))?>">退换成功</a></li>
	<li style="" <?php  if($status == -1) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'returnDish', 'status' => -1))?>">退换失败</a></li>
</ul>
		

<table class="table  table-bordered table-hover">
			<thead >
				<tr>
				    <th style="width:400px;text-align:center;">宝贝</th>
					<th style="width:80px;text-align:center;">单价</th>
					<th style="width:80px;text-align:center;">数量</th>
					
					<th style="width:100px;text-align:center;">售后状态</th>
					<th style="width:95px;text-align:center;">收货人</th>
					<th style="width:95px;text-align:center;">下单时间</th>
					<th style="width:70px;text-align:center;">支付方式</th>
					<th style="width:120px;text-align:center;" >订单状态</th>
					<th style="width:150px;text-align:center;">实收款</th>     
					<th style="width:50px;text-align:center;">标记</th>
				</tr>
			</thead>
			<tbody>
				<?php  foreach($list_data as $item) { ?>
				<tr><td align="left" colspan="10" style="background:#E9F8FF;margin-top:10px;"><?php  echo $item['ordersn'];?>&nbsp;&nbsp;</td></tr>
				<tr class="order_info">
				    <td  colspan="4">
					<?php 
					    if ( !empty($item['goods']) ){
							$goods = $item['goods'];
					?>
					    <div class="items">
						      <ul>
							      <li class="img"><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $goods['goodsid']))?>"><img src="<?php echo download_pic($goods['thumb'],40,40,2); ?>" height="40" /></a></li>
								  <li class="title"><div><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $goods['goodsid']))?>" class="tab_title"><?php echo $goods['title']; ?></a></div>
									  <div>
										  <div class="name"><?php echo getGoodsProductPlace($goods['transport_id']); ?></div>
										  &nbsp;&nbsp;&nbsp;<span style="padding: 0 3px; border: 1px solid #fe3d53;color: #fe3d53;font-size: 10px;display:inline-block;"><?php  echo getGoodsCategory($goods['p1']);?></span>
								  		  <?php if($item['isdraw'] == 1) { ?>
										   &nbsp;&nbsp; <span class="label label-success">抽奖团</span>
										  <?php }else{  ?>
										   &nbsp;&nbsp; <span class="label label-success"><?php echo getGoodsType($goods['shop_type']); ?></span>
										  <?php } ?>
								      </div>
									  <div class="sn">商品货号: <?php echo $goods['productsn']; ?></div>
								  </li>
								  <li class="price"><?php echo $goods['orderprice']; ?></li>
								  <li class="tot"><?php echo $goods['total']; ?></li>
								  <li class="tot">
									  <span class="shouhou_status">
								   <?php
								   		if($goods['order_type'] == 1 && $goods['order_status'] == 1)  echo getOrderAfterSlaseUrl("退货申请中",$goods['order_id'],$item['id'],'good');
								   		if($goods['order_type'] == 1 && $goods['order_status'] == 2)  echo getOrderAfterSlaseUrl("<b>退货审核通过</b>",$goods['order_id'],$item['id'],'good');
								   		if($goods['order_type'] == 1 && $goods['order_status'] == 3)  echo getOrderAfterSlaseUrl("买家发货中",$goods['order_id'],$item['id'],'good');
								   		if($goods['order_type'] == 1 && $goods['order_status'] == 4)  echo getOrderAfterSlaseUrl("退货成功",$goods['order_id'],$item['id'],'good');
								   		if($goods['order_type'] == 1 && $goods['order_status'] == -1)  echo getOrderAfterSlaseUrl("退货审核驳回",$goods['order_id'],$item['id'],'good');
								   		if($goods['order_type'] == 1 && $goods['order_status'] == -2)  echo getOrderAfterSlaseUrl("买家撤销退货",$goods['order_id'],$item['id'],'good');
									   if($goods['order_type'] == 3 && $goods['order_status'] == 1)  echo getOrderAfterSlaseUrl("退款申请中",$goods['order_id'],$item['id'],'money');
									   if($goods['order_type'] == 3 && $goods['order_status'] == 2)  echo getOrderAfterSlaseUrl("<b>退款审核通过</b>",$goods['order_id'],$item['id'],'money');
									   if($goods['order_type'] == 3 && $goods['order_status'] == 4)  echo getOrderAfterSlaseUrl("退款成功",$goods['order_id'],$item['id'],'money');
									   if($goods['order_type'] == 3 && $goods['order_status'] == -1)  echo getOrderAfterSlaseUrl("退款审核驳回",$goods['order_id'],$item['id'],'money');
									   if($goods['order_type'] == 3 && $goods['order_status'] == -2)  echo getOrderAfterSlaseUrl("买家撤销退款",$goods['order_id'],$item['id'],'money');

								   ?>
										  </span>
								   </li>
							  </ul>
						</div>

					<?php } ?>
					</td>
			       
					<td align="" valign="middle" style="vertical-align: middle;">
				       <div>姓名: <?php  echo $item['address_realname'];?></div>
					   <div>电话: <?php  echo $item['address_mobile'];?></div>
					   <?php if ( !empty($item['remark'])){ ?>
					   <div class="remark-btn-div">
					   		<a class="remark-modal" type="button" href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="<?php echo $item['remark']; ?>">
					   			<img src="images/tag.png" />
					   		</a>
					   		<div class='remark-detail'>  
								<?php echo $item['remark']; ?>
							</div>
					   	</div>
					   
					   <?php } ?>
					</td>
					<td align="center" valign="middle" style="vertical-align: middle;"><?php  echo date('Y-m-d H:i', $item['createtime'])?></td>
		           <td align="center" valign="middle" style="vertical-align: middle;">
						<?php  if($item['paytypecode']=='bank'){?>	<span class="label label-danger" ><?php } ?><?php  echo $item['paytypename'];?><?php  if($item['paytypecode']=='bank'){?>	</span><?php } ?>
					   <?php
					   if(!empty($item['retag'])){
						   $retag = json_decode($item['retag'],true);
						   if(!empty($retag['recoder'])){
							   echo "<input type='hidden' value='{$item['retag']}' class='hide_order_log'/>";
							   echo "<span style='display:block;font-weight: bolder;color: #00D20D;cursor: pointer'><img src='images/tag.png' class='show_order_log' /></span>";
						   }
					   }
					   ?>
						</td>
					<td align="center" valign="middle" style="vertical-align: middle;">
					    <div>
					     <?php  if($item['status'] == 0) { ?><span class="label label-warning" >待付款</span><?php  } ?>
							<!--已经付钱的，团购中 或者团购未开奖 这叫做已支付，因为不在待发货中展示，其他的叫待发货-->
							<?php  if($item['status'] == 1) {
								if(checkGroupBuyCanSend($item)){
									echo '<span class="label label-danger" >待发货</span>';
								}else{
									echo '<span class="label label-danger" >已支付</span>';
								}

							}
							?>
						<?php  if($item['status'] == 2) { ?><span class="label label-warning">待收货</span><?php  } ?>
						<?php  if($item['status'] == 3) { ?><span class="label label-success" >已完成</span><?php  } ?>
						<?php  if($item['status'] == -1) { ?><span class="label label-success">已关闭</span><?php  } ?>
						<?php  if($item['status'] == -2) { ?><span class="label label-danger">退款中</span><?php  } ?>
						<?php  if($item['status'] == -3) { ?><span class="label label-danger">换货中</span><?php  } ?>
						<?php  if($item['status'] == -4) { ?><span class="label label-danger">退货中</span><?php  } ?>
						<?php  if($item['status'] == -5) { ?><span class="label label-success">已退货</span><?php  } ?>
						<?php  if($item['status'] == -6) { ?><span class="label  label-success">已退款</span><?php  } ?>
						<?php  if($item['status'] == -7) { ?><span class="label  label-success">付款审核</span><?php  } ?>
						</div>
						<div><a  href="<?php  echo web_url('order', array('op' => 'detail', 'id' => $item['id']))?>"><i class="icon-edit"></i>查看详情</a></div>
<!--						<div><a  href="--><?php // echo web_url('order', array('op' => 'identity', 'id' => $item['id']))?><!--"><i class="icon-edit"></i>查看清关材料</a></div>-->
					</td>
					<td align="center" valign="middle" style="vertical-align: middle;">
						<div><?php  echo $item['price']+$item['balance_sprice']+$item['freeorder_price'];?> 元 </div>
						<?php  if($item['hasbonus']>0) { ?><div class="label label-success">惠<?php echo $item['bonusprice'];?></div><?php  }?>
						<div style="font-size:10px;color:#999;">(含运费:<?php  echo $item['dispatchprice'];?> 元)</div>
						<div style="font-size:10px;color:#999;">(含进口税:<?php  echo $item['taxprice'];?> 元)</div>
					</td>
					<td align="center" valign="middle" style="vertical-align: middle;">
						<div style="position:relative">
							<a class="tag-remark" type="button" href="<?php  echo web_url('order', array('op' => 'detail', 'id' => $item['id']))?>"><img src="images/btag<?php echo $item['tag']; ?>.png" /></a>
							<div class="tag-remark-detail">
								<?php if(!empty($item['retag'])){ $retag_json = json_decode($item['retag'],true); echo $retag_json['beizhu'];}else{ echo '没有标注信息'; } ?>
							</div>
						</div>
					</td>
				</tr>
				<?php  } ?>
			</tbody>
		</table>

		<!-- 订单日志弹出框 -->
		<div class="modal fade" id="orderLogModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">订单操作日志</h4>
					</div>
					<div class="modal-body">
						<p class="modal_ordersn"></p>
						<p class="modal_title"></p>
						<table class="table">
							<thead>
							<tr>
								<th>管理员</th>
								<th>操作信息</th>
								<th>时间</th>
							</tr>
							</thead>
							<tbody class="modal_order_log">

							</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal -->
		</div>

		<script type="text/javascript">
			$(".refund").click(function(){
				if(confirm('确定批量退款')){
					var url = "<?php  echo web_url('order',array('op'=>'refundbat'));?>";
					$(".refund_form").attr('action',url);
					$(".refund_form").submit();
				}
			})
			$(".remark-modal").click(function(){
				$(this).siblings(".remark-detail").toggle();
			})
			$(".tag-remark").hover(function(){
				$(this).siblings(".tag-remark-detail").show();
			},function(){
				$(this).siblings(".tag-remark-detail").hide();
			})

			$(".add-more-btn").click(function(){
				$(".hide-tr").toggle();
			});

			//点击查看订单日志
			$(".show_order_log").click(function(){
				var log_string = $(this).parent().prev().val();
				log_obj  = JSON.parse(log_string);
				//格式 2-测试订单-54815154545;3-已经发货-2323423  分号分开的字符串
				log_info = log_obj.recoder;					
				$("#orderLogModal").modal();				
				var ordersn = '订单号：'+ $(this).closest('.order_info').prev().find('td').html();
				var tit     = $(this).closest('.order_info').find('.tab_title').html();
				$("#orderLogModal .modal_ordersn").html(ordersn);
				$("#orderLogModal .modal_title").html(tit);

				var log_info = log_info.split(";"); //字符串截取，成为数组
				var log_html = "";
				 for(var i=0; i<log_info.length;i++){
					 log_html += '<tr>';
					 var one_log     = log_info[i];
					 //["2", "测试订单", "54815154545"]
					 var one_log_arr =  one_log.split("-");
					 var url = "<?php echo web_url('order',array('op'=>'getAdminName')); ?>";
					 url += "&uid="+one_log_arr[0];
					 //这里必须用ajax的 async false同步进行，不能改用$.get或者$.post异步进行。会导致还没拼接完，进入下一个循环
					 $.ajax({
						 url:url,
						 type: "POST",
						 async: false,
						 dataType:'json',
						 success:function(data,xml){
							 var admin   = data.message;
							 var message =  one_log_arr[1];
							 var time    =  string_to_time(one_log_arr[2]);

							 log_html   += "<td>"+ admin +"</td>";
							 log_html   += "<td>"+ message +"</td>";
							 log_html   += "<td>"+ time +"</td>";
							 log_html += '</tr>';
						 }
					 });
				 }

				$("#orderLogModal .modal_order_log").html(log_html);
			})

			function string_to_time(time){
				var datetime = new Date();
				datetime.setTime(time*1000);
				var year = datetime.getFullYear();
				var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1;
				var date = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate();
				var hour = datetime.getHours()< 10 ? "0" + datetime.getHours() : datetime.getHours();
				var minute = datetime.getMinutes()< 10 ? "0" + datetime.getMinutes() : datetime.getMinutes();
				var second = datetime.getSeconds()< 10 ? "0" + datetime.getSeconds() : datetime.getSeconds();
				return year + "-" + month + "-" + date+" "+hour+":"+minute+":"+second;
			}
		</script>
		<?php  echo $pager;?>

<?php  include page('footer');?>
