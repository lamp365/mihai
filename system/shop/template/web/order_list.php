<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>

	<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/css/datetimepicker.css" />
		<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/datetimepicker.js"></script>

<script>
	function cleartime()
	{
	document.getElementById("begintime").value='';
	document.getElementById("endtime").value='';
	}
	</script>
	<h3 class="header smaller lighter blue">订单管理</h3>
	
<form action="" target="_blank">
	<input type="hidden" name="name" value="addon16" />
	<input type="hidden" name="do"  value="print" />
	<input type="hidden" name="op"  value="normal_print" />
		<input type="hidden" name="mod"  value="site" />
	
	<input type="hidden" name="print_orderid" id="print_orderid" value="" />
		<div id="modal-normalprint" class="modal  fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">快递单打印</h4>
      </div>
      <div class="modal-body">
      	
      		  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 打印模板：</label>

										<div class="col-sm-9">
														<select name="print_modle_id"  >
																	<?php  foreach($normal_order_list as $item){?>
										<option value="<?php echo $item['id'];?>" data-name=""><?php echo $item['name'];?></option>
										
													<?php } ?>
                                        </select>
										</div>
									</div>
      	
      	
      	  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > </label>

										<div class="col-sm-9">
      								</div>
									</div>
      </div>
      <div class="modal-footer">
      	<button type="submit" class="btn btn-primary" name="do_normal_print" value="yes">打印</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
</form>

<form action="" target="_blank">
		<input type="hidden" name="name" value="addon16" />
	<input type="hidden" name="do"  value="print" />
	<input type="hidden" name="op"  value="express_print" />
			<input type="hidden" name="mod"  value="site" />
	<input type="hidden" name="print_express_orderid" id="print_express_orderid" value="" />
		<div  id="modal-expressprint"  class="modal  fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">发货单打印</h4>
      </div>
      <div class="modal-body">
      	
      		  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 打印模板：</label>

										<div class="col-sm-9">
														<select name="print_modle_id"  >
																	<?php  foreach($express_order_list as $item){?>
										<option value="<?php echo $item['id'];?>" data-name=""><?php echo $item['name'];?></option>
										
													<?php } ?>
                                        </select>
										</div>
									</div>
									
									  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > </label>

										<div class="col-sm-9">
      								</div>
									</div>
      </div>
      <div class="modal-footer">
      	<button type="submit" class="btn btn-primary" name="do_normal_print" value="yes">打印</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭窗口</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
</form>
	
<form action="" method="get">
	
	<input type="hidden" name="mod" value="site"/>
	<input type="hidden" name="name" value="shop"/>
	<input type="hidden" name="do" value="order"/>
	<input type="hidden" name="op" value="display"/>
	<input type="hidden" name="status" value="<?php  echo $_GP['status'];?>"/>
				 <table  class="table" style="width:95%;" align="center">
					<tbody>
						<tr>
							<td align="right" style="font-size: 14px;font-weight: bold;width:120px">订单编号：</td>
			<td  style="width:200px">
<input name="ordersn" type="text" value="<?php  echo $_GP['ordersn'];?>" /> 
			</td>	
			
					<td align="right" style="font-size: 14px;font-weight: bold;width:130px">下单时间：</td>
			<td style="width:400px;">
<input name="begintime" id="begintime" type="text" value="<?php  echo $_GP['begintime'];?>" readonly="readonly"  /> - <input id="endtime" name="endtime" type="text" value="<?php  echo $_GP['endtime'];?>" readonly="readonly"  /> <a href="javascript:;" onclick="cleartime()">清空</a>
		
			<script type="text/javascript">
		$("#begintime").datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			minView: "0",
			//pickerPosition: "top-right",
			autoclose: true
		});
	</script> 
	<script type="text/javascript">
		$("#endtime").datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			minView: "0",
			autoclose: true
		});
	</script>
			</td>	
			<td align="right" style="font-size: 14px;font-weight: bold;width:130px;">支付方式：</td>
			<td style="width:300px;">
				<select style="margin-right:15px;" id="paytype" name="paytype" > 
					 <option value="" <?php  echo empty($_GP['paytype'])?'selected':'';?>>--未选择--</option>
				<?php  if(is_array($payments)) { foreach($payments as $item) { ?>
                 <option value="<?php  echo $item["code"];?>" <?php  echo $item['code']==$_GP['paytype']?'selected':'';?>><?php  echo $item['name']?></option>
                  	<?php  } } ?>
                   </select>
                   
			</td>	

						</tr>								
							<tr>
											<td align="right" style="font-size: 14px;font-weight: bold;">收货人姓名：</td>
			<td >
<input name="address_realname" type="text" value="<?php  echo $_GP['address_realname'];?>" />
			</td>	
			
					<td align="right" style="font-size: 14px;font-weight: bold;width:100px">收货人手机：</td>
			<td >
<input name="mobile" type="text" value="<?php  echo $_GP['mobile'];?>" />
			</td>	
			<td align="right" style="font-size: 14px;font-weight: bold;">产品名称：</td>
			<td><input name="goodsname" type="text" value="<?php  echo $_GP['title'];?>" /></td>
						</tr>
						<tr>
							<td align="right" style="font-size: 14px;font-weight: bold;">导出模板：</td>
			<td >
                     <select name="template">
                          <option value="2" <?php  echo $_GP['template']==2?'selected':'';?>>彩虹快递发货</option>
						  <option value="1" <?php  echo $_GP['template']==1?'selected':'';?>>平潭保税区发货</option>
					 </select>
			</td>	
			<td align="right" style="font-size: 14px;font-weight: bold;">标记：</td>
			<td >
                         <select name="tag">
						  <option value="-1" selected>--未选择--</option>
                          <option value="0" <?php  echo $_GP['tag']==0?'selected':'';?>>灰色</option>
						  <option value="1" <?php  echo $_GP['tag']==1?'selected':'';?>>红色</option>
						  <option value="2" <?php  echo $_GP['tag']==2?'selected':'';?>>黄色</option>
						  <option value="3" <?php  echo $_GP['tag']==3?'selected':'';?>>绿色</option>
						  <option value="4" <?php  echo $_GP['tag']==4?'selected':'';?>>蓝色</option>
						  <option value="5" <?php  echo $_GP['tag']==5?'selected':'';?>>紫色</option>
					  </select>			
			</td>	
						<td></td>
						</tr>
						<tr>
						    <td></td>
							<td colspan="3"><input type="submit" name="submit" value=" 查 询 " class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" name="report" value="report" class="btn btn-warning">导出excel</button>&nbsp;&nbsp;
							<a  href="<?php echo $_SERVER['REQUEST_URI'] ?>&print=print" target="_blank">打印订单</a>
							</td></tr>
						
						
						<tr>
							<td></td>
							<td colspan="3">
							
							</td>
						</tr>	
					</tbody>
				</table>
			</form>
			
			<form action="" method="post" class="form-horizontal refund_form" enctype="multipart/form-data">
				<table  class="table" style="width:95%;" align="center">
				<tbody>
					<tr>
						<td align="right" style="font-size: 14px;font-weight: bold;width: 120px;">退款表单：</td>
						<td>
							<input name="myxls" type="file"   value="" />
						</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="3">
							<button type="button" class="refund btn btn-md btn-warning">批量退款</button>
						</td>
					</tr>	
				</tbody>		
				</table>
			</form>
			
<h3 class="blue">	<span style="font-size:18px;"><strong>订单总数：<?php echo $total ?></strong></span></h3>
			<ul class="nav nav-tabs" >
	<li style="width:7%" <?php  if($status == -99) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => -99))?>">全部</a></li>
	<li style="width:7%" <?php  if($status == 0) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => 0))?>">待付款</a></li>
	<li style="width:7%" <?php  if($status == 1) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => 1))?>">待发货</a></li>
	<li style="width:7%" <?php  if($status == 2) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => 2))?>">待收货</a></li>
	<li style="width:7%" <?php  if($status == 3) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => 3))?>">已完成</a></li>
	<li style="width:7%" <?php  if($status == -1) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => -1))?>">已关闭</a></li>
		<li style="width:7%" <?php  if($status == -2) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => -2))?>">退款中</a></li>
		<li style="width:7%" <?php  if($status == -4) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => -4))?>">退货中</a></li>
		<li style="width:7%" <?php  if($status == 34) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => 34))?>">退款完成</a></li>
		<li style="width:7%" <?php  if($status == 14) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => 14))?>">退货完成</a></li>
		<li style="width:7%" <?php  if($status == -321) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => -321))?>">退款关闭</a></li>
		<li style="width:7%" <?php  if($status == -121) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => -121))?>">退货关闭</a></li>
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
				<tr><td align="left" colspan="10" style="background:#E9F8FF;margin-top:10px;"><?php  echo $item['ordersn'];?>&nbsp;&nbsp;</td></tr>
				<tr>
				    <td  colspan="4">
					<?php 
					    if ( is_array($item['goods']) ){
                               foreach ( $item['goods'] as $goods ){
					?>
					    <div class="items">
						      <ul>
							      <li class="img"><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $goods['aid']))?>"><img src="<?php echo getGoodsThumb($goods['gid']); ?>" height="40" /></a></li>
								  <li class="title"><div><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $goods['aid']))?>"><?php echo $goods['title']; ?></a></div>
									  <div>
										  <div class="name"><?php echo getGoodsProductPlace($goods['pcate']); ?></div>
								  		  <?php if($goods['draw'] == 1) { ?>
										   &nbsp;&nbsp; <span class="label label-success">抽奖团</span>
										  <?php }else{  ?>
										   &nbsp;&nbsp; <span class="label label-success"><?php echo getGoodsType($goods['shop_type']); ?></span>
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
						<?php  if($item['status'] == 1) { ?><span class="label label-danger" >已支付</span><?php  } ?>
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
						<?php  if($hasaddon11) { ?>
						&nbsp;<a class="btn btn-xs btn-info"  href="<?php  echo create_url('site',array('name' => 'addon11','do' => 'orderPrint','orderid' =>$item['id']))?>"><i class="icon-print"></i>小票打印</a>
						<?php  } ?>
					<?php  if($hasaddon16) { ?>
						&nbsp;<a class="btn btn-xs btn-info"   onclick="document.getElementById('print_orderid').value='<?php  echo $item['id']?>';$('#modal-normalprint').modal()" href="javascript:;">发货单打印</a>
						&nbsp;
						<a  class="btn btn-xs btn-info"  onclick="document.getElementById('print_express_orderid').value='<?php  echo $item['id']?>';$('#modal-expressprint').modal()" href="javascript:;">快递单打印</a>
					<?php  } ?>
				&nbsp;&nbsp;
						</td>
						<td align="center" valign="middle" style="vertical-align: middle;"><div><?php  echo $item['price'];?> 元 </div><?php  if($item['hasbonus']>0) { ?><div class="label label-success">惠<?php echo $item['bonusprice'];?></div><?php  }?><div style="font-size:10px;color:#999;">(含运费:<?php  echo $item['dispatchprice'];?> 元)</div><div style="font-size:10px;color:#999;">(含进口税:<?php  echo $item['taxprice'];?> 元)</div></td>
						<td align="center" valign="middle" style="vertical-align: middle;"><a type="button" href="<?php  echo web_url('order', array('op' => 'detail', 'id' => $item['id']))?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo !empty($item['retag'])?$item['retag']:'没有标注信息'; ?>"><img src="images/btag<?php echo $item['tag']; ?>.png" /></a></td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
		<script type="text/javascript">
			$(".refund").click(function(){
				if(confirm('确定批量退款')){
					var url = "<?php  echo web_url('order',array('op'=>'refundbat'));?>";
					$(".refund_form").attr('action',url);
					$(".refund_form").submit();
				}
			})
		</script>
		<?php  echo $pager;?>

<?php  include page('footer');?>
