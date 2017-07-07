<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/bootstrap3/js/bootstrap-dropdown.js"></script>
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
	#dLabel{
		cursor: pointer;
		padding-right: 15px;
	}
	.wholesale-price{
		color: #d22046;
    	font-weight: bold;
	}
	.wholesale-div li{
		padding: 5px 0 5px 10px;
	}
	.hide{
		display: none;
	}
</style>
<h3 class="header smaller lighter blue">宝贝审核</h3>
<!--增加的操作-->
<form action=""  class="form-horizontal" method="post">
	
	<table class="table table-striped table-bordered table-hover">
		<tbody>
			<tr class="shop-list-tr">
				<td>
					<li>
						<select class="industry1" onchange="industry1(this)">
							<option value="1">行业一1</option>
							<option value="2">行业一2</option>
							<option value="3">行业一3</option>
							<option value="4">行业一4</option>
						</select>
						<select class="industry2 hide" onchange="industry2()">
							<option value="1">行业二1</option>
							<option value="2">行业二2</option>
							<option value="3">行业二3</option>
							<option value="4">行业二4</option>
						</select>
						<select class="category1 hide" onchange="category1()">
							<option value="1">分类一1</option>
							<option value="2">分类一2</option>
							<option value="3">分类一3</option>
							<option value="4">分类一4</option>
						</select>
						<select class="category2 hide" onchange="category2()">
							<option value="1">分类二1</option>
							<option value="2">分类二2</option>
							<option value="3">分类二3</option>
							<option value="4">分类二4</option>
						</select>
					</li>
					<li>
						<button class="btn btn-primary btn-sm" ><i class="icon-search icon-large"></i> 搜索</button>
					</li>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="table table-striped table-bordered table-hover" style="display:none;">
			<tbody>
				<tr class="shop-list-tr">
				<td>
				        <li >
						<select  name="ac_list" onchange="fetchChildarea(this.options[this.selectedIndex].value)">
							<option value="0">请选择活动</option>
							<?php  if(is_array($act_list)) { foreach($act_list as $row) { ?>
							<option value="<?php  echo $row['ac_id'];?>" <?php  if($row['ac_id'] == $_GP['ac_list']) { ?> selected="selected"<?php  } ?>><?php  echo $row['ac_title'];?></option>
							<?php  } } ?>
						</select>
						</li>
						<li >
						   <select name="type" >
							   <option value="-1" selected>所属行业</option>
							   <option value="0" <?php if($_GP['type']===0){?>selected="selected"<?php  } ?>>一般商品</option>
                               <option value="1" <?php if($_GP['type']==1){?>selected="selected"<?php  } ?> >团购商品</option>
                               <option value="2" <?php if($_GP['type']==2){?>selected="selected"<?php  } ?>>秒杀商品</option>
                               <option value="3" <?php if($_GP['type']==3){?>selected="selected"<?php  } ?>>今日特价商品</option>
						       <option value="4" <?php if($_GP['type']==4){?>selected="selected"<?php  } ?>>限时促销</option>
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
						<li>
						    <span class="left-span">产品名称</span>
							<input class="li-height" name="keyword" id="" type="text" value="<?php  echo $_GP['keyword'];?>">
						</li>
						<li>
						<button class="btn btn-primary btn-sm" ><i class="icon-search icon-large"></i> 搜索</button>
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
	  <th class="text-center">店铺名称</th>
	  <th class="text-center">产品图片</th>
    <th class="text-center" width="300">产品名称</th>
	<th class="text-center" >条形码</th>
	<th class="text-center">销售价格</th>
    <th class="text-center">活动价格</th>
	<th class="text-center">活动库存</th>
	<th class="text-center">卖出件数</th>
	<th class="text-center">时间区间</th>
  </tr>

		<?php if(is_array($au_list)) { foreach($au_list as $item) { ?>
				<tr>
				 	<td style="text-align:center;" class="dish-id">
				 		<input type="checkbox" class="dishvalue" name="disvalue[]" value="<?php  echo $item['ac_dish_id'];?>"/>
				 		<?php  echo $item['id'].'-'.$item['ac_dish_id'];?>
				 	</td>
					<td style="text-align:center;">
						<?php $the_store = member_store_getById($item['ac_shop'],'sts_name'); echo $the_store['sts_name']; ?>
					</td>
					<td style="text-align:center;"><img src="<?php echo $item['thumb']; ?>" height="38" width="38" /></td>
                	<td style="text-align:left;" class="product-title">
                		<?php  echo $item['title'];?>
                	</td>
					<td style="text-align:center;"><?php  echo $item['goodssn']; ?> </td>
					<td style="text-align:center;" ><?php  echo $item['timeprice'] / 100 ;?></td>
					<td style="text-align:center;" class="wholesale-td"><?php  echo $item['ac_dish_price'] / 100 ;?></td>
					<td style="text-align:center;" class="product-stock">
						<span><?php  echo $item['ac_dish_total'];?></span>
					</td>
					<td style="text-align:center;">
						<span><?php  echo $item['ac_dish_sell_total'];?></span>
					</td>
					<td style="text-align:center;">
						<?php echo getAreaTitleByAreaid($item['ac_action_id']); ?>
					</td>
				</tr>
		<?php  } } ?>
 	
</table>

<?php if ( !empty( $list_op )  ){ ?>
<div class="form-group">
<select name="" id="option" onchange="fetchOption()">
         <option value="-1">批量操作</option>
		 <?php foreach ( $list_op as $key=>$list_op_value ){ ?>
	     <option value="<?php echo $list_op_value; ?>"><?php echo $key; ?></option>
		<?php } ?>
</select>
<?php if ( is_array( $au_reason ) && !empty( $au_reason) ){  ?>
<select name="" id="reason" style="display:none;" onchange="auOption()">
      <option value="-1">请选择原因</option>
      <?php foreach ($au_reason as $reason_value ){ ?>
            <option value="<?php echo $reason_value; ?>"><?php echo $reason_value; ?></option>
	  <?php } ?>
</select>
<?php } ?>
</div>
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
		$('#reason').hide();
		return;
	}
	if ( $("#option").val() == 2)
	{
         $('#reason').show();
	}else{
		 $('#reason').hide();
		 auOption();  
	}
 }
 // 审核操作
 function auOption(){
	if ( $("#option").val() == 2 && $("#reason").val() == -1)
	{
		return;
	}
	if ($("#option").val() == 2)
	{
		var reason = $("#reason").val();
	}else{
        var reason = '';
	}
    //选中了哪些宝贝,将他们的value存进数组list
 	var list=[];
 	$("input[name = 'disvalue[]']:checked").each(function(){ 		
 		list.push(this.value);
 	})
 	if(confirm("确定"+$(":selected","#option")[0].innerHTML+"选中的宝贝？")){	
		$.post('', {'id' : list,'value':$("#option").val(),'reason':reason}, function(s) {
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

 function industry1(obj){
 	var check_val = $(obj).val();
 	var url = "";
 	selectPost(url,check_val,".industry2");
 }
  function industry2(obj){
 	var check_val = $(obj).val();
 	var url = "";
 	selectPost(url,check_val,".category1");
 }
  function category1(obj){
 	var check_val = $(obj).val();
 	var url = "";
 	selectPost(url,check_val,".category2");
 }

 function selectPost(url,checkVal,element){
 	var option = "";
 	var html = "";
 	$.post(url,{value:checkVal},function(data){
 		if( data.errno == 1 ){
 			//服务端接口出来后补上
 			$(element).removeClass("hide");
 			option = data.option;
 			$.each(option,function(){
 				html+="<option value='111'></option>"
 			})
 			$(element).html(html)
 		}else{

 		}
 	},'json')
 }
</script>
<?php  include page('footer');?>
