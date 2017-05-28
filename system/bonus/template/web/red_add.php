<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">红包管理</h3>

	<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/css/datetimepicker.css" />
		<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/datetimepicker.js"></script>
<form action="" method="post" class="form-horizontal" >
	
	<input type="hidden" name="id" value="<?php  echo $red['id'];?>" />
	  	  
			   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >红包金额池</label>

										<div class="col-sm-9">
												
									<input type="text" name="amount" class="col-xs-10 col-sm-2" value="<?php  echo $red['amount'];?>" />
										</div>
									</div>
									<div class="form-group">
								<label class="col-sm-2 control-label no-padding-left" >红包类型</label>
										<div class="col-sm-9">	
										     <select name="type">
                                                   <option value="1" <?php if($red['type']=='1'){ echo 'selected'; } ?>>现金</option>
											 </select>
										</div>
									</div>	
									
									   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >单个红包领取最大值</label>

										<div class="col-sm-9">
												
									<input type="text" name="goldmax" class="col-xs-10 col-sm-2" value="<?php  echo $red['goldmax'];?>" />
										</div>
									</div>
									
									
									   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >中奖率</label>

										<div class="col-sm-9">
												
												<input type="text" name="winrate" class="col-xs-10 col-sm-2" value="<?php  echo $red['winrate'];?>" />
										<p class="help-block">例:0.0000</p>
										</div>
									</div>
									  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >每日最大摇奖数</label>
										<div class="col-sm-9">
												<input type="text" name="sendmax" class="col-xs-10 col-sm-2" value="<?php  echo $red['sendmax'];?>" />
										</div>
									</div>
									    <div class="form-group" id="sel1">
										<label class="col-sm-2 control-label no-padding-left" > 开始时间</label>

										<div class="col-sm-9">
												
   										 	  <input type="text" readonly="readonly" name="begintime" id="begintime" class="col-xs-10 col-sm-2" value="<?php  echo date('Y-m-d H:i',empty($red['begintime'])?time():$red['begintime']);?>" />
     							</div>
									</div>
									
									
									 <div class="form-group" id="sel2">
										<label class="col-sm-2 control-label no-padding-left" > 结束时间</label>

										<div class="col-sm-9">
												
   										 	  <input type="text" readonly="readonly" name="endtime" id="endtime" class="col-xs-10 col-sm-2" value="<?php  echo date('Y-m-d H:i',empty($red['endtime'])?time():$red['endtime']);?>" />
								
     							</div>
									</div>
											 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > </label>

										<div class="col-sm-9">
                       
					<input name="submit" type="submit" value="提交" class="btn btn-primary span3">
												</div>
									</div>
</form>   
<script>
		$("#begintime").datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			minView: "0",
			//pickerPosition: "top-right",
			autoclose: true
		});
		$("#endtime").datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			minView: "0",
			autoclose: true
		});
		
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

function showunit(get_value)
{
  gObj("1").style.display =  (get_value == 2) ? "" : "none";

  return;
}
showunit(<?php  echo intval($red['send_type'])?>);
	</script>
<?php  include page('footer');?>
