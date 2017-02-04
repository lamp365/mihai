<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
	图文笔记列表&nbsp;&nbsp;&nbsp;
</h3>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center; width: 30px">ID</th>
			<th style="text-align: center;">标题</th>
			<th style="text-align: center;">是否推荐</th>
			<th style="text-align: center;">是否审核</th>
			<th style="text-align: center;">创建时间</th>
			<th style="text-align: center;">操作</th>
		</tr>
	</thead>
	<tbody>
        <?php if(is_array($list)) { foreach($list as $value) { ?>
        <tr style="text-align: center;">
			<td><?php echo $value['note_id'];?></td>
			<td><?php echo $value['title'];?></td>
			<td><?php echo empty($value['isrecommand'])?'否':'是'; ?></td>
			<td><?php echo empty($value['check'])?'否':'已审核'; ?></td>
			<td><?php echo date('Y-m-d H:i:s',$value['createtime']);?></td>
			<td style="text-align: center;">
				<a class="btn btn-xs btn-info" href="<?php echo web_url('note', array('op' => 'edit', 'note_id' => $value['note_id']))?>"><i
					class="icon-edit"></i>&nbsp;查&nbsp;看&nbsp;</a> &nbsp;&nbsp;
				<a class="btn btn-xs btn-danger" href="<?php  echo web_url('note', array('op'=>'delete','id' => $value['note_id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;		</a>
			</td>
		</tr>
        <?php  } } ?>
     </tbody>
</table>
<?php  echo $pager;?>
<?php  include page('footer');?>