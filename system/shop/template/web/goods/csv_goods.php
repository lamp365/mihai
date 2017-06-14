<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">批量导入产品 <a href="<?php  echo web_url('goods', array('op' => 'display'))?>" style="float:right;font-size:14px;"><i class="icon-plus-sign-alt"></i>产品列表</a></h3>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" >
    <div class="form-group">
	     <label class="col-sm-2 control-label no-padding-left" >CSV文件</label>
		 <div class="col-sm-9">						
			  <input name="csv" id="csv" type="file"  />
	     </div>
	</div>
    <div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > </label>
	    <div class="col-sm-9">             
		<input name="submit" type="submit" value="提交" class="btn btn-primary span3">
		</div>
	</div>
</form>
<?php  include page('footer');?>
