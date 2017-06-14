<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<h3 class="header smaller lighter blue">报名管理</h3>
		<form action=""  class="form-horizontal" method="post">
	<table class="table table-striped table-bordered table-hover">
			<tbody >
				<tr>
				<td>
				<li style="float:left;list-style-type:none;">
						<select  style="margin-right:10px;margin-top:10px;width: 150px; height:34px; line-height:28px; padding:2px 0" name="messid" onchange="fetchChildarea(this.options[this.selectedIndex].value)">
							<option value="0">全部</option>
							<?php  if(is_array($select)) { foreach($select as $row) { ?>
							<?php  if($row['parentid'] == 0) { ?>
							<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GP['messid']) { ?> selected="selected"<?php  } ?>><?php  echo $row['title'];?></option>
							<?php  } ?>
							<?php  } } ?>
						</select>
						</li>
						<li style="list-style-type:none;">
						<button class="btn btn-primary" style="margin-right:10px;margin-top:10px;"><i class="icon-search icon-large"></i> 搜索</button>
						</li>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
	<table class="table table-striped table-bordered table-hover">
  <tr >
 <th class="text-center" >序号</th>
      <th class="text-center">姓名</th>
      <th class="text-center">手机号码</th>
	  <th class="text-center">团购ID</th>
	  <th class="text-center">团购名称</th>
	  <th class="text-center">销售区域</th>
	  <th class="text-center">报名时间</th>
      <th class="text-center" >操作</th>
  </tr>

		<?php $index=0; if(is_array($list)) { $index=$index+1; foreach($list as $item) { ?>
				<tr>
				 <td style="text-align:center;"><?php echo  $index ?></td>         	
				  <td style="text-align:center;"><?php  echo (empty($item['optionname'])?'':$item['optionname']); ?></td>					
                   <td style="text-align:center;"><?php  echo $item['comment'];?></td>
				  <td style="text-align:center;"><?php  echo $item['messid'];?></td>
				  <td style="text-align:center;"><?php  echo $item['title'];?></td>
				  <td style="text-align:center;"><?php echo $item['city'];?></td>
				  <td style="text-align:center;"><?php echo date("Y-m-d",$item['createtime']);?></td>
				  <td style="text-align:center;">
						<a  class="btn btn-xs btn-info" href="<?php  echo web_url('mess', array('id' => $item['id'], 'op' => 'del'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a></a>
					</td>
				</tr>
				<?php  } } ?>
 	
		</table>
		<?php  echo $pager;?>

<?php  include page('footer');?>
