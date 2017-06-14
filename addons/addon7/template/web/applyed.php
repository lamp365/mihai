<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<div class="panel with-nav-tabs panel-default" style="margin-top: 20px">	
	    <div class="panel-heading">
	            <ul class="nav nav-tabs">
	                <li <?php if ($_GP['op'] =='xinyuan' || empty($_GP['op'])) {echo 'class="active"';}?> ><a href="<?php echo web_url('applyed',array('id'=>$_GP['id'],'op'=>'xinyuan'));?>" >心愿记录</a></li>
					<li <?php if ($_GP['op'] == 'change') {echo 'class="active"';}?> ><a href="<?php echo web_url('applyed',array('id'=>$_GP['id'],'op'=>'change'));?>" >兑换记录</a></li>
	            </ul>
	    </div>
	    <div class="panel-body third-party">
	        <div class="tab-content">
	    <?php  if($_GP['op'] == 'xinyuan' || empty($_GP['op'])){  ?>

	            <div class="tab-pane fade in active">
	            	<h3 class="header smaller lighter blue" style="display: inline-block">中奖者</h3>&nbsp;&nbsp;
					<span>开奖时间：
						<?php if($win['state'] <2) {
								echo "尚未锁定";
							}else {
								echo date("Y-m-d H:i",$win['date']);
							}
						?>
					</span>
						<table class="table table-striped table-bordered table-hover">
							<thead >
								<tr>
									<th style="text-align:center;min-width:100px;">奖品图片</th>
									<th style="text-align:center;min-width:30px;">中奖产品</th>
								    <th style="text-align:center; min-width:60px;">中奖号码</th>
								    <th style="text-align:center; min-width:80px;">中奖者</th>
									<th style="text-align:center; min-width:80px;">手机号码</th>
									<th style="text-align:center; min-width:180px;">配送地址</th>
									<th style="text-align:center; min-width:80px;">兑奖方式</th>
									<th style="text-align:center; min-width:60px;">状态</th>
								</tr>
							</thead>
							
							<tbody>
								<?php  if(is_array($win)) {  ?>
								<tr>
									<td style="text-align:center;"><img src="<?php  echo $win['logo'];?>" height="50" /></td>
										<td style="text-align:center;"><?php  echo $win['title'];?></td>
										<td style="text-align:center;">
										<?php 
										if ( $win['state'] >2 ){
										  echo $win['sn'];
							            }else{
				                          echo '-';
										}
										?>
										</td>
										<td style="text-align:center;"><?php 
										if ( $win['state'] >2 ){
										  echo $win['name'];
							            }else{
				                          echo '-';
										}
										?></td>
										<td style="text-align:center;"><?php 
										if ( $win['state'] >2 ){
										  echo $win['mobile'];
							            }else{
				                          echo '-';
										}
										?></td>
										<td style="text-align:center;"><?php 
										if ( $win['state'] >2 ){
										  echo $win['address'];
							            }else{
				                          echo '-';
										}
										?></td>
										<td style="text-align:center;"><?php 
										if ( $win['state'] >2 ){
											if(empty($win['shiptype'])){

											}else if ( $win['shiptype'] == 'xian_chan' ){
				                              echo '现场颁奖';
										   }else{
											   echo "物流{$win['shipstr']}号码<a target='_blank' href='http://m.kuaidi100.com/index_all.html?type={$win['shiptype']}&amp;postid={$win['shipping']}#input'>{$win['shipping']}</a>";
										   }
							            }else{
											echo '-';
										}
										?></td>
										<td style="text-align:center;"><?php 
										if ( $win['state'] == 4 ){
										  echo '已兑奖';
							            }elseif ($win['state'] == 3){
				                          echo '未兑奖';
										}else if($win['state'] == 2){
											echo "<font color='red'>等待开奖</font>";
										}else{
				                          echo '进行中';
										}
										?></td>
								</tr>
								<?php   } ?>
							</tbody>
				</table>
				<?php  if($win['state'] == 3) {  ?>
				<form action="" method="post" onsubmit="return check()">
				<table class="table table-striped table-bordered table-hover">
							<thead >
								<tr>
									<th style="text-align:center;max-width:100px;">领取方式</th>
									<th style="text-align:center;min-width:100px;">物流单号</th>
									<th style="text-align:center;min-width:100px;">中奖者</th>
									<th style="text-align:center;min-width:100px;">手机号码</th>
									<th style="text-align:center;min-width:100px;">配送地址</th>
									<th style="text-align:center; min-width:80px;">操作</th>
								</tr>
							</thead>
							<tbody>
								<tr>
								    <td style="text-align:center;">
									<select id="type" name="type" onchange="getShipStr(this)">
									    <option value="xian_chan">现场颁奖</option>
										<?php foreach($dispatchlist as $wuliu){
											echo "<option value='{$wuliu['code']}'>{$wuliu['name']}</option>";
										} ?>
									</select>
										<input type="hidden" name="shipstr" id="shipstr" value="">
									</td>

									<td style="text-align:center;"><input type="text" name="shipping" id="shipping" value="" /></td>
									<td style="text-align:center;"><input type="text" name="draw_name" id="draw_name" value="" /></td>
									<td style="text-align:center;"><input type="text" name="draw_mobile" id="draw_mobile" value="" /></td>
									<td style="text-align:center;">
										<textarea name="draw_address" id="draw_address" cols="50" rows="2"></textarea>
										<input type="hidden" name="draw_id" value="<?php echo $winer['id'];?>">
							        </td>

									<td><input type="submit" value="兑奖" /></td>
								</tr>
								
							</tbody>
				</table>
				<script>

				  function check(){
				     if ($("#type").val() != 'xian_chan')
				     {
						 if (!$("#shipping").val())
						 {   
							 alert('请输入物流单号');
							 return false;
						 }
						 if($("#draw_address").val() == ''){
							 alert('请输入配送地址！');
							 return false;
						 }
				     }
					 return true;
				  }
					function getShipStr(obj){
						if($(obj).val() != 'xian_chan'){
							var index = obj.selectedIndex; // 选中索引
							var text = obj.options[index].text; // 选中文本
							var wuliu_str = $.trim(text);
							$("#shipstr").val(wuliu_str);
						}else{
							$("#shipstr").val('');
						}
					}
				</script>
				</form>
				<?php   } ?>
				<h3 class="header smaller lighter blue">云购记录</h3>
						<table class="table table-striped table-bordered table-hover">
							<thead >
								<tr>
									<th style="text-align:center;max-width:100px;">序号</th>
									<th style="text-align:center;min-width:100px;">心愿数字</th>
				<!--					<th style="text-align:center;min-width:30px;">购买份数</th>-->
									<th style="text-align:center;min-width:30px;">购买时间</th>
								    <th style="text-align:center; min-width:60px;">姓名</th>
								    <th style="text-align:center; min-width:60px;">微信名</th>
								    <th style="text-align:center; min-width:80px;">电话</th>
									<th style="text-align:center; min-width:180px;">地址</th>
								</tr>
							</thead>
							
							<tbody>
								<?php  if(is_array($awardlist)) { foreach($awardlist as $key=>$item) { ?>
								<?php if($win['sn'] == $item['star_num_order']){ ?>
								<tr style="font-weight: bolder;color: red">
								<?php }else{  ?>
								<tr>
								<?php }  ?>
									<td style="text-align:center;"><?php  echo ++$key;?></td>
									<td style="text-align:center;"><?php  echo $item['star_num_order'];?></td>

				<!--						<td style="text-align:center;">--><?php // echo $item['count'];?><!--</td>-->
										<td style="text-align:center;"><?php  echo date("Y-m-d H:i:s",$item['createtime']);?></td>
										<td style="text-align:center;"><?php  echo $item['pc_name'];?></td>
										<td style="text-align:center;"><?php  echo $item['wx_name'];?></td>
										<td style="text-align:center;"><?php  echo $item['mobile'];?></td>
										<td style="text-align:center;"><?php  echo $item['address'];?></td>
								</tr>
								<?php  } } ?>
							</tbody>
						</table>
				        <?php echo $pager; ?>
	            </div>
	    <?php }else if($_GP['op'] == 'change'){ ?>
            <div class="tab-pane fade in active" >
	            <h3 class="header smaller lighter blue" style="display: inline-block">兑换礼品</h3>
						<table class="table table-striped table-bordered table-hover">
							<thead >
								<tr>
									<th style="text-align:center;min-width:100px;">礼品图片</th>
									<th style="text-align:center;min-width:30px;">礼品标题</th>
									<th style="text-align:center;min-width:30px;">礼品类型</th>
								    <th style="text-align:center; min-width:60px;">兑换积分</th>
								    <th style="text-align:center; min-width:60px;">兑换人数</th>
								</tr>
							</thead>
							
							<tbody>
								<?php  if(is_array($win)) {  ?>
								<tr>
									<td style="text-align:center;"><img src="<?php  echo $win['logo'];?>" height="50" /></td>
									<td style="text-align:center;"><?php  echo $win['title'];?></td>
									<td style="text-align:center;">
										<?php
										if ( $win['award_type'] == 1 ){
											echo '自定义礼品';
										}elseif ( $win['award_type'] == 2){
											echo "<font color='red'>优惠卷</font>";
										}elseif ( $win['award_type'] == 3){
											echo '自有平台商品';
										}
										?>
									</td>
									<td style="text-align:center;"><?php  echo $win['jifen_change'];?></td>
									<td style="text-align:center;"><?php  echo $total;?></td>
								</tr>
								<?php   } ?>
							</tbody>
				</table>

				<h3 class="header smaller lighter blue">兑换记录</h3>
				<li style="float:left;list-style-type:none;margin-bottom: 15px;">
					<select name="state" onchange="sel_by_status(this)" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
						<option value="0" <?php if($_GP['status'] ==0){ echo "selected";}?>>全部记录</option>
						<option value="2" <?php if($_GP['status'] ==2){ echo "selected";}?>>兑换成功</option>
						<option value="3" <?php if($_GP['status'] ==3){ echo "selected";}?>>兑换失败</option>
						<option value="1" <?php if($_GP['status'] ==1){ echo "selected";}?>>正在申请</option>
					</select>
					&nbsp;
					<?php if($win['award_type'] == 2){ ?>
						<span class="btn btn-md btn-primary"  id="bat_success">批量审阅</span>
					<?php }else { ?>
						<span class="btn btn-md btn-primary"  id="bat_success">批量审核成功</span>
						<span class="btn btn-md btn-danger" id="bat_fail">批量审核失败</span>
					<?php } ?>

				</li>
				<br/>
						<table class="table table-striped table-bordered table-hover">
							<thead >
								<tr>
									<th style="text-align:center;max-width:40px;"><label for="choose_all"><input type="checkbox" value="" id="choose_all">全选</label></th>
									<th style="text-align:center;max-width:80px;">序号</th>
									<th style="text-align:center;max-width:120px;">兑换时间</th>
								    <th style="text-align:center; max-width:90px;">姓名</th>
								    <th style="text-align:center; max-width:90px;">微信名</th>
								    <th style="text-align:center; max-width:90px;">电话</th>
									<th style="text-align:center; min-width:180px;">地址</th>
									<th style="text-align:center; min-width:120px;">操作</th>
								</tr>
							</thead>
							
							<tbody>
								<?php  if(is_array($awardlist)) { foreach($awardlist as $key=>$item) { ?>
								<tr>
									<td style="text-align:center;">
										<?php if($item['status'] != 1){ $dis="disabled";}else{ $dis = '';} ?>
										<input type="checkbox" name="id" value="<?php echo $item['id'];?>" class="choose_son" <?php echo $dis; ?>>
									</td>
									<td style="text-align:center;"><?php  echo ++$key;?></td>
									<td style="text-align:center;"><?php  echo date("Y-m-d H:i:s",$item['createtime']);?></td>
									<td style="text-align:center;"><?php  echo $item['pc_name']."({$item['m_mobile']})";?></td>
									<td style="text-align:center;"><?php  echo $item['wx_name'];?></td>
									<td style="text-align:center;"><?php  echo $item['mobile'];?></td>
									<td style="text-align:center;"><?php  echo "<font color='red'>{$item['credit']}</font> ".$item['address'];?></td>
									<td style="text-align:center;">
										<span class="btn btn-xs btn-info">修改地址</span>
										<?php if($item['status'] == 1){ ?>
											<?php if($win['award_type'] == 2){ ?>
												<a class="btn btn-xs btn-primary" href="<?php echo web_url('applyed',array('id'=>$item['id'],'status'=>2,'op'=>'checked'));?>">已经审阅</a>
											<?php }else{ ?>
												<a class="btn btn-xs btn-primary" href="<?php echo web_url('applyed',array('id'=>$item['id'],'status'=>2,'op'=>'checked'));?>">审核成功</a>
												<a class="btn btn-xs btn-warning" href="<?php echo web_url('applyed',array('id'=>$item['id'],'status'=>3,'op'=>'checked'));?>">审核失败</a>
											<?php }?>

										<?php }else if($item['status'] == 2){ ?>
											<span class="btn btn-xs btn-success">兑换成功</span>
										<?php }else if($item['status'] == 3){?>
											<span class="btn btn-xs btn-danger">兑换失败</span>
										<?php } ?>
									</td>
								</tr>
								<?php  } } ?>
							</tbody>
						</table>
				        <?php echo $pager; ?>
	            </div>
			<script>
				function sel_by_status(obj){
					var status = $(obj).val();
					var url = "<?php echo web_url('applyed',array('id'=>$_GP['id'],'op'=>'change'));?>";
					url = url+"&status="+status;
					window.location.href = url;
				}
				$("#choose_all").click(function(){
					var ischeck = this.checked;
					if(ischeck){
						$(".choose_son").each(function(index,thisObj){
							if($(thisObj).prop("disabled")==false){
								$(thisObj).prop('checked',true);
							}
						});
					}else{
						$(".choose_son").prop('checked',false);
					}
				})
				$("#bat_success").click(function(){
					confirm('确认批量审核','',function(isconfirm){
						if(isconfirm){
							var id_arr = [];
							$(".choose_son").each(function(index,thisObj){
								if($(thisObj).prop("checked")==true){
									id_arr.push($(thisObj).val());
								}
							});
							if(id_arr.length<1){
								alert('请先选择要操作的兑换记录');
								return '';
							}
							var url = "<?php echo web_url('applyed',array('op'=>'checked','status'=>2));?>";
							$.post(url,{'id':id_arr},function(data){
								var errno = data.errno;
								alert(data.message,'',function(){
									if(errno == 200){
										window.location.reload();
									}
								},{type:'success'});
							},"json")
						}
					},{confirmButtonText: '确认审核', cancelButtonText: '稍后审核', width: 400});
				})
				$("#bat_fail").click(function(){
					confirm('确认批量审核','',function(isconfirm){
						if(isconfirm){
							var id_arr = [];
							$(".choose_son").each(function(index,thisObj){
								if($(thisObj).prop("checked")==true){
									id_arr.push($(thisObj).val());
								}
							});
							if(id_arr.length<1){
								alert('请先选择要操作的兑换记录');
								return '';
							}
							var url = "<?php echo web_url('applyed',array('op'=>'checked','status'=>3));?>";
							$.post(url,{'id':id_arr},function(data){
								var errno = data.errno;
								alert(data.message,'',function(){
									if(errno == 200){
										window.location.reload();
									}
								},{type:'success'});
							},"json")
						}
					},{confirmButtonText: '确认审核', cancelButtonText: '稍后审核', width: 400});
				})
			</script>

	    <?php } ?>
	        </div>
	    </div>

<?php  include page('footer');?>