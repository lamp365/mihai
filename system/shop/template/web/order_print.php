<?php  include page('header');?>
<?php  if(is_array($list)) { foreach($list as $item) { ?>
<input type="hidden" value="199" name="id">
		<table class="table">
			<tbody><tr>
			    <th style="width:150px;padding:3px;"><label for="">订单编号:</label></th>
				<td style="padding:3px;">
					<?php  echo $item['ordersn'];?>				</td>
			<th style="width:150px;padding:3px;"></th>
			</tr>
			<tr>
			<th style="padding:3px;"><label for="">下单时间:</label></th>
				<td style="padding:3px;">
									<?php  echo date('Y-m-d H:i:s', $item['createtime'])?>			</td>
				<th style="padding:3px;"><label for="">总金额:</label></th>
				<td style="padding:3px;">
						<?php  echo $item['price'];?> 元 <?php  if($item['hasbonus']>0) { ?><span class="label label-success">惠<?php echo $item['bonusprice'];?></span><?php  }?>				</td>
			</tr>
			<tr>
				<th style="padding:3px;"><label for="">支付方式:</label></th>
				<td style="padding:3px;">
					<?php  if($item['paytypecode']=='bank'){?>	<span class="label label-danger" ><?php } ?><?php  echo $item['paytypename'];?><?php  if($item['paytypecode']=='bank'){?>	</span><?php } ?>			</td>
				<th style="padding:3px;"><label for="">配送:</label></th>
				<td style="padding:3px;">
							<?php  echo $dispatchdata[$item['dispatch']]['dispatchname'];?>				</td>
			</tr>
						
									</tbody></table>
			<h3 class="header smaller lighter blue">收货人信息</h3>
		
			<table class="table ">
					<tbody><tr>
				<th style="width:150px;padding:3px;"><label for="">收货人姓名:</label></th>
				<td style="width:250px;padding:3px;"><?php  echo $item['address_realname'];?>
									</td>
				<th style="padding:3px;"><label for="">收货地址:</label></th>
				<td style="padding:3px;">
						<?php  echo $item['mess_name'];?> </td>
			</tr>
				<tr>
								<th style="width:150px;padding:3px;"><label for="">联系电话:</label></th>
				<td  style="padding:3px;">
							<?php  echo $item['mobile'];?>			</td>
				<th  style="padding:3px;"><label for="">订单备注:</label></th>
				<td style="padding:3px;">
				123
				</td>
			</tr>
								<tr>
						<th style="width:150px;padding:3px;"><label for="">微信账户:</label></th>
				<td>
												<?php  echo $item['address_realname'];?>										</td>
						<th style="width:150px;padding:3px;"></th>
				<td>
									</td>
			</tr>
			
					</tbody></table>
		
<table class="table table-striped table-bordered table-hover" style="margin:0;" >
			<thead>
				<tr>
					<th style="width:50px;">序号</th>
					<th>商品标题</th>
                    <th>商品规格</th>
					<th>货号</th>
                    <th style="color:red;">成交价</th>
					<th>数量</th>
				</tr>
			</thead>						
			<tbody>
				<?php  $i=1;?>
			<?php  if(is_array($item['order']['goods'])) { foreach($item['order']['goods'] as $goods) { ?>
			<tr>
				<td><?php  echo $i;$i++?></td>
				<td><?php  echo $goods['title'];?>
                                </td>
                                <td> <?php  if(!empty($goods['weight'])) { ?><?php  echo $goods['weight'];?><?php  } ?></td>
				<td><?php  echo $goods['goodssn'];?></td>

         <td style='color:red;font-weight:bold;'><?php  echo $goods['orderprice'];?></td>
				<td><?php  echo $goods['total'];?></td>
				
			</tr>
			<?php  } } ?>
					</tbody></table>
     <table class="table table-striped table-bordered table-hover" style="display:none;">
			<thead >
				<tr>
					<th style="width:120px;">订单编号</th>
					<th style="width:100px;">收货人姓名</th>
					<th style="width:100px;">食堂</th>
					<th style="width:80px;">联系电话</th>
					<th style="width:80px;">支付方式</th>
					<th style="width:80px;">配送方式</th>
					<th style="width:50px;">运费</th>
					<th style="width:100px;">总价</th>         
					<th style="width:150px;">下单时间</th>
				</tr>
			</thead>
			<tbody>
			
				<tr>
					<td><?php  echo $item['ordersn'];?>
						<?php  if( $item['isguest']==1){?>
						<span class="label label-success">游客</span><?php  }?></td>
					<td><?php  echo $item['address_realname'];?></td>
					<td><?php  echo $item['mess_name'];?></td>
					<td><?php  echo $item['mobile'];?></td>
					<td>
						<?php  if($item['paytypecode']=='bank'){?>	<span class="label label-danger" ><?php } ?><?php  echo $item['paytypename'];?><?php  if($item['paytypecode']=='bank'){?>	</span><?php } ?>
						</td>
				
					<td>
						<?php  echo $dispatchdata[$item['dispatch']]['dispatchname'];?>
						</td>
           <td><?php  echo $item['dispatchprice'];?></td>
			
					<td><?php  echo $item['price'];?> 元 <?php  if($item['hasbonus']>0) { ?><span class="label label-success">惠<?php echo $item['bonusprice'];?></span><?php  }?></td>
					<td><?php  echo date('Y-m-d H:i:s', $item['createtime'])?></td>
				
				</tr>
		
			</tbody>
		</table>
			<div style='PAGE-BREAK-AFTER:always'></div>
				<?php  } } ?>