<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <?php include page('seller_header');?>
    <style>
        .nav-tabs li a{
            padding-left: 18px;
            padding-right: 18px;
            text-align: center;
        }
    </style>
</head>
<body style="padding:10px;">
<div class="layui-form">
    <h3 class="blue" style="margin-top:5px;margin-bottom:5px;">	<span style="font-size:18px;"><strong>退货地址配置</strong></span></h3>
    <ul class="nav nav-tabs" >
        <li><a href="<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'index'));?>">物流选择</a></li>
        <li><a href="<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'freePrice','status'=>1));?>">邮费配置</a></li>
        <li class="active"><a href="<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'returnAddress','status'=>2));?>">退货地址配置</a></li>
        
    </ul>
<form method="post" action="<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'returnAddress','type'=>'add','status'=>2));?>">
    <br >
    <div class="layui-form">
        <label class="layui-form-label">退货地址选择</label>
        <div class="layui-input-inline">
               <select name="address_province" lay-filter="address_province" id="address_province">
                    <option value="">--请选择--</option>
                    <?php if ($province){foreach ($province as $val){?>
                    <option value="<?php echo $val['region_id'];?>" <?php if ($val['region_id']==$region['province_id']){?>selected<?php }?>><?php echo $val['region_name'];?></option>
                    <?php }}?>
               </select>
               <input type="hidden" id="save_address_province" name="save_address_province" value="<?php echo $info['address_province'];?>">
         </div>
         <div class="layui-input-inline">
               <select name="address_city" lay-filter="address_city" id="address_city">
                    <?php if ($city){foreach ($city as $val){?>
                        <option value="<?php echo $val['region_id'];?>" <?php if ($val['region_id']==$region['city_id']){?>selected<?php }?>><?php echo $val['region_name'];?></option>
                    <?php }}else{?>
                    <option value="">--选择--</option>
                    <?php }?>
               </select>
                <input type="hidden" id="save_address_city" name="save_address_city" value="<?php echo $info['address_city'];?>">
         </div>
         <div class="layui-input-inline">
               <select name="address_area" lay-filter="address_area" id="address_area">
                    <?php if ($area){foreach ($area as $val){?>
                        <option code="<?php echo $val['region_code'];?>" value="<?php echo $val['region_id'];?>" <?php if ($val['region_id']==$region['qu_id']){?>selected<?php }?>><?php echo $val['region_name'];?></option>
                    <?php }}else{?>
                    <option value="">--选择--</option>
                    <?php }?>
               </select> 
               <input type="hidden" id="save_address_area" name="save_address_area" value="<?php echo $info['address_area'];?>">
               <input type="hidden" id="code" name="code" value="<?php echo $info['code'];?>">
         </div>
    </div>
    <br >
    <div class="layui-form">
        <label class="layui-form-label">具体地址</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="address_address" lay-verify="required" placeholder="具体地址" class="layui-input"  value="<?php echo $info["address_address"];?>">
        </div>
    </div>
    <br >
    <div class="layui-form">
        <label class="layui-form-label">收货人姓名</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="address_realname" lay-verify="required" placeholder="收货人姓名" class="layui-input"  value="<?php echo $info["address_realname"];?>">
        </div>
    </div>
    <br >
    <div class="layui-form">
        <label class="layui-form-label">电话号码</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="address_mobile" lay-verify="required" placeholder="电话号码" class="layui-input"  value="<?php echo $info["address_mobile"];?>">
        </div>
    </div>
    <br >
    <div class="layui-form">
        <label class="layui-form-label">邮政编码</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="postcode" placeholder="邮政编码" class="layui-input"  value="<?php echo $info["postcode"];?>">
        </div>
    </div>
    <br >
    <button class="btn btn-sm btn-info" lay-submit lay-filter="*" style="margin-left: 30px;">
        确定提交
    </button>
</form>
<script type="text/javascript">
layui.use(['form', 'layedit', 'laydate'], function(){
	  var form = layui.form();
	  form.on('select(address_province)', function(data){
	      	$.ajax({
	              type: "post",
	              url: "<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'ajaxGetChildMenu')); ?>",
	              data: {id:data.value},
	              dataType: "json",
	              success: function(data){
	              	$("#address_city").html('<option value="">--选择--</option>');//清空二级栏目表单
	              	$("#address_area").html('<option value="">--选择--</option>');//清空三级栏目表单
	                  if(data.errno != 0){
                      	$.each(data['data'], function(key, val){
                      		$("#address_city").append("<option value="+val.region_id+">"+val.region_name+"</option>");
                        	});
	                  }
	                  form.render();
	               }
	          });
	          
	    	});
	      form.on('select(address_city)', function(data){
	      	$.ajax({
	              type: "post",
	              url: "<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'ajaxGetChildMenu')); ?>",
	              data: {id:data.value},
	              dataType: "json",
	              success: function(data){
	            	  $("#address_area").html('<option value="">--选择--</option>');//清空三级栏目表单
	                  if(data.errno != 0){
	                      if(data.errno == 1){
	                          $.each(data['data'], function(key, val){
	                      		$("#address_area").append("<option value="+val.region_id+" code="+val.region_code+">"+val.region_name+"</option>");
	                        	});
	                      }
	                  }
	                  form.render();
	               }
	          });
	          
	    	});
		  	form.on('submit(*)', function(data){
		  		$("#save_address_province").val($("#address_province option:selected").text());
			  	$("#save_address_city").val($("#address_city option:selected").text());
			  	$("#save_address_area").val($("#address_area option:selected").text());
			  	$("#code").val($("#address_area option:selected").attr('code'));
			  	
			  	var address_province = $("#save_address_province").val();
			  	var address_city = $("#save_address_city").val();
			  	var address_area = $("#save_address_area").val();
			  	var code = $("#code").val();

			  	if(address_province == ''){
			  		layer.open({
		                title: '提示',
		                content: '请选择省份'
		            });
		        	return false;
			  	}
			  	if(address_city == ''){
			  		layer.open({
		                title: '提示',
		                content: '请选择城市'
		            });
		        	return false;
			  	}
			  	if(address_area == ''){
			  		layer.open({
		                title: '提示',
		                content: '请选择地区'
		            });
		        	return false;
			  	}
			  	if(code == ''){
			  		layer.open({
		                title: '提示',
		                content: '请选择配送地点'
		            });
		        	return false;
			  	}
		  	}); 
	});

	
</script>
</div>
</body>
</html>

