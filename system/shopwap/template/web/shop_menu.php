<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">商城菜单管理&nbsp;&nbsp;&nbsp;</h3>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
		<input type="hidden" name="id" class="col-xs-10 col-sm-2" value="<?php echo $shop_menu['id'];?>" />
		<input type="hidden" name="menu_type" class="col-xs-10 col-sm-2" value="shop" />
      <div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" > 图标</label>
							<div class="col-sm-9">
									<input type="text" name="icon" id="icon" class="col-xs-10 col-sm-2" value="<?php echo $shop_menu['icon'];?>" />&nbsp;&nbsp;&nbsp;<a  data-toggle="modal" data-target="#icon-list-modal" href="javascript:;">选择图标</a>
							</div>
					</div>
		
		 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 名称</label>

										<div class="col-sm-9">
													<input type="text" name="tname" class="col-xs-10 col-sm-2" value="<?php echo $shop_menu['tname'];?>" />
										</div>
									</div>
									
						  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 缩略图</label>
										<div class="col-sm-9">	
									<div class="fileupload fileupload-new" data-provides="fileupload">
			                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
			                        	 <?php  if(!empty($shop_menu['img'])) { ?>
			                            <img style="width:100%" src="<?php  echo $shop_menu['img'];?>" alt="" onerror="$(this).remove();">
			                              <?php  } ?>
			                            </div>
			                        <div>
			                         <input name="img" id="img" type="file"  />
			                            <a href="#" class="fileupload-exists" data-dismiss="fileupload">移除图片</a>
			                            	 <?php  if(!empty($shop_menu['img'])) { ?>
			                              <input name="img_del" id="img_del" type="checkbox" value="1" />删除已上传图片
			                                 <?php  } ?>
			                        </div>
			                    </div>
												
											
										</div>
									</div>		
		
				 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 链接</label>

										<div class="col-sm-9">
																   		<span id="urltr2" >
					<input type="text" name="url" id="url" style="width:400px" value="<?php echo $shop_menu['url'];?>"  />&nbsp;
					<br>
								<a href="javascript:;" onclick="actionurl('<?php echo WEBSITE_ROOT.create_url('mobile',array('name' => 'shopwap','do' => 'shopindex'));?>');"><i class="icon-home"></i>商城首页</a>&nbsp;
							<a href="javascript:;" onclick="actionurl('<?php echo WEBSITE_ROOT.create_url('mobile',array('name' => 'shopwap','do' => 'shop'));?>');"><i class="icon-home"></i>个人中心</a>&nbsp;
							<a href="javascript:;" onclick="actionurl('<?php echo WEBSITE_ROOT.create_url('mobile',array('name' => 'shopwap','do' => 'goodlist','op'=>'limit'));?>');"><i class="icon-home"></i>限时购</a>&nbsp;
						<a href="javascript:;" onclick="actionurl('<?php echo WEBSITE_ROOT.create_url('mobile',array('name' => 'shopwap','do' => 'help'));?>');"><i class="icon-home"></i>帮助说明</a>&nbsp;
						</span>
										</div>
									</div>
		                            <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >类别</label>
										<div class="col-sm-9">
										     <select name="type">
                                                  <option value='1' <?php if($shop_menu['type'] == 1 ){ echo 'selected';} ?> >WEB端</option>
												  <option value='2' <?php if($shop_menu['type'] == 2 ){ echo 'selected';} ?> >WAP端</option>
											 </select>
										</div>
									</div>
										 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 排序</label>

										<div class="col-sm-9">
													<input type="text" name="torder" class="col-xs-10 col-sm-2" value="<?php echo $shop_menu['torder'];?>" />
													 <div class="help-block">越大越前</div>
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
