<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
	<style>
		.set-balance{
			color:green;
			display:none;
			position: absolute;
			top: 12px;
			right: 25px;
		}
	</style>
        <h3 class="header smaller lighter blue">积分商品列表</h3>

		<table class="table table-striped table-bordered table-hover">
			<thead >
				<tr>
				    <th style="text-align:center;min-width:70px;">礼品图片</th>
					<th style="text-align:center;min-width:100px;">礼品名称</th>
					<th style="text-align:center;min-width:100px;">礼品类型</th>
					<th style="text-align:center; min-width:30px;">总价值</th>
					<th style="text-align:center; min-width:30px;">状态</th>
					<th style="text-align:center; min-width:30px;">开始时间</th>
					<th style="text-align:center; min-width:30px;">排序</th>
					<th style="text-align:center; min-width:30px;">推荐</th>
				    <th style="text-align:center; min-width:50px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($awardlist)) { foreach($awardlist as $item) { ?>
				<tr>
				    <td style="text-align:center;"><img src="<?php echo $item['logo'];?>" width="50" height="50" /></td>
					<td style="text-align:center;white-space: normal;word-break: break-all;max-width: 230px;">
						<?php if($item['add_jifen_change'] == 1 && $config['open_gift_change'] == 1){ $tip = checkHasNewJifenChange($item['id']); echo $tip; }?>
						<?php  echo $item['title'];?>
					</td>
					<td style="text-align:center;">
						<?php
						if ( $item['award_type'] == 1 ){
							echo '自定义礼品';
						}elseif ( $item['award_type'] == 2){
							echo "<font color='red'>优惠卷</font><br/>";
							echo "结束时间:".getBonusSendTime($item['gid']);
						}elseif ( $item['award_type'] == 3){
							echo '自有平台商品';
						}
						?> </td>
					<td style="text-align:center;"><?php  echo $item['price'];?> 元</td>

					<td style="text-align:center;">
					<?php  
					    if ($item['endtime'] > time() ){ 
                             echo '未开始';
                        }else{
							echo '进行中';
						}
					?>
					</td>
					<td style="text-align:center;"><?php  echo date("Y-m-d H:i",$item['endtime']);?> </td>
					<td style="text-align:center;">
						<input type="number" class="set_order" data-id="<?php echo $item['id'];?>" style="width: 65px;text-align: center" value="<?php echo $item['sort'];?>" />
						<i class="set-balance icon-ok-sign"></i>
					</td>
					<td style="text-align:center;">
						<?php if($item['isrecommand'] == 1){ echo "<font color='red'>推荐</font>"; }else{ echo "未推荐";} ?>
					</td>
					<td style="text-align:center;">
						<a class="btn btn-xs btn-info"  href="<?php  echo web_url('editaward', array('id' => $item['id']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>
						<a class="btn btn-xs btn-info"  href="<?php  echo web_url('applyed', array('id' => $item['id'],'op'=>'change'))?>"><i class="icon-search"></i>&nbsp;查&nbsp;看&nbsp;</a>

						<a class="btn btn-xs btn-danger" data-url="<?php  echo web_url('deleteaward', array('id' => $item['id'],'op'=>'remove_jifen'))?>" onclick="change_delete(this)"><i class="icon-edit"></i>移除积分兑换</a>

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

		$(".set_order").blur(function(){
			var sort = $(this).val();
			var id   = $(this).data('id');
			var url = "<?php echo web_url('awardlist',array('op'=>'set_order','name'=>'addon7')); ?>";
			$.post(url,{'id':id,'sort':sort},function(data){
				if(data.errno == 200){
					$this.siblings(".set-balance").show();
					setTimeout(function(){
						$this.siblings(".set-balance").hide();
					},2500);
				}
			},'json');
		})
	</script>
<?php  echo $pager;?>
<?php  include page('footer');?>