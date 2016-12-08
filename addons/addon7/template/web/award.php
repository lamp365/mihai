<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
	<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/css/datetimepicker.css" />
		<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/datetimepicker.js"></script>
		<h3 class="header smaller lighter blue">
					<?php 
					if ($_GET['do'] == 'editaward'){ 
						echo '编辑云购商品';
                    }else{
                        echo '添加云购商品';
					}
					?>
					</h3>
<form role="form" class="form-horizontal">
 <div class="form-group">
           	<label class="col-sm-2 control-label no-padding-left" > 查询产品：</label>
			<div class="col-sm-9">
			<select  style="margin-right:15px;" id="pcates" name="pcates" onchange="fetchChildCategory(this.options[this.selectedIndex].value)"  autocomplete="off">
                <option value="0">请选择一级分类</option>
                <?php  if(is_array($category)) { foreach($category as $row) { ?>
                <?php  if($row['parentid'] == 0) { ?>
                <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['pcate']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
                <?php  } ?>
                <?php  } } ?>
            </select>
            <select  id="cates_2" name="ccates" onchange="fetchChildCategory2(this.options[this.selectedIndex].value)" autocomplete="off">
                <option value="-1">请选择二级分类</option>
                <?php  if(!empty($item['ccate']) && !empty($childrens[$item['pcate']])) { ?>
                <?php  if(is_array($childrens[$item['pcate']])) { foreach($childrens[$item['pcate']] as $row) { ?>
                <option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $item['ccate']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
                <?php  } } ?>
                <?php  } ?>
            </select>
			<select  id="cate_3" name="ccate2" autocomplete="off">
                <option value="0">请选择三级分类</option>
                <?php 
				    if(!empty($item['ccate2']) && !empty($childrens[$item['ccate']])) { 
				       if(is_array($childrens[$item['ccate']])) { 
						   foreach($childrens[$item['ccate']] as $row) { 
				?>
                         <option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $item['ccate2']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
                <?php  } } } ?>
            </select>
		</div>
  </div>
  <div class="form-group">
					<label class="col-sm-2 control-label no-padding-left" ></label>
					<div class="col-sm-9">
				    <a href="javascript:void(0)" onclick="findgoods()" class="btn btn-primary span2" name="submit" ><i class="icon-edit"></i>查找产品</a>    
					</div>
		</div>
  </form>
 <hr>
 <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return fillform()">
        <input type="hidden" name="id" value="<?php  echo $award['id'];?>" />
		<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 选择产品：</label>
		<div class="col-sm-9">
			  <select name="c_goods" id="c_goods">
			       <?php if (!empty($award['gname'])){ ?>
                   <option value='<?php echo $award['gid']; ?>'><?php echo $award['gname']; ?></option>
				   <?php }else{ ?>
			       <option value='0'>未选择产品</option>
				   <?php } ?>
			  </select>
		</div>
       </div>			
        <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >特别描述</label>

										<div class="col-sm-9">
											 <input type="text" name="names"  value="<?php  echo $award['names'];?>" class="col-xs-10 col-sm-2" />
										</div>
									</div>
					<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 首页推荐：</label>

										<div class="col-sm-9">
				 <input type="checkbox" name="isrecommand" value="1" id="isrecommand" <?php  if($award['isrecommand'] == 1) { ?> checked <?php  } ?> /> 首页推荐
				 		
										</div>
		</div>			
					<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 宣传图</label>

										<div class="col-sm-9">
											 				<div class="fileupload fileupload-new" data-provides="fileupload">
			                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
			                        	 <?php  if(!empty($award['logo'])) { ?>
			                            <img style="width:100%" src="<?php  echo $award['logo'];?>" alt="" onerror="$(this).remove();">
			                              <?php  } ?>
			                            </div>
			                        <div>
			                         <input name="logo" id="logo" type="file"  />
			                            <a href="#" class="fileupload-exists" data-dismiss="fileupload">移除图片</a>
			                        </div>
			                    </div>
										</div>
									</div>
									
									     <div class="form-group" style="display:none;">
										<label class="col-sm-2 control-label no-padding-left" > 兑换类型</label>

										<div class="col-sm-9">
										<input type="radio" name="awardtype" value="0" <?php  if($award['awardtype'] == 0) { ?>checked="true"<?php  } ?> /> 人民币  &nbsp;&nbsp;
             
                <input type="radio" name="awardtype" value="1"  <?php  if($award['awardtype'] == 1) { ?>checked="true"<?php  } ?> /> 积分 <input type="hidden" name="gold"  value="<?php  echo $award['gold'];?>" />
										</div>
									</div>
									
									
									
								
									
									
									     <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 开始日期</label>

										<div class="col-sm-9">
											 <input name="endtime" id="endtime" type="text" value="<?php  echo empty($award['endtime'])?date('Y-m-d H:i',time()):date('Y-m-d H:i',$award['endtime']);?>" readonly="readonly"  /> 
													<script type="text/javascript">
		$("#endtime").datetimepicker({
			format: "yyyy-mm-dd hh:ii",
			minView: "0",
			//pickerPosition: "top-right",
			autoclose: true
		});
	</script> 
										</div>
									</div>
									
									
									     <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >市场价格</label>

										<div class="col-sm-9">
											 <input type="text" name="price"  value="<?php  echo $award['price'];?>" class="col-xs-10 col-sm-2" />
										</div>
									</div>
									   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 份数切割</label>

										<div class="col-sm-9">
											 <input type="text" name="amount"  value="<?php  echo $award['amount'];?>" class="col-xs-10 col-sm-2" />
										</div>
									</div>
									
									    <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 单份价格 </label>

										<div class="col-sm-9">
											 <input type="text" name="credit_cost"  value="<?php  echo $award['credit_cost']==0?1:$award['credit_cost'];?>" class="col-xs-10 col-sm-2" />
										</div>
									</div>
									
									
										    <div class="form-group" style="display:none;">
										<label class="col-sm-2 control-label no-padding-left" > 简介</label>

										<div class="col-sm-9">
											 		<textarea name="content" id="content" cols="60" rows="8"><?php  echo $award['content'];?></textarea>
										</div>
									</div>
									
									
								  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

										<div class="col-sm-9">
										<input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
										
										</div>
									</div>

    </form>

<Script>
  var category = <?php  echo json_encode($childrens)?>;
 function fetchChildCategory(cid) {
	var html = '<option value="0">请选择二级分类</option>';

	if (!category || !category[cid]) {
		$('#cates_2').html(html);
			fetchChildCategory2(document.getElementById("cates_2").options[document.getElementById("cates_2").selectedIndex].value);
		return false;
	}
	for (i in category[cid]) {
		html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
	}
	$('#cates_2').html(html);
    	fetchChildCategory2(document.getElementById("cates_2").options[document.getElementById("cates_2").selectedIndex].value);

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
  function findgoods(){
    var pcate = $('#pcates').val();
	if (pcate == 0)
	{
		alert('请选择分类');
		return false;
	}
	var ccate = $('#cates_2').val();
	var ccate2 = $('#cate_3').val();
    $.post('<?php  echo create_url('site',array('name' => 'shop','do' => 'mess','op' => 'query'))?>',{pcate:pcate,ccate:ccate,ccate2:ccate2},function(m){
	    $('#c_goods').html(m);
	},"html");	
}
  function fillform()
{
	if ( $('#c_goods').val() == 0)
	{   
		alert('请选择产品');
		return false;
	}
	return true;
}
</script>
<?php  include page('footer');?>