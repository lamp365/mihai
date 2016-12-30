<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>

	<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/css/datetimepicker.css" />
		<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/datetimepicker.js"></script>
<style type="text/css">
	.dummy-table-list li{
			margin-top:3px;
			float: left;
			margin-right: 10px;
			list-style: none;
		}
		.dummy-table-list tr{
			background-color: #f9f9f9;
			border-top: 1px solid #ddd;
		}
		.dummy-table-list td{
			border: 1px solid #ddd;
		}
		.dummy-table-list li select{
			height:26px;
		}
		.dummy-table-list li span{
			display: inline-block;
			height:24px;
			line-height: 24px;
		}
</style>
<script>
	function cleartime()
	{
	document.getElementById("begintime").value='';
	document.getElementById("endtime").value='';
	}
	</script>
	<h3 class="header smaller lighter blue">订单审核</h3>
	
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
				<tr class="order_info">
				    <td  colspan="4">
					<?php 
					    if ( is_array($item['goods']) ){
                               foreach ( $item['goods'] as $goods ){
					?>
					    <div class="items">
						      <ul>
							      <li class="img"><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $goods['aid']))?>"><img src="<?php echo getGoodsThumb($goods['gid']); ?>" height="40" /></a></li>
								  <li class="title"><div><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $goods['aid']))?>" class="tab_title"><?php echo $goods['title']; ?></a></div>
									  <div>
										  <div class="name"><?php echo getGoodsProductPlace($goods['pcate']); ?></div>
								  		  <?php if($item['isdraw'] == 1) { ?>
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
					   <div class="remark-btn-div"><a class="remark-modal" type="button" href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="<?php echo $item['remark']; ?>"><img src="images/tag.png" /></a></div>
					   <div class='modal fade remark-detail' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>  
							<div class='modal-dialog modal-lg'>
								<div class='modal-content'>
									<div class='modal-header'> 
										<button type='button' class='close' data-dismiss='modal'>
											<span aria-hidden='true'>&times;</span>
											<span class='sr-only'>Close</span>
										</button>
										<h4 class='modal-title' id='myModalLabel'>备注信息</h4>
									</div>
									<div class='modal-body'>
										<?php echo $item['remark']; ?>
									</div>
								</div>
							</div>
						</div>
					   <?php } ?>
					</td>
					<td align="center" valign="middle" style="vertical-align: middle;"><?php  echo date('Y-m-d H:i:s', $item['createtime'])?></td>
		           <td align="center" valign="middle" style="vertical-align: middle;">
						<?php  if($item['paytypecode']=='bank'){?>	<span class="label label-danger" ><?php } ?><?php  echo $item['paytypename'];?><?php  if($item['paytypecode']=='bank'){?>	</span><?php } ?>
					   <?php
					   if(!empty($item['retag'])){
						   $retag = json_decode($item['retag'],true);
						   if(!empty($retag['recoder'])){
							   echo "<input type='hidden' value='{$item['retag']}' class='hide_order_log'/>";
							   echo "<span style='display:block;font-weight: bolder;color: #00D20D;cursor: pointer'><span class='glyphicon glyphicon-comment show_order_log'></span></span>";
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
						<td align="center" valign="middle" style="vertical-align: middle;"><a type="button" href="<?php  echo web_url('order', array('op' => 'detail', 'id' => $item['id']))?>" data-toggle="tooltip" data-placement="bottom" title="<?php if(!empty($item['retag'])){ $retag_json = json_decode($item['retag'],true); echo $retag_json['beizhu'];}else{ echo '没有标注信息'; } ?>"><img src="images/btag<?php echo $item['tag']; ?>.png" /></a></td>
				</tr>
				<?php  } } ?>
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
				$(this).parents(".remark-btn-div").siblings(".remark-detail").modal();
			})

			//点击查看订单日志
			$(".show_order_log").click(function(){
				var log_string = $(this).parent().prev().val();
				log_obj  = JSON.parse(log_string);
				//格式 2-测试订单-54815154545;3-已经发货-2323423  分号分开的字符串
				log_info = log_obj.recoder;
				//弹出框
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
