<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<h3 class="header smaller lighter blue">觅海全球购</h3> 
<form action=""  class="form-horizontal" method="post">
	<table class="table table-striped table-bordered table-hover">
			<tbody >
				<tr>
				<td>
				        <li style="float:left;list-style-type:none;">
						<select  style="margin-right:10px;margin-top:10px;width: 150px; height:34px; line-height:28px; padding:2px 0" name="cate_1" onchange="fetchChildarea(this.options[this.selectedIndex].value)">
							<option value="0">请选择保税仓</option>
							<?php  if(is_array($area)) { foreach($area as $row) { ?>
							<?php  if($row['parentid'] == 0) { ?>
							<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GP['cate_1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
							<?php  } ?>
							<?php  } } ?>
						</select>
						</li>
						<li style="float:left;list-style-type:none;">
						<select  style="margin-right:10px;margin-top:10px;width: 150px; height:34px; line-height:28px; padding:2px 0" name="p1" onchange="fetchChildCategory(this.options[this.selectedIndex].value)">
							<option value="0">请选择一级分类</option>
							<?php  if(is_array($category)) { foreach($category as $row) { ?>
							<?php  if($row['parentid'] == 0) { ?>
							<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GP['p1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
							<?php  } ?>
							<?php  } } ?>
						</select>
						<select onchange="fetchChildCategory2(this.options[this.selectedIndex].value)" style="margin-right:10px;margin-top:10px;width: 150px; height:34px; line-height:28px; padding:2px 0" id="p2" name="p2">
							<option value="0">请选择二级分类</option>
							<?php  if(!empty($_GP['p1']) && !empty($childrens[$_GP['p1']])) { ?>
							<?php  if(is_array($childrens[$_GP['p1']])) { foreach($childrens[$_GP['p1']] as $row) { ?>
							<option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $_GP['p2']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
							<?php  } } ?>
							<?php  } ?>
						</select>
						</li>
						<li style="float:left;list-style-type:none;">
						   <select name="type" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
							   <option value="-1" selected>类型</option>
							   <option value="0" <?php if($_GP['type']===0){?>selected="selected"<?php  } ?>>一般商品</option>
                               <option value="1" <?php if($_GP['type']==1){?>selected="selected"<?php  } ?> >团购商品</option>
                               <option value="2" <?php if($_GP['type']==2){?>selected="selected"<?php  } ?>>秒杀商品</option>
                               <option value="3" <?php if($_GP['type']==3){?>selected="selected"<?php  } ?>>今日特价商品</option>
						       <option value="4" <?php if($_GP['type']==4){?>selected="selected"<?php  } ?>>限时促销</option>
						   </select>
						</li>
						<li style="float:left;list-style-type:none;">
						<select name="status" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
							<option value="1" <?php if($_GP['status']==1){?>selected="selected"<?php  } ?>> 开通中</option>
							<option value="0" <?php if($_GP['status']==0){?>selected="selected"<?php  } ?> >已关闭</option>
						</select>
						</li>
						
						<li style="float:left;list-style-type:none;">
											
											<input style="margin-right:5px;margin-top:10px;width: 300px; height:34px; line-height:28px; padding:2px 0" name="keyword" id="" type="text" value="<?php  echo $_GP['keyword'];?>">
											<select  name="key_type" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
                                                    <option value="title" <?php if($_GP['key_type']=='title'){?>selected="selected"<?php  } ?> >标题</option>
													<option value="id" <?php if($_GP['key_type']=='id'){?>selected="selected"<?php  } ?>>ID</option>
											</select>
						</li>
						<li style="list-style-type:none;">
						<button class="btn btn-primary" style="margin-top:10px;"><i class="icon-search icon-large"></i> 搜索</button>
						<button type="submit" name="report" value="report" class="btn btn-warning" style="margin-right:10px;margin-top:10px;">导出excel</button>
						</li>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
	<table class="table table-striped table-bordered table-hover">
  <tr>
    <th class="text-center" >
    	<input type="checkbox" onclick="selectAll()" id="selectAll"/>
    	宝贝ID
    </th>
    <th class="text-center" >首图</th>
    <th class="text-center" >产品库编号</th>
    <th class="text-center">产品名称</th>
	<th class="text-center" ><a href="<?php echo $sorturl."&orderprice=".$oprice; ?>">价格</a></th>
	<th class="text-center"><a href="<?php echo $sorturl."&ordertprice=".$otprice; ?>">特别价格</a></th>
    <th class="text-center">批发价格（美元)</th>
	<th class="text-center" ><a href="<?php echo $sorturl."&ordertot=".$otot; ?>">库存</a></th>
    <th class="text-center" >状态</th>
    <th class="text-center" >操作</th>
  </tr>

		<?php if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
				 	<td style="text-align:center;">
				 		<input type="checkbox" class="dishvalue" name="disvalue[]" value="<?php  echo $item['id'];?>"/>
				 		<?php  echo $item['id'];?>				 			
				 	</td>
				 	<td><p style="text-align:center"> 				                          
				        <img src="<?php  echo $item['imgs'];?>" height="60" width="60">				        	
				        </p>
				    </td>
                    <td style="text-align:center;"><?php  echo $item['gid'];?></td>
                	<td style="text-align:center;"><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $item['id']))?>"><?php  echo $item['title'];?></a></td>
					<td style="text-align:center;" ><?php  echo $item['marketprice'];?></td>
					<td style="text-align:center;" ><?php  echo $item['timeprice'];?></td>
					<td style="text-align:center;">
					     <?php if ( isset( $item['purchase_price'] ) ){ foreach ( $item['purchase_price'] as $purchase_price ){ ?>
					           <span class="label label-danger" style="margin-left:5px;"><?php echo $purchase_price['name'].$purchase_price['vip_price']; ?></span>  
						 <?php }} ?>
					</td>
					<td style="text-align:center;"><?php  echo $item['total'];?></td>											
                    <td style="text-align:center;display:none;">
                        <?php  echo $item['count']?$item['count']:0;?>                 
                    </td>
					<td style="text-align:center;"><?php  if($item['status']) { ?><span data='<?php  echo $item['status'];?>' onclick="setProperty1(this,<?php  echo $item['id'];?>,'status')" class="label label-success" style="cursor:pointer;">开通中</span><?php  } else { ?><span data='<?php  echo $item['status'];?>' onclick="setProperty1(this,<?php  echo $item['id'];?>,'status')" class="label label-danger" style="cursor:pointer;">已关闭</span><?php  } ?><!--&nbsp;<span class="label label-info"><?php  if($item['type'] == 1) { ?>实体二手车<?php  } else { ?>虚拟二手车<?php  } ?></span>--><span class="label label-danger" style="margin-left:5px;"><?php  echo $item['typename'];?></span><?php if ( isset($item['purchase']) ){echo '<span class="label label-danger" style="margin-left:5px;">'.$item['purchase'].'</span>';} ?>
					</td>
					<td style="text-align:center;">
					<a class="btn btn-xs btn-info" target="_blank" href="<?php echo WEBSITE_ROOT.mobile_url('detail',array('name'=>'shopwap','id'=>$item['id']));?>" style="display:none;"><i class="icon-eye-open"></i>&nbsp;查&nbsp;看&nbsp;</a>&nbsp;&nbsp;
					<?php if($item['type'] == 1 && isHasPowerToShow('shop','dish','open_groupbuy')){ ?>
						<?php if($item['open_groupbuy'] == 1){ ?>
							<a  class="btn btn-xs btn-danger" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'open_groupbuy','act'=>'close'))?>"><i class="icon-edit"></i>关闭凑单</a>&nbsp;&nbsp;
						<?php }else{ ?>
							<a  class="btn btn-xs btn-warning" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'open_groupbuy','act'=>'open'))?>"><i class="icon-edit"></i>开启凑单</a>&nbsp;&nbsp;
						<?php } ?>
					<?php } ?>
					<?php if(isHasPowerToShow('shop','dish','post','edit')){ ?>
						<a  class="btn btn-xs btn-info" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'post'))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
					<?php } ?>
					<?php if(isHasPowerToShow('shop','dish','delete')){ ?>
						<a  class="btn btn-xs btn-info" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
					<?php } ?>
						&nbsp;&nbsp;					
					</td>
				</tr>
				<?php  } } ?>
 	
		</table>
		
		<!--增加的操作-->
		<?php if ( !empty( $list_op )  ){ ?>
		<select style="margin-right:10px;margin-top:10px;width: 150px; height:34px; line-height:28px; padding:2px 0" name="" id="option" onchange="fetchOption()">
		    <?php foreach ( $list_op as $key=>$list_op_value ){ ?>
			<option value="<?php echo $list_op_value; ?>"><?php echo $key; ?></option>
			<?php } ?>
		</select>
		<?php } ?>
		<?php  echo $pager;?>
<script language="javascript">
   var area = <?php  echo json_encode($children)?>;
   function fetchChildarea(cid) {
	var html = '<option value="0">请选择二级分类</option>';
	if (!area || !area[cid]) {
		$('#cate_2').html(html);
		return false;
	}
	for (i in area[cid]) {
		html += '<option value="'+area[cid][i][0]+'">'+area[cid][i][1]+'</option>';
	}
	$('#cate_2').html(html);
    }
    
  var category = <?php  echo json_encode($childrens)?>;	
   function fetchChildCategory(cid) {
	var html = '<option value="0">请选择二级分类</option>';
	if (!category || !category[cid]) {
		$('#p2').html(html);
		fetchChildCategory2(document.getElementById("p2").options[document.getElementById("p2").selectedIndex].value);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#p2').html(html);
	fetchChildCategory2(document.getElementById("p2").options[document.getElementById("p2").selectedIndex].value);

}


  function fetchChildCategory2(cid) {
	var html = '<option value="0">请选择三级分类</option>';
	if (!category || !category[cid]) {
		$('#p3').html(html);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#p3').html(html);
 }
 
 //操作方法
  function fetchOption(){
	if ( $("#option").val() == -1){
		return;
	}
 	//选中了哪些宝贝,将他们的value存进数组list
 	var list=[];
 	$("input[name = 'disvalue[]']:checked").each(function(){ 		
 		list.push(this.value);
 	})
 	if(confirm("确定"+$(":selected","#option")[0].innerHTML+"选中的宝贝？")){ 		
		$.post('', {'id' : list,'op':'ajax','todo':$("#option").val()}, function(s) {
			  window.location.reload();
		});
 	}
 }
 //全选
 function selectAll(){
 	
	if($("#selectAll").is(':checked')){
	   $(".dishvalue").prop("checked",true);
	}else{
		$(".dishvalue").prop("checked",false);
	}
 }
 
 

</script>
<?php  include page('footer');?>
