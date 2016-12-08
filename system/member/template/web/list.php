<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
	<style>
		.modal-body i{
			color: red;
		}
	</style>
<h3 class="header smaller lighter blue" style="display: inline-block">会员管理</h3> &nbsp; &nbsp;<a href="javascript:;" data-toggle="modal" data-target="#addModal" class="btn btn-md btn-info">添加会员</a>
<form action="" method="get" class="form-horizontal" enctype="multipart/form-data" >
				<input type="hidden" name="act" value="module" />
				<input type="hidden" name="name" value="member" />
				<input type="hidden" name="do" value="list" />
				<input type="hidden" name="mod" value="site"/>					
				
				<table class="table" style="width:95%;" align="center">
					<tbody>
						<tr>
							<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:120px">用户名；</td>
			<td style="width:300px">

												<input name="realname"  type="text" value="<?php  echo $_GP['realname'];?>" />
			</td>	
			
					<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">手机号码：</td>
			<td>
				
												<input name="mobile" type="text"   value="<?php  echo $_GP['mobile'];?>" />
				
			</td>	
						</tr>
				
					<tr>
							<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:120px">状态：</td>
							<td style="width:300px">
								<select style="margin-right:15px;" name="showstatus">
		 <option value="1" <?php if(empty($_GP['showstatus'])||$_GP['showstatus']==1){?>selected=""<?php }?>>正常</option>
				                 <option value="-1" <?php if($_GP['showstatus']==-1){?>selected=""<?php }?>>禁用</option>
                  	                   </select>
								</td>
						
					<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">会员等级</td>
			<td>
								<select name="rank_level"  style="margin-right:10px;margin-top:10px;width: 150px; height:34px; line-height:28px; padding:2px 0">
     				<option value="0">选择会员等级</option>   
     				<?php foreach($rank_model_list as $rank_model){?>
     				  				<option value="<?php echo $rank_model['rank_level']?>" <?php if($rank_model['rank_level']==$_GP['rank_level']){?>selected=""<?php }?>><?php echo $rank_model['rank_name']?></option> 
     							<?php }?>
     				</select>
			</td>	
						</tr>
				
					<tr>
							<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:120px">微信昵称</td>
							<td style="width:300px">
						<input name="weixinname"  type="text" value="<?php  echo $_GP['weixinname'];?>" />
								</td>
						
					<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">支付宝昵称</td>
			<td><input name="alipayname"  type="text" value="<?php  echo $_GP['alipayname'];?>" />
			</td>	
						</tr>
						<tr>
						<td style="vertical-align: middle;font-size: 14px;font-weight: bold;">按食堂筛选：</td>
						<td >
			<select style="margin-right:15px;" id="mess" name="mess" >
		 <option value="" <?php  echo empty($_GP['dispatch'])?'selected':'';?>>--未选择--</option>
				<?php  if(is_array($_mess)) { foreach($_mess as $item) { ?>
                 <option value="<?php  echo $item["id"];?>" <?php  echo $item['id']==$_GP['mess']?'selected':'';?>><?php  echo $item['title']?></option>
                  	<?php  } } ?>
                   </select>
			</td>	
			</tr>
						<tr>
							<td></td>
							<td colspan="3">
										<input name="submit" type="submit" value=" 查 找 " class="btn btn-info"/></td>
						
					<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px"></td>
			<td>
				
				
			</td>	
						</tr>
					</tbody>
				</table>
				
				     
									
				
			</form>
<h3 class="blue">	<span style="font-size:18px;"><strong>会员总数：<?php echo $total ?></strong></span></h3>
		
					<table class="table table-striped table-bordered table-hover">
			<thead >
				<tr>
					<th style="text-align:center;">手机号码</th>
						<th style="text-align:center;">微信昵称</th>
						<th style="text-align:center;">支付宝昵称</th>
					<th style="text-align:center;">用户名</th>
					<th style="text-align:center;">食堂名称</th>
					<th style="text-align:center;">注册时间</th>
					<th style="text-align:center;">会员等级</th>
					<th style="text-align:center;">状态</th>
					<th style="text-align:center;">积分</th>
					<th style="text-align:center;">余额</th>
					<th style="text-align:center;">操作</th>
				</tr>
			</thead>
			<tbody>
 <?php  if(is_array($list)) { 
	 foreach($list as $v) { ?>
								<tr>
										<td class="text-center">
										<?php  echo $v['mobile'];?>
									</td>
											<td class="text-center">
											<?php foreach($v['weixin'] as $wxfans) { ?>
						<?php echo $wxfans['nickname']; ?><br/>
						<?php  }?>
									</td>
										<td class="text-center">
										<?php foreach($v['alipay'] as $alifans) { ?>
						<?php echo $alifans['nickname']; ?><br/>
						<?php  }?>
									</td>
									<td class="text-center">
										<?php  echo $v['realname'];?>
									</td>
									<td class="text-center">
									    <?php echo $v['mess_name'];?>
									</td>
									<td class="text-center">
										<?php  echo date('Y-m-d',$v['createtime'])?>
									</td>
									
											<td class="text-center">
												<?php   $member_rank_model=member_rank_model($v['experience']);if(empty($member_rank_model)){ echo '无';}else{echo $member_rank_model['rank_name'];}?>
									</td>
									<td class="text-center">
									<?php  if($v['status']==0) { ?>
										<span class="label label-important">已禁用</span>
									<?php  } else { ?>
										<span class="label label-success">正常</span>
									<?php  } ?>
									</td>
										<td class="text-center">
												<?php  echo $v['credit'];?>
									</td>
									<td class="text-center">
										<?php  echo $v['gold'];?>
									</td>
									<td class="text-center">
											<?php  if($v['status']==1) { ?>
									<a class="btn btn-xs btn-danger" href="<?php  echo web_url('delete',array('name'=>'member','openid' => $v['openid'],'status' => 0));?>" onclick="return confirm('确定要禁用该账户吗？');"><i class="icon-edit"></i>禁用账户</a>
										
											<?php  } else { ?>
										<a class="btn btn-xs btn-success" href="<?php  echo web_url('delete',array('name'=>'member','openid' => $v['openid'],'status' => 1));?>" onclick="return confirm('确定要恢复该账户吗？');"><i class="icon-edit"></i>恢复账户</a>
										
									<?php  } ?>
										&nbsp;<a  class="btn btn-xs btn-info" href="<?php  echo web_url('detail',array('name'=>'member','openid' => $v['openid']));?>"><i class="icon-edit"></i>账户编辑</a>&nbsp;<br/><br/>
										<a class="btn btn-xs btn-info" href="<?php  echo web_url('recharge',array('name'=>'member','openid' => $v['openid'],'op'=>'credit'));?>"><i class="icon-edit"></i>积分管理</a>&nbsp;
										<a class="btn btn-xs btn-info" href="<?php  echo web_url('recharge',array('name'=>'member','openid' => $v['openid'],'op'=>'gold'));?>"><i class="icon-edit"></i>余额管理</a>	
									</td>
								</tr>
								<?php  } } ?>
  </tbody>
    </table>
		<?php  echo $pager;?>

	<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<form action="<?php  echo web_url('purchase',array('op'=>'add'));?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">添加会员</h4>
					</div>
					<div class="modal-body" style="overflow: hidden">
							<table class="table" style="width:100%;" align="left">
								<tbody>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px"><i>*</i>手机号码：</td>
									<td>
										<input name="mobile" type="text"   value="" />
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:120px">用户名：</td>
									<td style="width:300px">
										<input name="realname"  type="text" value="" />
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:120px"><i>*</i>密码：</td>
									<td style="width:300px">
										<input name="pwd"  type="password" value="" />
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">email：</td>
									<td>
										<input name="email" type="text"   value="" />
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">分配业务员：</td>
									<td>
										<select name="relation_uid" style="width: 175px;height: 30px;line-height: 28px;">
											<option value="0">请选择</option>
											<?php
											if(!empty($user_rolers)){
												foreach($user_rolers as $row){
													echo "<option value='{$row['uid']}'>{$row['username']}</option>";
												}
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">会员身份：</td>
									<td>
										<select name="parent_roler_id" style="width: 175px;height: 30px;line-height: 28px;" onchange="fetchChild(this,this.options[this.selectedIndex].value)">
											<option value="0">请选择</option>
											<?php
											if(!empty($purchase)){
												foreach($purchase as $row){
													echo "<option value='{$row['id']}'>{$row['name']}</option>";
												}
											}
											?>
										</select>
										<select name="son_roler_id" style="width: 175px;height: 30px;line-height: 28px;" class="child_choose">
											<option value="0">请选择</option>
										</select>
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">平台名称：</td>
									<td>
										<input name="platform_name" type="text"   value="" />
									</td>
								</tr>
								<tr>
									<td style="vertical-align: middle;font-size: 14px;font-weight: bold;width:130px">平台链接：</td>
									<td>
										<input name="platform_url" type="text"   value="" />
									</td>
								</tr>
								</tbody>
							</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
						<button type="submit" class="btn btn-primary">确认添加</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal -->
		</form>
	</div>
	<script>
		var purchase = <?php echo json_encode($childrens);?>;
		function fetchChild(o_obj,pid){
			var html = '<option value="0">请选择</option>';
			if (!purchase || !purchase[pid]) {
				$(o_obj).parent().find('.child_choose').html(html);
				return false;
			}

			var html = '';
			for (i in purchase[pid]) {
				html += '<option value="'+purchase[pid][i]['id']+'">'+purchase[pid][i]['name']+'</option>';
			}
			$(o_obj).parent().find('.child_choose').html(html);
		}

	</script>
<?php  include page('footer');?>