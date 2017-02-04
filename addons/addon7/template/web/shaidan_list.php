<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
	心愿晒单&nbsp;&nbsp;&nbsp;<a href="<?php echo web_url('shaidan',array('op'=>'post')) ?>" class="btn btn-primary btn-sm">添加晒单</a>
</h3>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center; width: 30px">ID</th>
			<th style="text-align: center; width: 70px">商品ID</th>
			<th style="text-align: center;">标题</th>
			<th style="text-align: center;">缩略图</th>
			<th style="text-align: center;">作者</th>
			<th style="text-align: center;">创建时间</th>
			<th style="text-align: center;">操作</th>
		</tr>
	</thead>
	<tbody>
        <?php if(is_array($article_list)) { foreach($article_list as $value) { ?>
        <tr style="text-align: center;">
			<td><?php echo $value['id'];?></td>
			<td><?php echo $value['award_id'];?></td>
			<td><?php echo $value['title'];?></td>
			<td><img src="<?php echo download_pic($value['thumb'],50,50,2);?>"/></td>
			<td><?php $member = member_get($value['openid']); echo empty($member['realname'])? $member['mobile']:$member['realname']; ?></td>
			<td><?php echo date('Y-m-d H:i:s',$value['createtime']);?></td>
			<td style="text-align: center;">
				<a class="btn btn-xs btn-info" href="<?php echo web_url('shaidan', array('op' => 'post', 'id' => $value['id']))?>"><i
					class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> &nbsp;&nbsp;
				<?php if($value['is_top'] == 1){  ?>
					<a class="btn btn-xs btn-success settop" data-top="1" data-url="<?php echo web_url('shaidan', array('op' => 'settop', 'id' => $value['id']))?>">取消置顶</a>
		       <?php }else{ ?>
					<a class="btn btn-xs btn-info settop" data-top="0" data-url="<?php echo web_url('shaidan', array('op' => 'settop', 'id' => $value['id']))?>">设置置顶</a>
		       <?php } ?>
			</td>
		</tr>
        <?php  } } ?>
     </tbody>
</table>
	<script>
		$(".settop").click(function(){
			var obj = this;
			var is_top = $(this).data('top');
			var url    = $(this).data('url');
			$.post(url,{'is_top':is_top},function(data){
				if(is_top == 1){
					$(obj).data('top',0);
					$(obj).html('设置置顶');
					$(obj).removeClass('btn-success');
					$(obj).addClass('btn-info');
				}else{
					$(obj).data('top',1);
					$(obj).html('取消置顶');
					$(obj).removeClass('btn-info');
					$(obj).addClass('btn-success');
				}

			})
		})
	</script>
<?php  echo $pager;?>
<?php  include page('footer');?>