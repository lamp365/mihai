<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">促销免运费管理</h3>

	<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/css/datetimepicker.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<form action="" method="post" class="form-horizontal" >
	
	<input type="hidden" name="id" value="<?php  echo $pro['id'];?>" />
	  	  
			   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 促销活动类型</label>

										<div class="col-sm-3">
												

											<input  name="radioPromotionType" type="radio"  value="1"  checked="checked" >满额免运费
				
										</div>
									</div>

									
									   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > <label for=""><span id='money'>*满足金额(元) </span>	</label></label>

										<div class="col-sm-3">
											<input type="hidden" name="type" value="0">
											<input type="text" name="promotionmoney" class="form-control" value="<?php echo $pro['condition']?>" />
										</div>
									</div>
									
									
									   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 促销活动名称</label>

										<div class="col-sm-3">
												
												<input type="text" name="promotionname" class="form-control" value="<?php  echo $pro['pname'];?>" />
										</div>
									</div>
									
										
								
									
									    <div class="form-group" >
										<label class="col-sm-2 control-label no-padding-left" > 起始日期</label>

										<div class="col-sm-3">
												
   										 	  <input type="text" readonly="readonly" name="start_time" id="start_time"  class="form-control" value="<?php  echo date('Y-m-d H:i',empty($pro['starttime'])?time():$pro['starttime']);?>" />
							
     							</div>
									</div>
									
									
									 <div class="form-group" id="sel2">
										<label class="col-sm-2 control-label no-padding-left" > 终止日期</label>

										<div class="col-sm-3">
												
   										 	  <input type="text" readonly="readonly" name="end_time"  id="end_time"  class="form-control" value="<?php  echo date('Y-m-d H:i',empty($pro['endtime'])?time():$pro['endtime']);?>" />
							
     							</div>
									</div>
										
												 <div class="form-group" id="sel2">
										<label class="col-sm-2 control-label no-padding-left" > 描述</label>

										<div class="col-sm-3">
											<textarea name="description" class="form-control span6" cols="70"><?php echo $pro['description'];?></textarea>
     							</div>
									</div>
									
									
											 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > </label>

										<div class="col-sm-9">
                       
											<input name="submit" type="submit" value="提交" class="btn btn-primary span3">
											<button onclick="window.history.back()"  class="btn btn-primary span3">返回上页</button>
												</div>
									</div>
</form>   
<script>

		laydate({
	        elem: '#start_time',
	        istime: true, 
	        event: 'click',
	        format: 'YYYY-MM-DD hh:mm:ss',
	        istoday: true, //是否显示今天
	        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	    });
	    laydate({
	        elem: '#end_time',
	        istime: true, 
	        event: 'click',
	        format: 'YYYY-MM-DD hh:mm:ss',
	        istoday: true, //是否显示今天
	        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	    });
	    laydate.skin("molv");

		 function to_change(){
        var obj  = document.getElementsByName('radioPromotionType');
        for(var i=0;i<obj.length;i++){
            if(obj[i].checked==true){
                if(obj[i].value==0){
						document.getElementById('num').style.display="block";
				document.getElementById('money').style.display="none";
		
                }else if(obj[i].value==1){
		
                   		document.getElementById('num').style.display="none";
          document.getElementById('money').style.display="block"; 
                }
            }
        }
    }
	</script>
<?php  include page('footer');?>
