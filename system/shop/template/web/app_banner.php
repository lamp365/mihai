<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>

<h3 class="header smaller lighter blue">app端banner设置</h3>

<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="op" value="post">
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 排序：</label>

		<div class="col-sm-9">
			<input type="text" name="displayorder" class="span6" value="<?php  echo $appBanner['displayorder'];?>" />(越大越前)
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 图片：(1240x585)</label>

		<div class="col-sm-9">
			<div class="fileupload fileupload-new" data-provides="fileupload">
				<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
			         <?php  if(!empty($appBanner['thumb'])) { ?>
			             <img style="width: 100%" src="<?php echo $appBanner['thumb'];?>" >
			          <?php  } ?>
			    </div>
				<div>
					<input name="thumb" id="thumb" type="file" /> 
				</div>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 显示页面：</label>

		<div class="col-sm-9">
			<select name="position" id="J_position">
				<option value=''></option>
				<option value='1' <?php  if($appBanner['position'] == 1) { ?>
					selected="selected" <?php  } ?>>首页顶部</option>
				<option value='2' <?php  if($appBanner['position'] == 2) { ?>
					selected="selected" <?php  } ?>>秒杀</option>
				<option value='3' <?php  if($appBanner['position'] == 3) { ?>
					selected="selected" <?php  } ?>>每日特价</option>
				<option value='4' <?php  if($appBanner['position'] == 4) { ?>
					selected="selected" <?php  } ?>>觅海头条</option>
				<option value='5' <?php  if($appBanner['position'] == 5) { ?>
					selected="selected" <?php  } ?>>晒物笔记</option>
			</select>
		</div>
	</div>
	
	<div class="form-group" id="J_link_type_div">
		<label class="col-sm-2 control-label no-padding-left"> 跳转类型：</label>

		<div class="col-sm-9">
			<select name="link_type">
				<option value='1' <?php  if($appBanner['link_type'] == 1) { ?>selected="selected" <?php  } ?>>商品详情页</option>
				<option value='2' <?php  if($appBanner['link_type'] == 2) { ?>selected="selected" <?php  } ?>>类目列表页</option>
				<option value='3' <?php  if($appBanner['link_type'] == 3) { ?>selected="selected" <?php  } ?>>搜索结果页</option>
				<option value='4' <?php  if($appBanner['link_type'] == 4) { ?>selected="selected" <?php  } ?>>自定义H5页</option>
			</select>
		</div>
	</div>

	<div class="form-group" id="J_link_div">
		<label class="col-sm-2 control-label no-padding-left"> 链接：</label>

		<div class="col-sm-9">
			<input type="text" name="link" id='link' class="span6" value="<?php  echo $appBanner['link'];?>" size="90" maxlength="255"/>
			<p class="help-block" id="J_link_example1">【商品详情页链接示例】   http://www.hinrc.com/index.php?dish_id=15</p>
			<p class="help-block" id="J_link_example2">【类目列表页链接示例】   http://www.hinrc.com/index.php?cate_id=15</p>
			<p class="help-block" id="J_link_example3">【搜索结果页链接示例】   http://www.hinrc.com/index.php?keyword=test</p>
			<p class="help-block" id="J_link_example4">【自定义H5页链接示例】   http://www.hinrc.com/index.php?mod=mobile&name=addon8&id=28&do=article&is_app=1</p>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 是否显示：</label>

		<div class="col-sm-9">
			<input type="radio" name="enabled" value="1" id="enabled1"
				<?php  if(empty($appBanner) || $appBanner['enabled'] == 1) { ?> checked="true"
				<?php  } ?> /> 是 &nbsp;&nbsp;&nbsp; 
				<input type="radio" name="enabled" value="0" id="enabled2"
				<?php  if(!empty($appBanner) && $appBanner['enabled'] == 0) { ?> checked="true"
				<?php  } ?> /> 否
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"
			for="form-field-1"> </label>

		<div class="col-sm-9">
			<input name="submit" type="submit" value=" 提 交 " class="btn btn-info" />
		</div>
	</div>
</form>

<?php include page('footer');?>

<script type="text/javascript">

	$(document).ready(function(){

		if($("#J_position").val()==1)
		{
			$('#J_link_div').show();
			$('#J_link_type_div').show();
		}
		else{
			$('#J_link_div').hide();
			$('#J_link_type_div').hide();
		}
		
		$("#J_position").change( function() {

			if($(this).val()==1)
			{
				$('#J_link_div').show();
				$('#J_link_type_div').show();
			}
			else{
				$('#J_link_div').hide();
				$('#J_link_type_div').hide();
			}
			  
		});
	});
</script>