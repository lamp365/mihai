<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">优惠券管理</h3>

	<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/css/datetimepicker.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<div class="alert alert-info" style="margin:10px 0; width:auto;">
	<i class="icon-lightbulb"></i> 对于活动优惠卷请小心设置使用,最大领取数量目前意义是：为0一次性用户只能领取一张，不为0用户一次性能领取的张数
</div>
<form action="" method="post" class="form-horizontal" >
	
	<input type="hidden" name="id" value="<?php  echo $bonus['type_id'];?>" />
	  	  
			   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 类型名称</label>

										<div class="col-sm-9">
												
									<input type="text" name="type_name" class="col-xs-10 col-sm-2" value="<?php  echo $bonus['type_name'];?>" />
										</div>
									</div>
									
									
									   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 优惠券金额</label>

										<div class="col-sm-9">
												
									<input type="number" required name="type_money" class="col-xs-10 col-sm-2" value="<?php  echo $bonus['type_money'];?>" />
									<div class="help-block">此类型的优惠券可以抵销的金额</div>
										</div>
									</div>
									
									
									   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 最小订单金额</label>

										<div class="col-sm-9">
												
												<input type="number" required name="min_goods_amount" class="col-xs-10 col-sm-2" value="<?php  echo $bonus['min_goods_amount'];?>" />
										<p class="help-block">只有商品总金额达到这个数的订单才能使用这种优惠券</p>
										</div>
									</div>
									  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >最大领取数量</label>
										<div class="col-sm-9">
												<input type="text" name="send_max" class="col-xs-10 col-sm-2" value="<?php  echo $bonus['send_max']?$bonus['send_max']:0;?>" />
										<p class="help-block">为0则不限制</p>
										</div>
									</div>
										   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 如何发放此类型优惠券</label>

										<div class="col-sm-9">
											<select name="send_type" id="" onchange="showunit(this)">
												<option value="-1">选择优惠卷类型</option>
												<?php foreach($bonus_enum_arr as $key_type => $bon) {
													$sel = '';
													if(empty($bonus['send_type']) && $bonus['send_type'] == null){
														$sel = '';
													}else if($bonus['send_type'] == $key_type){
														$sel = "selected";
													}
														echo "<option value='{$key_type}' {$sel}>{$bon}</option>";
												}?>
											</select>
      									</div>
									</div>
									 
									 

									    <div class="form-group" id="the_min_amount" style="<?php if($bonus['send_type'] != 2){ echo 'display:none';} ?>">
										<label class="col-sm-2 control-label no-padding-left" > 订单下限</label>

										<div class="col-sm-9">
												
   										  <input type="text" name="min_amount" class="col-xs-10 col-sm-2" value="<?php  echo $bonus['min_amount'];?>" />
									<p class="help-block">只要订单金额达到该数值，就会发放优惠券给用户</p>
     							</div>
									</div>
									
								
									
									    <div class="form-group" id="sel1">
										<label class="col-sm-2 control-label no-padding-left" > 发放起始日期</label>

										<div class="col-sm-9">
												
   										 	  <input type="text" readonly="readonly" name="send_start_date" id="send_start_date" class="col-xs-10 col-sm-2" value="<?php  echo date('Y-m-d H:i',empty($bonus['send_start_date'])?time():$bonus['send_start_date']);?>" />
										<div class="help-block">只有当前时间介于起始日期和截止日期之间时，此类型的优惠券才可以发放</div>
     							</div>
									</div>
									
									
									 <div class="form-group" id="sel2">
										<label class="col-sm-2 control-label no-padding-left" > 发放结束日期</label>

										<div class="col-sm-9">
												
   										 	  <input type="text" readonly="readonly" name="send_end_date" id="send_end_date" class="col-xs-10 col-sm-2" value="<?php  echo date('Y-m-d H:i',empty($bonus['send_end_date'])?time():$bonus['send_end_date']);?>" />
								
     							</div>
									</div>
										
										 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 使用起始日期</label>

										<div class="col-sm-9">
												
   										 	  <input type="text" readonly="readonly" name="use_start_date" id="use_start_date" class="col-xs-10 col-sm-2" value="<?php  echo date('Y-m-d H:i',empty($bonus['use_start_date'])?time():$bonus['use_start_date']);?>" />
									<div class="help-block">只有当前时间介于起始日期和截止日期之间时，此类型的优惠券才可以使用</div>
     							</div>
									</div>
									
										 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 使用结束日期</label>

										<div class="col-sm-9">
												
   										 	  <input type="text" readonly="readonly" name="use_end_date" id="use_end_date" class="col-xs-10 col-sm-2" value="<?php  echo date('Y-m-d H:i',empty($bonus['use_end_date'])?time():$bonus['use_end_date']);?>" />
								
     							</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > APP显示</label>

										<div class="col-sm-9">
											<?php if(empty($_GP['id'])){ ?>
												<input type="radio" name="app_show" value="1" > 是
												<input type="radio" name="app_show" value="0" checked> 否
											<?php }else{  ?>
												<input type="radio" name="app_show" value="1" <?php if($bonus['app_show'] == 1){ echo "checked"; } ?>> 是
												<input type="radio" name="app_show" value="0" <?php if($bonus['app_show'] == 0){ echo "checked"; } ?>> 否
											<?php } ?>
										</div>
									</div>

									
									
											 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > </label>

										<div class="col-sm-9">
                       
					<input name="submit" type="submit" value="提交" class="btn btn-primary span3">
												</div>
									</div>
									<br/><br/>
</form>   
<script>
		laydate({
	        elem: '#send_start_date',
	        istime: true, 
	        event: 'click',
	        format: 'YYYY-MM-DD hh:mm:ss',
	        istoday: true, //是否显示今天
	        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	    });

	    laydate({
	        elem: '#send_end_date',
	        istime: true, 
	        event: 'click',
	        format: 'YYYY-MM-DD hh:mm:ss',
	        istoday: true, //是否显示今天
	        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	    });
	    laydate({
	        elem: '#use_start_date',
	        istime: true, 
	        event: 'click',
	        format: 'YYYY-MM-DD hh:mm:ss',
	        istoday: true, //是否显示今天
	        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	    });
	    laydate({
	        elem: '#use_end_date',
	        istime: true, 
	        event: 'click',
	        format: 'YYYY-MM-DD hh:mm:ss',
	        istoday: true, //是否显示今天
	        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	    });
	    laydate.skin("molv");

	function gObj(obj)
{
  var theObj;
  if (document.getElementById)
  {
    if (typeof obj=="string") {
      return document.getElementById(obj);
    } else {
      return obj.style;
    }
  }
  return null;
}

function showunit(obj)
{
	var get_value = $(obj).val();
	if(get_value == 2){
		$("#the_min_amount").show();
	}else{
		$("#the_min_amount").hide();
	}
}

	</script>
<?php  include page('footer');?>
