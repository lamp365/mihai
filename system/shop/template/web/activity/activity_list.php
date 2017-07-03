<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">活动列表&nbsp;&nbsp;&nbsp;<a href="<?php  echo web_url('activity', array('name'=>'shop','op'=>'add'));?>" class="btn btn-primary">添加活动</a></h3>
<table class="table table-striped table-bordered table-hover">
	<thead>
	<tr>
    <th class="text-center" >ID</th>
    <th class="text-center" >活动名称</th>
    <th class="text-center">开始时间</th>
    <th class="text-center">结束时间</th>
	<th class="text-center">时间区间</th>
    <th class="text-center">商品数量</th>
	<th class="text-center">商家数量</th>
	<th class="text-center">状态</th>
    <th class="text-center">操作</th>
	</tr>
	</thead>
		  <?php  if(is_array($act_list)) { foreach($act_list as $item) { ?>
		  <tr>
		  <td class="text-center"><?php echo $item['ac_id']; ?></td>
		  <td class="text-center"><?php echo $item['ac_title']; ?></td>
          <td class="text-center"><?php echo $item['ac_time_str']; ?></td>
		  <td class="text-center"><?php echo $item['ac_time_end']; ?></td>
		  <td class="text-center"><?php echo $item['ac_area']."小时间隔"; ?></td>
		  <td class="text-center"><?php echo $item['ac_dish_num']; ?></td>
          <td class="text-center"><?php echo $item['ac_shop_num']; ?></td>
          <td class="text-center"><?php echo  $item['ac_time_info'];?></td>
          <td class="text-center">
                   <a class="btn btn-xs btn-info"  href="<?php  echo web_url('activity', array('name' => 'shop','op'=>'add','id'=>$item['ac_id']))?>"><i class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a>
			  		<a class="btn btn-xs btn-success" href="<?php echo web_url('activity',array('op'=>'showdish','ac_id'=>$item['ac_id'])); ?>">查看列表</a>
			      <?php if ( $item['status'] != 2 ) { ?>
                   <a class="btn btn-xs btn-info" onclick="return confirm('此操作不可恢复，确认删除？');return false;"  href="<?php  echo web_url('activity', array('name' => 'shop','op'=>'delete','id'=>$item['ac_id']))?>"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
				   <?php } ?>
				   </td>
          </td>
	      </tr>
		  <?php  } } ?>
		</table>

<?php  include page('footer');?>
								