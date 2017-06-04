<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
	<style>
		.show_roles .each_role{
			display: inline-block;
			margin-right:10px;
		}
		.show_roles>div{
			line-height:30px;
		}
		.show_roles .role_title{
			border-bottom:1px solid #f5f2e5;
			margin-top:15px;
		}
		.field{max-height: 320px;overflow: hidden;}
		.field>div{
			border: 1px solid #e5e5e5;
			border-radius: 3px;
			overflow-y: auto;
			max-height: 300px;
		}
		.field .tit{
			padding-left: 15px;
			background: #F9F9F9;
			border-bottom:1px solid  #e5e5e5;
			height:28px;
			line-height:28px;
		}
		.field p{
			border-bottom:1px solid  #e5e5e5;
			height:28px;
			line-height:28px;
			padding-left:15px;
			margin-bottom:0px;
			cursor: pointer;
		}
		.field p:hover{
			background: #F9F9F9;
		}
		.z_none{
			height:28px;
			line-height:28px;
			padding-left:15px;
		}
		.check-code{
			margin-left: 10px;
		}
	</style>
 <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" >
        <input type="hidden" name="id" value="<?php  echo $adv['id'];?>" />
		<h3 class="header smaller lighter blue">新增用户</h3>
	 <div class="alert alert-info" style="margin:10px 0; width:auto;">
		 <i class="icon-lightbulb"></i> 部分管理员需要手机号，如业务员。有填写手机号则，需要验证，没有填写手机号，则无需验证
	 </div>
        <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > <font color="red">*</font>用户名：</label>

			<div class="col-sm-3">
				 <input type="text" name="username"  class="form-control" />
			</div>
		</div>
	 <div class="form-group">
		 <label class="col-sm-2 control-label no-padding-left" > 手机号：</label>

		 <div class="col-sm-3">
			 <input type="text" name="mobile"  class="mobile-input form-control" />
		 </div>
		 <div class="col-sm-2">
			 <div class="btn btn-info sm-btn check-code">验证码</div>
		 </div>
	 </div>
	 <div class="form-group">
		 <label class="col-sm-2 control-label no-padding-left" > 验证码：</label>

		 <div class="col-sm-3">
			 <input type="text" name="code"  class="mobile-input form-control" />
		 </div>
	 </div>
	   <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> <font color="red">*</font>新密码：</label>

			<div class="col-sm-3">
				   <input type="password"  name="newpassword"  class="form-control" />
			</div>
		</div>
									
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> <font color="red">*</font>确认密码：</label>

			<div class="col-sm-3">
				<input type="password"  name="confirmpassword" class="form-control"  />
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> <font color="red">*</font>角色分组：</label>

			<div class="col-sm-3">
				<select  name="rolers_id" class="form-control" >
					<option value="0">选择角色分组</option>
					<?php
						foreach($all_rolers as $one){
							echo "<option value='{$one['id']}'>{$one['name']}</option>";
						}
					?>
				</select>
			</div>
		</div>



	  <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

			<div class="col-sm-9">
			<input name="submit" type="submit" value=" 提 交 " class="btn btn-info tijiao"/>

			</div>
		</div>


    </form>
<?php  include page('footer');?>
<script type="text/javascript">
	$(function(){
		var countdown = 60;
		$(".check-code").click(function(){
			var $this = $(this);
			if( $this.hasClass('disabled') ){
				return false;
			}else{
				var $this = $(this);
				var mobile_val = $(".mobile-input").val();
				checkInfo(mobile_val);
				var url = "<?php echo web_url('user',array('name'=>'user','op'=>'adduser'));?>";
				$.post(url,{mobile:mobile_val,'check_code':1},function(data){
					if( data.errno == 200 ){
						var message = data.message;
						alert(message.tit,message.des);
						$this.addClass("disabled");
						setInterval(function(){
							if( countdown == 0 ){
								countdown = 60;
							}else{
								$this.text(countdown);
								countdown--;
							}
						},1000);
						
					}else{
						alert(data.message);
					}
				},'json');
			}
			
		});

		function checkInfo(form) {
			if (!form) {
				alert('请输入您的手机号码！');
				return false;
			}
			if (form.search(/^([0-9]{11})?$/) == -1) {
				alert('请输入正确的手机号码！');
				return false;
			}
		}
	})
</script>