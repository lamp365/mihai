<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
	本周交易成功订单列表&nbsp;&nbsp;&nbsp;
</h3>

<div class="alert alert-info" style="margin:10px 0; width:auto;">
	<i class="icon-lightbulb"></i> <?php echo date('Y-m-d',$starttime);?> ~ 今日的交易成功的订单
</div>

<ul class="nav nav-tabs">
	<li style="width:7%"><a href="<?php echo web_url('free_order',array('op' =>'new_list'))?>">待配置免单</a></li>
	<li style="width:7%"><a href="<?php echo web_url('free_order')?>">已配置免单</a></li>
	<li class="active"><a href="<?php echo web_url('free_order',array('op' =>'order_finish'))?>">本周交易成功订单</a></li>
</ul>

<table class="table  table-bordered table-hover">
			<thead>
				<tr>
				    <th style="width:280px;text-align:center;">宝贝</th>
					<th style="width:80px;text-align:center;">单价</th>
					<th style="width:100px;text-align:center;">数量</th>
					<th style="width:160px;text-align:center;">买家</th>
					<th style="width:150px;text-align:center;">下单时间</th>
					<th style="width:120px;text-align:center;">交易完成时间</th>
					<th style="width:180px;text-align:center;">实收款</th>     
				</tr>
			</thead>
			<tbody>
				<?php if(is_array($list)) { foreach($list as $value) { ?>
				<tr><td align="left" colspan="7" style="background:#E9F8FF;margin-top:10px;"><?php echo $value['ordersn']?></td></tr>
				<tr class="order_info">
				    <td colspan="3">
					    <?php 
						    if ( is_array($value['dishs']) ){
	                               foreach ( $value['dishs'] as $dishs ){
						?>
						<div class="items">
						    <ul>
						    	<li class="img"><a target="_blank" href="<?php echo mobile_url('detail', array('name'=>'shopwap','id' => $dishs['aid']))?>"><img src="<?php echo getGoodsThumb($dishs['shopgoodsid']); ?>" height="40" /></a></li>
								<li class="title"><div><a target="_blank" href="index.php?mod=mobile&amp;name=shopwap&amp;id=494&amp;do=detail" class="tab_title"><?php echo $dishs['title'];?></a></div>
									<div>
										 <span style="padding: 0 3px; border: 1px solid #fe3d53;color: #fe3d53;font-size: 10px;display:inline-block;"><?php echo $dishs['category_name'];?></span>
									</div>
								</li>
								<li class="price"><?php echo $dishs['price'];?></li>
								<li class="tot"><?php echo $dishs['total'];?></li>
							</ul>
						</div>
						<?php } } ?>
					</td>
			       
					<td align="center" valign="middle" style="vertical-align: middle;">
				       <div>收货人：<?php echo $value['address_realname'];?></div>
					   <div>电话：<?php echo $value['address_mobile'];?></div>
					</td>
					<td align="center" valign="middle" style="vertical-align: middle;"><?php echo date('Y-m-d H:i:s', $value['createtime'])?></td>
					<td align="center" valign="middle" style="vertical-align: middle;"><?php echo date('Y-m-d H:i:s', $value['completetime'])?></td>
					<td align="center" valign="middle" style="vertical-align: middle;">
						<div><?php  echo $value['price'];?> 元 </div>
						<?php if($value['has_balance']>0) { ?><div class="label label-success">余额抵扣<?php echo $value['balance_sprice'];?></div><?php  }?>
						<?php if($value['freeorder_price']>0) { ?><div class="label label-success">免单余额抵扣<?php echo $value['freeorder_price'];?></div><?php  }?>
						<?php  if($value['hasbonus']>0) { ?><div class="label label-success">惠<?php echo $value['bonusprice'];?></div><?php  }?>
						<div style="font-size:10px;color:#999;">(含运费:<?php  echo $value['dispatchprice'];?> 元)</div>
						<div style="font-size:10px;color:#999;">(含进口税:<?php  echo $value['taxprice'];?> 元)</div>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
</table>
<?php echo $pager;?>
<?php include page('footer');?>