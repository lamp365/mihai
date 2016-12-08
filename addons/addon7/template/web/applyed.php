<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">中奖者</h3>
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
					<td style="text-align:center;"><img src="<?php  echo $win['thumb'];?>" height="50" /></td>
						<td style="text-align:center;"><?php  echo $win['title'];?></td>
						<td style="text-align:center;">
						<?php 
						if ( $win['state'] >=2 ){
						  echo $win['sn'];
			            }else{
                          echo '-';
						}
						?>
						</td>
						<td style="text-align:center;"><?php 
						if ( $win['state'] >=2 ){
						  echo $win['name'];
			            }else{
                          echo '-';
						}
						?></td>
						<td style="text-align:center;"><?php 
						if ( $win['state'] >=2 ){
						  echo $win['mobile'];
			            }else{
                          echo '-';
						}
						?></td>
						<td style="text-align:center;"><?php 
						if ( $win['state'] >=2 ){
						  echo $win['address'];
			            }else{
                          echo '-';
						}
						?></td>
						<td style="text-align:center;"><?php 
						if ( $win['state'] > 2 ){
						   if ( $win['shiptype'] == 1 ){
                              echo '物流号码'.$win['shipping'];
						   }else{
                              echo '现场颁奖';
						   }
			            }
						?></td>
						<td style="text-align:center;"><?php 
						if ( $win['state'] > 2 ){
						  echo '已兑奖';
			            }elseif ($win['state'] == 2){
                          echo '未兑奖';
						}else{
                          echo '进行中';
						}
						?></td>
				</tr>
				<?php   } ?>
			</tbody>
</table>
<?php  if($win['state'] == 2) {  ?>
<form action="" method="post" onsubmit="return check()">
<table class="table table-striped table-bordered table-hover">
			<thead >
				<tr>
					<th style="text-align:center;min-width:30px;">领取方式</th>
					<th style="text-align:center;min-width:100px;">物流单号</th>
					<th style="text-align:center; min-width:80px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				    <td style="text-align:center;">
					<select id="type" name="type" onchange="setype(this);">
					   <option value="1">物流配送</option>
					    <option value="2">现场颁奖</option>
					</select>
					</td>
										<td style="text-align:center;"><input type="text" name="shipping" id="shipping" value="" /></td>

					<td><input type="submit" value="兑奖" /></td>
				</tr>
				
			</tbody>
</table>
<script>
   function setype(obj){
	    if (obj.value == 2)
	    {
			$("#shipping").attr("disabled","true");
	    }else{
            $("#shipping").removeAttr("disabled");
		}
  }
  function check(){
     if ($("#type").val() == 1)
     {
		 if (!$("#shipping").val())
		 {   
			 alert('请输入物流单号');
			 return false;
		 }
     }
	 return true;
  }
</script>
</form>
<?php   } ?>
<h3 class="header smaller lighter blue">云购记录</h3>
		<table class="table table-striped table-bordered table-hover">
			<thead >
				<tr>
					<th style="text-align:center;min-width:100px;">云购号码</th>
					<th style="text-align:center;min-width:30px;">购买份数</th>
					<th style="text-align:center;min-width:30px;">购买时间</th>
				    <th style="text-align:center; min-width:60px;">姓名</th>
				    <th style="text-align:center; min-width:80px;">电话</th>
					<th style="text-align:center; min-width:180px;">地址</th>
				</tr>
			</thead>
			
			<tbody>
				<?php  if(is_array($awardlist)) { foreach($awardlist as $item) { ?>
				<tr>
					<td style="text-align:center;"><?php  echo $item['sn'];?></td>
						<td style="text-align:center;"><?php  echo $item['count'];?></td>
						<td style="text-align:center;"><?php  echo date("Y-m-d",$item['createtime']);?></td>
						<td style="text-align:center;"><?php  echo $item['realname'];?></td>
						<td style="text-align:center;"><?php  echo $item['mobile'];?></td>
						<td style="text-align:center;"><?php  echo $item['address'];?></td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
        <?php echo $pager; ?>
<?php  include page('footer');?>