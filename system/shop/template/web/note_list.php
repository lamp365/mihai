<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
	图文笔记列表&nbsp;&nbsp;&nbsp;<a class="btn btn-info btn-sm add-user" href="<?php echo web_url('note', array('op' => 'add'))?>">新增笔记</a>
</h3>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center; width: 30px">ID</th>
			<th style="text-align: center;">标题</th>
			<th style="text-align: center;">作者</th>
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
			<td><?php echo $value['name'];?></td>
			<td><?php echo empty($value['isrecommand'])?'否':'是'; ?></td>
			<td><?php echo empty($value['check'])?'否':'已审核'; ?></td>
			<td><?php echo date('Y-m-d H:i:s',$value['createtime']);?></td>
			<td style="text-align: center;">
				<a href="javascript:;" class="btn btn-xs btn-info auther" data-noteid="<?php echo $value['note_id'];?>" >修改作者</a>
				<a class="btn btn-xs btn-info" href="<?php echo web_url('note', array('op' => 'edit', 'note_id' => $value['note_id']))?>"><i
					class="icon-edit"></i>&nbsp;查&nbsp;看&nbsp;</a> &nbsp;&nbsp;
				<a class="btn btn-xs btn-danger" href="<?php  echo web_url('note', array('op'=>'delete','id' => $value['note_id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;		</a>
			</td>
		</tr>
        <?php  } } ?>
     </tbody>
</table>

	<!-- 模态框（Modal） -->
	<div class="modal fade" id="authModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<form method="post" action="<?php echo web_url('note',array('op'=>'edit_auth')) ?>">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">修改作者</h4>
				</div>
				<div class="modal-body">
					<select class="form-control" name="openid">
						<?php foreach($dummy_member as $one){ ?>
						<option value="<?php echo $one['openid']?>"><?php echo $one['realname'].$one['choose']?></option>
						<?php } ?>
					</select>
					<?php if(empty($dummy_member)){ ?>
						<input type="hidden" class="du_member" value="1">
					<?php }else{ ?>
						<input type="hidden" class="du_member" value="2">
					<?php } ?>
					<input type="hidden" name="hide_noteid" class="hide_noteid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
					<button type="submit" class="btn btn-primary" >提交更改</button>
				</div>
			</div>
			</form><!-- /.modal-content -->
		</div><!-- /.modal -->
	</div>
	<script>
		$(".auther").click(function(){
			var du_member = $(".du_member").val();
			if(du_member == 1){
				alert("请给你的虚拟用户完善信息");
			}else{
				var note_id = $(this).data('noteid');
				$(".hide_noteid").val(note_id);
				$("#authModal").modal('show');
			}

		})
	</script>
<?php  echo $pager;?>
<?php  include page('footer');?>