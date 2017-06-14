<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
        <h3 class="header smaller lighter blue">许愿商品列表</h3>
		<h5 class="smaller red">提示: 可以删除还没开始的云购产品，不能删除及编辑已经开始的云购产品</h5>
<form action=""  class="form-horizontal" method="post">
	<table class="table table-striped table-bordered table-hover">
			<tbody >
				<tr>
				<td>
					<li style="float:left;list-style-type:none;">
						<select name="state" onchange="sel_by_state(this)" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
							 <?php
                                  foreach ( $state as $key=>$value ){
									  if ( isset($_GP['state']) && ($key == $_GP['state']) ){
                                         echo "<option selected value='".$key."'>".$value."</option>";
									  }else{
                                         echo "<option value='".$key."'>".$value."</option>";
									  }
                                  }
							 ?>
						</select>
						<select name="isrecommand"  onchange="sel_by_recommand(this)" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
							<option value="-1">全部商品</option>
							<option value="1" <?php if($_GP['isrecommand'] == 1){ echo "selected";} ?>>已推荐</option>
							<option value="0" <?php if(isset($_GP['isrecommand']) && $_GP['isrecommand'] == 0){ echo "selected";} ?>>未推荐</option>
						</select>

						<select name="isrecommand"  onchange="sel_by_addxy(this)" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
							<option value="-1">全部商品</option>
							<option value="1" <?php if($_GP['deleted'] == 1){ echo "selected";} ?>>已移除许愿池</option>
							<option value="0" <?php if(isset($_GP['deleted']) && $_GP['deleted'] == 0){ echo "selected";} ?>>已添加许愿池</option>
						</select>

				   </li>
					<li style="line-height: 50px;">总价值：<?php echo getShareTotalPrice();?>元</li>
					<script>
						function sel_by_state(obj){
							var state = $(obj).val();
							var url = "<?php echo web_url('awardlist',array('name'=>'addon7'));?>";
							url = url + "&state="+state;
							window.location.href = url;
						}
						function sel_by_recommand(obj){
							var isrecommand = $(obj).val();
							var url = "<?php echo web_url('awardlist',array('name'=>'addon7'));?>";
							url = url + "&isrecommand="+isrecommand;
							window.location.href = url;
						}
						function sel_by_addxy(obj){
							var deleted = $(obj).val();
							var url = "<?php echo web_url('awardlist',array('name'=>'addon7'));?>";
							url = url + "&deleted="+deleted;
							window.location.href = url;
						}
					</script>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		<table class="table table-striped table-bordered table-hover">
			<thead >
				<tr>
				    <th style="text-align:center;min-width:70px;">奖品图片</th>
					<th style="text-align:center;min-width:100px;">奖品名称</th>
					<th style="text-align:center;min-width:80px;">总量 / 已参与</th>
					<th style="text-align:center; min-width:30px;">总价值</th>
					<th style="text-align:center; min-width:30px;">心愿数</th>
					<th style="text-align:center; min-width:30px;">状态</th>
					<th style="text-align:center; min-width:30px;">开始时间</th>
					<th style="text-align:center; min-width:150px;">云购明细</th>
				    <th style="text-align:center; min-width:50px;">推荐</th>
				    <th style="text-align:center; min-width:50px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($awardlist)) { foreach($awardlist as $item) { ?>
				<tr>
				    <td style="text-align:center;"><img src="<?php echo $item['logo'];?>" width="50" height="50" /></td>
					<td style="text-align:center;white-space: normal;word-break: break-all;max-width: 230px;">
						<?php  echo $item['title'];?>
					</td>
					<td style="text-align:center;"><?php  echo $item['amount'].' / '.$item['dicount'];?></td>
					<td style="text-align:center;"><?php  echo $item['price'];?> 元</td>
					<td style="text-align:center;"><?php  echo $item['credit_cost'];?> </td>
					<td style="text-align:center;">
					<?php  
					    if ($item['endtime'] > time() ){ 
                             echo '未开始';
                        }else{
							 if ( $item['state'] == 0 ){
								  echo '进行中';
							 }else if($item['state'] == 1){
								 echo '待锁定';
							 }elseif ( $item['state'] == 2){
								  echo "<font color='red'>可以开奖</font>";
							 }elseif ( $item['state'] == 3){
								  echo '已开奖';
							 }else{
								 echo "<font color='blue'>已兑奖</font>";
							 }
						}
					?>
					</td>
					<th style="text-align:center;"><?php  echo date("Y-m-d H:i",$item['endtime']);?> </th>
				    <td style="text-align:center;">
					    <?php
                            if ( empty($item['sn']) ){
						      if ($item['states'] != 0){
                                  echo '未开奖';
							  }else{
                                  echo '-';
							  }
				         	}else{
                                echo '中奖号码:'.$item['sn'].'<br/>标准数据'.$item['stext'].'<br/>开奖日期'.date("Y-m-d H:i",$item['date']);
							}
						?>
					</td>
					<td style="text-align:center;">
						<?php if($item['isrecommand'] == 1){ echo "已推荐";}else{echo "未推荐";} ?>
					</td>
					<td style="text-align:center;">
						<a class="btn btn-xs btn-info"  href="<?php  echo web_url('editaward', array('id' => $item['id']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>
						<a class="btn btn-xs btn-info"  href="<?php  echo web_url('applyed', array('id' => $item['id']))?>"><i class="icon-search"></i>&nbsp;查&nbsp;看&nbsp;</a>
						<?php if($item['deleted'] == 1){  ?>
						<a class="btn btn-xs btn-danger" data-url="<?php  echo web_url('deleteaward', array('id' => $item['id'],'deleted'=>0))?>" onclick="change_delete(this)"><i class="icon-edit"></i>加入许愿池</a>
						<?php }else{  ?>
						<a class="btn btn-xs btn-warning" data-url="<?php  echo web_url('deleteaward', array('id' => $item['id'],'deleted'=>1))?>" onclick="change_delete(this)"><i class="icon-edit"></i>移除许愿池</a>
                       <?php } ?>

					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>

	<script>
		function change_delete(obj){
			var url = $(obj).data('url');
			confirm("确认操作？",'',function(issure){
				if(issure){
					window.location.href = url;
				}
			})
		}
	</script>
<?php  echo $pager;?>
<?php  include page('footer');?>