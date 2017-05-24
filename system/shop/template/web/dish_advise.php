<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<h3 class="header smaller lighter blue">家乡菜推荐</h3>

	<table class="table table-striped table-bordered table-hover">
  <tr >
 <th class="text-center" >序号</th>
     <th class="text-center">评论人昵称/手机号</th>
     <th class="text-center">食堂</th>
    <th class="text-center">标题</th>
    <th class="text-center">内容</th>
    <th class="text-center">图片</th>
	 <th class="text-center">发布时间</th>
    <th class="text-center" >操作</th>
  </tr>

		<?php $index=0; if(is_array($list)) {  foreach($list as $item) { $index=$index+1; ?>
				<tr>
				 <td style="text-align:center;"><?php echo  $index ?></td>				
				 <td style="text-align:center;"><?php  echo empty($item['realname'])?$item['mobile']:$item['realname'];?></td>
                 <td style="text-align:center;"><?php  echo $item['messname'].(empty($item['optionname'])?'':'['.$item['optionname'].']'); ?></td>
				 <td style="text-align:center;"><?php  echo $item['title']; ?></td>
                 <td style="text-align:center;"><?php  echo $item['descript'];?></td>
                  <td style="text-align:center;"><a href="/attachment/<?php echo $item['thumb']; ?>" target="_blank">
                  <img src="/attachment/<?php echo $item['thumb']; ?>" width="100" >
                  </a></td>
				 <td style="text-align:center;"><?php  echo date("Y-m-d",$item['createtime']);?></td>
										<td style="text-align:center;">
						<a  class="btn btn-xs btn-info" href="<?php  echo create_url('site', array('id' => $item['id'], 'do' => 'dish','op' => 'delete1','name'=>'shop'))?>" 
						onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
				
					
					</td>
				</tr>
				<?php  } } ?>
 	
		</table>
		<?php  echo $pager;?>

<?php  include page('footer');?>
