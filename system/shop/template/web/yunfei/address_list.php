<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<style>
	.nav-tabs li a{
		padding: 6px 22px;
	}
</style>
<br/>
<ul class="nav nav-tabs" >
	<li style="" <?php  if($_GP['do'] == 'disharea') { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'disharea','op' => 'display'))?>">运费管理</a></li>
	<li style="" <?php  if($_GP['do'] == 'promotion')  { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'promotion','do'=>'promotion','op' => 'display'))?>">促销免邮</a></li>
	<li style="" <?php  if($_GP['do'] == 'address')  { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'address','op' => 'index'))?>">退货地址</a></li>
</ul>

<h3 class="header smaller lighter blue">退货地址&nbsp;&nbsp;&nbsp;</h3>
<form action="<?php echo web_url('address'); ?>" method="post" class="form-horizontal" >
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 收货人</label>

		<div class="col-sm-3">
			<input type="text" name="username" class="form-control" value="<?php  echo $address['username'];?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 联系电话</label>

		<div class="col-sm-3">

			<input type="text" name="mobile" class="form-control" value="<?php  echo $address['mobile'];?>" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 退货地址</label>

		<div class="col-sm-3">

			<input type="text" name="address" class="form-control" value="<?php  echo $address['address'];?>" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 邮编</label>

		<div class="col-sm-3">

			<input type="text" name="code" class="form-control" value="<?php  echo $address['code'];?>" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > </label>

		<div class="col-sm-3">
			<input type="hidden" name="id"  value="<?php echo $list['id']; ?>"/>
			<input type="hidden" name="sure_add"  value="1"/>
			<input type="submit" name=""  value="确认提交" class="btn btn-md btn-primary"/>
		</div>
	</div>

</form>


<?php  include page('footer');?>
