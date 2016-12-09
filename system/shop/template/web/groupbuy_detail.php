<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">拼团订单详情&nbsp;&nbsp; <span class="btn btn-xs btn-info return">返回列表</span></h3>
<p>团购条件：<?php echo $list[0]['goods'][0]['team_buy_count'];?>人团</p>
<?php if($list[0]['goods'][0]['draw'] == 1) { ?>
	<p>抽奖说明：该商品已开启抽奖，抽奖人数为<?php echo $list[0]['goods'][0]['draw_num'];?>人</p>
<?php } ?>
<h3 class="blue">	<span style="font-size:18px;"><strong>订单总数：<?php echo count($list) ?></strong></span></h3>
			<ul class="nav nav-tabs" >
				<li style="width:7%" class="active"><a href="javascript:;">团购成员</a></li>
		</ul>
		

<table class="table  table-bordered table-hover">
			<thead >
				<tr>
				    <th style="width:400px;text-align:center;">宝贝</th>
					<th style="width:80px;text-align:center;">单价</th>
					<th style="width:80px;text-align:center;">数量</th>
					
					<th style="width:100px;text-align:center;">售后状态</th>
					<th style="width:50px;text-align:center;">买家</th>
					<th style="width:150px;text-align:center;">下单时间</th>
					<th style="width:80px;text-align:center;">支付方式</th>
					<th style="width:120px;text-align:center;" >订单状态</th>
					<th style="width:150px;text-align:center;">实收款</th>     
					<th style="width:50px;text-align:center;">标记</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr><td align="left" colspan="10" style="background:#E9F8FF;margin-top:10px;"><?php  echo $item['ordersn'];?>&nbsp;&nbsp;<?php if($item['group_id']!=0){ echo '<span class="btn btn-xs btn-danger">团购商品</span>';}?></td></tr>
				<tr>
				    <td  colspan="4">
					<?php 
					    if ( is_array($item['goods']) ){
                               foreach ( $item['goods'] as $goods ){
					?>
					    <div class="items">
						      <ul>
								  <li class="img"><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $goods['aid']))?>"><img src="<?php echo getGoodsThumb($goods['gid']); ?>" height="40" /></a></li>
								  <li class="title">
									  <div><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $goods['aid']))?>"><?php echo $goods['title']; ?></a></div>
									  <div>
										  <div class="name"><?php echo getGoodsProductPlace($goods['pcate']); ?></div>
										  <?php if($goods['draw'] == 1 || $item['isprize'] != 0) { ?>
											  &nbsp;&nbsp; <span class="btn btn-xs btn-info">抽奖团</span>
										  <?php }else{ ?>
											  &nbsp;&nbsp; <span class="btn btn-xs btn-info">团购商品</span>
										  <?php } ?>
									  </div>
									  <div class="sn">商家编码: <?php echo $goods['goodssn']; ?></div>
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
					<?php
							   }
					    }?>
					</td>
			       
					<td align="center" valign="middle" style="vertical-align: middle;">
				       <div>收货人：<?php  echo $item['address_realname'];?></div>
					   <div>电话：<?php  echo $item['address_mobile'];?></div>
					   <?php if ( !empty($item['remark'])){ ?>
					   <div><a type="button" href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="<?php echo $item['remark']; ?>"><img src="images/tag.png" /></a></div>
					   <?php } ?>
					</td>
					<td align="center" valign="middle" style="vertical-align: middle;"><?php  echo date('Y-m-d H:i:s', $item['createtime'])?></td>
		           <td align="center" valign="middle" style="vertical-align: middle;">
						<?php  if($item['paytypecode']=='bank'){?>	<span class="label label-danger" ><?php } ?><?php  echo $item['paytypename'];?><?php  if($item['paytypecode']=='bank'){?>	</span><?php } ?>
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
						</div>
						<div><a  href="<?php  echo web_url('order', array('op' => 'detail', 'id' => $item['id']))?>"><i class="icon-edit"></i>查看详情</a></div>
						<div><a  href="<?php  echo web_url('order', array('op' => 'identity', 'id' => $item['id']))?>"><i class="icon-edit"></i>查看清关材料</a></div>

						</td>
						<td align="center" valign="middle" style="vertical-align: middle;"><div><?php  echo $item['price'];?> 元 </div><?php  if($item['hasbonus']>0) { ?><div class="label label-success">惠<?php echo $item['bonusprice'];?></div><?php  }?><div style="font-size:10px;color:#999;">(含运费:<?php  echo $item['dispatchprice'];?> 元)</div><div style="font-size:10px;color:#999;">(含进口税:<?php  echo $item['taxprice'];?> 元)</div></td>
						<td align="center" valign="middle" style="vertical-align: middle;"><a type="button" href="<?php  echo web_url('order', array('op' => 'detail', 'id' => $item['id']))?>" data-toggle="tooltip" data-placement="bottom" title="<?php if(!empty($item['retag'])){ $retag_json = json_decode($item['retag'],true); echo $retag_json['beizhu'];}else{ echo '没有标注信息'; } ?>"><img src="images/btag<?php echo $item['tag']; ?>.png" /></a></td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
<script>
	$(".return").click(function(){
		window.history.back();
	})
</script>
<?php  include page('footer');?>
