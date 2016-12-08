<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<style>
	.operate label{cursor: pointer;}
</style>
<h3 class="header smaller lighter blue">文章管理(<?php echo $total;?>)&nbsp;&nbsp;&nbsp;<a href="<?php  echo web_url('article', array('op'=>'post'));?>" class="btn btn-primary">添加文章</a></h3>
<table class="table table-striped table-bordered table-hover">
	<tbody>
	<tr>
		<td>
			<li style="float:left;list-style-type:none;">
				<select style="margin-right:10px;margin-top:10px;width: 150px; height:34px; line-height:28px; padding:2px 0" onchange="findArticle(this)">
					<option value="0">选择文章分类</option>
					<?php
						if(!empty($category_pcate)){
							foreach($category_pcate as $row){
								if($_GP['pcate'] == $row['id']){
									$sel = "selected";
								}else{
									$sel = '';
								}
								echo "<option value='{$row['id']}' {$sel}>{$row['name']}</option>";
							}
						}
					?>
				</select>
			</li>
		</td>
	</tr>
	</tbody>
</table>
<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
		 <th class="text-center" >文章名称</th>
    <th class="text-center"  >文章分类</th>
    <th class="text-center" width="100px">阅读次数</th>
    <th class="text-center" >属性</th>
    <th class="text-center" >位置类别</th>
    <th class="text-center" >链接</th>
    <th class="text-center">操作</th>
				</tr>
			</thead>
		<?php  if(is_array($article_list)) { foreach($article_list as $item) { ?>
				<tr>
					<td class="text-center"><?php echo $item['title']; ?></td>
          <td class="text-center"><?php echo    $category_pcate[$item['pcate']]['name']; ?><?php if(!empty($item['ccate'])){ ?>-<?php   } ?><?php echo  $category_ccate[$item['ccate']]['name']; ?></td>
           <td class="text-center"><?php echo $item['readcount']; ?></td>
          <td class="text-center operate">
			  <?php if(empty($item['ishot'])){ ?>
				  <label data="1" class="label label-info" data-op="sethot">设置热门</label>
			  <?php  }else{  ?>
				  <label data="1" class="label label-danger" data-op="canclehot">取消热门</label>
			  <?php  } ?>
          	  <?php if(empty($item['iscommend'])){ ?>
				  <label data="1" class="label label-info" data-op="setcommend">设置推荐</label>
			  <?php }else{   ?>
				  <label data="1" class="label label-danger" data-op="canclecommend">取消推荐</label>
			  <?php } ?>
			  <input type="hidden" class="hide_id" value="<?php echo $item['id'];?>">
		  </td>
			<td class="text-center">
				<?php if($item['state'] !=0){ echo $stats_arr[$item['state']];}?>
			</td>
              <td class="text-center"> 
              	<input readonly="readlony" type="text"  class="col-sm-10" value="<?php echo WEBSITE_ROOT;?><?php  echo create_url('mobile',array('name' => 'addon8','do' => 'article','id'=>$item['id']))?>" /> </td>
         <td class="text-center">
                                                    	<a class="btn btn-xs btn-info"  href="<?php  echo web_url('article', array('op' => 'post', 'id' => $item['id']))?>"><i class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> 
                    	&nbsp;&nbsp;	<a class="btn btn-xs btn-info" onclick="return confirm('此操作不可恢复，确认删除？');return false;"  href="<?php  echo web_url('article', array('op' => 'delete', 'id' => $item['id']))?>"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a> </td>
                                </td>
				</tr>
				<?php  } } ?>
		</table>
<?php echo $pager;?>
<script>
	function findArticle(obj){
		var pcate = $(obj).val();
		var url = "<?php echo web_url('article');?>"
		url = url +"&pcate="+pcate;
		window.location.href=url;
	}

	$(".operate label").click(function(){
		var op = $(this).data('op');
		var id = $(this).parent().find('.hide_id').val();
		var url = "<?php echo web_url('article'); ?>";
		url = url +"&op="+op+"&id="+id;
		window.location.href=url;
	})
</script>
<?php  include page('footer');?>
