<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
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
												<input type="text" name="mobile" id="mobile" maxlength="100" class="col-xs-10 col-sm-2"  value="<?php  echo $member['mobile'];?>" />
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
										 <select name="relation_uid" style="width: 175px;height: 30px;line-height: 28px;">
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

								 <div class="form-group">
									 <label class="col-sm-2 control-label no-padding-left" > 会员身份:</label>

									 <div class="col-sm-9">
										 <select name="parent_roler_id"  class="purchase_roler_id" style="width: 175px;height: 30px;line-height: 28px;" onchange="fetchChild(this,this.options[this.selectedIndex].value)">
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
								 <div class="form-group">
									 <label class="col-sm-2 control-label no-padding-left" > 平台名称:</label>

									 <div class="col-sm-9">
										 <input type="text" name="platform_name" id="platform_name" maxlength="100" class="col-xs-10 col-sm-2"  value="<?php  echo $member['platform_name'];?>" />
									 </div>
								 </div>
								 <div class="form-group">
									 <label class="col-sm-2 control-label no-padding-left" > 平台链接:</label>

									 <div class="col-sm-9">
										 <input type="text" name="platform_url" id="platform_url" maxlength="100" class="col-xs-10 col-sm-2"  value="<?php  echo $member['platform_url'];?>" />
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

		var purchase_roler_id_val = $('.purchase_roler_id').val();
		var son_roler_id_val = '<?php echo $member['son_roler_id'];?>';
		if(purchase_roler_id_val != 0){
			fetchChild($('.purchase_roler_id'),purchase_roler_id_val,son_roler_id_val)
		}
	</script>
<?php  include page('footer');?>