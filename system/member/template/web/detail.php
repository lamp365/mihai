<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/webuploader.css" />
<script src="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/webuploader.js" type="text/javascript" charset="utf-8"></script>
	<style>
		.the_box{
			display: none;
		}
		.s_upload{
		height: 24px;
		width: 47px;
		border-radius: 3px;
		color: #ffffff;
		display: block;
		text-align: center;
		cursor: pointer;
		line-height: 24px;
		background-color: #31b0d5;
    	border-color: #269abc;
	}
	.upload_pic{width: 90px;height: 90px;float: left;margin-right:6px;border: 1px solid #F1F1F1;padding: 1px;background: #ffffff;position: relative}
	.upload_pic img{width: 88px;height: 88px;}
	.upload_button_close{
		position: absolute;
		top: -8px;
		right: -8px;
		width: 17px;
		height: 17px;
		background: url('images/close_icon.png') no-repeat -25px 0;
		cursor: pointer;
	}
	.webuploader-pick{
		height: 24px;
	}
	</style>
<h3 class="header smaller lighter blue">会员信息</h3>


 <form action="" method="post" enctype="multipart/form-data" class="form-horizontal" >
		<input type="hidden" name="openid" value="<?php  echo $member['openid'];?>">
		       <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 用户名：</label>

										<div class="col-sm-9">
												<input type="text" name="realname" id="realname"  class="col-xs-10 col-sm-2"  value="<?php  echo $member['realname'];?>" />
										</div>
									</div>
		
				       <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 联系电话：</label>

										<div class="col-sm-9">
											<?php if(!empty($member['mobile'])){  $disabled = "disabled"; }else{ $disabled=''; } ;?>
											<input type="text" <?php echo $disabled; ?> name="mobile" id="mobile" maxlength="100" class="col-xs-10 col-sm-2"  value="<?php  echo $member['mobile'];?>" />
										</div>
									</div>
									
										       <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 邮箱:</label>

										<div class="col-sm-9">
												<input type="text" name="email" id="email" maxlength="100" class="col-xs-10 col-sm-2"  value="<?php  echo $member['email'];?>" />
										</div>
									</div>
									
										       <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 注册时间:</label>

										<div class="col-sm-9">
												<?php  echo date('Y-m-d H:i:s', $member['createtime'])?>
										</div>
									</div>
									
									      <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 新密码:</label>

										<div class="col-sm-9">
												<input type="password" name="password" id="password" maxlength="100" class="col-xs-10 col-sm-2"  value="" />
										</div>
									</div>
									
											      <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 确认密码:</label>

										<div class="col-sm-9">
													<input type="password" name="repassword" id="repassword" maxlength="100" class="col-xs-10 col-sm-2"  value="" />
										</div>
									</div>

								 <div class="form-group">
									 <label class="col-sm-2 control-label no-padding-left" > 分配业务员:</label>

									 <div class="col-sm-9">
										 <select name="relation_uid" class="relation_uid" style="width: 175px;height: 30px;line-height: 28px;" onchange="show_box()">
											 <option value="0">请选择</option>
											 <?php
											 if(!empty($user_rolers)){
												 foreach($user_rolers as $row){
													 if($row['uid'] == $member['relation_uid']){
														 $check = 'selected="selected"';
													 }else{
														 $check = '';
													 }
													 echo "<option value='{$row['uid']}' {$check}>{$row['username']}</option>";
												 }
											 }
											 ?>
										 </select>
									 </div>
								 </div>

								 <div class="form-group the_box">
									 <label class="col-sm-2 control-label no-padding-left" > 会员身份:</label>

									 <div class="col-sm-9">
										 <select name="parent_roler_id" class="purchase_roler_id" style="width: 175px;height: 30px;line-height: 28px;" onchange="fetchChild(this,this.options[this.selectedIndex].value)">
											 <option value="0">请选择</option>
											 <?php
											 if(!empty($purchase)){
												 foreach($purchase as $row){
													 if($row['id'] == $member['parent_roler_id']){
														 $check = 'selected="selected"';
													 }else{
														 $check = '';
													 }
													 echo "<option value='{$row['id']}' {$check}>{$row['name']}</option>";
												 }
											 }
											 ?>
										 </select>
										 <select name="son_roler_id" style="width: 175px;height: 30px;line-height: 28px;" class="child_choose">
											 <option value="0">请选择</option>
										 </select>
									 </div>
								 </div>
								 <div class="form-group the_box">
									 <label class="col-sm-2 control-label no-padding-left" > 平台名称:</label>

									 <div class="col-sm-9">
										 <input type="text" name="platform_name" id="platform_name" maxlength="100" class="col-xs-10 col-sm-2"  value="<?php  echo $member['platform_name'];?>" />
									 </div>
								 </div>
								 <div class="form-group the_box">
									 <label class="col-sm-2 control-label no-padding-left" > 平台链接:</label>

									 <div class="col-sm-9">
										 <input type="text" name="platform_url" id="platform_url" maxlength="100" class="col-xs-10 col-sm-2"  value="<?php  echo $member['platform_url'];?>" />
									 </div>
								 </div>
								 <div class="form-group the_box">
									 <label class="col-sm-2 control-label no-padding-left" > 平台主图:</label>

									 <div class="col-sm-9">
										 <div class="show_pic">
											<span class="s_upload">上传</span>
										</div>
										 <div class="show_pic_list">
										 <?php if(!empty($member['platform_pic'])) {
											 $platform_pic = explode(',', $member['platform_pic']);
											 foreach($platform_pic as $pic){
										 ?>
												 <div class="upload_pic">
													 <input type="hidden" name="picurl[]" value="<?php  echo $pic;?>">
													 <img src="<?php  echo $pic;?>">
													 <span class="upload_button_close" title="删除图" onclick="del(this);"></span>
												 </div>
										 <?php }}  ?>
										 </div>

									 </div>
								 </div>
								  <div class="form-group">
									 <label class="col-sm-2 control-label no-padding-left" > QQ:</label>

									 <div class="col-sm-9">
										 <input type="text" name="QQ" id="QQ" maxlength="100" class="col-xs-10 col-sm-2"  value="<?php  echo $member['QQ'];?>" />
									 </div>
								 </div>
								  <div class="form-group">
									 <label class="col-sm-2 control-label no-padding-left" > 微信:</label>

									 <div class="col-sm-9">
										 <input type="text" name="weixin" id="weixin" maxlength="100" class="col-xs-10 col-sm-2"  value="<?php  echo $member['weixin'];?>" />
									 </div>
								 </div>
								  <div class="form-group">
									 <label class="col-sm-2 control-label no-padding-left" > 旺旺:</label>

									 <div class="col-sm-9">
										 <input type="text" name="wanwan" id="wanwan" maxlength="100" class="col-xs-10 col-sm-2"  value="<?php  echo $member['wanwan'];?>" />
									 </div>
								 </div>
										      <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 积分:</label>

										<div class="col-sm-9">
													<?php  echo $member['credit'];?>
										</div>
									</div>
									
									      <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 余额:</label>

										<div class="col-sm-9">
														<?php  echo $member['gold'];?>
										</div>
									</div>
									
									    <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 优惠券:</label>

										<div class="col-sm-9">
														<?php  echo $bonuscount;?>&nbsp;&nbsp;<a href="<?php  echo create_url('site', array('name' => 'bonus','do' => 'userbonuslist','openid'=>$member['openid']))?>" target="_blank"><strong>查看</strong></a>
										</div>
									</div>
				<?php  if(!empty($weixininfo['weixin_openid']))
								{?>
									      <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 微信头像:</label>

										<div class="col-sm-9">
												<img class="img-rounded" src="<?php  echo $weixininfo['avatar'];?>" width="45px" height="45px" />
										</div>
									</div>
									
											      <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 微信性别:</label>

										<div class="col-sm-9">
												<?php  echo $weixininfo['gender']=='1'?'男':($weixininfo['gender']=='2'?'女':'保密');?>
										</div>
									</div>
									
										      <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 微信昵称:</label>

										<div class="col-sm-9">
												<?php  echo $weixininfo['nickname'];?>
										</div>
									</div>
											<?php		}?>
									
		
					  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

										<div class="col-sm-9">
										<input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
										
										</div>
									</div>
		
			
			
		
	</form>

	<script>
	var uploader = WebUploader.create({

	// 选完文件后，是否自动上传。
	auto: true,

	swf: '__RESOURCE__/recouse/js/webuploader/Uploader.swf',

	// 文件接收服务端。
	server: 'fileupload.php?savelocal=0',

	// 选择文件的按钮。可选。
	// 内部根据当前运行是创建，可能是input元素，也可能是flash.

	pick: '.show_pic',

	//可以重复上传
	duplicate: true,

	// 只允许选择图片文件。
	accept: {
		title: 'Images',
		extensions: 'gif,jpg,jpeg,bmp,png',
		mimeTypes: 'image/jpg,image/jpeg,image/png'
	}
});
// 当有文件被添加进队列的时候
uploader.on( 'fileQueued', function( file ) {
	uploader.makeThumb(file, function(error, src) {
		if(error) {
			alert('不能预览图片');
			return;
		}
	}, 50, 50);
});
// 文件上传成功，给item添加成功class, 用样式标记上传成功。
uploader.on('uploadSuccess', function(file, response) {
	var data = eval("(" +response._raw+ ")");
	if(data.hasOwnProperty('error')){
		alert('上传失败');
	}else{
		var html = "<div class='upload_pic'>"+
			"<input type='hidden' name='picurl[]' value='"+ data.name +"'>"+
			"<img src='"+data.name+"' /><span class='upload_button_close' title='删除图' onclick='del(this);'></span>"+
			"</div>";
		$('.show_pic_list').append(html);

		$('#' + file.id).addClass('upload-state-done');
	}

});
// 文件上传失败，显示上传出错。
uploader.on('uploadError', function(file) {
	alert('上传失败');
});


		var purchase = <?php echo json_encode($childrens);?>;
		function fetchChild(o_obj,pid,sonid){
			var html = '<option value="0">请选择</option>';
			if (!purchase || !purchase[pid]) {
				$(o_obj).parent().find('.child_choose').html(html);
				return false;
			}

			var html = '';
			var sel = '';
			for (i in purchase[pid]) {
				if(sonid){
					if(purchase[pid][i]['id'] == sonid){
						sel = "selected='selected'";
					}else{
						sel ='';
					}
				}

				html += '<option value="'+purchase[pid][i]['id']+'"  '+sel+'>'+purchase[pid][i]['name']+'</option>';
			}
			$(o_obj).parent().find('.child_choose').html(html);
		}

		$(function(){
			var purchase_roler_id_val = $('.purchase_roler_id').val();
			var son_roler_id_val = '<?php echo $member['son_roler_id'];?>';
			if(purchase_roler_id_val != 0){
				fetchChild($('.purchase_roler_id'),purchase_roler_id_val,son_roler_id_val)
			}
		});



		function show_box(num){
			if(!num){
				var val = $(".relation_uid option:selected").val();
			}else{
				var val = num;
			}
			if(val == 0){
				$(".the_box").hide();
			}else{
				$(".the_box").show();
			}
		}

		$(function(){
			show_box(<?php echo $member['parent_roler_id'];?>)
		})
		function del(ele){
			$(ele).parent('.upload_pic').remove();
		}
	</script>
<?php  include page('footer');?>