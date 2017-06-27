<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">添加活动</h3>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/css/datetimepicker.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<form action="" method="post" class="form-horizontal" >	  	
	<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 活动名称</label>
			<div class="col-sm-9">
			<input type="text" name="ac_title" class="col-xs-10 col-sm-2" value="<?php  echo $pro['ac_title'];?>" />
			</div>
	</div>
	<div class="form-group" >
			<label class="col-sm-2 control-label no-padding-left" > 起始日期</label>
			<div class="col-sm-9">
   					<input type="text" readonly="readonly" name="ac_time_str" id="start_time"  class="col-xs-10 col-sm-2" value="<?php  echo $pro['ac_time_str']; ?>" />
     		</div>
	</div>
	<div class="form-group">
		    <label class="col-sm-2 control-label no-padding-left" > 终止日期</label>
			<div class="col-sm-9">
   				<input type="text" readonly="readonly" name="ac_time_end"  id="end_time"  class="col-xs-10 col-sm-2" value="<?php  echo $pro['ac_time_end']; ?>" />
     		</div>
	</div>
	<div class="form-group">
		    <label class="col-sm-2 control-label no-padding-left" >区间组</label>
			<div class="col-sm-9">
   				 <select name="ac_list_id" >
                      <option value="0">不选择</option>
					  <?php if ( is_array( $area_list ) ) { foreach( $area_list as $area_list_value ) { ?>
                            <option value="<?php echo $area_list_value; ?>" <?php if (isset($pro['ac_area']) && ($pro['ac_area'] == $area_list_value) ){ echo 'selected'; }?>  ><?php echo $area_list_value."小时制"; ?></option>
					  <?php }} ?>
				 </select> 
     		</div>
	</div>
	<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 状态</label>
			<div class="col-sm-9">
					<input id="s_1" name="ac_status" value="1" <?php if ($pro['ac_status'] == 1) echo 'checked="true"'; ?> type="radio"><label for="s_1">开启</label>   
                    <input id="s_2" name="ac_status" value="2" <?php if ($pro['ac_status'] != 1) echo 'checked="true"'; ?> type="radio"><label for="s_2">关闭</label> 
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
