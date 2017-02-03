<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<div class="memberinto-wrap">
	<div class="panel with-nav-tabs panel-default">	
	    <div class="panel-heading">
	            <ul class="nav nav-tabs">
	                <li class="active"><a href="#tab1primary" data-toggle="tab">基础查询</a></li>
	                <li><a href="#tab2primary" data-toggle="tab">批量导入</a></li>
	            </ul>
	    </div>
	    <div class="panel-body third-party">
	        <div class="tab-content">
	            <div class="tab-pane fade in active" id="tab1primary">
	            数据总数：<?php  echo $total;?>&nbsp;已分配：<?php  echo $is_allot;?>&nbsp;&nbsp;未分配：<?php  echo $no_allot;?>&nbsp;&nbsp;已入驻：<?php  echo $is_into;?>&nbsp;&nbsp;未入驻：<?php  echo $no_into;?>
		            <form action="<?php  echo web_url('memberinto',array('op'=>'display'));?>" method="post">
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
							<li>
								<span class="left-span">部门</span>
								<select name="department" class="department input-height">
									<option value="0" >请选择部门</option>
			                        <?php  if(is_array($manager_a)) { 
	 								foreach($manager_a as $manv) { 
	 									if ($manv == $manager) {
	 										$mased = "selected";
	 									}else{
	 										$mased = "";
	 									}
	 									?>
	 									<option value="<?php  echo $manv;?>" <?php  echo $mased;?>><?php  echo $manv;?></option>
	 								<?php  } } ?>
								</select>
							</li>
							<li>
								<span class="left-span">金额范围</span>
								<input type="text" name="d_money" class="d_money input-height" placeholder="最低金额" value="<?php echo $d_money;?>"> ~ <input type="text" name="h_money" class="h_money input-height" placeholder="最高金额" value="<?php echo $h_money;?>">
							</li>
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
								<span class="left-span">未分配</span>
								<div class="checkbox-div">
									<input type="checkbox" name="allot" class="allot" <?php if($allot){echo 'checked="checked"';}?>>
								</div>
							</li>
							<li>
								<span class="left-span">未入驻</span>
								<div class="checkbox-div">
									<input type="checkbox" name="ienter" class="ienter" <?php if($ienter){echo 'checked="checked"';}?>>
								</div>
							</li>
							<li>
								<input type="submit" name="submit" value=" 查 询 "  class="btn btn-primary btn-sm">
							</li>
							<li>
								<input type="button" name="button" value=" 分 配 "  class="btn btn-primary btn-sm batch-distribute">
							</li>
						</ul>
						<div class="panel panel-default third-party-user-list">
				            <table class="table">
					            <thead >
					                <tr>
					                    <!-- <th>旺旺</th> -->
					                    <th width="60px">姓名</th>
					                    <th>手机</th>
					                    <th>邮箱</th>
					                    <th>差评</th>
					                    <th>退过款</th>
					                    <th>黑名单</th>
					                    <th>城市</th>
					                    <th width="150px">地址</th>
					                    <th>上次购买时间</th>
					                    <th>购买次数</th>
					                    <th>购买金额</th>
					                    <th>会员等级</th>
					                    <th>店铺</th>
					                    <th>分配人员</th>
					                    <th>客户状态</th>
					                    <th>操作</th>
					                </tr>
					            </thead>
						        <tbody>
						        <?php  if(is_array($al_member)) { 
	 								foreach($al_member as $almv) { ?>
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
					                    <td class="text-center manager_name"><?php  echo $almv['salesman'];?></td>
					                    <td class="text-center"><?php  echo $almv['status'];?></td>
					                    <td class="text-center"><a  class="btn btn-xs btn-info edit-distribute" data_id="<?php echo $almv['id'];?>" href="javascript:;"><i class="icon-edit"></i>分配</a></td>
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

					</form>
					<?php  echo $pager;?>
	            </div>
	            <div class="tab-pane fade" id="tab2primary">
		            <form action="" method="post" class="form-horizontal refund_form" enctype="multipart/form-data">
						<table  class="table dummy-table-list" align="center">
							<tbody>
								<tr>
									<td>
										<li style="line-height: 26px;">用户表单：</li>
										<li >
											<input style="line-height: 26px;" name="myxls" type="file"   value="" />
										</li>
										<li >
											<button type="button" class="refund btn btn-md btn-warning btn-sm">开始导入</button>
										</li>
									</td>
								</tr>	
							</tbody>		
						</table>
					</form>
	            </div>
	        </div>
	    </div>
	</div>
</div>
<script>
$(function(){
	// $(".city").select2();
	$(".refund").click(function(){
		if(confirm('确定开始导入')){
			var url = "<?php  echo web_url('memberinto',array('op'=>'into'));?>";
			$(".refund_form").attr('action',url);
			$(".refund_form").submit();
		}
	});
	
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
		 	d_money = $(".d_money").val();
		 	h_money = $(".h_money").val();
		 	allot = $(".checkbox-div .allot").prop("checked");
		 	ienter = $(".checkbox-div .ienter").prop("checked");

		 	url = "<?php  echo web_url('memberinto',array('op' => 'check_allot'));?>";
		 	if( department == 0){
		 		alert("请选择部门");
		 	}else{
		 		$.post(url,{city:city,member:member,shop:shop,department:department,bad:bad,refund:refund,blacklist:blacklist,d_money:d_money,h_money:h_money,allot:allot,ienter:ienter},function(data){
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
		 	d_money = $(".d_money").val();
		 	h_money = $(".h_money").val();
		 	allot = $(".checkbox-div .allot").prop("checked");
		 	ienter = $(".checkbox-div .ienter").prop("checked");

		 	url = "<?php  echo web_url('memberinto',array('op' => 'allot_all'));?>";
		 	$.post(url,{city:city,member:member,shop:shop,department:department,bad:bad,refund:refund,blacklist:blacklist,d_money:d_money,h_money:h_money,allot:allot,ienter:ienter},function(data){
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
		 	url = "<?php  echo web_url('memberinto',array('op' => 'allot_ones'));?>",
		 	$this = $(this);
		 	if( department == 0){
		 		alert("请选择部门");
		 	}else{
		 		$.post(url,{department:department,data_id:data_id},function(data){
		 			alert(data.message);
		 			// location.reload(true);
		 			if(data.manager_name != ""){
		 				$this.parent("td").siblings(".manager_name").text(data.manager_name);
		 			}
				},'json');
		 	}
	});
}
</script>
<?php  include page('footer');?>