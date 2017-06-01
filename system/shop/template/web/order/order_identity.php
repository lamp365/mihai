<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<h3 class="header smaller lighter blue">清关材料</h3>

<?php if($identity){?>
<table class="table">
	<tr>
		<th style="width: 150px"><label for="">姓名:</label></th>
		<td>
			<?php  echo $identity['identity_name']?>
		</td>
	</tr>
	<tr>
		<th><label for="">身份证号码:</label></th>
		<td>
			<?php  echo $identity['identity_number']?>
		</td>
	</tr>

	<tr>
		<th><label for="">正面:</label></th>
		<td>
			<?php if($identity['identity_front_image']){?>
			<img src="<?php echo $identity['identity_front_image'];?>">
			<?php }?>
		</td>
	</tr>

	<tr>
		<th><label for="">反面:</label></th>
		<td>
			<?php if($identity['identity_back_image']){?>
			<img src="<?php echo $identity['identity_back_image'];?>">
			<?php }?>
		</td>
	</tr>
</table>
<?php }else{?>
<span style="font-size: 24px;font-weight: bold;">无清关材料</span>
<?php }?>

<table class="table">
	<tr>
		<th style="width: 50px"></th>
		<td>
			<button type="button" class="btn btn-default" data-dismiss="modal" onclick="window.location.href='<?php echo web_url('order')?>'">取消</button>
		</td>
	</tr>
</table>
