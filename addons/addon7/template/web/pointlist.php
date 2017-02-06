<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">开奖参数管理&nbsp;&nbsp;&nbsp;

</h3>
为落实责任人，只有当前签名的人可以修改数据。只有第三个人可以开奖，当开奖后，则任何人不得修改数据。
<br/><Br/>
<table class="table table-striped table-bordered table-hover">
	<thead>
	<tr>
	<th class="text-center" >数据信息</th>
    <th class="text-center"  >图片参照</th>
    <th class="text-center" width="100px">开奖时间</th>
    <th class="text-center" >验证人一</th>
    <th class="text-center" >验证人二</th>
	<th class="text-center" >验证人三</th>
	<th class="text-center" >状态</th>
    <th class="text-center">操作</th>
	</tr>
			</thead>
		 <?php if (is_array($article_list)){ foreach($article_list as $value){ ?>
		 <tr>
		 <td class="text-center"><?php echo $value['nums']; ?></td>
		 <td class="text-center"><?php if (!empty($value['thumb'])){ ?><img src="<?php echo $value['thumb']; ?>" height="50" /><?php } ?></td>
		 <td class="text-center"><?php echo date("Y-m-d H:i",get_open_time($value['lock_time'])); ?></td>
		 <td class="text-center"><?php echo $value['v1']; ?></td>
		 <td class="text-center"><?php echo $value['v2']; ?></td>
		 <td class="text-center"><?php echo $value['v3']; ?></td>
		 <td class="text-center">
		 <?php 
		 switch ($value['vn']){
            case 3:
				echo '签名结束';
				break;
			default:		
		    	echo '等待签名';
				break;
         }
		 ?></td>
         <td class="text-center">
		     <?php if ($value['states'] != 1){ ?>
                 <a class="btn btn-xs btn-info"  href="<?php  echo web_url('point', array('op' => 'update', 'id' => $value['id']))?>"><i class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> 
                 <?php if ($value['vn'] != 3){ ?>
				 <a class="btn btn-xs btn-info"  href="<?php  echo web_url('point', array('op' => 'sign', 'id' => $value['id']))?>"><i class="icon-edit"></i>&nbsp;签&nbsp;名&nbsp;</a> 
				  <?php }elseif ( $value['states'] == 0 ){ ?>
                 <a class="btn btn-xs btn-info"  href="<?php  echo web_url('point', array('op' => 'open', 'id' => $value['id']))?>"><i class="icon-edit"></i>&nbsp;开&nbsp;奖&nbsp;</a> 
				  <?php } ?>
			 <?php }else{ ?>
                 已完成
			 <?php } ?>
         </td>
                                </td>
							
				</tr>
			<?php } }?>
		</table>

<?php  include page('footer');?>
