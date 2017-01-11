<?php 
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 百家威信 <QQ:2752555327> <http://www.squdian.com>
// +----------------------------------------------------------------------
defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<style type="text/css">
	.sub-title{
		border: 1px solid #ddd;padding: 7px 0;
	}
	.left-span{
		float: left;
	    line-height: 28px;
	    background-color: #ededed;
	    padding: 0 5px;
	    border: 1px solid #cdcdcd;
	    border-right: 0;
	    font-size: 12px;
	}
	.sub-title-div{
		float: left;    
		margin-right: 10px;
	}
	.sub-title .li-height{
	    height: 30px;
	    padding-left: 5px;
	}
</style>
<h3 class="header smaller lighter blue">会员消费排行</h3>
		<div class="alert alert-info" style="margin:10px 0; width:auto;">
			<i class="icon-lightbulb"></i>  查询有成交记录的会员的订单数和购物金额,并按购物金额从高到低排行。
		</div>
		
		<form action="">
		<input type="hidden" name="mod" value="site" />
				<input type="hidden" name="name" value="addon6" />
				<input type="hidden" name="do" value="memberranking" />
		
<h4 class="sub-title">
	<div class="sub-title-div">
		<span class="left-span">起始日期</span>
		<input name="start_time" id="start_time" type="text" class="li-height" value="<?php  echo empty($start_time)?date('Y-m-d',time()):date('Y-m-d',$start_time);?>" readonly="readonly"  /> 
	</div>
	<div class="sub-title-div">
		<span class="left-span">终止日期</span>
		<input name="end_time" id="end_time" type="text" class="li-height" value="<?php  echo empty($end_time)?date('Y-m-d',time()):date('Y-m-d',$end_time);?>" readonly="readonly"  /> 
	</div>
	<select name="sortname"  style="width:150px;height: 30px">
		<option <?php  if($sortname == 'ordermoney') { ?>selected="selected"<?php  } ?> value="ordermoney">消费金额</option>
		<option <?php  if($sortname == 'ordercount') { ?>selected="selected"<?php  } ?>value="ordercount">订单数</option>
	</select>
	<input type="submit" name="" value=" 查 询 " class="btn btn-primary btn-sm" >&nbsp;
	<button type="submit" name="memberrankingEXP01" value="memberrankingEXP01" class="btn btn-warning btn-primary btn-sm">导出excel</button>
	</h4>
</form>

		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th width="85"  >排行</th>
					<th width="41" >会员手机号</th>
					<th width="41" >会员昵称</th>
					<th width="42" >订单数</th>
					<th width="85" >消费金额</th>
				</tr>
			</thead>
			<tbody>
				<?php  $index=1?>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td><?php  echo $index;?> <?php  if($index==1||$index==2||$index==3) { ?>
						<img  src="<?php  echo WEBSITE_ROOT;?>addons/addon6/images/000<?php  echo $index;?>.gif" style="border-width:0px;">
						<?php  } ?><?php  $index++?></td>
							<td><?php  echo $item['mobile'];?></td>
					<td><?php  echo $item['realname'];?></td>
					<td><?php  echo $item['ordercount'];?></td>
					<td><?php  if(empty($item['ordermoney'])) { ?>0 <?php  } else { ?><?php  echo $item['ordermoney'];?><?php  } ?></td>
				</tr>
				<?php  } } ?>

			</tr>
		</table>
<script type="text/javascript">
		laydate({
	        elem: '#start_time',
	        istime: true, 
	        event: 'click',
	        format: 'YYYY-MM-DD hh:mm:ss',
	        istoday: true, //是否显示今天
	        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	    });
	    laydate({
	        elem: '#end_time',
	        istime: true, 
	        event: 'click',
	        format: 'YYYY-MM-DD hh:mm:ss',
	        istoday: true, //是否显示今天
	        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	    });
	    laydate.skin("molv");
	</script>
<?php  include page('footer');?>