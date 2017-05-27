<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<style type="text/css">
	.grade-table{
		text-align: center;
	}
	.grade-table th{
		text-align: center;
	}
</style>
<div class="panel with-nav-tabs panel-default" style="margin-top: 20px;">	
	<div class="panel-heading">
	            <ul class="nav nav-tabs">
	                <li class="active"><a href="#tab1primary" data-toggle="tab">基础信息</a></li>
	                <li id="second"><a href="#tab2primary" data-toggle="tab">会员等级</a></li>
	                <li id="third"><a href="#tab3primary" data-toggle="tab">会员特权</a></li>
	            </ul>
	    </div>
	    <div class="panel-body third-party">
	        <div class="tab-content">
	        	<div class="tab-pane fade in active" id="tab1primary">
					<div class="alert alert-info" style="margin:10px 0; width:auto;">
						<i class="icon-lightbulb"></i> 注：送积分的同时也会送相等经验值
					</div><br/>
	            	<form method="post" action="<?php echo web_url('rank',array('op'=>'post_base'));?>" name="">
						<div class="clearfix" style="margin-bottom: 10px;">
							<label class="col-sm-1">每天签到送积分</label>
							<div class="col-sm-2">
								<input class="form-control" type="text" name="every_day_jifen" value="<?php echo $setting['every_day_jifen']; ?>" >
							</div>
						</div>
	            		<div class="clearfix" style="margin-bottom: 10px;">
	            			<label class="col-sm-1">连续签到4天送积分</label>
	            			<div class="col-sm-2">
	            				<input class="form-control" type="text" name="continue_4day_jifen" value="<?php echo $setting['continue_4day_jifen']; ?>">
	            			</div>
	            		</div>
	            		<div class="clearfix" style="margin-bottom: 10px;">
	            			<label class="col-sm-1">连续签到7天送积分</label>
	            			<div class="col-sm-2">
	            				<input class="form-control" type="text" name="continue_7day_jifen" value="<?php echo $setting['continue_7day_jifen']; ?>">
	            			</div>
	            		</div>

	            		<div class="clearfix">
	            			<label class="col-sm-1">&nbsp;</label>
	            			<div class="col-sm-2">
	            				<button type="submit" class="btn btn-primary btn-sm">确定</button>
	            			</div>
	            		</div>
	            		
	            	</form>
	            </div>
	            <div class="tab-pane fade" id="tab2primary">
					<div class="alert alert-info" style="margin:10px 0; width:auto;display: none" id="rank_top_tip">
						<i class="icon-lightbulb"></i> <span class="tip_txt"></span>
					</div>
	            	<h3 class="header smaller lighter blue">会员等级管理&nbsp;&nbsp;&nbsp;<a href="<?php  echo web_url('rank',array('op'=>'detail'));?>" class="btn btn-primary">新建等级</a></h3>
					<table class="table table-striped table-bordered table-hover">
						<thead >
							<tr>
								<th style="text-align:center;">等级(数字)</th>
								<th style="text-align:center;">等级名称</th>
								<th style="text-align:center;">icon</th>
								<th style="text-align:center;">wap_icon</th>
								<th style="text-align:center;">所需经验</th>
								<th style="text-align:center;">排序</th>
								<th style="text-align:center;">操作</th>
							</tr>
						</thead>
						<tbody>
						 <?php  if(is_array($list)) { foreach($list as $v) { ?>
							<tr>
									<td class="text-center">
									<?php  echo $v['rank_level'];?>
								</td>
								<td class="text-center">
									<?php  echo $v['rank_name'];?>
								</td>
								<td class="text-center">
									<?php  if ($v['icon']){ echo "<img width='25' src='{$v['icon']}'/>"; };?>
								</td>
								<td class="text-center">
									<?php  if ($v['wap_icon']){ echo "<img width='25' src='{$v['wap_icon']}'/>"; };?>
								</td>
						<td class="text-center">
									<?php  echo $v['experience'];?>
								</td>
								<td  class="text-center"><input type="text" class="rank_order" style="width: 36px;text-align: center" data-sort="<?php echo $v['sort'];?>" data-id="<?php echo $v['rank_level'];?>" value="<?php echo $v['sort'];?>"></td>
								<td class="text-center">
									&nbsp;&nbsp;<a  class="btn btn-xs btn-info" href="<?php  echo web_url('rank',array('op'=>'detail','rank_level' => $v['rank_level']));?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
									
									&nbsp;&nbsp;<a  class="btn btn-xs btn-info" href="<?php  echo web_url('rank',array('op'=>'del','rank_level' => $v['rank_level']));?>"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>&nbsp;&nbsp;
									</td>
							</tr>
							<?php  } } ?>
						  </tbody>
			    	</table>
	            </div>
	            
	            <div class="tab-pane fade" id="tab3primary">
					<div class="alert alert-info" style="margin:10px 0; width:auto;display: none" id="top_tip">
						<i class="icon-lightbulb"></i> <span class="tip_txt"></span>
					</div>
					<h3 class="header smaller lighter blue">会员特权管理&nbsp;&nbsp;&nbsp;<a href="<?php  echo web_url('rank',array('op'=>'post_priviel'));?>" class="btn btn-primary">新建特权</a></h3>
	            	<form method="post" action="<?php echo web_url('rank',array('name'=>'member','op'=>'set_priviel')) ?>">
		            	<table class="table table-striped table-bordered table-hover grade-table">
		            		<tbody>
		            			<tr>
		            				<th>会员特权</th>
									<?php foreach($list as $row){ echo "<th>{$row['rank_name']}</th>"; } ?>
									<th>排序</th>
		            				<th>操作</th>
		            			</tr>
								<?php foreach($priviel_list as $p_one){ ?>
		            			<tr>
		            				<td><?php if(!empty($p_one['icon'])){ echo "<img src='{$p_one['icon']}' width='22'/>";} ?> <?php echo $p_one['name'];?></td>

									<?php foreach($list as $row){  ?>
									<td>
										<?php
											$id_arr = explode(',',$row['privile']);
											if(in_array($p_one['id'],$id_arr)){
												$check = "checked";
											}else{
												$check = '';
											}
										?>
										<input type="checkbox" <?php echo $check;?>  input_val="<?php echo $row['rank_level']; ?>" value="<?php echo $p_one['id']; ?>" class="grade-check-input grade-check-<?php echo $row['rank_level']; ?>" name="<?php echo "box_ids[{$row['rank_level']}][]"; ?>">
									</td>
		            				<?php } ?>

									<td><input type="text" class="set_order" style="width: 36px;text-align: center" data-sort="<?php echo $p_one['sort'];?>" data-id="<?php echo $p_one['id'];?>" value="<?php echo $p_one['sort'];?>"></td>
									<td>
										<span class="btn btn-xs btn-danger del_this" data-id="<?php echo $p_one['id'];?>">删除</span>
										<a class="btn btn-xs btn-info edit_this" href="<?php echo web_url('rank',array('name'=>'member','op'=>'post_priviel','id'=>$p_one['id']));?>">修改</a>
									</td>
		            			</tr>
								<?php } ?>
		            		</tbody>
		            	</table>
		            	<input type="submit" name="" value="确定"  class="btn btn-primary btn-sm">
	            	</form>
	            </div>
	        </div>
	    </div>
	    
</div>
<?php  echo $pager;?>
<?php  include page('footer');?>


<script type="text/javascript">
$(function(){
	var td_index = 0 ;
	$(".grade-check-input").on("click",function(){
		var $this = $(this);
		td_index =parseInt($this.parent("td").index());
		var ischeck = $this.prop("checked");
		if(ischeck){
			$this.parents("td").siblings("td").each(function(index,ele){
				if( parseInt(index) >= td_index){
					$(ele).find("input").prop("checked",true);
				}
			})
		}else{
			$this.parents("td").siblings("td").each(function(index,ele){
				if( parseInt(index) < td_index){
					$(ele).find("input").prop("checked",false);
				}
			})
		}
	});
})

function locationHash(){
	var home = location.hash;
	if( home == "#tab2primary" ){
		$('#second a').tab('show');
	}else if(home == "#tab3primary" ){
		$('#third a').tab('show');
	}
}
locationHash();

$(".set_order").blur(function(){
	var old_sort = $(this).data('sort');
	var id       = $(this).data('id');
	var new_sort = $(this).val();
	if(new_sort != old_sort){
		var url = "<?php echo web_url('rank',array('name'=>'member','op'=>'set_sort'));?>";
		$.post(url,{'sort':new_sort,'id':id},function(data){
			if(data.errno == 200){
				$("#top_tip .tip_txt").html(data.message);
				$("#top_tip").fadeIn();
				setTimeout(function(){
					$("#top_tip").fadeOut();
				},2000)
			}
		},'json')
	}
});

$(".del_this").click(function(){
	var obj = this;
	confirm("确认删除么？", "", function (isConfirm) {
		if (isConfirm) {
			//after click the confirm
			var id = $(obj).data('id');
			var url = "<?php echo web_url('rank',array('name'=>'member','op'=>'del_privile'));?>";
			url = url + "&id="+id;
			$.post(url,{},function(data){
				if(data.errno == 200){
					$("#top_tip .tip_txt").html(data.message);
					$("#top_tip").fadeIn();
					setTimeout(function(){
						$("#top_tip").fadeOut();
					},2000)
					$(obj).parent().parent().remove();
				}
			},'json')
		}
	}, {confirmButtonText: '确认删除', cancelButtonText: '取消删除', width: 400});
});

$(".rank_order").blur(function(){
	var old_sort = $(this).data('sort');
	var id       = $(this).data('id');
	var new_sort = $(this).val();
	if(new_sort != old_sort){
		var url = "<?php echo web_url('rank',array('name'=>'member','op'=>'rank_sort'));?>";
		$.post(url,{'sort':new_sort,'id':id},function(data){
			if(data.errno == 200){
				$("#rank_top_tip .tip_txt").html(data.message);
				$("#rank_top_tip").fadeIn();
				setTimeout(function(){
					$("#rank_top_tip").fadeOut();
				},2000)
			}
		},'json')
	}
});

</script>