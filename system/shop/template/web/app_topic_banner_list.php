<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
	app端专题banner列表&nbsp;&nbsp;&nbsp;
	<a href="<?php  echo web_url('app_topic_banner',array('op' =>'new'))?>" class="btn btn-primary">添加banner</a>
</h3>

<ul class="nav nav-tabs">
	<li style="width:7%"><a href="<?php echo web_url('app_topic')?>">专题列表</a></li>
	<li style="width:7%" class="active"><a href="<?php echo web_url('app_topic_banner')?>">专题banner</a></li>
</ul>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center; width: 30px">ID</th>
			<th style="text-align: center;">显示顺序</th>
			<th style="text-align: center;">图片</th>
			<th style="text-align: center;">所属专题</th>
			<th style="text-align: center;">是否显示</th>
			<th style="text-align: center;">链接</th>
			<th style="text-align: center;">创建时间</th>
			<th style="text-align: center;">操作</th>
		</tr>
	</thead>
	<tbody>
        <?php if(is_array($list)) { foreach($list as $value) { ?>
        <tr style="text-align: center;">
			<td><?php echo $value['banner_id'];?></td>
			<td><?php echo $value['displayorder'];?></td>
			<td><img src="<?php echo $value['thumb'];?>" style="width:150px;height:100px"></td>
			<td><?php echo $value['title'];?></td>
			<td><?php echo empty($value['enabled'])?'否':'是'; ?></td>
			<td><?php echo $value['link'];?></td>
			<td><?php echo date('Y-m-d H:i:s',$value['createtime']);?></td>
			<td style="text-align: center;">
				<a class="btn btn-xs btn-info" href="<?php echo web_url('app_topic_banner', array('op' => 'edit', 'banner_id' => $value['banner_id']))?>"><i
					class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> &nbsp;&nbsp;
				<a class="btn btn-xs btn-info" href="<?php echo web_url('app_topic_banner', array('op' => 'delete', 'banner_id' => $value['banner_id']))?>"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
			</td>
		</tr>
        <?php  } } ?>
     </tbody>
</table>
<?php  echo $pager;?>
<?php  include page('footer');?>