<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue"><?php if(empty($info)){ echo '添加';}else{ echo '编辑';}?>商铺等级&nbsp;&nbsp;&nbsp;</h3>
<form action="<?php echo web_url('shop_level_manage',array('op'=>'edit')) ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="rank_level" class="col-xs-10 col-sm-2" value="<?php echo $info['rank_level'];?>" />
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" >等级<?php echo $info['rank_level']?>—名称 </label>
		<div class="col-sm-9">
			<input type="text" name="rank_name" class="col-xs-10 col-sm-3" value="<?php echo $info['rank_name'];?>" />
		</div>
	</div>
       
    <div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" >级别</label>
		<div class="col-sm-9">
			<select type="text" name="level_type" class="col-xs-10 col-sm-1" >
                <option value="1" <?php echo $info['level_type']==1?'selected="true"':"";?> >区代理</option>
                <option value="2" <?php echo $info['level_type']==2?'selected="true"':"";?> >市代理</option> 
                <option value="3" <?php echo $info['level_type']==3?'selected="true"':"";?> >省代理</option>
            </select>
		</div>
	</div>
    
    <div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" >是否免费</label>
		<div class="col-sm-9">
			<input type="checkbox" name="is_free" id="is_free" value="1" <?php echo $info['is_free']==1?'checked="true"':"";?> />
		</div>
	</div>
 
    
    <div class="form-group" id="money_div" style=" <?php echo $info['is_free']==1?"display: none":"";?> ">
		<label class="col-sm-2 control-label no-padding-left" >收费金额</label>
		<div class="col-sm-9">
			<input type="text" name="money" class="col-xs-10 col-sm-3" value="<?php echo $info['money'];?>" />
		</div>
	</div>
    <div class="form-group" id="dish_num_div">
		<label class="col-sm-2 control-label no-padding-left" >可上传商品数量</label>
		<div class="col-sm-9">
			<input type="text" name="dish_num" class="col-xs-10 col-sm-3" value="<?php echo $info['dish_num'];?>" />
		</div>
	</div>
    <div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" >有效期（年）</label>
		<div class="col-sm-9">
			<input type="number" name="time_range" class="col-xs-10 col-sm-3" value="<?php echo $info['time_range'];?>" />
		</div>
	</div>
    
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> pc图片：</label>

		<div class="col-sm-9">
			<div class="fileupload fileupload-new" data-provides="fileupload">
				<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
					<?php  if(!empty($info['icon'])) { ?>
						<img style="width: 100%" src="<?php echo $info['icon'];?>" >
					<?php  } ?>
				</div>
				<div>
					<input name="icon" id="icon" type="file" />
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> wap图片：</label>

		<div class="col-sm-9">
			<div class="fileupload fileupload-new" data-provides="fileupload">
				<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
					<?php  if(!empty($info['wap_icon'])) { ?>
						<img style="width: 100%" src="<?php echo $info['wap_icon'];?>" >
					<?php  } ?>
				</div>
				<div>
					<input name="wap_icon" id="wap_icon" type="file" />
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

		<div class="col-sm-9">
			<input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>

		</div>
	</div>
</form>
<script>
$(function(){
    $("#is_free").change(function() { 
        var tmp= $("input[name='is_free']").is(':checked');
        if(tmp == true){
            $("#money_div").hide(); $("#dish_num_div").show();
        }else{
            $("#money_div").show(); $("#dish_num_div").hide();
        }
    });
})
</script>
<?php  include page('footer');?>
