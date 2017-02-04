<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>

<h3 class="header smaller lighter blue">图文笔记编辑</h3>

<form action="" method="post" class="form-horizontal">
	<input type="hidden" name="op" value="post">
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 标题：</label>

		<div class="col-sm-9">
			<?php echo $note['title'];?>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 图片：</label>
		<div class="col-sm-9">
			 <?php  if(!empty($note['pic'])) { 
					$arrPic = explode(";", $note['pic']);
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
			<?php echo $note['description'];?>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 是否推荐：</label>

		<div class="col-sm-9">
			<input type="radio" name="isrecommand" value="1" id="isrecommand"
				<?php  if(empty($note) || $note['isrecommand'] == 1) { ?> checked="true"
				<?php  } ?> /> 是 &nbsp;&nbsp;&nbsp; 
				<input type="radio" name="isrecommand" value="0" id="isrecommand"
				<?php  if(!empty($note) && $note['isrecommand'] == 0) { ?> checked="true"
				<?php  } ?> /> 否
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 审核通过：</label>

		<div class="col-sm-9">
			<input type="radio" name="ischeck" value="0" id="ischeck"
				<?php  if(empty($note) || $note['check'] == 0) { ?> checked="true"
				<?php  } ?> /> 否 &nbsp;&nbsp;&nbsp; 
				<input type="radio" name="ischeck" value="1" id="ischeck"
				<?php  if(!empty($note) && $note['check'] == 1) { ?> checked="true"
				<?php  } ?> /> 是
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 创建时间：</label>

		<div class="col-sm-9">
			<?php echo date('Y-m-d H:i:s',$note['createtime']);?>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 编辑时间：</label>

		<div class="col-sm-9">
			<?php echo date('Y-m-d H:i:s',$note['modifiedtime']);?>
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