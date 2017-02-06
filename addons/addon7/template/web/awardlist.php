<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
        <h3 class="header smaller lighter blue">积分商品列表</h3>
		<h5 class="smaller red">提示: 可以删除还没开始的云购产品，不能删除及编辑已经开始的云购产品</h5>
<form action=""  class="form-horizontal" method="post">
	<table class="table table-striped table-bordered table-hover">
			<tbody >
				<tr>
				<td>
					<li style="float:left;list-style-type:none;">
						<select name="state" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
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
				   </li>
						<li style="float:left;list-style-type:none;">
						<button class="btn btn-primary" style="margin-right:10px;margin-top:10px;"><i class="icon-search icon-large"></i> 搜索</button>
						</li>
						<li style="float:left;list-style-type:none;">
						<a class="btn btn-primary" style="margin-right:10px;margin-top:10px;" onclick="updates();"><i class="icon-search icon-large"></i> 更新</a>
						<script>
                             function updates(){
								var url = window.location.href;
                                $.get(url+"&c=update",function(s){
									alert('更新完成');
								});
							 }
						</script>
						</li>
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
					<th style="text-align:center; min-width:150px;">云购明细</th>
				    <th style="text-align:center; min-width:50px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($awardlist)) { foreach($awardlist as $item) { ?>
				<tr>
				    <td style="text-align:center;"><img src="<?php echo $item['imgs'];?>" width="50" height="50" /></td>
					<td style="text-align:center;"><?php  echo $item['title'];?></td>
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
				    <td style="text-align:center;">
					    <?php
                            if ( empty($item['sn']) ){
						      if ($item['states'] != 0){
                                  echo '未开奖';
							  }else{
                                  echo '-';
							  }
				         	}else{
                                echo '中奖号码:'.$item['sn'].'<br/>标准数据'.$item['stext'].'<br/>数据日期'.$item['date'];
							}
						?>
					</td>
						<td style="text-align:center;">
						<a class="btn btn-xs btn-info"  href="<?php  echo web_url('editaward', array('id' => $item['id']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>
						&nbsp;&nbsp;
						<a class="btn btn-xs btn-danger" href="<?php  echo web_url('deleteaward', array('id' => $item['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
                        <a class="btn btn-xs btn-info"  href="<?php  echo web_url('applyed', array('id' => $item['id']))?>"><i class="icon-search"></i>&nbsp;查&nbsp;看&nbsp;</a>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
<?php  echo $pager;?>
<?php  include page('footer');?>