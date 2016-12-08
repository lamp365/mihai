<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">开奖信息&nbsp;&nbsp;&nbsp;</h3>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
		<input type="hidden" name="id" class="col-xs-10 col-sm-2" value="<?php echo $article['id'];?>" />
		 <div class="form-group">
					<label class="col-sm-2 control-label no-padding" >日期确认:</label>
								<div class="col-sm-9">
											<?php echo $article['date']; ?>
								  </div>
					</div>
									

		                        <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >数据信息(*)</label>

										<div class="col-sm-9">
													<input type="text" name="nums" class="col-xs-10 col-sm-3" value="<?php echo $article['nums'];?>" />
										</div>
									</div>
											   <div class="form-group">
						<label class="col-sm-2 control-label no-padding-left" >参照图(*)</label>
						  <div class="col-sm-9">
							<div class="fileupload fileupload-new" data-provides="fileupload">
			                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
			                        	 <?php  if(!empty($article['thumb'])) { ?>
			                            <img style="width:100%" src="<?php  echo $article['thumb'];?>" alt="" onerror="$(this).remove();">
			                              <?php  } ?>
			                            </div>
			                        <div>
			                         <input name="thumb" id="thumb" type="file"  />
			                            <a href="#" class="fileupload-exists" data-dismiss="fileupload">移除图片</a>
			                           <?php  if(!empty($article['thumb'])) { ?>
			                          <input name="thumb_del" id="thumb_del" type="checkbox" value="1" />删除已上传图片
			                            <?php  } ?>
			                        </div>
			                    </div>
												
											
										</div>
									</div>
								
 
										
												  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

										<div class="col-sm-9">
										<input name="submit" type="submit" value=" 签 名 提 交 " class="btn btn-info"/>
										
										</div>
									</div>
		</form>
<?php  include page('footer');?>
