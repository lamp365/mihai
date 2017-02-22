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
}
.radio-inline img{
	margin-left: 5px;
}		
.product-table table tr th,.text-center{
	text-align: center;
}
.order-number{
	border: 1px solid #ddd!important;
	border-left: none!important;
}
.order_info td{
	border-bottom: none!important;
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
</style>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/select2.min.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/select2.min.js"></script>
<div class="memberinto-wrap">
	<div class="panel with-nav-tabs panel-default">	
	    <div class="panel-heading">
	            <ul class="nav nav-tabs">
	                <li class="active"><a href="#tab1primary" data-toggle="tab">商品数据</a></li>
					<li><a href="#tab2primary" data-toggle="tab">订单数据</a></li>
	                <li><a href="#tab3primary" data-toggle="tab">数据导入</a></li>
					<li><a href="#tab4primary" data-toggle="tab">制单导出</a></li>
	            </ul>
	    </div>
	    <div class="panel-body third-party">
	        <div class="tab-content">
	            <div class="tab-pane fade in active" id="tab1primary">
		            <form action="<?php  echo web_url('productsalestatistics', array('op' => 'refresh_goods'));?>" method="post">
						<ul class="search-ul">
							<li >
								<span class="left-span">产品名称</span>
								<input type="text" name="title" class="input-height" placeholder="产品名称" value="">
							</li>
							<li >
								<span class="left-span">货号</span>
								<input type="text" name="dishsn" class="input-height" placeholder="货号" value="">
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
								<a href="<?php  echo web_url('productsalestatistics', array('op' => 'refresh_goods'));?>" onclick="return confirm('确认刷新商品数据？');return false;">刷新</a>
							</li>
						</ul>
						
						<div class="panel panel-default product-table">
				            <table class="table table-striped table-bordered">
					            <thead >
					                <tr>
					                	<th>产品名称</th>
					                    <th>品名</th>
					                    <th>品牌</th>
					                    <th>货号</th>
					                    <th>规格</th>
					                    <th>重量</th>
					                    <th>单位</th>
					                    <th>组合</th>
					                    <th>类型</th>
					                    <th>一级分类</th>
					                    <th>二级分类</th>
					                    <th>价格</th>
					                </tr>
					            </thead>
						        <tbody>
						        	<?php  if(is_array($dish)) { 
	 								foreach($dish as $almv) { 
	 									?>
					                <tr>
					                	<td class="text-center">
					                		<input type="text" name="title" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php  echo $almv['title'];?></span>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="name" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php  echo $almv['name'];?></span>
					                	</td>
					                	<td class="text-center">
					                		<?php  echo get_brand($almv['brand']);?>
					                	</td>
					                	<td class="text-center">
					                		<?php  echo $almv['dishsn'];?>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="origin" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php  echo $almv['origin'];?></span>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="weight" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php  echo $almv['weight'];?></span>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="unit" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php  echo $almv['unit'];?></span>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="lists" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php  echo $almv['lists'];?></span>
					                	</td>
					                	<td class="text-center">
					                		<select class="type-select" onchange="selectChange(1,'type')">
					                			<option value="1" <?php if ($almv['type'] == '0') {
					                				echo 'selected';
					                			} ?>>一般商品</option>
					                			<option value="2" <?php if ($almv['type'] == '1') {
					                				echo 'selected';
					                			} ?>>组合商品</option>
					                		</select>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="p1" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php  echo $almv['p1'];?></span>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="p2" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php  echo $almv['p2'];?></span>
					                	</td>
					                	<td class="text-center">
					                		<input type="text" name="price" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $almv['id'];?>">
					                		<span class="modify-span"><?php  echo $almv['productprice'];?></span>
					                	</td>
					                </tr>
					                <?php  } } ?>
					            </tbody>
				            </table>
				        </div>
				        <!--添加商品数据模态框-->
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
												<select  style="height: 26px" id="pcates" name="pcates" class="pcates" onchange="fetchChildCategory(this,this.options[this.selectedIndex].value)"  autocomplete="off">
										            <option value="0">请选择一级分类</option>
										            <?php  if(is_array($category)) { foreach($category as $row) { ?>
										            <?php  if($row['parentid'] == 0) { ?>
										            <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['p1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
										            <?php  } ?>
										            <?php  } } ?>
										        </select>
										        <select  style="height: 26px" id="cates_2" name="ccates" class="cates_2" onchange="fetchChildCategory2(this,this.options[this.selectedIndex].value)" autocomplete="off">
										            <option value="0">请选择二级分类</option>
										            <?php  if(!empty($item['p2']) && !empty($childrens[$item['p1']])) { ?>
										            <?php  if(is_array($childrens[$item['p1']])) { foreach($childrens[$item['p1']] as $row) { ?>
										            <option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $item['p2']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
										            <?php  } } ?>
										            <?php  } ?>
										        </select>
												<select style="height: 26px" id="cate_3" name="ccate2" class="cate_3" autocomplete="off">
										            <option value="0">请选择三级分类</option>
										            <?php 
													    if(!empty($item['p3']) && !empty($childrens[$item['p3']])) { 
													       if(is_array($childrens[$item['p3']])) { 
															   foreach($childrens[$item['p3']] as $row) { 
													?>
										                     <option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $item['p3']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
										            <?php  } } } ?>
										        </select>
										        <a href="javascript:void(0)" onclick="findgoods()" class="btn btn-primary btn-sm span2" name="submit" ><i class="icon-edit"></i>查找产品</a>    
											</div>
										</div>
										<label><span class="modal-span">产品名称</span>
											<select name="c_goods" class="js-example-responsive" id="c_goods" >
										       <?php if (!empty($item['gname'])){ ?>
							                   <option value='<?php echo $item['gid']; ?>'><?php echo $item['gname']; ?></option>
											   <?php }else{ ?>
										       <option value='0'>未选择产品</option>
											   <?php } ?>
											</select>
										</label>
										<label><span class="modal-span">品名</span><input type="" name=""></label>
										<label><span class="modal-span">品牌</span><input type="" name=""></label>
										<label><span class="modal-span">货号</span><input type="" name=""></label>
										<label><span class="modal-span">规格</span><input type="" name=""></label>
										<label><span class="modal-span">重量</span><input type="" name=""></label>
										<label><span class="modal-span">单位</span><input type="" name=""></label>
										<label><span class="modal-span">组合</span><input type="" name=""></label>
										<label>
											<span class="modal-span">类型</span>
											<input type="" name="">
										</label>
										<label><span class="modal-span">一级分类</span><input type="" name=""></label>
										<label><span class="modal-span">二级分类</span><input type="" name=""></label>
										<label><span class="modal-span">价格</span><input type="" name=""></label>
									</div>
									<div class="modal-footer">
										<button class="btn btn-primary" type="button">保存</button>
									    <button class="btn btn-default" type="button" data-dismiss="modal">关闭</button>
									</div>
								</div>
							</div>
						</div>

					</form>
					<?php  echo $pager;?>
	            </div>
				<div class="tab-pane fade" id="tab2primary">
				    <form action="<?php  echo web_url('memberinto',array('op'=>'display'));?>" method="post">
						<ul class="search-ul">
							<li>
								<span class="left-span">订单号</span>
								<input type="text" name="order-number" class="input-height" placeholder="订单号">
							</li>
							<li>
								<span class="left-span">买家</span>
								<input type="text" name="buyers" class="input-height" placeholder="买家">
							</li>
							<li>
								<span class="left-span">状态</span>
								<input type="text" name="buyers" class="input-height" placeholder="状态">
							</li>
							<li>
								<span class="left-span">物流</span>
								<input type="text" name="buyers" class="input-height" placeholder="物流">
							</li>
							
							<li>
								
								<div class="btn-group">
								  <input type="submit" name="submit" value=" 查 询 "  class="btn btn-primary btn-sm">
								  <button type="button" class="btn btn-primary btn-sm dropdown-toggle add-more-btn" data-toggle="dropdown">
								    <span class="caret"></span>
								    <span class="sr-only">Toggle Dropdown</span>
								  </button>
								</div>
							</li>
			
							<ul class="hide-tr" style="width: 100%;overflow: hidden;padding: 0">
								<li >
									<span class="left-span">起始日期</span>
										<input class="input-height" name="begintime" id="begintime" type="text" value="<?php  echo $_GP['begintime'];?>" readonly="readonly"  placeholder="起始日期"/>
								</li>	
								<li> - </li>
								<li>
									<span class="left-span">终止日期</span>
									<input class="input-height" id="endtime" name="endtime" type="text" value="<?php  echo $_GP['endtime'];?>" readonly="readonly" placeholder="终止日期" />
								</li>
							</ul>
						</ul>
						
						<div class="panel panel-default product-table">
				            <table class="table table-striped table-bordered">
					            <thead >
					                <tr>
					                    <th>宝贝</th>
					                    <th>单价</th>
					                    <th>数量</th>
					                    <th>省份</th>
					                    <th>城市</th>
					                    <th>地区</th>
					                    <th>用户</th>
					                    <th>手机号码</th>
					                    <th>身份证</th><!-- 可编辑 -->
					                    <th>下单时间</th>
					                    <th>订单状态</th>
					                    <th>实收款</th>
					                    <th>标记</th>
					                </tr>
					            </thead>
						        <tbody>
						       		<tr>
						       			<td colspan="13" class="order-number">SN20170110827789</td>
						       		</tr>
					                <tr class="order_info">
					                    <td class="text-center">美国进口新章男士多种维生素矿物质成人男性综合复合维生素*72片</td>
					                    <td class="text-center">258.00</td>
					                    <td class="text-center">2</td>
					                    <td class="text-center">福建省</td>
					                    <td class="text-center">福州市</td>
					                    <td class="text-center">仓山区</td>
					                   	<td class="text-center">刘建凡</td>
					                   	<td class="text-center">18850737047</td>
					                   	<td class="text-center">
					                   		<input type="text" name="identity_id" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $item['id'];?>">
					                		<span class="modify-span">35042601235646499555</span>
					                   	</td>
					                   	<td class="text-center">2017-01-10 10:55:12</td>
					                    <td class="text-center"><span class="label label-success">已导出</span></td>
					                    <td class="text-center">832.39 元</td>
					                   	<td class="text-center"><img class="mark" onclick="mark(3)" src="images/btag0.png" /></td>
					                </tr>
					                <tr>
					                    <td class="text-center">美国进口新章男士多种维生素矿物质成人男性综合复合维生素*72片</td>
					                    <td class="text-center">258.00</td>
					                    <td class="text-center">2</td>
					                    <td class="text-center">福建省</td>
					                    <td class="text-center">福州市</td>
					                    <td class="text-center">仓山区</td>
					                   	<td class="text-center">刘建凡</td>
					                   	<td class="text-center">18850737047</td>
					                   	<td class="text-center">
					                   		<input type="text" name="identity_id" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $item['id'];?>">
					                		<span class="modify-span">35042601235646499555</span>
					                   	</td>
					                   	<td class="text-center">2017-01-10 10:55:12</td>
					                    <td class="text-center"><span class="label label-success">已导出</span></td>
					                    <td class="text-center">832.39 元</td>
					                   	<td class="text-center"><img class="mark" onclick="mark(3)" src="images/btag0.png" /></td>
					                </tr>
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
					                     	<input type="radio" name="tag" id="tag0" value="0">平潭<img src="images/tag0.png">
					                   	</label> 
									 	<label class="radio-inline">
					                     	<input type="radio" name="tag" id="tag1" value="1">彩虹<img src="images/tag1.png">
					                   	</label> 
									 	<label class="radio-inline">
					                     	<input type="radio" name="tag" id="tag2" value="2">贝海<img src="images/tag2.png">
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
		$(this).siblings(".modify-input").show().val(this_val).focus();
	});
	$(".modify-input").on("blur",function(){
		var thisObj = $(this);
		var this_id = thisObj.attr("ajax-title-id");
		var this_val = thisObj.val();
		var this_name = thisObj.attr("name");
		var url = "";
		$.post(url,{'op':'ajax_product','ajax_id':this_id,'ajax_value':this_val,'field_name':this_name},function(data){
			if( data.message == 1 ){
				this_title.siblings(".modify-span").text(data.value);
			}else{
				alert(data.message);
			}
		},"json");
		$(this).hide();
	})
});
//订单标记
function mark(product_id){
	$(".product_id").val(product_id);
	$(".mark-modal-dialog").modal();
}
//保存订单标记
function markSave(){
	var mark_value = $(".radio-inline input[name='tag']:checked").val();
	var product_id = $(".product_id").val();
	$.post("",{id:product_id,value:mark_value},function(data){
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
	var url = "";
	$.post(url,{'op':'ajax_product','ajax_id':ajax_id,'ajax_value':this_val,'field_name':field_name},function(data){
		if( data.message == 1 ){
			alert(data.message);
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
    $.post('<?php  echo create_url('site',array('name' => 'shop','do' => 'dish','op' => 'query'))?>',{pcate:pcate,ccate:ccate,ccate2:ccate2},function(m){
	    $('#c_goods').html(m);
	},"html");	
}
function fetchChildCategory(o_obj,cid) {
	var html = '<option value="0">请选择二级分类</option>';

	var obj = $(o_obj).parent().find('.cates_2').get(0);
	if (!category || !category[cid]) {
		$(o_obj).parent().find('.cates_2').html(html);

			fetchChildCategory2(o_obj,obj.options[obj.selectedIndex].value);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$(o_obj).parent().find('.cates_2').html(html);
    	fetchChildCategory2(o_obj,obj.options[obj.selectedIndex].value);

 }
   function fetchChildCategory2(o_obj,cid) {
	var html = '<option value="0">请选择三级分类</option>';
	if (!category || !category[cid]) {
		$(o_obj).parent().find('.cate_3').html(html);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	  $(o_obj).parent().find('.cate_3').html(html);
 }
</script>
<?php  include page('footer');?>