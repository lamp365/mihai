<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<style>
	.nav-tabs li a{
		padding: 6px 22px;
	}
</style>
<br/>
<ul class="nav nav-tabs" >
	<li style="" <?php  if($_GP['do'] == 'disharea') { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'disharea','op' => 'display'))?>">运费管理</a></li>
	<li style="" <?php  if($_GP['do'] == 'promotion')  { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'promotion','do'=>'promotion','op' => 'display'))?>">促销免邮</a></li>
	<li style="" <?php  if($_GP['do'] == 'address')  { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'address','op' => 'index'))?>">退货地址</a></li>
</ul>

<h3 class="header smaller lighter blue">仓库运费列表</h3>


		<form action="" class="form-horizontal" method="post" onsubmit="return formcheck(this)">
				<table class="table table-striped table-bordered table-hover">
  <tr>
				<tr>
				
					
					<th style="width:80px;">仓库名称</th>
					<th style="width:80px;">快递名</th>
					<th style="width:80px;">运费</th>
					<th style="width:80px;">状态</th>
					<th style="width:80px;">操作</th>
				</tr>
			<tbody>
			<?php  if(is_array($disharea)) { foreach($disharea as $row) { ?>
				<tr>
            <td>
              <?php  echo $row['name'];?>
			</td>
			<td>
              <?php  echo $row['kuaidi'];?>
			</td>
			<td> <?php  echo $row['displayorder'];?></td>
           <td>
			   <?php  if($row['enabled']==1) { ?>
				<span class='label label-success'>显示</span>
				<?php  } else { ?>
				<span class='label label-danger'>隐藏</span>
				<?php  } ?>
		   </td>
				<td>
						<?php  if(empty($row['parentid'])) { ?>
						<?php if(isHasPowerToShow('shop','disharea','post','add')){ ?>
							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('disharea', array('parentid' => $row['id'], 'op' => 'post'))?>" style="display:none;"><i class="icon-plus-sign-alt"></i> 添加子区域</a><?php  } ?>&nbsp;&nbsp;
						<?php } ?>
						<?php if(isHasPowerToShow('shop','disharea','post','edit')){ ?>
							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('disharea', array('op' => 'post', 'id' => $row['id']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
						<?php } ?>
						<?php if(isHasPowerToShow('shop','disharea','delete','delete')){ ?>
							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('disharea', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此区域吗？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
						<?php } ?>

					</td>
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
						<?php if(isHasPowerToShow('shop','disharea','post','edit')){ ?>
							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('disharea', array('op' => 'post', 'id' => $row['id'], 'parentid'=>$row['parentid']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
						<?php } ?>
						<?php if(isHasPowerToShow('shop','disharea','delete','delete')){ ?>
							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('disharea', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此区域吗？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
						<?php } ?>
					</td>
				</tr>
				<?php  } } ?>
			<?php  } } ?>
				<tr>
					<td colspan="5">
						<?php if(isHasPowerToShow('shop','disharea','post','add')){ ?>
							<a href="<?php  echo web_url('disharea', array('op' => 'post'))?>"><i class="icon-plus-sign-alt"></i> 添加仓库运费</a>
						<?php } ?>
					</td>
				</tr>

			</tbody>
		</table>
		</form>
<?php  include page('footer');?>
