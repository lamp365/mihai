<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>

<h3 class="header smaller lighter blue">app视频设置</h3>

<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="op" value="post">
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 视频URL：</label>

		<div class="col-sm-9">
			<div class="fileupload fileupload-new" data-provides="fileupload">
			         <?php  if(!empty($videoInfo['video_url'])) { ?>
			             <?php echo $videoInfo['video_url'];?>
			          <?php  } ?>
				<div>
					<br>
					<input name="video" type="file" /> 
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 是否显示：</label>

		<div class="col-sm-9">
			<input type="radio" name="enabled" value="1" id="enabled1"
				<?php  if(empty($videoInfo) || $videoInfo['enabled'] == 1) { ?> checked="true"<?php  } ?> /> 是 &nbsp;&nbsp;&nbsp; 
				<input type="radio" name="enabled" value="0" id="enabled2"
				<?php  if(!empty($videoInfo) && $videoInfo['enabled'] == 0) { ?> checked="true"<?php  } ?> /> 否
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