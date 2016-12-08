<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">编辑文章&nbsp;&nbsp;&nbsp;</h3>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
		<input type="hidden" name="id" class="col-xs-10 col-sm-2" value="<?php echo $article['id'];?>" />
		 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 链接</label>

										<div class="col-sm-9">
											<?php if(!empty($article['id'])){?>
													<input readonly="readlony" type="text"  name="mobile_url" class="col-xs-10 col-sm-6" value="<?php echo WEBSITE_ROOT;?><?php  echo create_url('mobile',array('name' => 'addon8','do' => 'article','id'=>$article['id']))?>" /> &nbsp;&nbsp;&nbsp;<a target="_blank" href="<?php echo WEBSITE_ROOT;?><?php  echo create_url('mobile',array('name' => 'addon8','do' => 'article','id'=>$article['id']))?>">预览</a>
													<?php }else{?>
													提交后生成链接
														<?php }?>
										</div>
									</div>
									
  
		 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 名称</label>

										<div class="col-sm-9">
													<input type="text" name="title" class="col-xs-10 col-sm-3" value="<?php echo $article['title'];?>" />
										</div>
									</div>
								   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >文章属性</label>

										<div class="col-sm-9">
											<select name="state" onchange="checkPcate(this)" id="state">
												<option value="0">选择属性</option>
												<?php
													foreach($stats_arr as $key=> $name){
														if(!empty($article['state']) && $article['state'] ==$key )
															$sel ='selected';
														else
															$sel ='';
														echo "<option value='{$key}' {$sel}>{$name}</option>";
													}
												?>
											</select>
											&nbsp;&nbsp;&nbsp;&nbsp;
											  <input type="checkbox" name="iscommend" value="1" <?php  if(!empty($article['iscommend'])) { ?> checked<?php  } ?>> 热门，
											  <input type="checkbox" name="ishot" value="1" <?php  if(!empty($article['ishot'])) { ?> checked<?php  } ?>> 推荐
										</div>
									</div>	
																								
								 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 阅读次数</label>

										<div class="col-sm-9">
													<input type="text" name="readcount" class="col-xs-10 col-sm-2" value="<?php echo $article['readcount'];?>" />
										</div>
									</div>


	 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 显示顺序</label>

										<div class="col-sm-9">
													<input type="text" name="displayorder" class="col-xs-10 col-sm-2" value="<?php echo $article['displayorder'];?>" />
										</div>
									</div>		
									
								
										 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 分类：</label>

										<div class="col-sm-9">
												  <select  style="margin-right:15px;" id="pcate" name="pcate" onchange="fetchChildCategory(this.options[this.selectedIndex].value)"  autocomplete="off">
                <option value="0">请选择一级分类</option>
                <?php  if(is_array($category)) { foreach($category as $row) { ?>
                <?php  if($row['parentid'] == 0) { ?>
                <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $article['pcate']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
                <?php  } ?>
                <?php  } } ?>
            </select>
            <select  id="cate_2" name="ccate" autocomplete="off">
                <option value="0">请选择二级分类</option>
                <?php  if(!empty($article['ccate']) && !empty($children[$article['pcate']])) { ?>
                <?php  if(is_array($children[$article['pcate']])) { foreach($children[$article['pcate']] as $row) { ?>
                <option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $article['ccate']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
                <?php  } } ?>
                <?php  } ?>
            </select>
										</div>
		</div>
											   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 缩略图</label>

										<div class="col-sm-9">
												
															 <div class="fileupload fileupload-new" data-provides="fileupload">
			                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
			                        	 <?php  if(!empty($article['thumb'])) { ?>
			                            <img style="width:100%" src="<?php  echo $article['thumb'];?>" alt="" onerror="$(this).remove();">
			                              <?php  } ?>
			                            </div>
			                        <div>
			                         <input name="thumb" id="thumb" type="file"  />
			                            <a href="#" class="fileupload-exists" data-dismiss="fileupload">移除图片</a>
			                            	 <?php  if(!empty($article['thumb'])) { ?>
			                              <input name="thumb_del" id="thumb_del" type="checkbox" value="1" />删除已上传图片
			                                 <?php  } ?>
			                        </div>
			                    </div>
												
											
										</div>
									</div>
								
									 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 主题模板</label>

										<div class="col-sm-9">
											 <select  id="mobileTheme" name="mobileTheme">
											 	   <option value="0" <?php  if(empty( $article['mobileTheme'])) { ?> selected="selected"<?php  } ?>>无主题</option>
											 	   <option value="1" <?php  if( $article['mobileTheme']==1) { ?> selected="selected"<?php  } ?>>主题模板1</option>
               								 </select>
											&nbsp;&nbsp;<span>目前只适用于wap</span>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 发布作者</label>

										<div class="col-sm-9">
											<select  id="openid" name="openid" >
												<option value="">请选择发布作者</option>
												<?php
													foreach($author_arr as $author_id=>$name){
														if($article['openid'] == $author_id)
															$sel = "selected";
														else
															$sel = '';
														echo "<option value='{$author_id}' {$sel}>{$name}</option>";
													}
												?>
											</select>
										</div>
									</div>
									
								
									
											 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >简介</label>

										<div class="col-sm-9">
													<textarea style="height:100px;" class="span7" name="description" cols="70"><?php echo $article['description'];?></textarea>
										</div>
									</div>	
									
																 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >内容</label>

										<div class="col-sm-9">
													<textarea name="content" class="span7" style="height:400px;width:800px" id="content"><?php echo $article['content'];?></textarea>
												<p>如果是健康文化系列文章，需要关联一些商品，请用占位符   [shop]宝贝id[/shop]  如：[shop]85[/shop]</p>
										</div>
									</div>	
									<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

										<div class="col-sm-9">
										<input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
										</div>
									</div>
		</form>

<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript">var ue = UE.getEditor('content');</script>
<script>

					
		var category = <?php  echo json_encode($children)?>;
   function fetchChildCategory(cid) {
	   var obj = $('#pcate>option:selected');
	   var txt = $(obj).text();
	   if($.trim(txt) == '健康文化'){
		   $("#state option").each(function(){
			   $(this).prop("selected",false);
			   if($(this).val()=='6'){
				   $(this).prop("selected",true);
			   }
		   })
	   }

		var html = '<option value="0">请选择二级分类</option>';
		if (!category || !category[cid]) {
			$('#cate_2').html(html);
			return false;
		}
		for (i in category[cid]) {
			html += '<option value="'+category[cid][i][0]+'">'+category[cid][i][1]+'</option>';
		}
		$('#cate_2').html(html);
	}

   fetchChildCategory(document.getElementById("pcate").options[document.getElementById("pcate").selectedIndex].value);
   document.getElementById("cate_2").value="<?php echo $article['ccate']?>";

	function checkPcate(obj){
		notheame();
		var stats = $(obj).val();
		if(stats == '6'){  //属于健康文化
			$("#pcate option").each(function(){
				$(this).prop("selected",false);
				var txt = $(this).text();
				if($.trim(txt)=='健康文化'){
					$(this).prop("selected",true);
				}
			})
			//需要有主题
			$("#mobileTheme option").prop("selected",false);
			$("#mobileTheme option[value='1']").prop("selected",true);			
			
		}else if(stats == '4') {  //属于自定义
			//不需要有主题
			$("#mobileTheme option").prop("selected",false);
			$("#mobileTheme option[value='0']").prop("selected",true);
			resetCate();
		}else{
			resetCate();
		}
	}

	function resetCate(){
		$("#pcate option").prop("selected",false);
		$("#pcate option[value='0']").prop("selected",true);
	}
	
	//健康文化文章不能选无主题
	function notheame(){
		//获取文章属性		
		var state = $("#state").val();		
		if(state == 6 || state ==4 ){
			//健康文化和自定义，不能选随便选模板
			$("#mobileTheme").attr("disabled",true); 
		}else{
			$("#mobileTheme").attr("disabled",false);
		}
	}
	
	$(document).ready(function(){
		notheame();
	})
</script>

<?php  include page('footer');?>
