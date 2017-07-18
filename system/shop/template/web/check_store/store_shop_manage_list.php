<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
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
	.nav-tabs li a{
		padding-left: 18px;
		padding-right: 18px;
		text-align: center;
	}
	.big-img-show{
		display: none;
		position: absolute;
		top: -200px;
		left: -600px;
		width: 600px;
		height: 600px;
		cursor: pointer;
		overflow: hidden;
		z-index: 9;
	}
	.big-img-show img{
		max-width: 100%;
		max-height: 100%;
	}
	.look-erweima{
		cursor: pointer;
	}
	.erweima{
		display: none;
		position: absolute;
	    top: 0;
	    left: -160px;
	    z-index: 5;
	    border: 1px solid #f1f1f1;
	    border-radius: 4px;
	}
	.erweima img{
		width: 160px;
	    height: 160px;
	    border-radius: 4px;
	}
</style>
</br>
<form method="post" action="" name="" class="">
<table class="table table-striped table-bordered table-hover">
			<tbody>
				<tr class="shop-list-tr">
				<td>
				        <li >
						<select  name="cate_1" onchange="fetchChildarea(this.options[this.selectedIndex].value)">
							<option value="0">请选择省份</option>
							<?php  if(is_array($area)) { foreach($area as $row) { ?>
							<?php  if($row['parentid'] == 0) { ?>
							<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GP['cate_1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
							<?php  } ?>
							<?php  } } ?>
						</select>
						</li>
						<li >
						<select  name="p1" onchange="fetchChildCategory(this.options[this.selectedIndex].value)">
							<option value="0">请选择城市</option>
							<?php  if(is_array($category)) { foreach($category as $row) { ?>
							<?php  if($row['parentid'] == 0) { ?>
							<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GP['p1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
							<?php  } ?>
							<?php  } } ?>
						</select>
						<select onchange="fetchChildCategory2(this.options[this.selectedIndex].value)"  id="p2" name="p2">
							<option value="0">请选择地区</option>
							<?php  if(!empty($_GP['p1']) && !empty($childrens[$_GP['p1']])) { ?>
							<?php  if(is_array($childrens[$_GP['p1']])) { foreach($childrens[$_GP['p1']] as $row) { ?>
							<option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $_GP['p2']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
							<?php  } } ?>
							<?php  } ?>
						</select>
						</li>
						<li >
						   <select name="type" >
							   <option value="-1" selected>请选择业务员</option>
							   <option value="0" <?php if($_GP['type']===0){?>selected="selected"<?php  } ?>>业务员1</option>
                               <option value="1" <?php if($_GP['type']==1){?>selected="selected"<?php  } ?> >业务员2</option>
                               <option value="2" <?php if($_GP['type']==2){?>selected="selected"<?php  } ?>>业务员3</option>
                               <option value="3" <?php if($_GP['type']==3){?>selected="selected"<?php  } ?>>业务员4</option>
						       <option value="4" <?php if($_GP['type']==4){?>selected="selected"<?php  } ?>>业务员5</option>
						   </select>
						</li>
						<li>
							<span class="left-span">起始日期</span>
							<input type="text" class="li-height" placeholder="起始日期" id="datepicker_timestart" name="timestart" value="<?php echo $_GP['timestart']; ?>" readonly="readonly" />
						</li>
						<li style="width: 20px;text-align: center;padding-top: 4px;"> - </li>
						<li>
							<span class="left-span">终止日期</span>
							<input type="text" class="li-height" placeholder="终止日期" id="datepicker_timeend" name="timeend" value="<?php echo $_GP['timeend']; ?>" readonly="readonly" />
							<script type="text/javascript">
								laydate({
							        elem: '#datepicker_timestart',
							        istime: true, 
							        event: 'click',
							        format: 'YYYY-MM-DD hh:mm:ss',
							        istoday: true, //是否显示今天
							        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
							    });
								laydate({
							        elem: '#datepicker_timeend',
							        istime: true, 
							        event: 'click',
							        format: 'YYYY-MM-DD hh:mm:ss',
							        istoday: true, //是否显示今天
							        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
							    });
							    laydate.skin("molv");
							    $(".add-more-btn").click(function(){
									$(".hide-tr").toggle();
								});
							</script>
						</li>
						<li style="margin-left:10px;">
						<button class="btn btn-primary btn-sm" ><i class="icon-search icon-large"></i> 搜索</button>
						<span style="margin-left:10px;">商家数量：1086</span>
						</li>
					</td>
				</tr>
			</tbody>
		</table>
<table class="table table-striped table-bordered table-hover">
  <tr>
    <th class="text-center" >
        <!--<input type="checkbox" onclick="selectAll()" id="selectAll"/>-->
        店铺名
    </th>
    <th class="text-center" >区域位置</th>
    <th class="text-center" >主营业务</th>
    <th class="text-center" >销售金额</th>
    <th class="text-center" >营业执照</th>
	<th class="text-center" >许可证</th>
    <th class="text-center">店面</th>
    <th class="text-center" >状态</th>
    <th class="text-center" >操作</th>
  </tr>
		<?php if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
				 	<td style="text-align:center;" class="dish-id">
				 		<!--<input type="checkbox" class="dishvalue" name="disvalue[]" value="<?php  echo $item['id'];?>"/>-->
				 		<?php  echo $item['sts_name'];?>				 			
				 	</td>
					<td>
						所在地区：<?php echo region_func_getNameByCode($item['sts_locate_add_1'])."-".region_func_getNameByCode($item['sts_locate_add_2'])."-".region_func_getNameByCode($item['sts_locate_add_3']).$item['sts_address'] ?>
						<br/>
						配送区域：<?php echo region_func_getNameByCode($item['sts_province'])."-".region_func_getNameByCode($item['sts_city'])."-".region_func_getNameByCode($item['sts_region']) ?>
						<br/>
						联系电话：<?php echo $item['sts_mobile']; ?>
					</td>
					<td style="text-align:center;">
						<?php echo $storeType[$item['sts_shop_type']];  ?><br/>
						<?php echo getIndustryByid($item['sts_category_p1_id']).'——'.getIndustryByid($item['sts_category_p2_id']); ?>
					</td>
					<td style="text-align:center;">
                    	998
                    </td>
					<td style="text-align:center;position:relative">    
                        <p class="small-img" style="text-align:center;padding:0;margin:0;"> 				                          
				        <img src="<?php  echo $item['ssi_yingyezhizhao'];?>" height="120" width="120">	
				        </p>
				        <div class="big-img-show">
							<img src="">
						</div>
                    </td>
                    
                	<td style="text-align:center;position:relative" class="product-title">
                		<p class="small-img" style="text-align:center;padding:0;margin:0;"> 				                          
				        <img src="<?php  echo $item['ssi_xukezheng'];?>" height="120" width="120">		
				        </p>
				        <div class="big-img-show">
							<img src="">
						</div>
                	</td>
                    <td style="text-align:center;position:relative" >
                        <p class="small-img" style="text-align:center;padding:0;margin:0;"> 				                          
                            <img src="<?php  echo $item['ssi_dianmian'];?>" height="120" width="120">	
				        </p>
				        <div class="big-img-show">
							<img src="">
						</div>
                    </td>
					
					<td style="text-align:center;">
                        <span id='span_<?php  echo $item['sts_id'];?>'  class="label 
                          <?php  if($item['sts_info_status']==0){echo "label-success";}?> 
                          <?php  if($item['sts_info_status']==2){echo "label-info";}?> 
                          <?php  if($item['sts_info_status']==3){echo "label-warning";}?> 
                              " style="cursor:pointer;"><?php  echo $this->info_status_text[$item['sts_info_status']];?></span>
					</td>
					<td style="text-align:center;position:relative">
						<div class="label label-success look-erweima" openid="<?php  echo $item['sts_openid'];?>">查看二维码</div>
						<div class="erweima">
							<img src="">
						</div>
					</td>
				</tr>
				<?php  } } ?>
 	
		</table>
</form>
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
 $(function(){
 	$(".small-img").on("click",function(){
 		var bigImg = $(this).find("img").attr("src");
 		$(".big-img-show").hide();
 		$(this).siblings(".big-img-show").fadeIn();
		$(this).siblings(".big-img-show").find("img").attr("src",bigImg);
 	})
 	$(".big-img-show").on("click",function(){
		$(this).fadeOut();
	});
	//查看二维码
	$(".look-erweima").on("click",function(){
		var url = "<?php echo web_url('store_shop_manage',array('op'=>'getQrcode'));?>";
		var openid = $(this).attr("openid");
		var _this = $(this);
		$.post(url,{openid:openid},function(res){
			if(res.errno==1){
				var imgUrl = res.data;
				$(".erweima").hide();
				_this.siblings(".erweima").find("img").attr("src",imgUrl);
				_this.siblings(".erweima").show();
			}
		},"json");
	});
	$(".erweima").click(function(){
		$(this).hide();
	})
 })
</script>
<?php  include page('footer');?>
