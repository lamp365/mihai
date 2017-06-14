<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>

<h3 class="header smaller lighter blue">app端专题banner设置</h3>

<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="op" value="post">
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 排序：</label>

		<div class="col-sm-9">
			<input type="text" name="displayorder" class="span6" value="<?php  echo $appBanner['displayorder'];?>" required/>(越大越前)
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 图片：</label>

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
		<label class="col-sm-2 control-label no-padding-left"> 所属专题：</label>

		<div class="col-sm-9">
			<select name="topic_id" required>
				<option value=''></option>
				 <?php if(is_array($arrTopic)) { foreach($arrTopic as $value) { ?>
				 <option value='<?php echo $value['topic_id'];?>' <?php  if($value['topic_id'] == $appBanner['topic_id']) { ?>
					selected="selected" <?php  } ?>><?php echo $value['title'];?></option>
				 <?php }}?>
			</select>
		</div>
	</div>

	<div class="form-group" id="J_link_div">
		<label class="col-sm-2 control-label no-padding-left"> 链接：</label>

		<div class="col-sm-9">
			<input type="text" name="link" id='link' class="span6" value="<?php  echo $appBanner['link'];?>" size="90" maxlength="255" required/>
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
			<a type="button" class="btn btn-primary span2" name="confirmsend" data-toggle="modal" href="<?php echo web_url('app_topic_banner')?>">返回</a>
		</div>
	</div>
</form>

<?php include page('footer');?>