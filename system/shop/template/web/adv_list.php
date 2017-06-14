<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<h3 class="header smaller lighter blue">幻灯片列表&nbsp;&nbsp;&nbsp;
	<a href="<?php  echo web_url('adv',array('op' =>'post'))?>" class="btn btn-primary">添加幻灯片</a>&nbsp;&nbsp;
	<select name="type" style="font-size: 14px;padding: 4px 2px;" onchange="choose_type(this)">
		<option value="0">查看全部</option>
		<option value='1' <?php  if($_GP['type'] == 1) { ?> selected="selected"<?php  } ?>>PC端</option>
		<option value='2' <?php  if($_GP['type'] == 2) { ?> selected="selected"<?php  } ?>>WAP端</option>
	</select>&nbsp;&nbsp;

	<select name="" id="" style="font-size: 14px;padding: 4px 2px;" onchange="choose_page(this)">
		<option value="0">查看全部</option>
		<option value='1' <?php  if($_GP['showpage'] == 1) { ?> selected="selected"<?php  } ?>>首页</option>
		<option value='4' <?php  if($_GP['showpage'] == 4) { ?> selected="selected"<?php  } ?>>首页顶部</option>
		<option value='2' <?php  if($_GP['showpage'] == 2) { ?> selected="selected"<?php  } ?>>团购</option>
		<option value='3' <?php  if($_GP['showpage'] == 3) { ?> selected="selected"<?php  } ?>>每日特价</option>
		<option value='5' <?php  if($_GP['showpage'] == 5) { ?> selected="selected"<?php  } ?>>限时购</option>
		<option value='6' <?php  if($_GP['showpage'] == 6) { ?> selected="selected"<?php  } ?>>俱乐部</option>
	</select>
</h3>

<table class="table table-striped table-bordered table-hover">
			     <thead >
                <tr>
                    <th  style="text-align:center;width:30px">ID</th>
                    <th  style="text-align:center;">显示顺序</th>					
                    <th  style="text-align:center;">幻灯</th>
					<th  style="text-align:center;">是否显示</th>
                    <th  style="text-align:center;">链接</th>
					<th  style="text-align:center;">显示平台</th>
					<th  style="text-align:center;">显示页面</th>
					<th  style="text-align:center;">显示位置</th>
                    <th  style="text-align:center;">操作</th>
                </tr>
            </thead>
		      <tbody>
                <?php $index=1; if(is_array($list)) { foreach($list as $adv) { ?>
                <tr style="text-align:center;">
                    <td><?php  echo $index++;?></td>
                    <td><?php  echo $adv['displayorder'];?></td>
                    <td> <img src="<?php  echo $adv['thumb'];?>" style="width:150px;height:100px"></td>
					<td><?php echo empty($adv['enabled'])?'否':'是'; ?></td>
                    <td><?php  echo $adv['link'];?></td>
					<td><?php  
                        switch ($adv['type']){
                            case 1:
								echo 'PC端';
								break;
							case 2:
								echo 'WAP端';
								break;
                        }
					?></td>
					
					<td><?php  
                        switch ($adv['page']){
                            case 1:
								echo '首页';
								break;
							case 2:
								echo '团购';
								break;
								
							case 3:
								echo '每日特价';
								break;
							case 4:
								echo '首页顶部';
								break;
							case 5:
								echo '限时购';
								break;
							case 6:
								echo '俱乐部';
								break;
							default:
								echo '未设置';
								break;
                        }
					?></td>
					<td>
						<?php  if($adv['position'] == 1){ echo '默认主图'; }else if($adv['position'] == 2){ echo '幅图小图'; } ?>
					</td>
                    <td style="text-align:center;">
                    	<a class="btn btn-xs btn-info"  href="<?php  echo web_url('adv', array('op' => 'post', 'id' => $adv['id']))?>"><i class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> 
                    	&nbsp;&nbsp;	<a class="btn btn-xs btn-info"  href="<?php  echo web_url('adv', array('op' => 'delete', 'id' => $adv['id']))?>"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a> </td>
                </tr>
                <?php  } } ?>
            </tbody>
		</table>
		  <?php  echo $pager;?>

	<script>
		function  choose_type(obj){
			var type = $(obj).val();
			var url = "<?php echo web_url('adv',array('name'=>'shop','op'=>'display'));?>";
			url = url + "&type="+type;
			window.location.href = url;
		}
		function  choose_page(obj){
			var page = $(obj).val();
			var url = "<?php echo web_url('adv',array('name'=>'shop','op'=>'display'));?>";
			url = url + "&showpage="+page;
			window.location.href = url;
		}
	</script>
<?php  include page('footer');?>