<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">
	觅海头条列表&nbsp;&nbsp;&nbsp;<a class="btn btn-info btn-sm add-user" href="<?php echo web_url('headline', array('op' => 'add'))?>">新增头条</a>
</h3>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center; width: 30px">ID</th>
			<th style="text-align: center;">标题</th>
			<th style="text-align: center;">类型</th>
			<th style="text-align: center;">图片</th>
			<th style="text-align: center;">内容预览</th>
			<th style="text-align: center;">是否推荐</th>
			<th style="text-align: center;">审核通过</th>
			<th style="text-align: center;">创建时间</th>
			<th style="text-align: center;">操作</th>
		</tr>
	</thead>
	<tbody>
        <?php if(is_array($list)) { foreach($list as $value) { ?>
        <tr style="text-align: center;">
			<td><?php echo $value['headline_id'];?></td>
			<td><?php echo $value['title'];?></td>
			<td><?php if (empty($value['video'])) {
				echo "图文";
			}else{
				echo "视频";
			}?></td>
			<td><?php if (empty($value['video'])) {
				$pic_ary = explode(';', $value['pic']);
				if (!empty($pic_ary)) {
					echo "<img src='".download_pic($pic_ary[0],90,70,2)."' />";
				}
			}else{
				echo "<img src='".download_pic($value["video_img"],90,90,2)."'></>";
			}?></td>
			<td><?php echo $value['preview'];?></td>
			<td><?php echo empty($value['isrecommand'])?'否':'是'; ?></td>
			<td><?php echo empty($value['ischeck'])?'否':'已审核'; ?></td>
			<td><?php echo date('Y-m-d H:i:s',$value['createtime']);?></td>
			<td style="text-align: center;">
				<a class="btn btn-xs btn-info" href="<?php echo web_url('headline', array('op' => 'edit', 'headline_id' => $value['headline_id']))?>"><i
					class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> &nbsp;&nbsp;
				<a class="btn btn-xs btn-danger" href="<?php  echo web_url('headline', array('op'=>'delete','id' => $value['headline_id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;		</a>
			</td>
		</tr>
        <?php  } } ?>
     </tbody>
</table>
<?php  echo $pager;?>
<?php  include page('footer');?>