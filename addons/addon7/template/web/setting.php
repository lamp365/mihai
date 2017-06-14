<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">参数设置</h3>

<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" >
		   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 活动界面:</label>

										<div class="col-sm-9">
												    <a href="<?php  echo create_url('mobile',array('name' => 'shopwap','do' => 'shareactive'))?>" target="_blank"><?php echo WEBSITE_ROOT;?><?php  echo create_url('mobile',array('name' => 'shopwap','do' => 'shareactive'))?></a>
                
										</div>
									</div>
	
	   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 兑换页面标题：</label>

										<div class="col-sm-9">
												  <input type="text" name="title" class="col-xs-10 col-sm-2" value="<?php  echo $setting['title'];?>" />
										</div>
									</div>



								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-left" > 许愿方式：</label>

									<div class="col-sm-9">
										<input type="radio" name="active_type"  value="1" <?php if($setting['active_type'] == 1){ echo "checked"; }?>/> 心愿许愿
										<input type="radio" name="active_type"  value="2"  <?php if($setting['active_type'] == 2){ echo "checked"; }?>/> 积分许愿
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-2 control-label no-padding-left" > 开启积分兑换：</label>

									<div class="col-sm-9">
										<input type="radio" name="open_gift_change"  value="0" <?php if($setting['open_gift_change'] == 0){ echo "checked"; }?>/> 关闭
										<input type="radio" name="open_gift_change"  value="1"  <?php if($setting['open_gift_change'] == 1){ echo "checked"; }?>/> 开启
									</div>
								</div>
											  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

										<div class="col-sm-9">
										<br/><input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
										
		                     </div>
		                     </div>
				
</form>
<?php  include page('footer');?>