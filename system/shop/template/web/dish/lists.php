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
</style>
<h3 class="header smaller lighter blue">觅海全球购</h3> 
<form action=""  class="form-horizontal" method="post">
	<table class="table table-striped table-bordered table-hover">
			<tbody>
				<tr class="shop-list-tr">
				<td>
						<li >
							<select name="p1" id="getShopCategory_p1" class="get_category" onchange="getShop_sonCategroy(this,1)" style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" >
								<option value="">请选择分类</option>
								<?php foreach($all_category as $item) {
									if($item['id'] == $_GP['p1']){
										$sel = "selected";
									}else{
										$sel = '';
									}
									echo "<option value='{$item['id']}' {$sel}>{$item['name']}</option>";
								}
								?>
							</select>
							<select name="p2" id="getShopCategory_p2" class="get_category" onchange=""  style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" >
								<option value="">请选择分类</option>
								<?php foreach($first_son as $item2) {
									if($item2['id'] == $_GP['p2']){
										$sel = "selected";
									}else{
										$sel = '';
									}
									echo "<option value='{$item2['id']}' {$sel}>{$item2['name']}</option>";
								}
								?>
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
							<option value="1" <?php if($_GP['status']==1){?>selected="selected"<?php  } ?>> 已上架</option>
							<option value="0" <?php if($_GP['status']==0){?>selected="selected"<?php  } ?> >已下架</option>
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
							&nbsp;&nbsp;&nbsp;&nbsp;
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
	<th class="text-center" >货号</th>
    <th class="text-center" width="300">产品名称</th>
	<th class="text-center" ><a href="<?php echo $sorturl."&orderprice=".$oprice; ?>">促销价</a></th>
	<th class="text-center"><a href="<?php echo $sorturl."&ordertprice=".$otprice; ?>">活动价</a></th>
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
				 	<td><p style="text-align:center;padding:0;margin:0;"> 				                          
				        <img src="<?php  echo download_pic($item['thumb'],40,40);?>" height="40" width="40">
				        </p>
				    </td>
					<td style="text-align:center;"><?php  echo $item['goodssn']; ?> </td>
                	<td style="text-align:center;" class="product-title">
                		<input type="text" name="" class="modify-title form-control modify-input" ajax-title-id="<?php  echo $item['id'];?>">
                		<a target="_blank" class="product-title-a" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $item['id']))?>"><?php  echo $item['title'];?></a>
                		<i class="modify-icon icon-pencil"></i>
                	</td>
					<td style="text-align:center;" ><?php  echo $item['marketprice'];?></td>
					<td style="text-align:center;" ><?php  echo $item['timeprice'];?></td>

					<td style="text-align:center;" class="product-stock">
						<input type="text" name="" class="modify-stock form-control modify-input" ajax-stock-id="<?php  echo $item['id'];?>"><span><?php  echo $item['total'];?></span>
					</td>											

					<td style="text-align:center;">
						<?php  if($item['status']) { ?>
						<span data='<?php  echo $item['status'];?>' onclick="setProperty1(this,<?php  echo $item['id'];?>,'status')" class="label label-success" style="cursor:pointer;">已上架</span>
						<?php  } else { ?>
						<span data='<?php  echo $item['status'];?>' onclick="setProperty1(this,<?php  echo $item['id'];?>,'status')" class="label label-danger" style="cursor:pointer;">已下架</span>
						<?php  } ?>

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
						<a  class="btn btn-xs btn-info" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'post_dish'))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
					<?php } ?>
					<?php if(isHasPowerToShow('shop','dish','delete')){ ?>
						<a  class="btn btn-xs btn-info" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
					<?php } ?>
						&nbsp;&nbsp;					
					</td>
				</tr>
				<?php  } } ?>
 	
		</table>
		<input type="hidden" name="" class="ajax-id" value="">
		<?php  echo $pager;?>
<script language="javascript">

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
		var url = "<?php echo web_url('dish',array('op'=>'ajax_title')); ?>";
		$.post(url,{ajax_id:id,ajax_title:title},function(data){
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
			var url = "<?php echo web_url('dish',array('op'=>'ajax_total')); ?>";
			$.post(url,{ajax_id:id,ajax_stock:stock},function(data){
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
 }
modify();

</script>
<?php  include page('footer');?>
