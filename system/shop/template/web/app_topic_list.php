<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
	app端专题管理&nbsp;&nbsp;&nbsp;
	<a href="<?php  echo web_url('app_topic',array('op' =>'new'))?>" class="btn btn-primary">添加专题</a>
</h3>

<ul class="nav nav-tabs">
	<li style="width:7%" class="active"><a href="<?php echo web_url('app_topic')?>">专题列表</a></li>
	<li style="width:7%"><a href="<?php echo web_url('app_topic_banner')?>">专题banner</a></li>
</ul>
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center; width: 30px">ID</th>
			<th style="text-align: center;">显示顺序</th>
			<th style="text-align: center;">标题</th>
			<th style="text-align: center;">布局类型</th>
			<th style="text-align: center;">是否显示</th>
			<th style="text-align: center;">创建时间</th>
			<th style="text-align: center;">操作</th>
		</tr>
	</thead>
	<tbody>
        <?php if(is_array($list)) { foreach($list as $value) { ?>
        <tr style="text-align: center;">
			<td><?php echo $value['topic_id'];?></td>
			<td><?php echo $value['displayorder'];?></td>
			<td><?php echo $value['title'];?></td>
			<td>
				<?php  
                    switch ($value['type']){
						case 1:
								echo '布局1';
								break;
						case 2:
								echo '布局2';
								break;
						case 3:
								echo '布局3';
								break;
								
						case 4:
								echo '布局4';
								break;
								
						case 5:
								echo '布局5';
								break;
									
						default:
								echo '未设置';
								break;
                    }
				?>
			</td>
			<td><?php echo empty($value['enabled'])?'否':'是'; ?></td>
			<td><?php echo date('Y-m-d H:i:s',$value['createtime']);?></td>
			<td style="text-align: center;">
				<a class="btn btn-xs btn-info" href="<?php echo web_url('app_topic', array('op' => 'edit', 'topic_id' => $value['topic_id']))?>"><i
					class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> &nbsp;&nbsp;
			</td>
		</tr>
        <?php  } } ?>
     </tbody>
</table>
<?php  echo $pager;?>
<?php  include page('footer');?>