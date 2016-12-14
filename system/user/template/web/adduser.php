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
	</style>
 <form action="" method="post" class="form-horizontal" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php  echo $adv['id'];?>" />
		<h3 class="header smaller lighter blue">新增用户</h3>
        <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 用户名：</label>

			<div class="col-sm-9">
				 <input type="text" name="username"  class="col-xs-10 col-sm-2" />
			</div>
		</div>
	 <div class="form-group">
		 <label class="col-sm-2 control-label no-padding-left" > 手机号：</label>

		 <div class="col-sm-9">
			 <input type="text" name="mobile"  class="col-xs-10 col-sm-2" />
		 </div>
	 </div>
	   <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 新密码：</label>

			<div class="col-sm-9">
				   <input type="password"  name="newpassword"  class="col-xs-10 col-sm-2" />
			</div>
		</div>
									
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 确认密码：</label>

			<div class="col-sm-9">
				<input type="password"  name="confirmpassword" class="col-xs-10 col-sm-2"  />
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
