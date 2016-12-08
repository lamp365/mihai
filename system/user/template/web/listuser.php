<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue" style="display: inline-block;margin-right: 15px;">用户列表</h3> <a  class="btn btn-primary" href="<?php  echo create_url('site', array('name' => 'user','do' => 'user','op' => 'adduser'))?>" >新增用户</a>
		<table class="table table-striped table-bordered table-hover">
			<tbody>
			<tr>
				<td>
					<li style="float:left;list-style-type:none;">
						<select style="margin-right:10px;margin-top:10px;width: 150px; height:34px; line-height:28px; padding:2px 0" onchange="findRolers(this)">
							<option value="0">请选择角色</option>
							<?php
							if(!empty($rolers)){
								foreach($rolers as $row){
									if($row['id'] == $_GP['id']){
										$sel = "selected";
									}else{
										$sel ='';
									}
									echo "<option value='{$row['id']}' {$sel}>{$row['name']}</option>";
								}
							}
							?>
						</select>
					</li>
				</td>
			</tr>
			</tbody>
		</table>
		<table class="table table-striped table-bordered table-hover" style="margin-top: 15px;">
			<thead >
				<tr>
					<th style="text-align:center;min-width:150px;">用户名</th>
					<th style="text-align:center;min-width:150px;">所属角色</th>
					<th style="text-align:center;min-width:150px;">创建时间</th>
					<th style="text-align:center; min-width:60px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td style="text-align:center;"><?php  echo $item['username'];?></td>
					<td style="text-align:center;"><?php  echo getAdminRolers($item['id']);?></td>
							<td style="text-align:center;">
									<?php  echo date('Y-m-d H:i:s', $item['createtime'])?></td>
						<td style="text-align:center;">
						<a class="btn btn-xs btn-info fenpei_rolers"  href="javascript:;"  data-uid="<?php echo $item['id'];?>"><i class="icon-edit"></i>分配角色</a>
						<a class="btn btn-xs btn-info"  href="<?php  echo web_url('user', array('op'=>'rule','id' => $item['id']))?>"><i class="icon-edit"></i>设置权限</a>
						&nbsp;&nbsp;
						<a class="btn btn-xs btn-info"  href="<?php  echo web_url('user', array('op'=>'changepwduser','id' => $item['id']))?>"><i class="icon-edit"></i>修改资料</a>&nbsp;&nbsp;
						<a class="btn btn-xs btn-danger" href="<?php  echo web_url('user', array('op'=>'deleteuser','id' => $item['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>

<div class="modal fade" id="rolersModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<form method="post" action=""
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">分配角色</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="name">选择角色</label>
					<select name="rolers_id" class="form-control">
						<option value="0">请选择</option>
						<?php
							if(!empty($rolers)){
								foreach($rolers as $row){
									echo "<option value='{$row['id']}'>{$row['name']}</option>";
								}
							}
						?>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
				<button type="submit" class="btn btn-primary">确定分配</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal -->
</div>
<script>
	$(".fenpei_rolers").click(function(){
		var url = "<?php echo web_url('user',array('op'=>'fenpei_rolers'));?>";
		var uid = $(this).data('uid');
		url = url + "&uid="+uid;
		$("#rolersModal").modal('show');
		$("#rolersModal form").attr('action',url);

	})

	function findRolers(obj){
		var id = $(obj).val();
		var url = "<?php echo web_url('user',array('op'=>'listuser')); ?>";
		var url = url+"&id="+id;
		window.location.href=url;
	}
</script>
<?php  include page('footer');?>
