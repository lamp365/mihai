<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/bootstrap.css">
		<script type="text/javascript"src="<?php echo RESOURCE_ROOT;?>addons/common/js/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/bootstrap.min.js"></script>
		<title>应聘者信息录入</title>
	</head>
	<style>
		*{margin: 0;padding: 0;}
		html,body{height: 100%;}
		
		label{			
			margin-top: 25px;
			width: 100px;
			text-align: right;
			font-weight: normal;
		}
		.col-sm-9{
			display: inline-block;			
			margin-top: 20px;
		}
		.form-control{
			display: inline-block;
		}
		form{
			width: 100%;
			overflow: hidden;
		}
		.modal-dialog{
			width: 800px;
			margin-top: 10%;
		}
		.col-sm-3{
			width: 150px;
		}
		table{
			width:100%;
			margin:20px auto;
			overflow: hidden;
			border: solid 1px gainsboro;
		}
		table tr{
			width: 100%;
			overflow: hidden;
			border-bottom: solid 1px gainsboro;			
		}
		table tr th{
			width: 14.2%;
			height: 30px;
			text-align: center;
			border: none;
		}
		table tr td{
			width: 14.2%;
			height: 50px;
			text-align: center;
			border: none;
		}
		table tr td span{
			cursor: pointer;			
		}
		table tr td span:hover{
			text-decoration: underline;
		}
		.viewport{
			width: 1190px;
			min-width: 320px;
			margin: 0 auto;
		}
		h3{
			border-bottom: solid 1px gainsboro;
			padding: 10px 0;
			font-weight: bold;
			margin-bottom: 20px;
		}
		button{
			margin: 20px 0;
			float: right;
		}
		.modal-body{
			padding: 20px 20px 40px 20px;
		}
		.search{
			float: right;margin: 0;
		    margin-right: 12px;
		    padding: 0 16px;
		    background-color: #f5f5f5;
		    border: solid 1px gainsboro;
		    border-left: none;
			display: inline-block;
			height: 24px;
			cursor: pointer;
		}
		#search_content{
			border:solid 1px gainsboro;
			height: 24px;
			text-indent: 10px;
			float: right;			
		}
	</style>
	<body>
	
		<div class="viewport">
			<h3>应聘人员列表</h3>
			<!--搜索栏-->
			<!-- <form>
				<span type="submit" class="search">搜索</span>
				<input type="text" id="search_content" value="" placeholder="请输入姓名或电话"/>				
			</form> -->
			<table border="" cellspacing="" cellpadding="">
				<tr style="background: #eee;">
					<th>姓名</th>
					<th>电话</th>
					<th>岗位</th>
					<th>工作经验</th>
					<th>期望薪资</th>
					<th>面试结果</th>
					<th>操作</th>
				</tr>
				<!--接下来一个tr就是一条信息-->
				<?php foreach ($all_man as $a_k => $a_v) {?>
					<tr class="one_row">
						<td class="text-center man_name"><?php echo $a_v['name'];?></td>
						<td class="text-center mobile"><?php echo $a_v['mobile'];?></td>
						<td class="text-center job"><?php echo $a_v['job'];?></td>
						<td class="text-center experience"><?php echo $a_v['experience'];?></td>
						<td class="text-center hope_pay"><?php echo $a_v['hope_pay'];?></td>
						<td class="text-center result"><?php echo $result_ary[$a_v['result']];?></td>
						<td>
							<!--点击修改出现的也是弹出框-->
							<a  class="edit" style="margin-right: 20px;" href="javascript:;" data-id="<?php echo $a_v['id'];?>">修改 </a>
							<a href="<?php  echo mobile_url('job', array('id' => $a_v['id'], 'op' => 'del'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;">删除</a>
						</td>
					</tr>
				<?php } ?>
			</table>
			<!--点击新增出现弹出框-->
			<button type="button" class="btn btn-danger"  data-toggle="modal" data-target="#new-detail">
					新增应聘人
			</button>
			<!--修改弹出框-->
			<div class="modal fade" taria-hidden="true" id="note-detail">
				<div class="modal-dialog">
					<div class="modal-content">	
						<div class="modal-header">
							请填写应聘人信息
						</div>													  	
					  	<div class="modal-body">
							<form action="  " method="post" role="form" class="form-horizontal edit_form">
								<label for="" class="col-sm-3 control-label">姓名：</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="man_name" placeholder="请输入应聘者姓名">
								</div>
								
								<label for="" class="col-sm-3 control-label">联系电话：</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="mobile" placeholder="请输入应聘者电话">
								</div>

								<label for="" class="col-sm-3 control-label">应聘岗位：</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="job" placeholder="请输入应聘岗位">
								</div>
								
								<label for="" class="col-sm-3 control-label">工作经验：</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="experience" placeholder="请输入工作经验">
								</div>
								
								<label for="" class="col-sm-3 control-label">期望薪资：</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="hope_pay" placeholder="请输入期望薪资">
								</div>
								
								<label for="" class="col-sm-3 control-label">面试结果：</label>
								<div class="col-sm-9">
									<select class="form-control" name="result">
										<option value="0">等待结果</option>
										<option value="1">录用</option>
										<option value="2">未录用</option>
									</select>
								</div>
								
								<label for="" class="col-sm-3 control-label"> </label>
								<div class="col-sm-9" style="text-align: center;">
									<input style="background:#eee;width: 240px;" type="submit" class="form-control" id="edit_man">
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>   	
			<!--新增弹出框-->
			<div class="modal fade" taria-hidden="true" id="new-detail">
				<div class="modal-dialog">
					<div class="modal-content">	
						<div class="modal-header">
							请填写应聘人信息
						</div>													  	
					  	<div class="modal-body">
							<form action="<?php echo mobile_url('job',array('op' => 'add'));?>" method="post" role="form" class="form-horizontal edit_form">
								<label for="" class="col-sm-3 control-label">姓名：</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="new_name" placeholder="请输入应聘者姓名">
								</div>
								
								<label for="" class="col-sm-3 control-label">联系电话：</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="new_mobile" placeholder="请输入应聘者电话">
								</div>

								<label for="" class="col-sm-3 control-label">应聘岗位：</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="new_job" placeholder="请输入应聘岗位">
								</div>
								
								<label for="" class="col-sm-3 control-label">工作经验：</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="new_experience" placeholder="请输入工作经验">
								</div>
								
								<label for="" class="col-sm-3 control-label">期望薪资：</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="new_hope_pay" placeholder="请输入期望薪资">
								</div>
								
								<label for="" class="col-sm-3 control-label">面试结果：</label>
								<div class="col-sm-9">
									<select class="form-control" name="new_result">
										<option value="0">等待结果</option>
										<option value="1">录用</option>
										<option value="2">未录用</option>
									</select>
								</div>
								
								<label for="" class="col-sm-3 control-label"> </label>
								<div class="col-sm-9" style="text-align: center;">
									<input style="background:#eee;width: 240px;" type="submit" class="form-control" id="add_man">
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>  	
		</div>
	</body>
	
	<script>
		//获取面试结果的值
		$("#result").change(function(){
			var result = $('#result').val();	
			console.log(result)
		})

		$(".edit").click(function(){
			var u_id = $(this).data('id');
			var url = "<?php  echo mobile_url('job',array('op' => 'edit'));?>";
			url = url + '&id='+u_id;
			$("#note-detail").modal('show');
			$(".edit_form").attr('action',url);
			var man_name = $(this).closest('.one_row').find(".man_name").html();
			var mobile = $(this).closest('.one_row').find(".mobile").html();
			var job = $(this).closest('.one_row').find(".job").html();
			var experience = $(this).closest('.one_row').find(".experience").html();
			var hope_pay = $(this).closest('.one_row').find(".hope_pay").html();
			var result = $(this).closest('.one_row').find(".result").html();
			$(".edit_form input[name='man_name']").val($.trim(man_name));
			$(".edit_form input[name='mobile']").val($.trim(mobile));
			$(".edit_form input[name='job']").val($.trim(job));
			$(".edit_form input[name='experience']").val($.trim(experience));
			$(".edit_form input[name='hope_pay']").val($.trim(hope_pay));
			$(".edit_form input[name='result']").val($.trim(result));
		})
	</script>
</html>
