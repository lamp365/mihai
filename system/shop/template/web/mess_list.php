<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<h3 class="header smaller lighter blue">换购列表</h3>
		<form action=""  class="form-horizontal" method="post">
	    <table class="table table-striped table-bordered table-hover">
			<tbody>
				<tr>
				<td  style="background-color: #fff">
				<li style="float:left;list-style-type:none;">
						<select  style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" name="cate_1" onchange="fetchChildarea(this.options[this.selectedIndex].value)">
							<option value="0">请选择一级区间</option>
							<?php  if(is_array($area)) { foreach($area as $row) { ?>
							<?php  if($row['parentid'] == 0) { ?>
							<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GP['cate_1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
							<?php  } ?>
							<?php  } } ?>
						</select>
						<select style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" id="cate_2" name="cate_2">
							<option value="0">请选择二级区间</option>
							<?php  if(!empty($_GP['cate_1']) && !empty($children[$_GP['cate_1']])) { ?>
							<?php  if(is_array($children[$_GP['cate_1']])) { foreach($children[$_GP['cate_1']] as $row) { ?>
							<option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $_GP['cate_2']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
							<?php  } } ?>
							<?php  } ?>
						</select>
						
						</li>
						<li style="float:left;list-style-type:none;">
						 <select name="status" style="margin-right:10px;width: 100px; height:30px; line-height:28px; padding:2px 0">
							<option value="-1">状态</option>
							<option value="1" <?php if($_GP['status'] == 1){ echo "selected";}?>>开通中</option>
							<option value="0" <?php if(isset($_GP['status']) && $_GP['status'] == 0){ echo "selected";}?>>已关闭</option>
						</select>
						</li>
						
						<li style="float:left;list-style-type:none;">
											<span>关键字</span>	<input style="margin-right:10px;width: 300px; height:30px; line-height:28px; padding:2px 0" name="keyword" id="" type="text" value="<?php  echo $_GP['keyword'];?>">
						</li>
						<li style="list-style-type:none;">
						<button class="btn btn-primary  btn-sm" style="margin-right:10px;"><i class="icon-search icon-large"></i> 搜索</button>
						</li>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		
	<table class="table table-striped table-bordered table-hover">
  <tr >
    <th class="text-center" >产品图</th>
    <th class="text-center" >宝贝ID</th>
    <th class="text-center">产品名称</th>
	<th class="text-center">销售价格</th>
	<th class="text-center">换购价格</th>
	<th class="text-center" >换购个数</th>
	<th class="text-center" >库存</th>
    <th class="text-center" >产品属性</th>
    <th class="text-center" >状态</th>
    <th class="text-center" >操作</th>
  </tr>

		<?php if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
				 <td><p style="text-align:center;margin:0;"><img src='<?php  echo $item['thumb'];?>' width="70" /></p></td>

                                           <td style="text-align:center;" ><?php  echo $item['gid'];?></td>
                                        	<td style="text-align:center;"><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $item['gid']))?>"><?php  echo $item['title'];?></a></td>
											
											
											<td style="text-align:center;" ><?php  echo $item['productprice'];?></td>
											<td style="text-align:center;" ><?php  echo $item['marketprice'];?></td>
											
											<td style="text-align:center;"><?php  echo $item['max_buy'];?></td>
											<td style="text-align:center;"><?php  echo $item['total'];?></td>

                                        <td style="text-align:center;"> <label  class='label label-info' ><?php  echo $item['area'];?></label>
                                     <?php  if($item['istime']==1) { ?>  <label data='<?php  echo $item['istime'];?>' class='label label-info' >限时</label><?php  } ?>
                                        <?php  if($item['issendfree']==1) { ?> <label data='<?php  echo $item['issendfree'];?>' class='label label-info'>包邮</label><?php  } ?>
                                       <?php  if($item['isrecommand']==1) { ?> <label data='<?php  echo $item['isrecommand'];?>' class='label label-info'>首页推荐</label><?php  } ?>
					                                        <?php  if($item['isnew']==1) { ?> <label data='<?php  echo $item['isnew'];?>' class='label label-info'>新品</label><?php  } ?>
                                       <?php  if($item['isfirst']==1) { ?> <label data='<?php  echo $item['isfirst'];?>' class='label label-info'>首发</label><?php  } ?>
                                         <?php  if($item['ishot']==1) { ?> <label data='<?php  echo $item['ishot'];?>' class='label label-info'>热卖</label><?php  } ?>
                                      <?php  if($item['isjingping']==1) { ?> <label data='<?php  echo $item['isjingping'];?>' class='label label-info'>精品</label><?php  } ?>
                                     
                                   </td>
					
					
					
					<td style="text-align:center;"><?php  if($item['status']) { ?><span data='<?php  echo $item['status'];?>' onclick="setProperty1(this,<?php  echo $item['id'];?>,'status')" class="label label-success" style="cursor:pointer;">开通中</span><?php  } else { ?><span data='<?php  echo $item['status'];?>' onclick="setProperty1(this,<?php  echo $item['id'];?>,'status')" class="label label-danger" style="cursor:pointer;">已关闭</span><?php  } ?></td>
					<td style="text-align:center;">
					<a class="btn btn-xs btn-info" target="_blank" href="<?php echo WEBSITE_ROOT.mobile_url('detail',array('name'=>'shopwap','id'=>$item['id']));?>" style="display:none;"><i class="icon-eye-open"></i>&nbsp;查&nbsp;看&nbsp;</a>&nbsp;&nbsp;
						<a  class="btn btn-xs btn-info" href="<?php  echo web_url('mess', array('id' => $item['id'], 'op' => 'post'))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
						<a  class="btn btn-xs btn-info" href="<?php  echo web_url('mess', array('id' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a></a>
				&nbsp;&nbsp;
					
					</td>
				</tr>
				<?php  } } ?>
 	
		</table>
		<?php  echo $pager;?>
<script language="javascript">
		var area = <?php  echo json_encode($children)?>;
   function fetchChildarea(cid) {
	var html = '<option value="0">请选择二级区域</option>';
	if (!area || !area[cid]) {
		$('#cate_2').html(html);
		return false;
	}
	for (i in area[cid]) {
		html += '<option value="'+area[cid][i][0]+'">'+area[cid][i][1]+'</option>';
	}
	$('#cate_2').html(html);
}
</script>
<?php  include page('footer');?>
