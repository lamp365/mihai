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
	.nav-tabs li a{
		padding-left: 18px;
		padding-right: 18px;
		text-align: center;
	}
</style>
<br/>
<ul class="nav nav-tabs" >
	<li style="" <?php  if( $_GP['op'] == 'index') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('store_shop_manage',  array('op' => 'index'))?>">申核通过</a></li>
	<li style="" <?php  if( $_GP['op'] == 'apply') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('store_shop_manage',  array('op' => 'apply'))?>">正处理中</a></li>
</ul>
<br/>
<table class="table table-striped table-bordered table-hover">
  <tr>
    <th class="text-center" >
        <!--<input type="checkbox" onclick="selectAll()" id="selectAll"/>-->
        店铺名
    </th>
    <th class="text-center" >区域位置</th>
    <th class="text-center" >主营业务</th>
    <th class="text-center" >营业执照</th>
	<th class="text-center" >许可证</th>
    <th class="text-center" width="300">店面</th>
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
                        <p style="text-align:center;padding:0;margin:0;"> 				                          
				        <img src="<?php  echo $item['ssi_yingyezhizhao'];?>" height="120" width="120">	
				        </p>
                    </td>
                	<td style="text-align:center;" class="product-title">
                		<p style="text-align:center;padding:0;margin:0;"> 				                          
				        <img src="<?php  echo $item['ssi_xukezheng'];?>" height="120" width="120">		
				        </p>
                	</td>
                    <td style="text-align:center;" >
                        <p style="text-align:center;padding:0;margin:0;"> 				                          
                            <img src="<?php  echo $item['ssi_dianmian'];?>" height="120" width="120">	
				        </p>
                    </td>
					
					<td style="text-align:center;">
                        <span id='span_<?php  echo $item['sts_id'];?>'  class="label 
                          <?php  if($item['sts_info_status']==0){echo "label-success";}?> 
                          <?php  if($item['sts_info_status']==2){echo "label-info";}?> 
                          <?php  if($item['sts_info_status']==3){echo "label-warning";}?> 
                              " style="cursor:pointer;"><?php  echo $this->info_status_text[$item['sts_info_status']];?></span>
					</td>
					<td style="text-align:center;">

					</td>
				</tr>
				<?php  } } ?>
 	
		</table>
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
</script>
<?php  include page('footer');?>
