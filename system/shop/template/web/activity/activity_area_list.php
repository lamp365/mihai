<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">区间设置</h3>
		<form action="" class="form-horizontal" method="post" onsubmit="return formcheck(this)">
			<table class="table table-striped table-bordered table-hover">
				<tr>
					<th style="width:10px;"></th>
					<th style="width:80px;">区间代码</th>
					<th style="width:80px;">区间名称</th>
					<th style="width:80px;">节点数量</th>
				</tr>
			<tbody>
			<?php  if(is_array($area_list)) { foreach($area_list as $row) { ?>
				<tr>
				   <td></td>
				   <td><?php echo $row; ?></td>
					<td style="width:10px;"><?php echo $row."小时制";?></td>
                    <td>
                         <?php  echo "[".(24 / $row) ."]个时间区间" ;?>
            	    </td>
				</tr>
			<?php }} ?>
				<tr>
					<td colspan="4">
						<a href="<?php  echo web_url('activity', array('op'=>'area'))?>"><i class="icon-plus-sign-alt"></i>添加新区间</a>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<input name="submit" type="submit" class="btn btn-primary" value=" 提 交 ">
					</td>
				</tr>
			</tbody>
		</table>
		</form>
<?php  include page('footer');?>
