<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
	<style>
		i{
			//color: red;
			font-weight: bolder;
		}
		body{
			position: relative;
		}
		.purchase-table tr{
			background-color: #fff!important;
		}
		.purchase-table li{
			float:left;list-style-type:none;
		}
		.piclist{
			cursor: pointer;
			position: relative;
		}
		.big-img-show{
			display: none;
			position: absolute;
			top: -28px;
			left: 50px;
			width: 300px;
			height: 300px;
			cursor: pointer;
		}
		.big-img-show-2{
			top: 3px;
			left: -300px;
		}
		.big-img-show img{
			max-width: 100%;
		}
		.set-profit{
			 position: absolute;
		    top: 90px;
		    right: 27px;
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
		<table class="table table-striped table-bordered table-hover purchase-table">
			<tbody>
			<tr>
				<td>
					<li>
						<select style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" onchange="findRolers(this)">
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
					<li>

					</li>
					<li>

						<input style="margin-right:5px;height:30px; line-height:28px; padding:2px 0" name="keyword" id="" type="text" value="<?php echo $_GP['keyword'] ?>"  placeholder="手机号或者用户名">

					</li>
					<li>
						<button class="btn btn-primary btn-sm" style="margin-right:10px;" type="submit"><!-- <i class="icon-search icon-large"></i> --> 查 询</button>
					</li>
				</td>
			</tr>
			</tbody>
		</table>
	</form>
		<form action="<?php echo web_url('purchase',array('name'=>'member','op'=>'set_ratio')); ?>" name="" method="post" class="set-profit">
			<span>设置收益比例(0.03表3%)</span>
			<input style="margin-right:5px;height:30px; line-height:28px; padding:2px 2px;" name="agent_ration" value="<?php echo $agent_ration['agent_ration'];?>" type="number" placeholder="设置收益">
			<button class="btn btn-primary btn-sm" style="margin-right:10px;" type="submit">设 置</button>
		</form>

		<table class="table table-striped table-bordered table-hover">
			<thead >
				<tr>
					<th style="text-align:center;">序号</th>
					<th style="text-align:center;">二维码</th>
					<th style="text-align:center;">手机号码</th>
					<th style="text-align:center;">用户名</th>
                    <th style="text-align:center;">QQ</th>
					<th style="text-align:center;">旺旺</th>
                    <th style="text-align:center;">微信</th>
					<th style="text-align:center;">业务员</th>
					<th style="text-align:center;">平台名称</th>
					<th style="text-align:center;width:250px">平台地址</th>
					<th style="text-align:center;">平台主图</th>
					<th style="text-align:center;">总收益</th>
					<th style="text-align:center;">确认收益</th>
					<th style="text-align:center;">操作</th>
				</tr>
			</thead>
			<tbody>
 <?php  if(is_array($list)) {
	 $j =1;
	 foreach($list as $v) { ?>
								<tr class="one_row">
									<!--<td style="width: 50px;">
										<input class='child_box' type="checkbox" name="openid[]" value="<?php /*echo $v['openid'];*/?>">
									</td>-->
									<td  style="width: 50px;"><?php echo $j++;?></td>
									<td  style="width: 50px;" class="piclist eweima">
										<img openid="<?php echo $v['openid'];?>" src="<?php $url = get_erweima_img($v['openid']); if($url){ echo download_pic($url,100,100,2);} ?>" width="22">
										<div class="big-img-show">
											<img src="">
										</div>
									</td>
									<td class="text-center mobile">
										<?php  echo isSelfAgent($v['mobile'],$v['relation_uid']);?>
									</td>
										<td class="text-center realname">
												<?php  echo $v['realname'];?>
									</td>
                                    <td class="text-center email">
											<?php  echo isSelfAgent($v['QQ'],$v['relation_uid']);?>
									</td>
									<td class="text-center email">
											<?php  echo isSelfAgent($v['wanwan'],$v['relation_uid']);?>
									</td>
									<td class="text-center email">
											<?php  echo isSelfAgent($v['weixin'],$v['relation_uid']);?>
									</td>
									<td class="text-center email">
										<?php  echo getAdminName($v['relation_uid']);?>
									</td>
									<td class="text-center avatar">
										<?php echo $v['platform_name'] ;?>
									</td>
									<td class="text-center">
										<a style="word-break: break-all;" href="<?php echo $v['platform_url'] ;?>" target="_blank"><?php echo $v['platform_url'] ;?></a>
									</td>
									<td class="text-center piclist">
										<?php if(!empty($v['platform_pic'])) {
											$platform_pic = explode(',', $v['platform_pic']);
											foreach($platform_pic as $pic){
												echo "<img src='{$pic}' width='24'/>";
											}
										}
			 							?>
										<div class="big-img-show big-img-show-2">
											<img src="">
										</div>
									</td>
									<td class="text-center">&nbsp;</td>
									<td class="text-center">&nbsp;</td>
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
<script type="text/javascript">
	$(function(){
		$(".piclist img").on("click",function(){
			var bigImg = $(this).attr("src");
			$(".big-img-show").hide();
			$(this).siblings(".big-img-show").fadeIn();
			$(this).siblings(".big-img-show").find("img").attr("src",bigImg);
		});
		$(".big-img-show").on("click",function(){
			$(this).fadeOut();
		});
	})
</script>