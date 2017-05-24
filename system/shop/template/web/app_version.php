<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>

<h3 class="header smaller lighter blue">app版本设置</h3>

<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="op" value="post">
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 版本号：</label>
		<div class="col-sm-9">
			<input type="text" name="version_no" class="span6" value="<?php  echo $appVersion['version_no'];?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 安装包下载地址：</label>

		<div class="col-sm-9">
			<div class="fileupload fileupload-new" data-provides="fileupload">
				<div>
					<input type="text" name="url" size="90" maxlength="300" value="<?php echo $appVersion['url'];?>" />
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 更新内容：</label>

		<div class="col-sm-9">
			<textarea name="comment" rows="5" cols="100"><?php echo $appVersion['comment']?></textarea>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> app类型：</label>
		<div class="col-sm-9">
			<input type="radio" name="app_type" value="0" id="enabled1"
				<?php  if(empty($appVersion) || $appVersion['app_type'] == 0) { ?> checked="true"<?php  } ?> /> 安卓 &nbsp;&nbsp;&nbsp; 
			<input type="radio" name="app_type" value="1" id="enabled2"
				<?php  if(!empty($appVersion) && $appVersion['app_type'] == 1) { ?> checked="true"<?php  } ?> /> IOS &nbsp;&nbsp;&nbsp;
			<input type="radio" name="app_type" value="2" id="enabled2"
				<?php  if(!empty($appVersion) && $appVersion['app_type'] == 2) { ?> checked="true"<?php  } ?> /> 应用宝安卓 &nbsp;&nbsp;&nbsp;
			<input type="radio" name="app_type" value="3" id="enabled2"
				<?php  if(!empty($appVersion) && $appVersion['app_type'] == 3) { ?> checked="true"<?php  } ?> /> 应用宝IOS &nbsp;&nbsp;&nbsp;
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 是否强制升级：</label>
		<div class="col-sm-9">
			<input type="radio" name="force_update" value="1" id="enabled1"
				<?php  if(empty($appVersion) || $appVersion['force_update'] == 1) { ?> checked="true"<?php  } ?> /> 是 &nbsp;&nbsp;&nbsp; 
			<input type="radio" name="force_update" value="0" id="enabled2"
				<?php  if(!empty($appVersion) && $appVersion['force_update'] == 0) { ?> checked="true"<?php  } ?> /> 否
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

		<div class="col-sm-9">
			<input name="submit" type="submit" value=" 提 交 " class="btn btn-info" />
		</div>
	</div>
</form>

<?php  include page('footer');?>