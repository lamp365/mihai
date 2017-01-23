<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<div class="customers-wrap" style="margin-top: 20px;min-height: 300px;">
	<div class="panel with-nav-tabs panel-default">	
	    <div class="panel-body third-party">
	        <div class="tab-content">
	            <div class="tab-pane fade in active" id="tab1primary">
		            <form action="<?php  echo web_url('customers',array('op'=>'display'));?>" method="post">
						<ul class="search-ul">
							<li>
								<span class="left-span">城市</span>
								<select name="city" class="city input-height">
									<option value="0" >请选择城市</option>
									<?php  if(is_array($city_a)) { 
	 								foreach($city_a as $cav) {
	 									if ($cav == $city) {
	 										$csed = "selected";
	 									}else{
	 										$csed = "";
	 									}
	 									?>
	 									<option value="<?php  echo $cav;?>" <?php  echo $csed;?>><?php  echo $cav;?></option>
	 								<?php  } } ?>
								</select>
							</li>
							<li>
								<span class="left-span">等级</span>
								<select name="member" class="member input-height">
									<option value="0" >请选择会员等级</option>
			                        <?php  if(is_array($level_a)) { 
	 								foreach($level_a as $lav) { 
	 									if ($lav == $level) {
	 										$lsed = "selected";
	 									}else{
	 										$lsed = "";
	 									}
	 									?>
	 									<option value="<?php  echo $lav;?>" <?php  echo $lsed;?>><?php  echo $lav;?></option>
	 								<?php  } } ?>
								</select>
							</li>
							<li>
								<span class="left-span">店铺</span>
								<select name="shop" class="shop input-height">
									<option value="0" >请选择店铺</option>
			                        <?php  if(is_array($shop_a)) { 
	 								foreach($shop_a as $sav) { 
	 									if ($sav == $shop) {
	 										$ssed = "selected";
	 									}else{
	 										$ssed = "";
	 									}
	 									?>
	 									<option value="<?php  echo $sav;?>" <?php  echo $ssed;?>><?php  echo $sav;?></option>
	 								<?php  } } ?>
								</select>
							</li>
							<?php  if ($is_boos) { ?>
							<li>
								<span class="left-span">负责人</span>
								<select name="department" class="department input-height">
									<option value="0" >请选择</option>
			                        <?php  if(is_array($staff_a)) { 
	 								foreach($staff_a as $manv) { 
	 									if ($manv == $staff) {
	 										$mased = "selected";
	 									}else{
	 										$mased = "";
	 									}
	 									?>
	 									<option value="<?php  echo $manv;?>" <?php  echo $mased;?>><?php  echo $manv;?></option>
	 								<?php  } } ?>
								</select>
							</li>
							<?php } ?>
							<li>
								<span class="left-span">差评</span>
								<div class="checkbox-div">
									<input type="checkbox" name="bad" class="bad" <?php if($review){echo 'checked="checked"';}?>>
								</div>
							</li>
							<li>
								<span class="left-span">退过款</span>
								<div class="checkbox-div">
									<input type="checkbox" name="refund" class="is-refund" <?php if($refund){echo 'checked="checked"';}?>>
								</div>
							</li>
							<li>
								<span class="left-span">黑名单</span>
								<div class="checkbox-div">
									<input type="checkbox" name="blacklist" class="blacklist" <?php if($blacklist){echo 'checked="checked"';}?>>
								</div>
							</li>
							<li>
								<input type="submit" name="submit" value=" 查 询 "  class="btn btn-primary btn-sm">
							</li>
							<?php  if ($is_boos) {
	                    		echo '<li><input type="button" name="button" value=" 分 配 "  class="btn btn-primary btn-sm batch-distribute"></li>';
	                    	}?>
						</ul>
						<div class="panel panel-default third-party-user-list">
				            <table class="table">
					            <thead >
					                <tr>
					                    <!-- <th >旺旺</th> -->
					                    <th width="60px">姓名</th>
					                    <th>手机</th>
					                    <th>邮箱</th>
					                    <th>差评</th>
					                    <th>退过款</th>
					                    <th>黑名单</th>
					                    <th>城市</th>
					                    <th width="150px;">地址</th>
					                    <th>上次购买时间</th>
					                    <th>购买次数</th>
					                    <th>购买金额</th>
					                    <th>会员等级</th>
					                    <th>店铺</th>
					                    <th>分配人员</th>
					                    <th>客户状态</th>
					                    <th>是否联系</th>
					                    <th>操作</th>
					                </tr>
					            </thead>
						        <tbody>
						        <?php  if(is_array($al_client)) { 
	 								foreach($al_client as $almv) { ?>
					                <tr>
					                    <!-- <td class="text-center"><?php  echo $almv['wanwan'];?></td> -->
					                    <td class="text-center"><?php  echo $almv['username'];?></td>
					                    <td class="text-center"><?php  echo $almv['mobile'];?></td>
					                    <td class="text-center"><?php  echo $almv['email'];?></td>
					                    <td class="text-center"><?php  echo $almv['review'];?></td>
					                    <td class="text-center"><?php  echo $almv['refund'];?></td>
					                    <td class="text-center"><?php  echo $almv['blacklist'];?></td>
					                    <td class="text-center"><?php  echo $almv['city'];?></td>
					                    <td class="text-center"><?php  echo $almv['address'];?></td>
					                    <td class="text-center"><?php  echo date('Y-m-d H:i',$almv['lasttime']);?></td>
					                    <td class="text-center"><?php  echo $almv['buytimes'];?></td>
					                    <td class="text-center"><?php  echo $almv['price'];?></td>
					                    <td class="text-center"><?php  echo $almv['level'];?></td>
					                    <td class="text-center"><?php  echo $almv['shop'];?></td>
					                    <td class="text-center staff_name"><?php  echo $almv['name'];?></td>
					                    <td class="text-center"><?php  echo $client_status[$almv['status']];?></td>
					                    <td class="text-center staff_name contact_state" ><?php  echo $contact_status[$almv['contact']];?></td>
					                    <td class="text-center">
					                    	<?php  if ($is_boos) {
					                    		echo '<a class="btn btn-xs btn-info single-distribute" data_id="'.$almv['id'].'" href="javascript:;"><i class="icon-edit">分配</i></a>';
					                    	}?>
						                    &nbsp<a class="btn btn-xs btn-info contact <?php  if ($almv['contact']=='1') {echo 'btn-danger';}?>" data_id="<?php  echo $almv['id'];?>" href="javascript:;"><i class="icon-edit"><?php  if ($almv['contact']=='0') {echo '联系';}else{echo '已联系';}?></i></a>
						                    &nbsp<a class="btn btn-xs btn-info send-message" data_id="<?php  echo $almv['id'];?>" href="javascript:;"><i class="icon-edit">发短信</i></a>
					                    </td>
					                </tr>
					            <?php  } } ?>
					            </tbody>
				            </table>
				        </div>

			        	<div class='modal fade batch-distribute-result' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>  
							<div class='modal-dialog modal-lg'>
								<div class='modal-content'>
									<div class='modal-header'> 
										<button type='button' class='close' data-dismiss='modal'>
											<span aria-hidden='true'>&times;</span>
											<span class='sr-only'>Close</span>
										</button>
										<h4 class='modal-title' class='myModalLabel'>分配数据展示</h4>
									</div>
									<div class='modal-body'>
										<div class="check_allot">
											<span>分配的数量:</span>
											<span class="check_allot_total"></span>
											<span style="margin-left: 15px;">是否要分配</span>
											<span class="btn btn-danger btn-sm check_allot_close">取消</span>
											<span class="btn btn-success btn-sm check_allot_sure">确定</span>
										</div>
									</div>
									<div class="modal-footer">
									    <button class="btn btn-default" type="button" data-dismiss="modal">关闭</button>
									</div>
								</div>
							</div>
						</div>

						<div class='modal fade message-demo' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>  
							<div class='modal-dialog modal-lg'>
								<div class='modal-content'>
									<div class='modal-header'> 
										<button type='button' class='close' data-dismiss='modal'>
											<span aria-hidden='true'>&times;</span>
											<span class='sr-only'>Close</span>
										</button>
										<h4 class='modal-title' class='myModalLabel'>短信模板</h4>
									</div>
									<div class='modal-body'>
										<ul class="message-ul">
											<li>
												<label>
													<input type="radio" name="radio_demo" class="radio_demo">
													<div class="demo-div">我是短信模板</div>
												</label>
											</li>
											<li>
												<label>
													<input type="radio" name="radio_demo" class="radio_demo">
													<div class="demo-div">是短信模板</div>
												</label>
											</li>
											<li>
												<label>
													<input type="radio" name="radio_demo" class="radio_demo">
													<div class="demo-div">短信模板</div>
												</label>
											</li>
										</ul>
									</div>
									<div class="modal-footer">
									    <button class="btn btn-default" type="button" data-dismiss="modal">关闭</button>
									</div>
								</div>
							</div>
						</div>
					</form>
	            </div>
	        </div>
	    </div>
	</div>
</div>
<?php  echo $pager;?>
<script>
$(function(){
	//联系操作
	$(".contact").on("click",function(){
		var $this = $(this);
		var data_id = $this.attr("data_id");
		var url = "<?php  echo web_url('customers',array('op'=>'contact'));?>";
		$.post(url,{data_id:data_id},function(data){
			if( data.message == 1 ){
				$this.addClass("btn-danger");
				$this.find("i").text("已联系");
				$this.parent("td").siblings(".contact_state").text("已联系");
			}else{
				$this.removeClass("btn-danger");
				$this.find("i").text("联系");
				$this.parent("td").siblings(".contact_state").text("未联系");
			}
		},'json');
	});
	//发送短信
	// $(".send-message").on("click",function(){
	// 	var $this = $(this);
	// 	var data_id = $this.attr("data_id");
	// 	$(".message-demo").modal();
	// });
	batchDistribute();
	singleDistribute();
});
//批量分配功能
function batchDistribute(){
	$(".batch-distribute").on("click",function(){
		var city = $(".city").val(),
		    member = $(".member").val(),
		 	shop = $(".shop").val(),
		 	department = $(".department").val(),
		 	bad = $(".checkbox-div .bad").prop("checked"),
		 	refund = $(".checkbox-div .is-refund").prop("checked"),
		 	blacklist = $(".checkbox-div .blacklist").prop("checked"),
		 	url = "<?php  echo web_url('customers',array('op' => 'check_allot'));?>";
		 	if( department == 0){
		 		alert("请选择员工");
		 	}else{
		 		$.post(url,{city:city,member:member,shop:shop,department:department,bad:bad,refund:refund,blacklist:blacklist},function(data){
		 			$(".batch-distribute-result").modal();
		 			$(".check_allot_total").text(data.total);
				},'json');
		 	}
	});
	
	$(".check_allot_close").on("click",function(){
		$('.batch-distribute-result').modal('hide');
	});
	$(".check_allot_sure").on("click",function(){
		var city = $(".city").val(),
		    member = $(".member").val(),
		 	shop = $(".shop").val(),
		 	department = $(".department").val(),
		 	bad = $(".checkbox-div .bad").prop("checked"),
		 	refund = $(".checkbox-div .is-refund").prop("checked"),
		 	blacklist = $(".checkbox-div .blacklist").prop("checked"),
		 	url = "<?php  echo web_url('customers',array('op' => 'allot_all'));?>";
		 	$.post(url,{city:city,member:member,shop:shop,department:department,bad:bad,refund:refund,blacklist:blacklist},function(data){
		 		alert(data.message);
		 		location.reload(true);
		 	},'json');
	});
}
//单个分配功能
function singleDistribute(){
	$(".single-distribute").on("click",function(){
		var department = $(".department").val(),
			data_id = $(this).attr("data_id"),
			$this = $(this),
		 	url = "<?php  echo web_url('customers',array('op' => 'allot_ones'));?>";
		 	if( department == 0){
		 		alert("请选择员工");
		 	}else{
		 		$.post(url,{department:department,data_id:data_id},function(data){
		 			alert(data.message);
		 			if(data.staff_name != ""){
		 				$this.parent("td").siblings(".staff_name").text(data.staff_name);
		 			}
				},'json');
		 	}
	});
}
</script>
<?php  include page('footer');?>