<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<style>
	.parent_show .second-level{
		width: 50px;
	    padding-left: 25px;
	}
	.parent_show .second-level-name{
		width:50px;margin-left: 10px;
	}
	.parent_show .second-level-img{
		width: 60px;
		height: 50px;
		padding: 1px;
	    border: 1px solid #ccc;
	    float: left;
	    margin-right: 10px;
	}
</style>
<h3 class="header smaller lighter blue">分类列表 <a href="<?php  echo web_url('category', array('op' => 'csv_post'))?>" style="float:right;font-size:14px;"><i class="icon-plus-sign-alt"></i>批量导入分类</a></h3>
<table class="table table-striped table-bordered table-hover goods-list-table">
	<tbody>
	<tr>
		<td>

			<li>
				<select name="industry_p1_id" id="industry_p1_id" onchange="get_next_instry(this)" style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0">
					<option value="0">请选择一级行业</option>
					<?php foreach($first_instu as $one_row) {
						$sel = '';
						if($one_row['gc_id'] == $_GP['industry_p1_id']){
							$sel = "selected";
						}
						echo "<option {$sel} value = '{$one_row['gc_id']}'>{$one_row['gc_name']}</option>";
						}
					?>
				</select>
				<select name="industry_p2_id" id="industry_p2_id" onchange="seach_category(this)" style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0">
					<?php
						if(empty($second_instu)){
							echo '<option value="0">请选择二级行业</option>';
						}else{
							foreach($second_instu as $two_row) {
								$sel = '';
								if($two_row['gc_id'] == $_GP['industry_p2_id']){
									$sel = "selected";
								}
								echo "<option {$sel} value = '{$two_row['gc_id']}'>{$two_row['gc_name']}</option>";
							}
						}
					?>


				</select>
			</li>
		</td>
	</tr>
	</tbody>
</table>

		<form action="" class="form-horizontal" method="post" onsubmit="return formcheck(this)" style="border-top:1px solid #ddd">
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
			   if(is_array($all_category)) { foreach($all_category as $row) {
				   $vid = $row['id'];
		    ?>
				<tr class="first_cat" data-id="<?php echo $vid;?>">
					<td style="width:50px;"><a href="javascript:void(0)" onclick="hiddens(this,<?php  echo $row['id'];?>)"><i class="icon-resize-full"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<input  type="text"  style="width:50px"  name="displayorder[<?php  echo $row['id'];?>]" value="<?php  echo $row['displayorder'];?>"></td>
               		<td>&nbsp;&nbsp;&nbsp;
					  <?php  echo $row['name'];?>&nbsp;&nbsp; <img src="<?php  echo $row['thumb'];?>"  height="40" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' />
					</td>
					<td>            <?php  if($row['isrecommand']==1) { ?>
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
							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('category', array('parentid' => $row['id'], 'op' => 'post'))?>"><i class="icon-plus-sign-alt"></i> 添加子分类</a>
							&nbsp;&nbsp;
						<?php } ?>

						<a class="btn btn-xs btn-info" href="<?php  echo create_url('mobile',array('name' => 'shopwap','do' => 'goodlist','pcate' =>  $row['id']))?>" target="_blank"><i class="icon-eye-open"></i>&nbsp;查&nbsp;看&nbsp;</a>&nbsp;&nbsp;


							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('category', array('op' => 'post', 'id' => $row['id']))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;


							<a class="btn btn-xs btn-info"  href="<?php  echo web_url('category', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此分类吗？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>

					</td>
				</tr>
			<?php  } } ?>
				<tr>
					<td colspan="4">
							<a  href="<?php  echo web_url('category', array('op' => 'post'))?>"><i class="icon-plus-sign-alt"></i> 添加新分类</a>&nbsp;&nbsp;
							<a  href="<?php  echo web_url('category', array('op' => 'csv_post'))?>"><i class="icon-plus-sign-alt"></i>批量导入分类</a>

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
//第二级分类
   function hiddens(thisObj,obj){
      $('.parent_'+obj).fadeToggle();
	  iFrame();
	  var url = "<?php echo web_url('category',array('name'=>'shop','op'=>'display'));?>";
	  if(  $('.parent_'+obj).hasClass('parent_show') ){
	  	return false;
	  }else{
	  		$.post(url,{id:obj},function(data){
		  	var data_val = data.message;
		  	var category_html="";
		  		if( data.errno == 200 ){
		  			$.each(data_val,function(index,ele){
					  	var isrecommand_html = "";
					  	var app_isrecommand_html = "";
					  	var enabled_html = "";
					  	var label_html = "";
						if(ele.isrecommand == 1){
			  				isrecommand_html = '&nbsp;<span class="label label-success">首页推荐</span>'
			  			}
			  			if(ele.app_isrecommand == 1){
			  				app_isrecommand_html = '&nbsp;<span class="label label-success">app首页推荐</span>'
			  			}
			  			if(ele.enabled == 1){
			  				enabled_html = '&nbsp;<span class="label label-success">显示</span>'
			  			}else{
			  				label_html = '&nbsp;<span class="label label-danger">隐藏</span>'
			  			}
			  			category_html += '<tr class="parent_'+obj+' parent_show"><td class="second-level"><a href="javascript:void(0)" onclick="secondHiddens(this,'+ele.id+')"><i class="icon-resize-full" style="color: #4fadff;"></i></a><input type="text" class="second-level-name" name="displayorder['+ele.id+']" value='+ele.displayorder+'></td>'+
			  								'<td>'+ele.name+'<img class="second-level-img" src="'+ele.thumb+'" onerror="$(this).remove()" ></td><td>'+isrecommand_html+app_isrecommand_html+enabled_html+label_html+'</td>'+
			  								'<td>'+
											'<a class="btn btn-xs btn-info" href="index.php?mod=site&amp;parentid='+ele.id+'&amp;op=post&amp;name=shop&amp;do=category"><i class="icon-plus-sign-alt"></i> 添加子分类</a> &nbsp;&nbsp;'+
											'<a class="btn btn-xs btn-info" href="index.php?mod=mobile&amp;name=shopwap&amp;do=goodlist&amp;ccate='+ele.id+'" target="_blank"><i class="icon-eye-open"></i>&nbsp;查&nbsp;看&nbsp;</a>&nbsp;&nbsp;'+
											'<a class="btn btn-xs btn-info" href="index.php?mod=site&amp;op=post&amp;id='+ele.id+'&amp;parentid='+ele.parentid+'&amp;name=shop&amp;do=category"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;'+
											'<a class="btn btn-xs btn-info" href="index.php?mod=site&amp;op=delete&amp;id='+ele.id+'&amp;name=shop&amp;do=category" onclick="return confirm("确认删除此分类吗？");return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>'+
											'</td></tr>';
					  	});
		  			$(thisObj).parents(".first_cat").after(category_html).show();
		  		}else{
//		  			alert(data.message);
		  		}
	   		},"json")
	  }

   }
   //第三级分类
   function secondHiddens(thisObj,obj){
      $('.second_'+obj).fadeToggle();
	  iFrame();
	  var url = "<?php echo web_url('category',array('name'=>'shop','op'=>'display'));?>";
	  if(  $('.second_'+obj).hasClass('parent_show') ){
	  	return false;
	  }else{
	  		$.post(url,{id:obj},function(data){
		  	var data_val = data.message;
		  	var category_html="";
		  		if( data.errno == 200 ){
		  			$.each(data_val,function(index,ele){
					  	var isrecommand_html = "";
					  	var app_isrecommand_html = "";
					  	var enabled_html = "";
					  	var label_html = "";
						if(ele.isrecommand == 1){
			  				isrecommand_html = '&nbsp;<span class="label label-success">首页推荐</span>'
			  			}
			  			if(ele.app_isrecommand == 1){
			  				app_isrecommand_html = '&nbsp;<span class="label label-success">app首页推荐</span>'
			  			}
			  			if(ele.enabled == 1){
			  				enabled_html = '&nbsp;<span class="label label-success">显示</span>'
			  			}else{
			  				label_html = '&nbsp;<span class="label label-danger">隐藏</span>'
			  			}
			  			category_html += '<tr class="second_'+obj+' parent_show"><td class="second-level" style="padding-left: 50px;"><input type="text" class="second-level-name" name="displayorder['+ele.id+']" value='+ele.displayorder+'></td>'+
			  								'<td>'+ele.name+'<img class="second-level-img" src="'+ele.thumb+'" onerror="$(this).remove()" ></td><td>'+isrecommand_html+app_isrecommand_html+enabled_html+label_html+'</td>'+
			  								'<td>'+
											'<a class="btn btn-xs btn-info" href="index.php?mod=site&amp;parentid='+ele.id+'&amp;op=post&amp;name=shop&amp;do=category"><i class="icon-plus-sign-alt"></i> 添加子分类</a> &nbsp;&nbsp;'+
											'<a class="btn btn-xs btn-info" href="index.php?mod=mobile&amp;name=shopwap&amp;do=goodlist&amp;ccate='+ele.id+'" target="_blank"><i class="icon-eye-open"></i>&nbsp;查&nbsp;看&nbsp;</a>&nbsp;&nbsp;'+
											'<a class="btn btn-xs btn-info" href="index.php?mod=site&amp;op=post&amp;id='+ele.id+'&amp;parentid='+ele.parentid+'&amp;name=shop&amp;do=category"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;'+
											'<a class="btn btn-xs btn-info" href="index.php?mod=site&amp;op=delete&amp;id='+ele.id+'&amp;name=shop&amp;do=category" onclick="return confirm("确认删除此分类吗？");return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>'+
											'</td></tr>';
					  	});
		  			$(thisObj).parents(".parent_show").after(category_html).show();
		  		}else{
//		  			alert(data.message);
		  		}
	   		},"json")
	  }

   }
   function iFrame() {
        var ifm= window.parent.document.getElementById("main");
        var subWeb = window.parent.document.frames ? window.parent.document.frames["main"].document :ifm.contentDocument;
            if(ifm != null && subWeb != null) {
                ifm.height = subWeb.body.scrollHeight + 60;
            }
    }

	function get_next_instry(obj){
		var industry_p1_id = $(obj).val();
		if(industry_p1_id == 0){
			$("#industry_p2_id").html("<option value='0'>请选择二级行业</option>");
		}else{
			var url = "<?php echo web_url('category',array('op'=>'getNextInstry'))?>";
			$.post(url,{industry_p1_id,industry_p1_id},function(data){
				if(data.errno == 1){
					var data_o = data.data;
					var option = '<option value="0">请选择二级行业</option>';
					for(var i=0;i<data_o.length;i++){
						var obj = data_o[i];
						option = option+"<option value='"+obj.gc_id+"'>"+obj.gc_name+"</option>"
					}
					$("#industry_p2_id").html(option);
				}else{
					$("#industry_p2_id").html("<option value='0'>请选择二级行业</option>");
				}
			},'json');
		}
	}

	function seach_category(obj){
		var industry_p1_id = $("#industry_p1_id").val();
		var industry_p2_id = $("#industry_p2_id").val();
		if(industry_p2_id == 0 || industry_p1_id == 0){
			return  false;
		}
		var url = "<?php echo web_url('category',array('op'=>'display')); ?>";
		url = url+"&industry_p1_id="+industry_p1_id+"&industry_p2_id="+industry_p2_id;
		window.location.href = url;
	}
</script>
<?php  include page('footer');?>
