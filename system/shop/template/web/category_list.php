<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">分类列表 <a href="<?php  echo web_url('category', array('op' => 'csv_post'))?>" style="float:right;font-size:14px;"><i class="icon-plus-sign-alt"></i>批量导入分类</a></h3>


		<form action="" class="form-horizontal" method="post" onsubmit="return formcheck(this)">
				<table class="table table-bordered table-hover">
  <tr>
				<tr>
					<th style="width:150px;">显示顺序</th>
					<th>分类名称</th>
				    <th style="width:218px;">状态</th>
					<th style="width:350px;">操作</th>
				</tr>
			<tbody>
			<?php  
			   // icon-resize-full  icon-resize-small
			   if(is_array($category)) { foreach($category as $row) { 
				   $vid =$row['id'];
		    ?>
				<tr>
					<td style="width:50px;"><a href="javascript:void(0)" onclick="hiddens(<?php  echo $row['id'];?>)"><i class="icon-resize-full"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<input  type="text"  style="width:50px"  name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
               <td>&nbsp;&nbsp;&nbsp;
              <?php  echo $row['name'];?>&nbsp;&nbsp; <img src="<?php  echo $row['thumb'];?>"  height="40" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' />
            
            	</td>
           <td>                                <?php  if($row['isrecommand']==1) { ?>
                                                <span class='label label-success'>首页推荐</span>
                                                 <?php  } ?>
											   <?php  if($row['app_isrecommand']==1) { ?>
												   <span class='label label-success'>app首页推荐</span>
											   <?php  } ?>
			   									<?php  if($row['enabled']==1) { ?>
                                                <span class='label label-success'>显示</span>
                                                <?php  } else { ?>
                                                <span class='label label-danger'>隐藏</span>
                                                <?php  } ?></td>
					<td>
						<?php  if(empty($row['parentid'])) { ?>
							<?php if(isHasPowerToShow('shop','category','post','add')){ ?>
							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('category', array('parentid' => $row['id'], 'op' => 'post'))?>"><i class="icon-plus-sign-alt"></i> 添加子分类</a><?php  } ?>&nbsp;&nbsp;
						<?php } ?>

						<a class="btn btn-xs btn-info" href="<?php  echo create_url('mobile',array('name' => 'shopwap','do' => 'goodlist','pcate' =>  $row['id']))?>" target="_blank"><i class="icon-eye-open"></i>&nbsp;查&nbsp;看&nbsp;</a>&nbsp;&nbsp;

						<?php if(isHasPowerToShow('shop','category','post','edit')){ ?>
							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('category', array('op' => 'post', 'id' => $row['id']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
						<?php } ?>
						<?php if(isHasPowerToShow('shop','category','delete','delete')){ ?>
							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('category', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此分类吗？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
						<?php } ?>
					</td>
				</tr>
				<?php 
					 if(is_array($children[$row['id']])) { 
						foreach($children[$row['id']] as $row) { 
							
				?>
				<tr class="parent_<?php echo $vid; ?>" style="display:none;">
					<td style="width:50px;"><input type="text" style="width:50px" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
					
					 <td>&nbsp;&nbsp;&nbsp;<?php  echo $row['name'];?>&nbsp;&nbsp; <img src="<?php  echo $row['thumb'];?>" width='60' height="50" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' /></td>
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
						<?php if(isHasPowerToShow('shop','category','post','add')){ ?>
							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('category', array('parentid' => $row['id'], 'op' => 'post'))?>"><i class="icon-plus-sign-alt"></i> 添加子分类</a> &nbsp;&nbsp;
						<?php } ?>
						<a class="btn btn-xs btn-info" href="<?php  echo create_url('mobile',array('name' => 'shopwap','do' => 'goodlist','ccate' =>  $row['id']))?>" target="_blank"><i class="icon-eye-open"></i>&nbsp;查&nbsp;看&nbsp;</a>&nbsp;&nbsp;
						<?php if(isHasPowerToShow('shop','category','post','edit')){ ?>
							<a class="btn btn-xs btn-info"   href="<?php  echo web_url('category', array('op' => 'post', 'id' => $row['id'], 'parentid'=>$row['parentid']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
						<?php } ?>
						<?php if(isHasPowerToShow('shop','category','delete','delete')){ ?>
							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('category', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此分类吗？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
						<?php } ?>
					</td>
				</tr>
				<?php  
					  if(is_array($children[$row['id']])) { 
						foreach($children[$row['id']] as $row) { 
                ?>
                     <tr style="display:none;">
					<td style="width:50px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" style="width:50px" name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
					
					 <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo $row['name'];?></td>
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
					<a class="btn btn-xs btn-info" href="<?php  echo create_url('mobile',array('name' => 'shopwap','do' => 'goodlist','ccate' =>  $row['id']))?>" target="_blank"><i class="icon-eye-open"></i>&nbsp;查&nbsp;看&nbsp;</a>&nbsp;&nbsp;
					<?php if(isHasPowerToShow('shop','category','post','edit')){ ?>
						<a class="btn btn-xs btn-info" href="<?php  echo web_url('category', array('op' => 'post', 'id' => $row['id'], 'parentid'=>$row['parentid']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
					<?php } ?>
					<?php if(isHasPowerToShow('shop','category','delete','delete')){ ?>
						<a class="btn btn-xs btn-info" href="<?php  echo web_url('category', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此分类吗？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
					<?php } ?>
					</td>
				 </tr>

				<?php
						} 
					} 
				?>
				<?php
						} 
					} 
				?>
			<?php  } } ?>
				<tr>
					<td colspan="4">
						<?php if(isHasPowerToShow('shop','category','post','add')){ ?>
							<a  href="<?php  echo web_url('category', array('op' => 'post'))?>"><i class="icon-plus-sign-alt"></i> 添加新分类</a>&nbsp;&nbsp;
						<?php } ?>
						<?php if(isHasPowerToShow('shop','category','csv_post')){ ?>
							<a  href="<?php  echo web_url('category', array('op' => 'csv_post'))?>"><i class="icon-plus-sign-alt"></i>批量导入分类</a>
						<?php } ?>
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
<script>
   function hiddens(obj){
      $('.parent_'+obj).fadeToggle();
	  iFrame();
   }
   function iFrame() {
        var ifm= window.parent.document.getElementById("main");
        var subWeb = window.parent.document.frames ? window.parent.document.frames["main"].document :ifm.contentDocument;
            if(ifm != null && subWeb != null) {
                ifm.height = subWeb.body.scrollHeight + 60;
            }
    }
</script>
<?php  include page('footer');?>
