<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>

<h3 class="header smaller lighter blue">免单开启管理</h3>

<ul class="nav nav-tabs">
	<li style="width:7%"><a href="<?php echo web_url('free_order',array('op' =>'new_list'))?>">待配置免单</a></li>
	<li style="width:7%" ><a href="<?php echo web_url('free_order')?>">已配置免单</a></li>
	<li><a href="<?php echo web_url('free_order',array('op' =>'order_finish'))?>">本周交易成功订单</a></li>
	<li class="active"><a href="<?php echo web_url('free_order',array('op' =>'free_order_enabled'))?>">免单开启管理</a></li>
</ul>
<br>
     <form method="post" class="form-horizontal" enctype="multipart/form-data">
     	<input type="hidden" name="op" value="free_order_enabled_post">	
			<div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" >是否开启：</label>
				<div class="col-sm-9">
					<label><input type="radio" name="free_order_enabled" value="1" <?php if($freeOrderEnabled){?>checked="true"<?php }?>>开启</label>
					<label><input type="radio" name="free_order_enabled" value="0" <?php if(empty($freeOrderEnabled)){?>checked="true"<?php }?>>关闭</label>
				</div>
			</div>
			<div class="form-group">
				 <label class="col-sm-2 control-label no-padding-left" >图片配置: <br/>（建议11250*300）</label>
				<div class="col-sm-9">
				  <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
                        	 <?php  if(!empty($freeIndexImage)) { ?>
                            <img src="<?php  echo $freeIndexImage;?>" alt="" onerror="$(this).remove();">
                              <?php  } ?>
                            </div>
                        <div>
                         <input name="thumb" id="thumb" type="file" />
                            <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                        </div>
                    </div>
				</div>
			<div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" ></label>

				<div class="col-sm-9">
					<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary span3" />
				</div>
			</div>
    </form>
<?php  include page('footer');?>