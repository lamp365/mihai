<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>

<h3 class="header smaller lighter blue">觅海头条编辑</h3>

<form action="" method="post" class="form-horizontal">
	<input type="hidden" name="op" value="post">
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 标题：</label>

		<div class="col-sm-9">
			<?php echo $headline['title'];?>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 图片：</label>
		<div class="col-sm-9">
			 <?php  if(!empty($headline['pic'])) { 
					$arrPic = explode(";", $headline['pic']);
					foreach($arrPic as $value){
			 ?>
			 <div class="fileupload-preview thumbnail" style="width: 200px; height: 160px;">
			     <img style="width: 200px; height: 150px;"src="<?php echo $value;?>" >
			 </div>
			 <?php  }} ?>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 内容：</label>

		<div class="col-sm-9">
			<?php echo $headline['description'];?>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 是否推荐：</label>

		<div class="col-sm-9">
			<input type="radio" name="isrecommand" value="1" id="isrecommand"
				<?php  if(empty($headline) || $headline['isrecommand'] == 1) { ?> checked="true"
				<?php  } ?> /> 是 &nbsp;&nbsp;&nbsp; 
				<input type="radio" name="isrecommand" value="0" id="isrecommand"
				<?php  if(!empty($headline) && $headline['isrecommand'] == 0) { ?> checked="true"
				<?php  } ?> /> 否
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 创建时间：</label>

		<div class="col-sm-9">
			<?php echo date('Y-m-d H:i:s',$headline['createtime']);?>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 编辑时间：</label>

		<div class="col-sm-9">
			<?php echo date('Y-m-d H:i:s',$headline['modifiedtime']);?>
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