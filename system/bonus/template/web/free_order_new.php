<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
	免单配置列表&nbsp;&nbsp;&nbsp;
</h3>

<div class="alert alert-info" style="margin:10px 0; width:auto;">
	<i class="icon-lightbulb"></i> 每周一可配置上周的免单分类、每期只能设置一个免单分类
</div>

<ul class="nav nav-tabs">
	<li style="width:7%" class="active"><a href="<?php echo web_url('free_order',array('op' =>'new_list'))?>">待配置免单</a></li>
	<li style="width:7%"><a href="<?php echo web_url('free_order')?>">已配置免单</a></li>
</ul>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center; width: 30px">ID</th>
			<th style="text-align: center;">分类</th>
			<th style="text-align: center;">免单期间</th>
			<th style="text-align: center;">免单金额</th>
			<th style="text-align: center;">免单人数</th>
			<th style="text-align: center;">操作</th>
		</tr>
	</thead>
	<tbody>
        <?php if(is_array($arrCategory)) { foreach($arrCategory as $value) { ?>
        <tr style="text-align: center;">
			<td><?php echo $value['id'];?></td>
			<td><?php echo $value['name'];?></td>
			<td><?php echo date('Y-m-d',$period['monday_time']).'  ~  '.date('Y-m-d',$period['sunday_time']); ?></td>
			<td><?php echo getFreeAmount($value['id'],$period['monday_time'],$period['sunday_time']);?></td>
			<td><?php echo getFreeMemberCount($value['id'],$period['monday_time'],$period['sunday_time']);?></td>
			<td>

				<a class="btn btn-xs btn-info" href="javascript:void(0);" onclick="formSubmit('<?php echo $value['id'];?>')"><i class="icon-edit"></i>&nbsp;免&nbsp;单&nbsp;</a>

			</td>
		</tr>
        <?php  } } ?>
     </tbody>
</table>
<?php  echo $pager;?>
<?php  include page('footer');?>

<script type="text/javascript">

	function formSubmit(category_id)
	{
		if(confirm("确定要提交免单设置吗？"))
		{
			location.href= '<?php echo web_url('free_order',array('op' =>'insert','category_id'=>$value['id']))?>'+'&category_id='+category_id;
		}
		else{
			return false;
		}
	}
</script>