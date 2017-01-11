<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<style type="text/css">
	.icon-pencil{
		padding: 0 8px;
		cursor: pointer;
	}
	td{
		position: relative;
	}
	.modify-input{
		position: absolute;
		width: 100%;
    	left: 0;
    	display: none;
	}
	.no-padding-left{
		line-height: 34px;
   		text-align: right;
	}
	.modal-title{
		text-align: center;
	}
	.form-group{
		overflow: hidden;
	}
	.wholesale-cogs{font-size: 16px;}
	.product-stock span,.wholesale-cogs{
		cursor: pointer;
	}
	.modal-content{
		margin-top: 300px;
	}
	.wholesale-td{
		position: relative;
	}
	.wholesale-cogs{
		position: absolute;
    	top: 41px;
	}
	.vip-form-desc{
		text-align: left;
	    margin: 0 auto;
	    border-bottom: 1px dotted #ddd;
	    margin-bottom: 15px;
	    padding-bottom: 15px;
	}
	.shop-list-tr{
		background-color: #fff!important;
	}
	.shop-list-tr li{
		float:left;list-style-type:none;
	}
	.shop-list-tr select{
		margin-right:10px;height:30px; line-height:28px; padding:2px 0;
	}
</style>
<h3 class="header smaller lighter blue">觅海全球购</h3> 
<form action=""  class="form-horizontal" method="post">

	<table class="table table-striped table-bordered table-hover">
			<tbody>
				<tr class="shop-list-tr">
				<td>
				        <li >
						<select  name="cate_1" onchange="fetchChildarea(this.options[this.selectedIndex].value)">
							<option value="0">请选择保税仓</option>
							<?php  if(is_array($area)) { foreach($area as $row) { ?>
							<?php  if($row['parentid'] == 0) { ?>
							<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GP['cate_1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
							<?php  } ?>
							<?php  } } ?>
						</select>
						</li>
						<li >
						<select  name="p1" onchange="fetchChildCategory(this.options[this.selectedIndex].value)">
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
						</li>
						<li >
						   <select name="type" >
							   <option value="-1" selected>类型</option>
							   <option value="0" <?php if($_GP['type']===0){?>selected="selected"<?php  } ?>>一般商品</option>
                               <option value="1" <?php if($_GP['type']==1){?>selected="selected"<?php  } ?> >团购商品</option>
                               <option value="2" <?php if($_GP['type']==2){?>selected="selected"<?php  } ?>>秒杀商品</option>
                               <option value="3" <?php if($_GP['type']==3){?>selected="selected"<?php  } ?>>今日特价商品</option>
						       <option value="4" <?php if($_GP['type']==4){?>selected="selected"<?php  } ?>>限时促销</option>
						   </select>
						</li>
						<li >
						<select name="status" >
							<option value="1" <?php if($_GP['status']==1){?>selected="selected"<?php  } ?>> 开通中</option>
							<option value="0" <?php if($_GP['status']==0){?>selected="selected"<?php  } ?> >已关闭</option>
						</select>
						</li>
						
						<li >
							<input style="margin-right:5px;width: 300px; height:30px; line-height:28px; padding:2px 0" name="keyword" id="" type="text" value="<?php  echo $_GP['keyword'];?>">
							<select  name="key_type" >
                                    <option value="title" <?php if($_GP['key_type']=='title'){?>selected="selected"<?php  } ?> >标题</option>
									<option value="id" <?php if($_GP['key_type']=='id'){?>selected="selected"<?php  } ?>>ID</option>
							</select>
						</li>
						<li >
						<button class="btn btn-primary btn-sm" ><i class="icon-search icon-large"></i> 搜索</button>
						<button type="submit" name="report" value="report" class="btn btn-warning btn-sm" style="margin-right:10px;">导出excel</button>
						</li>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
	<table class="table table-striped table-bordered table-hover">
  <tr>
    <th class="text-center" >
    	<input type="checkbox" onclick="selectAll()" id="selectAll"/>
    	宝贝ID
    </th>
    <th class="text-center" >首图</th>
    <th class="text-center" >产品库编号</th>
	<th class="text-center" >条形码</th>
    <th class="text-center">产品名称</th>
	<th class="text-center" ><a href="<?php echo $sorturl."&orderprice=".$oprice; ?>">价格</a></th>
	<th class="text-center"><a href="<?php echo $sorturl."&ordertprice=".$otprice; ?>">特别价格</a></th>
    <th class="text-center">批发价格（美元)</th>
	<th class="text-center" ><a href="<?php echo $sorturl."&ordertot=".$otot; ?>">库存</a></th>
    <th class="text-center" >状态</th>
    <th class="text-center" >操作</th>
  </tr>

		<?php if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
				 	<td style="text-align:center;" class="dish-id">
				 		<input type="checkbox" class="dishvalue" name="disvalue[]" value="<?php  echo $item['id'];?>"/>
				 		<?php  echo $item['id'];?>				 			
				 	</td>
				 	<td><p style="text-align:center"> 				                          
				        <img src="<?php  echo $item['imgs'];?>" height="60" width="60">				        	
				        </p>
				    </td>
                    <td style="text-align:center;"><?php  echo $item['gid'];?></td>
					<td style="text-align:center;"><?php  echo $item['goodssn']; ?> </td>
                	<td style="text-align:center;" class="product-title">
                		<input type="text" name="" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $item['id'];?>">
                		<a target="_blank" class="product-title-a" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $item['id']))?>"><?php  echo $item['title'];?></a>
                		<i class="modify-icon icon-pencil"></i>
                	</td>
					<td style="text-align:center;" ><?php  echo $item['marketprice'];?></td>
					<td style="text-align:center;" ><?php  echo $item['timeprice'];?></td>
					<td style="text-align:center;" class="wholesale-td">
						<div class="wholesale-div" ajax_id="<?php  echo $item['id'];?>">
					     <?php if ( isset( $item['purchase_price'] ) ){ foreach ( $item['purchase_price'] as $purchase_price ){ ?>
					           <span class="label label-danger wholesale" style="margin-left:5px;"><?php echo $purchase_price['name'].$purchase_price['vip_price']; ?></span>  					     	   
						 <?php }}?>
						 </div>
						<?php if(isHasPowerOperateField('shop_dish','vip_price')){ ?>
						 <i class="wholesale-cogs icon-cog" ajax-vip-price-id="<?php  echo $item['id'];?>"></i>
						<?php } ?>
					</td>
					<td style="text-align:center;" class="product-stock">
						<input type="text" name="" class="modify-stock form-control modify-input" ajax-stock-id="<?php  echo $item['id'];?>"><span><?php  echo $item['total'];?></span>
					</td>											
                    <td style="text-align:center;display:none;">
                        <?php  echo $item['count']?$item['count']:0;?>                 
                    </td>
					<td style="text-align:center;"><?php  if($item['status']) { ?><span data='<?php  echo $item['status'];?>' onclick="setProperty1(this,<?php  echo $item['id'];?>,'status')" class="label label-success" style="cursor:pointer;">开通中</span><?php  } else { ?><span data='<?php  echo $item['status'];?>' onclick="setProperty1(this,<?php  echo $item['id'];?>,'status')" class="label label-danger" style="cursor:pointer;">已关闭</span><?php  } ?><!--&nbsp;<span class="label label-info"><?php  if($item['type'] == 1) { ?>实体二手车<?php  } else { ?>虚拟二手车<?php  } ?></span>--><span class="label label-danger" style="margin-left:5px;"><?php  echo $item['typename'];?></span><?php if ( isset($item['purchase']) ){echo '<span class="label label-danger" style="margin-left:5px;">'.$item['purchase'].'</span>';} ?>
					</td>
					<td style="text-align:center;">
					<a class="btn btn-xs btn-info" target="_blank" href="<?php echo WEBSITE_ROOT.mobile_url('detail',array('name'=>'shopwap','id'=>$item['id']));?>" style="display:none;"><i class="icon-eye-open"></i>&nbsp;查&nbsp;看&nbsp;</a>&nbsp;&nbsp;
					<?php if($item['type'] == 1 && isHasPowerToShow('shop','dish','open_groupbuy')){ ?>
						<?php if($item['open_groupbuy'] == 1){ ?>
							<a  class="btn btn-xs btn-danger" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'open_groupbuy','act'=>'close'))?>"><i class="icon-edit"></i>关闭凑单</a>&nbsp;&nbsp;
						<?php }else{ ?>
							<a  class="btn btn-xs btn-warning" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'open_groupbuy','act'=>'open'))?>"><i class="icon-edit"></i>开启凑单</a>&nbsp;&nbsp;
						<?php } ?>
					<?php } ?>
					<?php if(isHasPowerToShow('shop','dish','post','edit')){ ?>
						<a  class="btn btn-xs btn-info" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'post'))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
					<?php } ?>
					<?php if(isHasPowerToShow('shop','dish','delete')){ ?>
						<a  class="btn btn-xs btn-info" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
					<?php } ?>
						&nbsp;&nbsp;					
					</td>
				</tr>
				<?php  } } ?>
 	
		</table>
		<input type="hidden" class="vip-number" value="<?php echo count($vip_list); ?>">
		<input type="hidden" name="" class="ajax-id" value="">
		<div class='modal fade wholesale-modal' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>  
			<div class='modal-dialog modal-lg'>
				<div class='modal-content'>
					<div class='modal-header'> 
						<button type='button' class='close' data-dismiss='modal'>
							<span aria-hidden='true'>&times;</span>
							<span class='sr-only'>Close</span>
						</button>
						<h4 class='modal-title' id='myModalLabel'>批发价格修改</h4>
					</div>
					<div class='modal-body'>
						<div class="vip-form-area">
							<div class="form-group form-inline vip-form">
					 	   		<label class="col-sm-3 control-label no-padding-left" >会员价格：</label>
					 	   		<div class="col-sm-7 set-vip-price">
									  <select  class="form-control set-select">
									  		<option value="-1">--请选择--</option>
									  </select>
				                      <div class="input-group">
				                       <span class="input-group-addon">$</span>
									   <input type="text"  class="form-control vip_price" value="" placeholder="请输入价格"/>
									  </div>
								</div>
								<div class="col-sm-2">
									<a href="javascript:void(0);" class="btn btn-danger remove_vip" >移除</a>
								</div>
					 	   </div>
				 	   </div>
				 	   <div class="form-group">
				 	   		<label class="col-sm-3 control-label no-padding-left" ></label>
				 	   		<div class="col-sm-9">
								<a href="javascript:void(0)" class="btn btn-primary addvip" name="button"><i class="icon-plus"></i>添加会员</a>
							</div>

				 	   </div>
					</div>
					<div class="modal-footer">
						<div class='vip-form-desc'>currency属性用来控制价格符号，1代表￥，2代表$,描述描述</div>
				        <button type="button" class="btn btn-primary btn-save">保存</button>
					</div>
				</div>
			</div>
		</div>
		<!--增加的操作-->
		<?php if ( !empty( $list_op )  ){ ?>
		<select style="display: block;margin-right:10px;margin-top:10px;width: 150px; height:34px; line-height:28px; padding:2px 0" name="" id="option" onchange="fetchOption()">
		    <?php foreach ( $list_op as $key=>$list_op_value ){ ?>
			<option value="<?php echo $list_op_value; ?>"><?php echo $key; ?></option>
			<?php } ?>
		</select>
		<?php } ?>
		<?php  echo $pager;?>
<script language="javascript">
   var area = <?php  echo json_encode($children)?>;
   function fetchChildarea(cid) {
	var html = '<option value="0">请选择二级分类</option>';
	if (!area || !area[cid]) {
		$('#cate_2').html(html);
		return false;
	}
	for (i in area[cid]) {
		html += '<option value="'+area[cid][i][0]+'">'+area[cid][i][1]+'</option>';
	}
	$('#cate_2').html(html);
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
 
 //操作方法
  function fetchOption(){
	if ( $("#option").val() == -1){
		return;
	}
 	//选中了哪些宝贝,将他们的value存进数组list
 	var list=[];
 	$("input[name = 'disvalue[]']:checked").each(function(){ 		
 		list.push(this.value);
 	})
 	if(confirm("确定"+$(":selected","#option")[0].innerHTML+"选中的宝贝？")){ 		
		$.post('', {'id' : list,'op':'ajax','todo':$("#option").val()}, function(s) {
			  window.location.reload();
		});
 	}
 }
 //全选
 function selectAll(){
	if($("#selectAll").is(':checked')){
	    $(".dishvalue").prop("checked",true);
	}else{
		$(".dishvalue").prop("checked",false);
	}
 }
 function modify(){
 	 //修改标题操作
	$(".modify-icon").on("click",function(){
		var titleVal = $(this).siblings(".product-title-a").text();
	 	$(this).siblings(".modify-title").show().focus();
	 	$(this).siblings(".modify-title").val(titleVal);
	});
	$(".modify-title").blur(function(){
		var id = $(this).attr("ajax-title-id");
		var title = $(this).val();
		var this_title = $(this);
		$.post("",{op:'ajax_title',ajax_id:id,ajax_title:title},function(data){
			if( data.errno == 200 ){
				this_title.siblings(".product-title-a").text(data.message);
				$(this).hide();
			}else{
				alert(data.message);
				$(this).hide();
			}
		},"json");
		$(this).hide();
	});
	//修改库存操作
	$(".product-stock span").on("click",function(){
		var stockVal = $(this).text();
		$(this).siblings(".modify-stock").show().focus();
		$(this).siblings(".modify-stock").val(stockVal);
	});
	$(".modify-stock").blur(function(){
		var id = $(this).attr("ajax-stock-id");
		var stock = parseInt($(this).val());
		var stock_this = $(this);
		var regEx = /^[0-9]*$/;
		if( !regEx.test(stock) ){
			alert("请输出入正确的库存");
		}else{
			$.post("",{op:'ajax_total',ajax_id:id,ajax_stock:stock},function(data){
				if( data.errno == 200){
					stock_this.siblings("span").text(data.message);
					$(this).hide();
				}else{
					alert(data.message);
					$(this).hide();
				}
			},"json");
		}
		$(this).hide();
	});

	//获取批发价格下拉列表
	$(".wholesale-cogs").on("click",function(){
		$(".wholesale-modal").modal();
		var id = $(this).attr("ajax-vip-price-id");
		$(".ajax-id").val(id);
		var option_html = "";
		var get_html = "";
		var currency_tap = "$";
		$(".set-select").html("");
		$.post("",{op:'ajax_get_vip',ajax_id:id},function(data){
			if( data.errno==200 ){
				//currency属性用来控制价格符号，1代表￥，2代表$
				$.each(data.message.vip_list,function(n,value){
					option_html += "<option currency="+1+" value="+value.id+">"+value.name+"</option>"
				});
				$(".vip-form-area").html("");
				if( data.message.vip_data!="" ){
					$.each(data.message.vip_data,function(data_n,data_value){
						if( data_value.currency ==1 ){
							currency_tap = '￥';
						}else if(data_value.currency ==2){
							currency_tap = '$';
						}
						get_html += "<div class='form-group form-inline vip-form'><label class='col-sm-3 control-label no-padding-left' >会员价格：</label><div class='col-sm-7 set-vip-price'> "+
								"<select  class='form-control set-select' onchange='changeFun(this)' v2='"+data_value.v2+"'></select><div class='input-group'><span class='input-group-addon'>"+currency_tap+"</span>"+
								"<input type='text'  class='form-control vip_price' value='"+data_value.vip_price+"' placeholder='请输入价格'/></div></div><div class='col-sm-2'><a href='javascript:void(0);' "+
								"class='btn btn-danger remove_vip' >移除</a></div></div>";						
					});
					$(".vip-form-area").html(get_html);
				}else{
					get_html += "<div class='form-group form-inline vip-form'><label class='col-sm-3 control-label no-padding-left' >会员价格：</label><div class='col-sm-7 set-vip-price'> "+
								"<select  class='form-control set-select' vid='' onchange='changeFun(this)'></select><div class='input-group'><span class='input-group-addon'>"+currency_tap+"</span>"+
								"<input type='text'  class='form-control vip_price' value='' placeholder='请输入价格'/></div></div><div class='col-sm-2'><a href='javascript:void(0);' "+
								"class='btn btn-danger remove_vip' >移除</a></div></div>";
					$(".vip-form-area").html(get_html);
				}
				$(".set-select").append("<option value='-1'>--请选择--</option>"+option_html);
				$(".set-select option").each(function(idnex,ele){
					if( $(ele).val() == $(ele).parent(".set-select").attr("v2")){
						$(ele).prop("selected","selected");
					}
				});
			}else{
				alert(data.message);
			}
		},"json");
	});
	//保存批发价格
	$(".btn-save").on("click",function(){
		var ajax_vip_data = {};
		var select_val = "";
		var input_val = "";
		var id = $(".ajax-id").val();
		var save_get_html = "";
		$(".vip-form").each(function(index,ele){
			select_val = parseInt($(ele).find(".set-select").val());
			input_val = $(ele).find(".vip_price").val();
			ajax_vip_data[select_val] = input_val;
		})
		$.post("",{op:'ajax_set_vip',ajax_id:id,ajax_vip_data:ajax_vip_data},function(data){
			if( data.errno==200 ){
				$.post("",{op:'ajax_get_vip',ajax_id:id},function(data){
					if( data.errno == 200 ){
						$(".wholesale-div").html();
						$.each(data.message.vip_data,function(data_i,data_val){
							$.each(data.message.vip_list,function(list_i,list_val){
								if( data_val.v2 == list_val.id ){
									save_get_html += "<span class='label label-danger wholesale' style='margin-left:5px;''>"+list_val.name+data_val.vip_price+"</span>";
								}else{
									return;
								}
							});
						});
						$(".wholesale-div[ajax_id="+id+"]").html(save_get_html);
					}else{
						alert(data.message);
					}
				},'json');
			}else{
				alert(data.message);
			}
		},'json')
		$(".wholesale-modal").modal('hide');
	});

	//会员价格
	var vipNum = parseInt($(".vip-number").val());
	var vip_i = 1 ;
	$(".addvip").on("click",function(){
		var addHtml = $(".vip-form:last").clone();
		if ( vip_i < vipNum){
			vip_i++;
			$(".vip-form:last").after(addHtml);
			$(".vip-form:last").find(".no-padding-left").text("");
			$(".vip-form:last").find(".vip_price").val("");
		}
	});
	$("body").on("click",".remove_vip",function(){
		var vipLength = $(".vip-form").length;
		if( vipLength == 1 ){
			$(".set-select").val(-1);
			$(".vip_price").val("");
			return false;
		}else{
			vip_i--;
			$(this).parents(".vip-form").remove();
		}
		$(".vip-form:first").find(".no-padding-left").text(" 会员价格：");
	});

	$("body").on("blur",".vip_price",function(){
		var regEx = /^(([1-9]\d*)|\d)(\.\d{1,2})?$/;
		if( !regEx.test($(this).val()) ){
			var price = parseFloat($(this).val());
			if ( isNaN(price) )
			{
				price = 0;
			}
			$(this).val(price);
		}
	});
 }
modify();

//批发价格修改下拉框值变化，修改对应的货币符号

function changeFun(obj){
	var currency_val = $(".set-select option:selected").attr("currency");
	if( currency_val ==1 ){
		$(obj).siblings('.input-group').find(".input-group-addon").text("￥");
	}else if( currency_val ==2 ){
		$(obj).siblings('.input-group').find(".input-group-addon").text("$");
	}
}
</script>
<?php  include page('footer');?>
