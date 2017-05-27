<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
	免单配置列表&nbsp;&nbsp;&nbsp;
</h3>

<div class="alert alert-info" style="margin:10px 0; width:auto;">
	<i class="icon-lightbulb"></i> 每周一可配置上周的免单分类、每期只能设置一个免单分类
</div>

<ul class="nav nav-tabs">
	<li style="width:7%"><a href="<?php echo web_url('free_order',array('op' =>'new_list'))?>">待配置免单</a></li>
	<li style="width:7%" class="active"><a href="<?php echo web_url('free_order')?>">已配置免单</a></li>
	<li><a href="<?php echo web_url('free_order',array('op' =>'order_finish'))?>">本周交易成功订单</a></li>
	<li><a href="<?php echo web_url('free_order',array('op' =>'free_order_enabled'))?>">免单开启管理</a></li>
</ul>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center; width: 60px">免单ID</th>
			<th style="text-align: center;">分类</th>
			<th style="text-align: center;">免单期间</th>
			<th style="text-align: center;">免单金额</th>
			<th style="text-align: center;">免单人数</th>
			<th style="text-align: center;">创建时间</th>
			<th style="text-align: center;">操作</th>
		</tr>
	</thead>
	<tbody>
        <?php if(is_array($list)) { foreach($list as $value) { ?>
        <tr style="text-align: center;">
			<td><?php echo $value['free_id'];?></td>
			<td><?php echo $value['name'];?></td>
			<td><?php echo date('Y-m-d',$value['free_starttime']).'  ~  '.date('Y-m-d',$value['free_endtime']); ?></td>
			<td><?php echo $value['free_amount'];?></td>
			<td><?php echo $value['free_member_count'];?></td>
			<td><?php echo date('Y-m-d H:i:s',$value['createtime']);?></td>
			<td><a href="<?php echo web_url('free_order',array('op' =>'free_detail','free_id'=>$value['free_id']))?>"><i class="icon-edit"></i>查看详情</a></td>
		</tr>
        <?php  } } ?>
     </tbody>
</table>
<?php  echo $pager;?>
<?php  include page('footer');?>