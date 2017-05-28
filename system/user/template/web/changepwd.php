<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<form action="" method="post" enctype="multipart/form-data" class="tab-content form-horizontal" role="form">
    	    <input type="hidden" value="<?php echo $id ?>"  name="id"  />
					<h3 class="header smaller lighter blue">修改密码</h3>

	<div class="alert alert-info" style="margin:10px 0; width:auto;">
		<i class="icon-lightbulb"></i> 部分管理员有手机号，如果修改了他的密码，同时也会把该手机号商城普通用户密码也同时修改
	</div>
        <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 用户名：</label>

										<div class="col-sm-9">
										 <?php echo $username ?>
										</div>
									</div>
								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-left" > 手机号：</label>

									<div class="col-sm-9">
										<?php echo $account['mobile']; ?>
									</div>
								</div>
									<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 昵称：</label>
										<div class="col-sm-9">
											<input type="text"  name="nickname" class="col-xs-10 col-sm-2" value="<?php echo $account['nickname'];?>" />
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left"> 头像：</label>

										<div class="col-sm-9">
												  <div class="fileupload fileupload-new" data-provides="fileupload">
								                        <div class="fileupload-preview thumbnail" style="width: 150px; height: 100px;">
								                        	 <?php  if(!empty($account['avatar'])) { ?>
								                            <img src="<?php  echo $account['avatar'];?>" alt="" onerror="$(this).remove();">
								                              <?php  } ?>
								                            </div>
								                        <div>
								                         <input name="thumb" id="thumb" type="file" />
								                            <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
								                        </div>
								                    </div>
																		</div>
										</div>
									</div>

									  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 新密码：</label>

										<div class="col-sm-9">
											   <input type="password"  name="newpassword" class="col-xs-10 col-sm-2"  />
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
										<input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
										
										</div>
									</div>

    </form>

<?php  include page('footer');?>
