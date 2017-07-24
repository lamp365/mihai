<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<style type="text/css">
.goods-list-table li{
	float:left;
	list-style-type:none;
}	
.goods-list-table tr{
	background-color: #fff!important;
}
</style>
<h3 class="header smaller lighter blue">商品列表</h3>
		<form action=""  class="form-horizontal" method="post" id="myform">
	<table class="table table-striped table-bordered table-hover goods-list-table">
			<tbody >
				<tr>
					<td>
                                                <li>
                                                    
                                                </li>
						<li>
							<select name="industry_p1_id" id="industry_p1_id" class="" style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" >
								<option value="">请选择一级行业分类</option>
								<?php foreach($oneIndustry as $item) {
									if($item['gc_id'] == $_GP['industry_p1_id']){
										$sel = "selected";
									}else{
										$sel = '';
									}
									echo "<option value='{$item['gc_id']}' {$sel}>{$item['gc_name']}</option>";
								}
								?>
							</select>
                                                    <select name="industry_p2_id" id="industry_p2_id" class="" style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0">
								<option value="">请选择二级行业分类</option>
								<?php
                                                                    foreach($twoIndustry as $item) {
                                                                        if($item['gc_id'] == $_GP['industry_p2_id']){
                                                                                $sel = "selected";
                                                                        }else{
                                                                                $sel = '';
                                                                        }
                                                                        echo "<option value='{$item['gc_id']}' {$sel}>{$item['gc_name']}</option>";
                                                                    }
								?>
							</select>
                                                    
                                                    
							<select name="p1" id="getShopCategory_p1" class="get_category" onchange="getShop_sonCategroy(this,1)" style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" >
								<option value="">请选择分类</option>
								<?php foreach($parent_category as $item) {
									if($item['id'] == $_GP['p1']){
										$sel = "selected";
									}else{
										$sel = '';
									}
									echo "<option value='{$item['id']}' {$sel}>{$item['name']}</option>";
								}
								?>
							</select>
							<select name="p2" id="getShopCategory_p2" class="get_category" onchange=""  style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" >
								<option value="">请选择分类</option>
								<?php foreach($first_son as $item2) {
									if($item2['id'] == $_GP['p2']){
										$sel = "selected";
									}else{
										$sel = '';
									}
									echo "<option value='{$item2['id']}' {$sel}>{$item2['name']}</option>";
								}
								?>
							</select>
						</li>
						<li>
							<select name="status" style="margin-right:10px;height:30px; line-height:28px; padding:2px 0">
								<option value="" selected>请选择上下架</option>
								<option value="1" >上架中</option>
								<option value="0" >已下架</option>
							</select>
						</li>
						<li>
							<input style="margin-right:5px;width: 300px; height:30px; line-height:28px; padding:2px 0" name="keyword" id="" type="text" value="<?php  echo $_GP['keyword'];?>">
							<select  name="key_type" style="margin-right:10px;width: 100px; height:30px; line-height:28px; padding:2px 0">
                                <option value="title" <?php if($_GP['key_type']=='title'){?>selected="selected"<?php  } ?> >标题</option>
								<option value="id" <?php if($_GP['key_type']=='id'){?>selected="selected"<?php  } ?>>ID</option>
								<option value="sn" <?php if($_GP['key_type']=='sn'){?>selected="selected"<?php  } ?>>SN</option>
							</select>
						</li>
						<li>
							<button class="btn btn-primary btn-sm" style="margin-right:10px;"><i class="icon-search icon-large"></i> 搜索</button>
						</li>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		
	<table class="table table-striped table-bordered table-hover">
  <tr >
  <th class="text-center" >ID</th>
 <th class="text-center" >首图</th>
    <th class="text-center">商品名称</th>
	<th class="text-center" >货号</th>
	<th class="text-center" >价格</th>
	<th class="text-center" >库存</th>
    <th class="text-center" >商品属性</th>  
    <th class="text-center" >状态</th>
    <th class="text-center" >操作</th>
  </tr>

		<?php if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
				<td class="text-center"><?php  echo $item['id'];?></td>
				 <td><p style="text-align:center"> <img src="<?php  echo $item['thumb'];?>" height="60" width="60"></p></td>

                                     
                                        	<td style="text-align:center;"><?php  echo $item['title'];?></td>
											
											<td style="text-align:center;"><?php  echo $item['goodssn'];?></td>
											
											<td style="text-align:center;"><?php  echo FormatMoney($item['marketprice'],0);?></td>
											
											<td style="text-align:center;"><?php  echo $item['store_count'];?></td>
											
                                        <td style="text-align:center;">
                                             重量:<?php echo $item['weight']; ?> 系数:<?php echo $item['coefficient']; ?> <?php if ( !empty( $item['Supplier'] ) ){ echo '<br/>组合产品'; } ?>
                                       </td>
					<td style="text-align:center;"><?php  if($item['status']) { ?><span data='<?php  echo $item['status'];?>' onclick="setProperty1(this,<?php  echo $item['id'];?>,'status')" class="label label-success" style="cursor:pointer;">上架中</span><?php  } else { ?><span data='<?php  echo $item['status'];?>' onclick="setProperty1(this,<?php  echo $item['id'];?>,'status')" class="label label-danger" style="cursor:pointer;">已下架</span><?php  } ?><!--&nbsp;<span class="label label-info"><?php  if($item['type'] == 1) { ?>实体商品<?php  } else { ?>虚拟商品<?php  } ?></span>--></td>
					<td style="text-align:center;">
						<?php if($dishid = getDishId($item['id'])){  ?>
							<a class="btn btn-xs btn-info" target="_blank" href="<?php echo WEBSITE_ROOT.mobile_url('detail',array('name'=>'shopwap','id'=>$dishid));?>"><i class="icon-eye-open"></i>&nbsp;查&nbsp;看&nbsp;</a>&nbsp;&nbsp;
						<?php }else{  ?>
							<a class="btn btn-xs btn-info" title="你还没发布宝贝" href="javascript:;"><i class="icon-eye-open"></i>&nbsp;查&nbsp;看&nbsp;</a>&nbsp;&nbsp;
						<?php } ?>
						<?php if(isHasPowerToShow('shop','goods','post','edit',$item['id'])){ ?>
							<a  class="btn btn-xs btn-info"  href="<?php  echo web_url('goods', array('id' => $item['id'], 'op' => 'post_product'))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
						<?php } ?>
						<?php if(isHasPowerToShow('shop','goods','delete','delete',$item['id'])){ ?>
							<a  class="btn btn-xs btn-info"  href="<?php  echo web_url('goods', array('id' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a></a>&nbsp;&nbsp;
						<?php } ?>
					</td>
				</tr>
				<?php  } } ?>
 	
		</table>
		<?php  echo $pager;?>
<script language="javascript">
		var category = <?php  echo json_encode($children)?>;
   function fetchChildCategory(cid) {
	var html = '<option value="0">请选择二级分类</option>';
	if (!category || !category[cid]) {
		$('#cate_2').html(html);
					fetchChildCategory2(document.getElementById("cate_2").options[document.getElementById("cate_2").selectedIndex].value);

		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#cate_2').html(html);
				fetchChildCategory2(document.getElementById("cate_2").options[document.getElementById("cate_2").selectedIndex].value);

}
  function fetchChildCategory2(cid) {
	var html = '<option value="0">请选择三级分类</option>';
	if (!category || !category[cid]) {
		$('#cate_3').html(html);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#cate_3').html(html);
 }
 
 $(function(){
     $('#industry_p1_id').on('change',function(){
        $.post("/index.php?mod=site&name=shop&do=goods&op=twoIndustry",{id:$(this).val()},function(data){
            $('#industry_p2_id').empty();
            var indu_p2_opt = "<option value='0'>请选择</option>";
            for(var i=0;i<data.length;i++){
                var item = data[i];
                indu_p2_opt   = indu_p2_opt+"<option value='"+item['gc_id']+"'>"+item['gc_name']+"</option>";
            }
            $("#industry_p2_id").html(indu_p2_opt);
        },"json");
    });
    
     $('#industry_p2_id').on('change',function(){
        $('#myform').submit();
    });
    
 })
</script>
<?php  include page('footer');?>
