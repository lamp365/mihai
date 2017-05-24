<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
	app版本列表&nbsp;&nbsp;&nbsp;
	<a href="<?php  echo web_url('app_version',array('op' =>'new'))?>" class="btn btn-primary">添加app版本</a>
</h3>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center; width: 30px">ID</th>
			<th style="text-align: center;">版本号</th>
			<th style="text-align: center;">是否强制升级</th>
			<th style="text-align: center;">app类型</th>
			<th style="text-align: center;">下载地址</th>
			<th style="text-align: center;">创建时间</th>
			<th style="text-align: center;">操作</th>
		</tr>
	</thead>
	<tbody>
        <?php if(is_array($list)) { foreach($list as $value) { ?>
        <tr style="text-align: center;">
			<td><?php echo $value['version_id'];?></td>
			<td><?php echo $value['version_no'];?></td>
			<td><?php echo empty($value['force_update'])?'否':'是'; ?></td>
			<td><?php echo $app_type_arr[$value['app_type']]; ?></td>
			<td><?php echo $value['url'];?></td>
			<td><?php echo $value['createtime'];?></td>
			<td style="text-align: center;">
				<a class="btn btn-xs btn-info" href="<?php  echo web_url('app_version', array('op' => 'edit', 'version_id' => $value['version_id']))?>"><i
					class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> &nbsp;&nbsp;
			</td>
		</tr>
        <?php  } } ?>
     </tbody>
</table>
<?php  echo $pager;?>
<?php  include page('footer');?>