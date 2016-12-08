<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">区域列表</h3>


		<form action="" class="form-horizontal" method="post" onsubmit="return formcheck(this)">
				<table class="table table-striped table-bordered table-hover">
  <tr>
				<tr>
					<th style="width:10px;"></th>
					<th style="width:50px;">显示顺序</th>
					<th style="width:80px;">区域名称</th>
						<th style="width:80px;">状态</th>
					<th style="width:80px;">操作</th>
				</tr>
			<tbody>
			<?php  if(is_array($area)) { foreach($area as $row) { ?>
				<tr>
					<td style="width:10px;"></td>
					<td style="width:50px;"><input type="text"  style="width:50px"  name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
            <td>
              <?php  echo $row['name'];?>
            
            	</td>
           <td>             <?php  if($row['isrecommand']==1) { ?>
                                                <span class='label label-success'>首页推荐</span>
                                                 <?php  } ?><?php  if($row['enabled']==1) { ?>
                                                <span class='label label-success'>显示</span>
                                                <?php  } else { ?>
                                                <span class='label label-danger'>隐藏</span>
                                                <?php  } ?></td>
					<td>
						<?php  if(empty($row['parentid'])) { ?>
          <a class="btn btn-xs btn-info" href="<?php  echo web_url('area', array('parentid' => $row['id'], 'op' => 'post'))?>"><i class="icon-plus-sign-alt"></i> 添加子区域</a><?php  } ?>&nbsp;&nbsp;
					<a class="btn btn-xs btn-info" href="<?php  echo web_url('area', array('op' => 'post', 'id' => $row['id']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
					<a class="btn btn-xs btn-info" href="<?php  echo web_url('area', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此区域吗？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a></td>
				</tr>
				<?php  if(is_array($children[$row['id']])) { foreach($children[$row['id']] as $row) { ?>
				<tr>
					<td style="width:10px;"></td>
					<td style="width:50px;">&nbsp;&nbsp;&nbsp;<input type="text" style="width:50px" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
					
					 <td>&nbsp;&nbsp;&nbsp;<?php  echo $row['name'];?></td>
					<td>
						 <?php  if($row['isrecommand']==1) { ?>
                                                <span class='label label-success'>首页推荐</span>
                                                 <?php  } ?>
						  <?php  if($row['enabled']==1) { ?>
                                                <span class='label label-success'>显示</span>
                                                <?php  } else { ?>
                                                <span class='label label-danger'>隐藏</span>
                                                <?php  } ?></td>
					<td>
					
					<a class="btn btn-xs btn-info" href="<?php  echo web_url('area', array('op' => 'post', 'id' => $row['id'], 'parentid'=>$row['parentid']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>
					&nbsp;&nbsp;<a class="btn btn-xs btn-info" href="<?php  echo web_url('area', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此区域吗？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a></td>
				</tr>
				<?php  } } ?>
			<?php  } } ?>
				<tr>
					<td style="width:10px;"></td>
					<td colspan="4">
						<a href="<?php  echo web_url('area', array('op' => 'post'))?>"><i class="icon-plus-sign-alt"></i> 添加新区域</a>
					</td>
				</tr>
				<tr>
					<td style="width:10px;"></td>
					<td colspan="4">
						<input name="submit" type="submit" class="btn btn-primary" value=" 提 交 ">
					</td>
				</tr>
			</tbody>
		</table>
		</form>
<?php  include page('footer');?>
