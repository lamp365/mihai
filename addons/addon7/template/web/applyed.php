<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue" style="display: inline-block">中奖者</h3>&nbsp;&nbsp;<span>开奖时间：<?php  echo date("Y-m-d H:i",$win['date']);?></span>
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
<?php  include page('footer');?>