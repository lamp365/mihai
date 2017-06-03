<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<h3 class="header smaller lighter blue"><?php  if(!empty($disharea['id'])) { ?>编辑<?php  }else{ ?>新增<?php  } ?>仓库运费</h3>
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
										<label class="col-sm-2 control-label no-padding-left" > 运费</label>

										<div class="col-sm-3">
														<input type="text" name="displayorder" class="form-control" value="<?php  echo $disharea['displayorder'];?>" />
										</div>
									</div>
	
			   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 仓库名称</label>

										<div class="col-sm-3">
												
									<input type="text" name="catename" class="form-control" value="<?php  echo $disharea['name'];?>" />
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 快递名称</label>

										<div class="col-sm-3">

											<input type="text" name="kuaidi" class="form-control" value="<?php  echo $disharea['kuaidi'];?>" />
										</div>
									</div>
									
									   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 图片</label>

										<div class="col-sm-9">
												
															 <div class="fileupload fileupload-new" data-provides="fileupload">
			                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
			                        	 <?php  if(!empty($disharea['thumb'])) { ?>
			                            <img style="width:100%" src="<?php  echo $disharea['thumb'];?>" alt="" onerror="$(this).remove();">
			                              <?php  } ?>
			                            </div>
			                        <div>
			                         <input name="thumb" id="thumb" type="file"  />
			                            <a href="#" class="fileupload-exists" data-dismiss="fileupload">移除图片</a>
			                            	 <?php  if(!empty($disharea['thumb'])) { ?>
			                              <input name="thumb_del" id="thumb_del" type="checkbox" value="1" />删除已上传图片
			                                 <?php  } ?>
			                        </div>
			                    </div>
												
											
										</div>
									</div>
	
	  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 描述</label>

										<div class="col-sm-4">
											
						<input type="text" name="description" class="form-control" value="<?php  echo $disharea['description'];?>" />
												</div>
									</div>
									
									<!-- <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 清关材料</label>

										<div class="col-sm-9">
					
                        <input type='radio' name='isrecommand' value=1' <?php /* if($disharea['isrecommand']==1) { */?>checked<?php /* } */?> /> 是&nbsp;
                        <input type='radio' name='isrecommand' value=0' <?php /* if($disharea['isrecommand']==0) { */?>checked<?php /* } */?> /> 否
												</div>
									</div>-->
	
	
			 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 是否显示</label>

										<div class="col-sm-9">
                        <input type='radio' name='enabled' value=1' <?php  if($disharea['enabled']==1) { ?>checked<?php  } ?> /> 是&nbsp;
                        <input type='radio' name='enabled' value=0' <?php  if($disharea['enabled']==0) { ?>checked<?php  } ?> /> 否
												</div>
									</div>
									
											 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > </label>

										<div class="col-sm-9">
                       
											<input name="submit" type="submit" value="提交" class="btn btn-primary span3">
											<button onclick="window.history.back()"  class="btn btn-primary span3">返回上页</button>
												</div>
									</div>
</form>
<?php  include page('footer');?>
