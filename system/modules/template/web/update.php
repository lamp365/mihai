<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
    <form method="post" class="form-horizontal" enctype="multipart/form-data" >
		<h3 class="header smaller lighter blue">系统升级</h3>
        <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 授权状态：</label>

										<div class="col-sm-9">
										  <iframe width="200" scrolling="no" height="30" frameborder="0" style="width:200px;height: 30px;overflow:hidden;"  src="<?php echo VERSION_GETSTATIC?>"></iframe>

										</div>
									</div>
									
									  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 本地版本：</label>

										<div class="col-sm-9">
											    <?php echo $localversion ?>
										</div>
									</div>
											  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 服务器版本：</label>

										<div class="col-sm-9">
											   <?php echo $version ?>
										</div>
									</div>
										  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 升级状态：</label>

										<div class="col-sm-9">
											   <?php  if(intval($localversion)<intval($version)){?>
                   发现最新补丁，<a href="<?php echo $versionurl?>" target="_blank">下载<?php echo $version?>最新补丁</a> 
                   <?php }else{?>
                   已为最新版本，无需升级。
                    <?php }?>
										</div>
									</div>
									
							 
						 </form>			
								
<?php  include page('footer');?>