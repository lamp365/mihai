<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
	觅海头条列表&nbsp;&nbsp;&nbsp;
</h3>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center; width: 30px">ID</th>
			<th style="text-align: center;">标题</th>
			<th style="text-align: center;">是否推荐</th>
			<th style="text-align: center;">创建时间</th>
			<th style="text-align: center;">操作</th>
		</tr>
	</thead>
	<tbody>
        <?php if(is_array($list)) { foreach($list as $value) { ?>
        <tr style="text-align: center;">
			<td><?php echo $value['headline_id'];?></td>
			<td><?php echo $value['title'];?></td>
			<td><?php echo empty($value['isrecommand'])?'否':'是'; ?></td>
			<td><?php echo date('Y-m-d H:i:s',$value['createtime']);?></td>
			<td style="text-align: center;">
				<a class="btn btn-xs btn-info" href="<?php echo web_url('headline', array('op' => 'edit', 'headline_id' => $value['headline_id']))?>"><i
					class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> &nbsp;&nbsp;
			</td>
		</tr>
        <?php  } } ?>
     </tbody>
</table>
<?php  echo $pager;?>
<?php  include page('footer');?>