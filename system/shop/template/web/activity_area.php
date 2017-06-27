<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<h3 class="header smaller lighter blue"><?php  if(!empty($area['id'])) { ?>编辑<?php  }else{ ?>新增<?php  } ?>区间组</h3>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" >
		<div class="form-group"> 
			 <label class="col-sm-2 control-label no-padding-left" > 区间制式</label>
			 <div class="col-sm-9">
			       <?php if ( is_array($area) ){ foreach ( $area as $area_value ){ ?>
				       <input id="s_<?php echo $area_value; ?>" name="ac_status" value="<?php echo $area_value; ?>" type="radio"><label for="s_<?php echo $area_value; ?>"><?php echo $area_value; ?>小时制</label>
				   <?php }} ?>
			 </div>
		</div>
		<div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" ></label>
			 <div class="col-sm-9">
				   <input name="submit" type="submit" value="提交" class="btn btn-primary span3">
			 </div>
		</div>
</form>
<?php  include page('footer');?>
