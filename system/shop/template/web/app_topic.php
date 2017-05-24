<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>

<h3 class="header smaller lighter blue">app端专题设置</h3>

<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="op" value="post">
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 排序：</label>

		<div class="col-sm-9">
			<input type="text" name="displayorder" class="span6" value="<?php  echo $appTopic['displayorder'];?>" required/>(越大越前)
		</div>
	</div>
	
	<div class="form-group" id="J_link_div">
		<label class="col-sm-2 control-label no-padding-left"> 标题：</label>
		<div class="col-sm-9">
			<input type="text" name="title" class="span6" value="<?php  echo $appTopic['title'];?>" required/>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 布局类型：</label>

		<div class="col-sm-9">
			<select name="type">
				<option value=''></option>
				<option value='1' <?php  if($appTopic['type'] == 1) { ?>
					selected="selected" <?php  } ?>>布局1(自定义宽高)</option>
				<option value='2' <?php  if($appTopic['type'] == 2) { ?>
					selected="selected" <?php  } ?>>布局2(宽度比1:1)</option>
				<option value='3' <?php  if($appTopic['type'] == 3) { ?>
					selected="selected" <?php  } ?>>布局3(宽度比1:1)</option>
				<option value='4' <?php  if($appTopic['type'] == 4) { ?>
					selected="selected" <?php  } ?>>布局4(宽度比1:1)</option>
				<option value='5' <?php  if($appTopic['type'] == 5) { ?>
					selected="selected" <?php  } ?>>布局5(宽度比310:220:220)</option>
			</select>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 是否显示：</label>

		<div class="col-sm-9">
			<input type="radio" name="enabled" value="1" id="enabled1"
				<?php  if(empty($appTopic) || $appTopic['enabled'] == 1) { ?> checked="true"
				<?php  } ?> /> 是 &nbsp;&nbsp;&nbsp; 
				<input type="radio" name="enabled" value="0" id="enabled2"
				<?php  if(!empty($appTopic) && $appTopic['enabled'] == 0) { ?> checked="true"
				<?php  } ?> /> 否
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"
			for="form-field-1"> </label>

		<div class="col-sm-9">
			<input name="submit" type="submit" value=" 提 交 " class="btn btn-info" />
			<a type="button" class="btn btn-primary span2" name="confirmsend" data-toggle="modal" href="<?php echo web_url('app_topic')?>">返回</a>
		</div>
	</div>
	
</form>

<?php include page('footer');?>