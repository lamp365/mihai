<?php 
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 百家威信 <QQ:2752555327> <http://www.squdian.com>
// +----------------------------------------------------------------------
defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">天猫订单处理</h3>
<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<style type="text/css">
.hide-tr{
		display: none;
	}
.mark{
	cursor: pointer;
	width: 30px;
	background: none;
}
.radio-inline img{
	margin-left: 5px;
}		
.product-table table tr th,.text-center{
	text-align: center;
	vertical-align: middle!important;
}
.order-number{
	border: 1px solid #ddd!important;
	border-left: none!important;
}
.order_info td{
	border-bottom: none!important;
	vertical-align: middle!important;
}
td{
	position: relative;
}
.modify-input{
	position: absolute;
	width: 100%;
	left: 0;
	top: 2px;
	display: none;
}
.modify-span{
	cursor: pointer;
	width: 100%;
    display: block;
}
.add-product-modal .modal-body .modal-span{
	width: 100px;
	float: left;
	text-align: right;
    padding-right: 15px;
}
.add-product-modal .modal-body label{
	width: 100%;
	overflow: hidden;
}
#c_goods{
	width: 300px;
}
.refresh-a{
	color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
    height: 29px;
    display: inline-block;
    width: 48px;
    text-align: center;
    line-height: 29px;
    border-radius: 3px;
    font-size: 12px;
    text-decoration: none;
}
.refresh-a:hover{
	color: #fff;
	background-color: #286090;
    border-color: #204d74;
    text-decoration: none;
}
</style>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/select2.min.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/select2.min.js"></script>
<div class="memberinto-wrap">
	<div class="panel with-nav-tabs panel-default">	
	    <div class="panel-heading">
	            <ul class="nav nav-tabs">
	                <li <?php if ($now_page==1) {echo 'class="active"';}?>><a href="#tab1primary" data-toggle="tab">商品数据</a></li>
					<li <?php if ($now_page==2) {echo 'class="active"';}?>><a href="#tab2primary" data-toggle="tab">订单数据</a></li>
	                <li><a href="#tab3primary" data-toggle="tab">数据导入</a></li>
					<li><a href="#tab4primary" data-toggle="tab">制单导出</a></li>
	            </ul>
	    </div>
	    <div class="panel-body third-party">
	        <div class="tab-content">
	            <div class="tab-pane fade <?php if ($now_page==1) {echo 'in active';}?>" id="tab1primary">
		            <form action="<?php  echo web_url('productsalestatistics', array('op' => 'display', 'nowpage' => '1'));?>" method="post">
						<ul class="search-ul">
							<li >
								<span class="left-span">产品名称</span>
								<input type="text" name="sg_title" class="input-height" placeholder="产品名称" value="<?php echo $title; ?>">
							</li>
							<li >
								<span class="left-span">货号</span>
								<input type="text" name="sg_dishsn" class="input-height" placeholder="货号" value="<?php echo $dishsn; ?>">
							</li>
							<li>
								<div class="btn-group">
								  	<input type="submit" name="submit" value=" 查 询 "  class="btn btn-primary btn-sm">
								</div>
							</li>
							<li>
								<input type="button" value="添 加" onclick="addProduct()" class="btn btn-primary btn-sm">
							</li>
							<li>
								<a class="refresh-a" href="<?php  echo web_url('productsalestatistics', array('op' => 'refresh_goods'));?>" onclick="return confirm('确认刷新商品数据？');return false;">刷 新</a>
							</li>
						</ul>
						
						<div class="panel panel-default product-table">
				            <table class="table table-striped table-bordered">
					            <thead >
					                <tr>
					                	<th>产品名称</th>
					                    <th><a href="<?php echo web_url('productsalestatistics', array('op' => 'display','ordername' => $oname)); ?>">品名</a></th>
					                    <th>品牌</th>
					                    <th>货号</th>
					                    <th><a href="<?php echo web_url('productsalestatistics', array('op' => 'display','orderorigin' => $oorigin)); ?>">规格</a></th>
					                    <th><a href="<?php echo web_url('productsalestatistics', array('op' => 'display','orderweight' => $oweight)); ?>">重量</a></th>
					                    <th><a href="<?php echo web_url('productsalestatistics', array('op' => 'display','orderunit' => $ounit)); ?>">单位</a></th>
					                    <th><a href="<?php echo web_url('productsalestatistics', array('op' => 'display','orderlists' => $olists)); ?>">组合</a></th>
					                    <th><a href="<?php echo web_url('productsalestatistics', array('op' => 'display','ordertype' => $otype)); ?>">类型</a></th>
					                    <th><a href="<?php echo web_url('productsalestatistics', array('op' => 'display','orderp1' => $op1)); ?>">一级分类</a></th>
					                    <th><a href="<?php echo web_url('productsalestatistics', array('op' => 'display','orderp2' => $op2)); ?>">二级分类</a></th>
					                    <th><a href="<?php echo web_url('productsalestatistics', array('op' => 'display','orderprice' => $oprice)); ?>">价格</a></th>
					                </tr>
					            </thead>
						        <tbody>
						        	<?php  if(is_array($dish)) { 
	 								foreach($dish as $almv) { 
	 									?>
					                <tr>
					                	<td class="text-center">
					                		<input type="text" name="title" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php if (empty($almv['title'])) {
					                			echo '&nbsp';
					                		}else{
					                			echo $almv['title'];
					                		}?></span>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="good_name" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php if (empty($almv['name'])) {
					                			echo '&nbsp';
					                		}else{
					                			echo $almv['name'];
					                		}?></span>
					                	</td>
					                	<td class="text-center">
					                		<?php  echo get_brand($almv['brand']);?>
					                	</td>
					                	<td class="text-center">
					                		<?php  echo $almv['dishsn'];?>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="origin" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php if (empty($almv['origin'])) {
					                			echo '&nbsp';
					                		}else{
					                			echo $almv['origin'];
					                		}?></span>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="weight" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php if (empty($almv['weight'])) {
					                			echo '&nbsp';
					                		}else{
					                			echo $almv['weight'];
					                		}?></span>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="unit" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php if (empty($almv['unit'])) {
					                			echo '&nbsp';
					                		}else{
					                			echo $almv['unit'];
					                		}?></span>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="lists" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php if (empty($almv['lists'])) {
					                			echo '&nbsp';
					                		}else{
					                			echo $almv['lists'];
					                		}?></span>
					                	</td>
					                	<td class="text-center">
					                		<select class="type-select" onchange="selectChange(<?php  echo $almv['id'];?>,'type')">
					                			<option value="0" <?php if ($almv['type'] == '0') {
					                				echo 'selected';
					                			} ?>>一般商品</option>
					                			<option value="1" <?php if ($almv['type'] == '1') {
					                				echo 'selected';
					                			} ?>>组合商品</option>
					                		</select>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="p1" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php if (empty($almv['p1'])) {
					                			echo '&nbsp';
					                		}else{
					                			echo $almv['p1'];
					                		}?></span>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="p2" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php if (empty($almv['p2'])) {
					                			echo '&nbsp';
					                		}else{
					                			echo $almv['p2'];
					                		}?></span>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="productprice" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php if (empty($almv['productprice'])) {
					                			echo '&nbsp';
					                		}else{
					                			echo $almv['productprice'];
					                		}?></span>
					                	</td>
					                </tr>
					                <?php  } } ?>
					            </tbody>
				            </table>
				        </div>
				        
					</form>
					<!--添加商品数据模态框-->
				        <form action="<?php  echo web_url('productsalestatistics', array('op' => 'add_good'));?>" method="post">
			        	<div class='modal fade add-product-modal' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>  
							<div class='modal-dialog'>
								<div class='modal-content'>
									<div class='modal-header'> 
										<button type='button' class='close' data-dismiss='modal'>
											<span aria-hidden='true'>&times;</span>
											<span class='sr-only'>Close</span>
										</button>
										<h4 class='modal-title' class='myModalLabel'>添加商品数据</h4>
									</div>
									<div class='modal-body'>
										<div class="form-group" style="overflow: auto;">
											<div class="col-sm-12">
												<select  name="p1" class="fetchChildCategory" onchange="fetchChildCategory(this.options[this.selectedIndex].value)">
													<option value="0">请选择一级分类</option>
													<?php  if(is_array($category)) { foreach($category as $row) { ?>
													<?php  if($row['parentid'] == 0) { ?>
													<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GP['p1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
													<?php  } ?>
													<?php  } } ?>
												</select>
										        <select onchange="fetchChildCategory2(this.options[this.selectedIndex].value)"  id="p2" name="p2">
													<option value="0">请选择二级分类</option>
													<?php  if(!empty($_GP['p1']) && !empty($childrens[$_GP['p1']])) { ?>
													<?php  if(is_array($childrens[$_GP['p1']])) { foreach($childrens[$_GP['p1']] as $row) { ?>
													<option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $_GP['p2']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
													<?php  } } ?>
													<?php  } ?>
												</select>
										        <a href="javascript:void(0)" onclick="findgoods()" class="btn btn-primary btn-sm span2" name="submit" ><i class="icon-edit"></i>查找产品</a>    
											</div>
										</div>
										<label><span class="modal-span">产品名称</span>
											<select name="c_goods" class="js-example-responsive" id="c_goods" onchange="c_goods_fun()">
										       <?php if (!empty($item['gname'])){ ?>
							                   <option value='<?php echo $item['gid']; ?>'><?php echo $item['gname']; ?></option>
											   <?php }else{ ?>
										       <option value='0'>未选择产品</option>
											   <?php } ?>
											</select>
										</label>
										<label><span class="modal-span">品名</span><input type="" name="ad_name"></label>
										<input type="hidden" class="add_brand_hidden" name="add_brand_hidden">
										<label><span class="modal-span">品牌</span><input type="" class="add_brand" name="ad_brand"></label>
										<label><span class="modal-span">货号</span><input type="" class="add_dishsn" name="ad_sn"></label>
										<label><span class="modal-span">规格</span><input type="" name="ad_origin"></label>
										<label><span class="modal-span">重量</span><input type="" name="ad_weight"></label>
										<label><span class="modal-span">单位</span><input type="" name="ad_unit"></label>
										<label><span class="modal-span">组合</span><input type="" name="ad_lists"></label>
										<label>
											<span class="modal-span">类型</span>
											<select name="ad_type">
												<option value="0">一般商品</option>
												<option value="1">组合商品</option>
											</select>
										</label>
										<input type="hidden" class="type-p1-hidden" name="type-p1">
										<input type="hidden" class="type-p2-hidden" name="type-p2">
										<label><span class="modal-span">一级分类</span><input class="type-p1" type="" name=""></label>
										<label><span class="modal-span">二级分类</span><input class="type-p2" type="" name=""></label>
										<label><span class="modal-span">价格</span><input type="" name="ad_price"></label>
									</div>
									<div class="modal-footer">
										<input type="submit" name="submit" value="保存"  class="btn btn-primary btn-sm">
									    <button class="btn btn-default" type="button" data-dismiss="modal">关闭</button>
									</div>
								</div>
							</div>
						</div>
						</form>
					<?php  echo $pager;?>
	            </div>
				<div class="tab-pane fade <?php if ($now_page==2) {echo 'in active';}?>" id="tab2primary">
				    <form action="<?php  echo web_url('productsalestatistics', array('op' => 'display', 'nowpage' => '2'));?>" method="post">
						<ul class="search-ul">
							<li>
								<span class="left-span">订单号</span>
								<input type="text" name="order_number" class="input-height" placeholder="订单号" value="<?php  echo $order_number;?>">
							</li>
							<li>
								<span class="left-span">物流</span>
								<select name="order_tag" class="input-height">
									<option value="0">请选择物流</option>
	 								<option value="1" <?php if ($order_tag == 1) {echo "selected";} ?>>平潭</option>
                                    <option value="2" <?php if ($order_tag == 2) {echo "selected";} ?>>彩虹</option>
									<option value="3" <?php if ($order_tag == 3) {echo "selected";} ?>>贝海</option>
								</select>
							</li>
							<li >
								<span class="left-span">起始日期</span>
									<input class="input-height" name="begintime" id="begintime" type="text" value="<?php  echo $begintime;?>" readonly="readonly"  placeholder="起始日期"/>
							</li>	
							<li>
								<span class="left-span">终止日期</span>
								<input class="input-height" id="endtime" name="endtime" type="text" value="<?php  echo $endtime;?>" readonly="readonly" placeholder="终止日期" />
							</li>
							
							<li>
								
								<div class="btn-group">
								  <input type="submit" name="submit" value=" 查 询 "  class="btn btn-primary btn-sm">
								</div>
							</li>
			
						</ul>
						
						<div class="panel panel-default product-table">
				            <table class="table table-striped table-bordered">
					            <thead >
					                <tr>
					                    <th style="width:370px">宝贝</th>
					                    <th style="width:80px">单价</th>
					                    <th style="width:50px">数量</th>
					                    <th>省份</th>
					                    <th>城市</th>
					                    <th>地区</th>
					                    <th>用户</th>
					                    <th>手机号码</th>
					                    <th>身份证</th><!-- 可编辑 -->
					                    <th>下单时间</th>
					                    <th>实收款</th>
					                </tr>
					            </thead>
						        <tbody>
						        	<?php  if(is_array($order)) { 
	 								foreach($order as $aork => $aorv) { 
	 									?>
						       		<tr>
						       			<td colspan="12" class="order-number" ><span style="display:inline-block;min-width:150px"><?php  echo $aorv['ordersn'];?></span><img class="mark mark_<?php  echo $aorv['id'];?>" onclick="mark(<?php  echo $aorv['id'];?>)" src="images/btag<?php  echo intval($aorv['tag'])-1;?>.png" /></td>
						       		</tr>
						       		<tr class="order_info">
							       		<td class="text-center" colspan="3" width="500px">
							       		<?php $use_goods = get_order_goods($aorv['ordersn']); foreach($use_goods as $rorv) {   ?>
						                	<div style="overflow:hidden;margin-bottom: 10px;">
						                    	<div style="float: left;width: 360px;text-align: left;"><?php  echo $rorv['tit'];?></div>
						                    	<div style="float: left;width:80px"><?php  echo $rorv['productprice'];?></div>
						                    	<div style="float: right;width:40px"><?php  echo $rorv['total'];?></div>
						                  	</div>  
						                <?php } ?>
						                </td>
					                	<td class="text-center"><?php  echo $aorv['address_province'];?></td>
					                    <td class="text-center"><?php  echo $aorv['address_city'];?></td>
					                    <td class="text-center"><?php  echo $aorv['address_area'];?></td>
					                   	<td class="text-center"><?php  echo $aorv['address_realname'];?></td>
					                   	<td class="text-center"><?php  echo $aorv['address_mobile'];?></td>
					                   	<td class="text-center">
					                   		<input type="text" name="identity_id" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $aorv['id'];?>">
					                		<span class="modify-span"><?php  echo $aorv['identity_id'];?></span>
					                   	</td>
					                   	<td class="text-center"><?php  echo date('Y-m-d H:i:s',$aorv['createtime']);?></td>
					                    <td class="text-center"><?php  echo $aorv['price'];?></td>	
					                </tr>
					            <?php  } } ?>
					            </tbody>
				            </table>
				        </div>
				        <!-- 订单标记模态框开始 -->
				        <input type="hidden" name="product_id" class="product_id">
			        	<div class='modal fade mark-modal-dialog' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>  
							<div class='modal-dialog'>
								<div class='modal-content'>
									<div class='modal-header'> 
										<button type='button' class='close' data-dismiss='modal'>
											<span aria-hidden='true'>&times;</span>
											<span class='sr-only'>Close</span>
										</button>
										<h4 class='modal-title' class='myModalLabel'>订单标记</h4>
									</div>
									<div class='modal-body'>										
				    			       	<label class="radio-inline">
					                     	<input type="radio" name="tag" id="tag0" value="1">平潭<img src="images/tag0.png">
					                   	</label> 
									 	<label class="radio-inline">
					                     	<input type="radio" name="tag" id="tag1" value="2">彩虹<img src="images/tag1.png">
					                   	</label> 
									 	<label class="radio-inline">
					                     	<input type="radio" name="tag" id="tag2" value="3">贝海<img src="images/tag2.png">
					                   	</label> 
									</div>
									<div class="modal-footer">
										<button class="btn btn-primary mark-save" onclick="markSave()" type="button" >保存</button>
									    <button class="btn btn-default" type="button" data-dismiss="modal">关闭</button>
									</div>
								</div>
							</div>
						</div>
						<!-- 订单标记模态框结束 -->
					</form>
					<?php  echo $pager;?>
				</div>
	            <div class="tab-pane fade" id="tab3primary">
		            <form action="" method="post" class="form-horizontal order_form" enctype="multipart/form-data">
						<table  class="table dummy-table-list" align="center">
							<tbody>
							    <tr><td><div class="alert alert-info" style="margin:10px 0; width:auto;">
			<i class="icon-lightbulb"></i> 提示: 每个导入的订单数据中，一定要导入商品数据，否则会无法获取订单中的商品种类, 无所谓顺序，标注只是为了方便。系统会自动判断是订单数据还是产品数据
		</div></td></tr>
								<tr>
									<td>
										<li style="line-height: 26px;">天猫订单数据：</li>
										<li >
											<input style="line-height: 26px;" name="myorder" type="file"   value="" />
										</li>
									</td>
								</tr>	
								<tr>
									<td>
										<li style="line-height: 26px;">订单商品数据：</li>
										<li >
											<input style="line-height: 26px;" name="mygoods" type="file"   value="" />
										</li>
										<li >
											<button type="button" class="order_input btn btn-md btn-warning btn-sm">开始导入</button>
										</li>
									</td>
								</tr>	
							</tbody>		
						</table>
					</form>
	            </div>
				<!-- 订单导出表单 -->
				 <div class="tab-pane fade" id="tab4primary">
		            <form action="" method="post" class="form-horizontal refund_form" enctype="multipart/form-data">
					    <ul class="search-ul">
							<li>
								<span class="left-span">物流</span>
								<select name="wuliu" class="city input-height">
									<option value="0" >请选择物流</option>
	 								<option value="1" >平潭</option>
                                    <option value="2" >彩虹</option>
									<option value="3" >贝海</option>
								</select>
							</li>
							<li>
                                 <button class="btn btn-sm btn-warning btn-primary" type="submit" name="orderstatisticsEXP01" value="orderstatisticsEXP01">导出excel</button>
                            </li>
						</ul>
					</form>
	            </div>
	        </div>
	    </div>
	</div>
</div>

<script>
$(function(){
	$(".order_input").click(function(){
		if(confirm('确定开始导入')){
			var url = "<?php  echo web_url('productsalestatistics',array('op'=>'into'));?>";
			$(".order_form").attr('action',url);
			$(".order_form").submit();
		}
	});
	var old_value='';
	$(".add-more-btn").click(function(){
		$(".hide-tr").toggle();
	});
	//时间初始化
	laydate({
	 	elem: '#begintime',
	 	istime: true, 
	 	event: 'click',
	 	format: 'YYYY-MM-DD hh:mm:ss',
	 	istoday: true, //是否显示今天
	 	start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	});
	laydate({
	 	elem: '#endtime',
	 	istime: true, 
	 	event: 'click',
	 	format: 'YYYY-MM-DD hh:mm:ss',
	 	istoday: true, //是否显示今天
	 	start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	});
	laydate.skin("molv"); 
	//编辑
	$(".modify-span").on("click",function(){
		var this_val = $(this).text();
		old_value = this_val;
		$(this).siblings(".modify-input").show().val(this_val).focus();
	});
	$(".modify-input").on("blur",function(){
		var thisObj = $(this);
		var this_id = thisObj.attr("ajax-title-id");
		var this_val = thisObj.val();
		var this_name = thisObj.attr("name");
		if( old_value == this_val){
			thisObj.hide();
		}else{
			var url = "<?php  echo web_url('productsalestatistics',array('op'=>'edit_data'));?>";
			$.post(url,{'ajax_id':this_id,'ajax_value':this_val,'field_name':this_name},function(data){
				if( data.message == 1 ){
					console.log(data.value);
					thisObj.siblings(".modify-span").text(data.value);
					thisObj.hide();
				}else{
					alert(data.message);
					thisObj.hide();
				}
			},"json");
		}
	})
});
//订单标记
function mark(product_id){
	$(".product_id").val(product_id);
	$(".mark-modal-dialog").modal();
	$("#tag"+product_id).prop("checked","true");
}
//保存订单标记
function markSave(){
	var mark_value = $(".radio-inline input[name='tag']:checked").val();
	var product_id = $(".product_id").val();
	var new_img = parseInt(mark_value)-1;
	var url = "<?php  echo web_url('productsalestatistics',array('op'=>'mark'));?>";
	$.post(url,{mark_id:product_id,mark_val:mark_value},function(data){
		if(data.message==1){
			$(".mark_"+product_id+"").attr("src","images/btag"+new_img+".png");
		}else{
			alert(data.message);
		}
		$(".mark-modal-dialog").modal('hide');
	},"json");
}
//刷新
function refresh(){
	window.location.reload();
}
//类型编辑
function selectChange(ajax_id,field_name){
	var this_val = $(".type-select").val();
	var url = "<?php  echo web_url('productsalestatistics',array('op'=>'edit_data'));?>";
	$.post(url,{'ajax_id':ajax_id,'ajax_value':this_val,'field_name':field_name},function(data){
		if( data.message == 1 ){
		}else{
			alert(data.message);
		}
	},"json");
}
//添加商品数据
function addProduct(){
	$(".add-product-modal").modal();
	$(".add-product-modal").on("shown.bs.modal", function(){
		$("#c_goods").select2();
	})
}
function findgoods(){
    var pcate = $('#pcates').val();
	var ccate = $('#cates_2').val();
	var ccate2 = $('#cate_3').val();
	var p2_text = $("#p2 option:selected").text();
	var p2_val = $("#p2").val();
	var fetchChildCategory = $(".fetchChildCategory option:selected").text();
	var fetchChildCategory_val = $(".fetchChildCategory").val();
	$(".type-p1").val(fetchChildCategory);
	$(".type-p1-hidden").val(fetchChildCategory_val);
	$(".type-p2").val(p2_text);
	$(".type-p2-hidden").val(p2_val);
    $.post('<?php  echo create_url('site',array('name' => 'shop','do' => 'dish','op' => 'query'))?>',{pcate:pcate,ccate:ccate,ccate2:ccate2},function(m){
	    $('#c_goods').html(m);
	},"html");	
}
//产品名称change事件
function c_goods_fun(){
	var url = "<?php  echo web_url('productsalestatistics', array('op' => 'get_good_val'));?>";
	var goods_val = $("#c_goods").val();
	$.post(url,{good_id:goods_val},function(data){
		if(data.message == 1){
			$(".add_brand").val(data.brand);
			$(".add_dishsn").val(data.dishsn);
			$(".add_brand_hidden").val(data.brandid);
		}else{
			alert(data.message);
		}
	},"json");
}
var category = <?php  echo json_encode($childrens)?>;	
function fetchChildCategory(cid) {
	var html = '<option value="0">请选择二级分类</option>';
	if (!category || !category[cid]) {
		$('#p2').html(html);
		fetchChildCategory2(document.getElementById("p2").options[document.getElementById("p2").selectedIndex].value);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#p2').html(html);
	fetchChildCategory2(document.getElementById("p2").options[document.getElementById("p2").selectedIndex].value);

}
   function fetchChildCategory2(cid) {
	var html = '<option value="0">请选择三级分类</option>';
	if (!category || !category[cid]) {
		$('#p3').html(html);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#p3').html(html);
 }
</script>
<?php  include page('footer');?>