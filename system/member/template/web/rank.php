<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue"><?php if(empty($rank)){ echo '添加';}else{ echo '编辑';}?>会员等级&nbsp;&nbsp;&nbsp;</h3>
<form action="<?php echo web_url('rank',array('op'=>'detail')) ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
	<input type="hidden" name="rank_level" class="col-xs-10 col-sm-2" value="<?php echo $rank['rank_level'];?>" />


	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" >等级名称 <?php echo $rank['rank_level']?></label>

		<div class="col-sm-9">
			<input type="text" name="rank_name" class="col-xs-10 col-sm-3" value="<?php echo $rank['rank_name'];?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 所需经验</label>

		<div class="col-sm-9">
			<input type="text" name="experience" class="col-xs-10 col-sm-3" value="<?php echo $rank['experience'];?>" />
			<div class="help-block"> 只能是数字，整数</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> pc图片：</label>

		<div class="col-sm-9">
			<div class="fileupload fileupload-new" data-provides="fileupload">
				<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
					<?php  if(!empty($rank['icon'])) { ?>
						<img style="width: 100%" src="<?php echo $rank['icon'];?>" >
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
					<?php  if(!empty($rank['wap_icon'])) { ?>
						<img style="width: 100%" src="<?php echo $rank['wap_icon'];?>" >
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

<?php  include page('footer');?>
