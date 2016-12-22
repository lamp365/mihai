<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
	<style>
		i{
			//color: red;
			font-weight: bolder;
		}
	</style>
	<script>
		function findRolers(obj){
			var uid = $(obj).val();
			var url = "<?php echo web_url('purchase'); ?>";
			var url = url+"&uid="+uid;
			window.location.href=url;
		}
	</script>
<h3 class="header smaller lighter blue">渠道商列表&nbsp;&nbsp; </h3>
<h3 class="blue">	<span style="font-size:18px;"><strong>会员总数：<?php echo $total ?></strong></span></h3>
	<form action="" class="form-horizontal" method="post">
		<table class="table table-striped table-bordered table-hover">
			<tbody>
			<tr>
				<td>
					<li style="float:left;list-style-type:none;">
						<select style="margin-right:10px;margin-top:10px;width: 150px; height:34px; line-height:28px; padding:2px 0" onchange="findRolers(this)">
							<option value="0">请选择业务员</option>
							<?php
								if(!empty($users)){
									foreach($users as $row){
										if($row['id'] == $_GP['uid']){
											$sel = "selected";
										}else{
											$sel ='';
										}
										echo "<option value='{$row['id']}' {$sel}>{$row['username']}</option>";
									}
								}
							?>
						</select>
					</li>
					<li style="float:left;list-style-type:none;">

					</li>
					<li style="float:left;list-style-type:none;">

						<input style="margin-right:5px;margin-top:10px;width: 300px; height:34px; line-height:28px; padding:2px 0" name="keyword" id="" type="text" value="<?php echo $_GP['keyword'] ?>"  placeholder="手机号或者用户名">

					</li>
					<li style="list-style-type:none;">
						<button class="btn btn-primary" style="margin-right:10px;margin-top:10px;" type="submit"><i class="icon-search icon-large"></i> 搜索</button>
					</li>
				</td>
			</tr>
			</tbody>
		</table>
	</form>
		
		<table class="table table-striped table-bordered table-hover">
			<thead >
				<tr>
					<th style="text-align:center;"><input type="checkbox"  value="" class="parent_box"></th>
					<th style="text-align:center;">序号</th>
					<th style="text-align:center;">手机号码</th>
					<th style="text-align:center;">用户名</th>
					<th style="text-align:center;">email</th>
					<th style="text-align:center;">分配业务员</th>
					<th style="text-align:center;">平台名称</th>
					<th style="text-align:center;">平台链接</th>
					<th style="text-align:center;">操作</th>
				</tr>
			</thead>
			<tbody>
 <?php  if(is_array($list)) {
	 $j =1;
	 foreach($list as $v) { ?>
								<tr class="one_row">
									<td style="width: 50px;">
										<input class='child_box' type="checkbox" name="openid[]" value="<?php echo $v['openid'];?>">
									</td>
									<td  style="width: 50px;"><?php echo $j++;?></td>
									<td class="text-center mobile">
										<?php  echo isSelfAgent($v['mobile'],$v['relation_uid']);?>
									</td>
										<td class="text-center realname">
												<?php  echo $v['realname'];?>
									</td>
									<td class="text-center email">
											<?php  echo isSelfAgent($v['email'],$v['relation_uid']);?>
									</td>
									<td class="text-center email">
										<?php  echo getAdminName($v['relation_uid']);?>
									</td>
									<td class="text-center avatar">
										<?php echo $v['platform_name'] ;?>
									</td>
									<td class="text-center">
										<a href="<?php echo $v['platform_url'] ;?>" target="_blank"><?php echo $v['platform_url'] ;?></a>
									</td>

									<td class="text-center">
										<?php if(!isAgentAdmin()){ ?>
										<a  class="btn btn-xs btn-info edit_member" href="<?php  echo web_url('detail',array('name'=>'member','openid' => $v['openid']));?>" ><i class="icon-edit"></i>编辑会员</a>&nbsp;
										<?php } ?>
			 						</td>
								</tr>
								<?php  } } ?>
  </tbody>
    </table>


		<?php  echo $pager;?>
<?php  include page('footer');?>