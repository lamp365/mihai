<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<h3 class="header smaller lighter blue"><?php  if(!empty($area['id'])) { ?>编辑<?php  }else{ ?>新增<?php  } ?>区域</h3>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" >
	
	<input type="hidden" name="parentid" value="<?php  echo $parent['id'];?>" />
	  		<?php  if(!empty($parentid)) { ?>
	   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 上级区域</label>

										<div class="col-sm-9">
														<?php  echo $parent['name'];?>
										</div>
									</div>
		<?php  } ?>
		
		   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 排序</label>

										<div class="col-sm-9">
														<input type="text" name="displayorder" class="col-xs-10 col-sm-2" value="<?php  echo $area['displayorder'];?>" />
										</div>
									</div>
	
			   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 区域名称</label>

										<div class="col-sm-9">
												
									<input type="text" name="catename" class="col-xs-10 col-sm-2" value="<?php  echo $area['name'];?>" />
										</div>
									</div>
									
									   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 区域图片</label>

										<div class="col-sm-9">
												
															 <div class="fileupload fileupload-new" data-provides="fileupload">
			                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
			                        	 <?php  if(!empty($area['thumb'])) { ?>
			                            <img style="width:100%" src="<?php  echo $area['thumb'];?>" alt="" onerror="$(this).remove();">
			                              <?php  } ?>
			                            </div>
			                        <div>
			                         <input name="thumb" id="thumb" type="file"  />
			                            <a href="#" class="fileupload-exists" data-dismiss="fileupload">移除图片</a>
			                            	 <?php  if(!empty($area['thumb'])) { ?>
			                              <input name="thumb_del" id="thumb_del" type="checkbox" value="1" />删除已上传图片
			                                 <?php  } ?>
			                        </div>
			                    </div>
												
											
										</div>
									</div>
	
	  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 区域描述</label>

										<div class="col-sm-9">
											
						<input type="text" name="description" class="col-xs-10 col-sm-5" value="<?php  echo $area['description'];?>" />
												</div>
									</div>
									
									 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 首页推荐</label>

										<div class="col-sm-9">
					
                        <input type='radio' name='isrecommand' value=1' <?php  if($area['isrecommand']==1) { ?>checked<?php  } ?> /> 是&nbsp;
                        <input type='radio' name='isrecommand' value=0' <?php  if($area['isrecommand']==0) { ?>checked<?php  } ?> /> 否
												</div>
									</div>
	
	
			 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 是否显示</label>

										<div class="col-sm-9">
                        <input type='radio' name='enabled' value=1' <?php  if($area['enabled']==1) { ?>checked<?php  } ?> /> 是&nbsp;
                        <input type='radio' name='enabled' value=0' <?php  if($area['enabled']==0) { ?>checked<?php  } ?> /> 否
												</div>
									</div>
									
											 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > </label>

										<div class="col-sm-9">
                       
					<input name="submit" type="submit" value="提交" class="btn btn-primary span3">
												</div>
									</div>
</form>
<?php  include page('footer');?>
