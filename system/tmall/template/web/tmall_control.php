<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/select2.min.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/select2.min.js"></script>
<style type="text/css">
.department-step-1 .select2-container--default .select2-selection--single{
	border-radius: 0;
}
.department-step-1 .select2-container .select2-selection--single{
	height: 30px;
}
.department-step-1 .department-manager{
	padding-right:5px;
}
.department-step-1 .select2-container--default .select2-selection--single{
	border-radius: 0;
}
.department-step-1 .select2-container .select2-selection--single{
	height: 30px;
}
</style>

<div class="department-wrap" style="min-height: 500px;">
	<h3>店铺管理<span class="btn btn-info btn-sm add-user">添加人员</span>&nbsp<span class="btn btn-info btn-sm add-department">添加店铺</span>&nbsp<span style="margin-top: -3px;margin-left: 10px;" class="btn btn-info btn-sm set-the-department">设置店铺</span></h3>
	<form method="post" action="" name="">
		<ul class="search-ul">
			<li>
				<span class="left-span">人员</span>
				<input type="text" name="staff" class="input-height" placeholder="请输入人员名称" value="<?php echo $staff;?>">
			</li>
			<li>
				<span class="left-span">店铺</span>
				<select class="input-height" name="department">
					<option value="0">请选择店铺</option>
					<?php  if(is_array($department_ary)) { 
					foreach($department_ary as $manv) { 
						if ($manv == $department) {
							$mased = "selected";
						}else{
							$mased = "";
						}
						?>
						<option value="<?php  echo $manv;?>" <?php  echo $mased;?>><?php  echo $manv;?></option>
					<?php  } } ?>
				</select>
			</li>
			<li>
				<span class="left-span">职位</span>
				<select class="input-height" name="identity">
					<option value="0">请选择</option>
					<?php  if(is_array($identity_ary)) { 
					foreach($identity_ary as $manv) { 
						if ($manv == $identity) {
							$mased = "selected";
						}else{
							$mased = "";
						}
						if ($manv == '1') {
							$idt = '店长';
						}elseif ($manv == '2') {
							$idt = '员工';
						}
						?>
						<option value="<?php  echo $manv;?>" <?php  echo $mased;?>><?php  echo $idt;?></option>
					<?php  } } ?>
				</select>
			</li>
			<li>
				<input type="submit" name="submit" value=" 查 询 "  class="btn btn-primary btn-sm">
			</li>
		</ul>
		<div class="panel panel-default department-table">
            <table class="table table-striped table-bordered table-hover">
	            <thead >
	                <tr>
	                	<th>ID</th>
	                    <th>姓名</th>
	                    <th>店铺归属</th>
	                    <th>职位</th>
	                    <th width='10%'>操作</th>
	                </tr>
	            </thead>
		        <tbody>
	                <?php  if(is_array($al_staff)) { 
					foreach($al_staff as $almv) { ?>
		                <tr>
		                    <td class="text-center"><?php  echo $almv['id'];?></td>
		                    <td class="text-center"><?php  echo $almv['name'];?></td>
		                    <td class="text-center"><?php  echo $almv['dpm_name'];?></td>
		                    <td class="text-center"><?php  echo $idt_ary[$almv['identity']];?></td>
		                    <td class="text-center">
		                		<a class="btn btn-xs btn-info" onclick="modify('<?php  echo $almv['id'];?>')"><i class="icon-edit"></i>账户编辑</a>
		                	</td>
		                </tr>
		            <?php  } } ?>
	            </tbody>
            </table>
        </div>
        <div class='modal fade add-user-modal' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>  
			<div class='modal-dialog'>
				<div class='modal-content'>
					<div class='modal-header'> 
						<button type='button' class='close' data-dismiss='modal'>
							<span aria-hidden='true'>&times;</span>
							<span class='sr-only'>Close</span>
						</button>
						<h4 class='modal-title' class='myModalLabel'>添加人员</h4>
					</div>
					<div class='modal-body'>
						<div class="add-user-input">
							<table>
								<tr>
									<td class="left-title">姓名:</td>
									<td><input type="text" name="" class="user-name"></td>
								</tr>
								<tr>
									<td class="left-title">后台账号:</td>
									<td>
										<select class="back-account">
											<option value="0">请选择账号</option>
											<?php  if(is_array($admin_ary)) { 
											foreach($admin_ary as $manv) { 
												?>
												<option value="<?php  echo $manv;?>" <?php  echo $mased;?>><?php  echo $manv;?></option>
											<?php  } } ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="left-title">选择店铺:</td>
									<td>
										<select class="check-department" >
											<option value="0">请选择店铺</option>
											<?php  if(is_array($department_ary)) { 
											foreach($department_ary as $manv) { 
												?>
												<option value="<?php  echo $manv;?>" <?php  echo $mased;?>><?php  echo $manv;?></option>
											<?php  } } ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="left-title">选择职务:</td>
									<td>
										<select class="check-idt" >
											<option value="2">员工</option>
											<option value="1">店长</option>
										</select>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary sure-btn" >确定添加</button>
					    <button class="btn btn-default" type="button" data-dismiss="modal">关闭</button>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" class="edit_id">
		<div class='modal fade edit-user-modal' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>  
			<div class='modal-dialog'>
				<div class='modal-content'>
					<div class='modal-header'> 
						<button type='button' class='close' data-dismiss='modal'>
							<span aria-hidden='true'>&times;</span>
							<span class='sr-only'>Close</span>
						</button>
						<h4 class='modal-title' class='myModalLabel'>编辑人员</h4>
					</div>
					<div class='modal-body'>
						<div class="add-user-input">
							<table>
								<tr>
									<td class="left-title">姓名:</td>
									<td><input type="text" name="" class="edit-user-name"></td>
								</tr>
								<tr>
									<td class="left-title">后台账号:</td>
									<td>
										<select class="edit-back-account">
											<option value="0">请选择账号</option>
											<?php  if(is_array($admin_ary)) { 
											foreach($admin_ary as $manv) { 
												?>
												<option value="<?php  echo $manv;?>" <?php  echo $mased;?>><?php  echo $manv;?></option>
											<?php  } } ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="left-title">选择店铺:</td>
									<td>
										<select class="edit-check-department" >
											<option value="0">请选择店铺</option>
											<?php  if(is_array($department_ary)) { 
											foreach($department_ary as $manv) { 
												?>
												<option value="<?php  echo $manv;?>" <?php  echo $mased;?>><?php  echo $manv;?></option>
											<?php  } } ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="left-title">选择职务:</td>
									<td>
										<select class="edit-check-idt" >
											<option value="2">员工</option>
											<option value="1">店长</option>
										</select>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary edit-btn" >确定</button>
					    <button class="btn btn-default" type="button" data-dismiss="modal">关闭</button>
					</div>
				</div>
			</div>
		</div>
		<div class='modal fade add-user-department' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>  
			<div class='modal-dialog'>
				<div class='modal-content'>
					<div class='modal-header'> 
						<button type='button' class='close' data-dismiss='modal'>
							<span aria-hidden='true'>&times;</span>
							<span class='sr-only'>Close</span>
						</button>
						<h4 class='modal-title' class='myModalLabel'>添加店铺</h4>
					</div>
					<div class='modal-body'>
						<div class="add-user-input">
							<table>
								<tr>
									<td class="left-title">店铺名称:</td>
									<td><input type="text" name="" class="department-name"></td>
								</tr>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary sure-btn" >确定添加</button>
					    <button class="btn btn-default" type="button" data-dismiss="modal">关闭</button>
					</div>
				</div>
			</div>
		</div>
		<div class='modal fade set-department' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>  
			<div class='modal-dialog'>
				<div class='modal-content'>
					<div class='modal-header'> 
						<button type='button' class='close' data-dismiss='modal'>
							<span aria-hidden='true'>&times;</span>
							<span class='sr-only'>Close</span>
						</button>
						<h4 class='modal-title' class='myModalLabel'>设置店长</h4>
					</div>
					<div class='modal-body'>
						<div class="department-wrap">
							<div class="department-step-1">
								<ul class="search-ul">
									<li>
										<span class="left-span">店铺</span>
										<select class="input-height setting-department">
											<option value="0">请选择店铺</option>
											<?php  if(is_array($department_ary)) { 
											foreach($department_ary as $manv) { 
												?>
												<option value="<?php  echo $manv;?>" <?php  echo $mased;?>><?php  echo $manv;?></option>
											<?php  } } ?>
										</select>
									</li>
									<li>
										<span class="left-span">店长</span>
										<select class="input-height department-manager">
											<option value="0">请选择店长</option>
											<?php  if(is_array($staff_ary)) { 
											foreach($staff_ary as $manv) { 
												?>
												<option value="<?php  echo $manv;?>" <?php  echo $mased;?>><?php  echo $manv;?></option>
											<?php  } } ?>
										</select>
									</li>
								</ul>	
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary setup-btn" >设置</button>
					    <button class="btn btn-default" type="button" data-dismiss="modal">关闭</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<?php  echo $pager;?>
<script>
$(function(){
	$.fn.modal.Constructor.prototype.enforceFocus = function () { };
	//添加人员
	$(".add-user").on("click",function(){
		$(".add-user-modal").modal();
		$(".add-user-modal .sure-btn").on("click",function(){
			var user_name = $(".user-name").val();
			var check_department = $(".check-department").val();
			var back_account = $(".back-account").val();
			var idt = $(".check-idt").val();
			var url = "<?php  echo web_url('tmall',array('op'=>'add_staff'));?>";
			if( user_name == "" ){
				alert("请输入人员姓名","",function () {
		          //回调函数
		        }, {type: 'error', confirmButtonText: 'OK'});
			}else if( back_account == '0' ){
				alert("请选择后台账号","",function () {
		          //回调函数
		        }, {type: 'error', confirmButtonText: 'OK'});
			}else if( check_department == '0' ){
				alert("请选择店铺","",function () {
		          //回调函数
		        }, {type: 'error', confirmButtonText: 'OK'});
			}else{
				$.post(url,{user_name:user_name,check_department:check_department,back_account:back_account,idt:idt},function(data){
					if( data.message == 1){
						alert("添加成功","",function () {
				          $(".add-user-modal").modal('hide');
				        }, {type: 'success', confirmButtonText: 'OK'});
				        location.reload(true);
					}else{
						alert("添加失败","",function () {
				          //回调函数
				          $(".add-user-modal").modal('hide');
				        }, {type: 'error', confirmButtonText: 'OK'});
				        location.reload(true);
					}
				},'json');
			}
		});
	});
	// 编辑人员
	$(".edit-user-modal .edit-btn").on("click",function(){
		var edit_id = $(".edit_id").val();
		var user_name = $(".edit-user-name").val();
		var check_department = $(".edit-check-department").val();
		var back_account = $(".edit-back-account").val();
		var idt = $(".edit-check-idt").val();
		var url = "<?php  echo web_url('tmall',array('op'=>'add_staff'));?>";
		if( user_name == "" ){
			alert("请输入人员姓名","",function () {
	          //回调函数
	        }, {type: 'error', confirmButtonText: 'OK'});
		}else if( back_account == '0' ){
			alert("请输入后台账号","",function () {
	          //回调函数
	        }, {type: 'error', confirmButtonText: 'OK'});
		}else if( check_department == '0' ){
			alert("请选择店铺","",function () {
	          //回调函数
	        }, {type: 'error', confirmButtonText: 'OK'});
		}else{
			$.post(url,{user_name:user_name,check_department:check_department,back_account:back_account,edit_id:edit_id,idt:idt},function(data){
				if( data.message == 1){
					alert("添加成功","",function () {
			          $(".edit-user-modal").modal('hide');
			        }, {type: 'success', confirmButtonText: 'OK'});
			        location.reload(true);
				}else{
					alert("添加失败","",function () {
			          //回调函数
			          $(".edit-user-modal").modal('hide');
			        }, {type: 'error', confirmButtonText: 'OK'});
			        location.reload(true);
				}
			},'json');
		}
	});
	//添加店铺
	$(".add-department").on("click",function(){
		$(".add-user-department").modal();
		$(".add-user-department .sure-btn").on("click",function(){
			var department_name = $(".department-name").val();
			var url = "<?php  echo web_url('tmall',array('op'=>'add_department'));?>";
			if( department_name == "" ){
				alert("请输入店铺名称");
			}else{
				$.post(url,{department_name:department_name},function(data){
					if( data.message == 1){
						alert("添加成功","",function () {
				          $(".add-user-department").modal('hide');
				        }, {type: 'success', confirmButtonText: 'OK'});
				        location.reload(true);
					}else{
						alert("添加失败","",function () {
				          //回调函数
				        }, {type: 'error', confirmButtonText: 'OK'});
				        location.reload(true);
					}
				},'json');
			}
		});
	});
	// 设置店铺
	$(".set-the-department").on("click",function(){
		$(".set-department").modal();
		$(".set-department").on("shown.bs.modal", function(){
		    $(".department-manager").select2();
		})
		$(".set-department .setup-btn").on("click",function(){
			var depart_name = $(".setting-department").val();
			var manager = $(".department-manager").val();
			var url = "<?php  echo web_url('tmall',array('op'=>'set_department'));?>";
			if( depart_name == "0" ){
				alert("请选择店铺");
			}else if(manager == "0"){
				alert("请选择店长");
			}else{
				$.post(url,{depart_name:depart_name, manager:manager},function(data){
					if( data.message == 1){
						alert("设置成功");
				        location.reload(true);
					}else{
						alert("设置失败");
					}
				},'json');
			}
		});
	});
})

function modify(id){
	var url = "<?php  echo web_url('tmall',array('op'=>'get_staff'));?>";
	$(".edit_id").val(id);
	$.post(url,{id:id},function(data){
		$(".edit-user-modal").modal();
		$(".edit-user-modal .edit-user-name").val(data.username);
		$(".edit-back-account option[value="+data.backaccount+"]").prop("selected",true);
		$(".edit-check-department option[value="+data.department+"]").prop("selected",true);
		$(".edit-check-idt option[value="+data.idt+"]").prop("selected",true);
	},'json');
}
</script>
<?php  include page('footer');?>