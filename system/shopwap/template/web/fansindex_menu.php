<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">个人中心菜单管理&nbsp;&nbsp;&nbsp;</h3>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
		<input type="hidden" name="id" class="col-xs-10 col-sm-2" value="<?php echo $fansindex_menu['id'];?>" />
		<input type="hidden" name="menu_type" class="col-xs-10 col-sm-2" value="fansindex" />
      <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 图标</label>

										<div class="col-sm-9">
							                            <div class="fileupload fileupload-new" data-provides="fileupload">
								                            <div class="fileupload-preview thumbnail" style="width: 100px; height: 100px;">
								                            <?php  if(!empty($fansindex_menu['icon'])) { ?>
								                                <img src="<?php  echo $fansindex_menu['icon'];?>" alt="" onerror="$(this).remove();">
								                            <?php  } ?>
								                            </div>
								                            <div>
								                            <input name="icon" id="icon" type="file" />
								                            <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
								                            </div>
								                        </div>
							                        </div>
										</div>
									</div>
		
		 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 名称</label>

										<div class="col-sm-9">
													<input type="text" name="tname" class="col-xs-10 col-sm-2" value="<?php echo $fansindex_menu['tname'];?>" />
										</div>
									</div>
									
								
		
				 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 链接</label>

										<div class="col-sm-9">
																   		<span id="urltr2" >
					<input type="text" name="url" id="url" style="width:400px" value="<?php echo $fansindex_menu['url'];?>"  />&nbsp;
					<br>
								<a href="javascript:;" onclick="actionurl('<?php echo WEBSITE_ROOT.create_url('mobile',array('name' => 'shopwap','do' => 'shopindex'));?>');"><i class="icon-home"></i>商城首页</a>&nbsp;
							<a href="javascript:;" onclick="actionurl('<?php echo WEBSITE_ROOT.create_url('mobile',array('name' => 'shopwap','do' => 'fansindex'));?>');"><i class="icon-home"></i>个人中心</a>&nbsp;
						<a href="javascript:;" onclick="actionurl('<?php echo WEBSITE_ROOT.create_url('mobile',array('name' => 'shopwap','do' => 'help'));?>');"><i class="icon-home"></i>帮助说明</a>&nbsp;
						</span>
										</div>
									</div>

									<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 说明</label>

		<div class="col-sm-9">
			<textarea style="height: 150px; margin: 0px; width: 379px;" id="remark" name="remark" cols="50"><?php echo $fansindex_menu['remark'];?></textarea>
		</div>
	</div>
		
										 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 排序</label>

										<div class="col-sm-9">
													<input type="text" name="torder" class="col-xs-10 col-sm-2" value="<?php echo $fansindex_menu['torder'];?>" />
													 <div class="help-block">越大越前</div>
										</div>
									</div>

									<div class="form-group">
											<label class="col-sm-2 control-label no-padding-left" > WAP:</label>
											<div class="checkbox-div">
												<input type="checkbox" name="wap" class="wap" <?php if($fansindex_menu['wap_use']=='1'){echo 'checked="checked"';}?>>
											</div>
									</div>
									<div class="form-group">
											<label class="col-sm-2 control-label no-padding-left" > WEB:</label>
											<div class="checkbox-div">
												<input type="checkbox" name="web" class="web" <?php if($fansindex_menu['web_use']=='1'){echo 'checked="checked"';}?>>
											</div>
										</div>
									<div class="form-group">
											<label class="col-sm-2 control-label no-padding-left" > APP:</label>
											<div class="checkbox-div">
												<input type="checkbox" name="app" class="app" <?php if($fansindex_menu['app_use']=='1'){echo 'checked="checked"';}?>>
											</div>
									</div>
									
												  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

										<div class="col-sm-9">
										<input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
										
										</div>
									</div>
									<?php  include page('icon-list-modal');?>
		</form>


<?php  include page('footer');?>
