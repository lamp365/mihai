<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<script>
function insertValueQuery(obj) {
	if (obj == 'left'){
		   var ids = $('#sqlquery');
           var myListBox = document.getElementById("sqlquery");
	}else{
		   var ids = $('#tablefields');
           var myListBox = document.sqlform.dummy;
	}
    if(myListBox.options.length > 0) {
        var chaineAj = "";
        var NbSelect = 0;
        for(var i=0; i<myListBox.options.length; i++) {
            if (myListBox.options[i].selected){
                NbSelect++;
                if (NbSelect > 0)
                  chaineAj += '<option value="'+myListBox.options[i].value+'">'+myListBox.options[i].text+'</option>';
				  myListBox.options[i].parentNode.removeChild(myListBox.options[i]);
            }
        }
       if (obj == 'left'){
           $('#tablefields').html($('#tablefields').html()+chaineAj);
	   }else{
		   $('#sqlquery').html($('#sqlquery').html()+chaineAj);
	   }
    }
}
</script>
<h3 class="header smaller lighter blue"><?php  if(!empty($category['id'])) { ?>编辑<?php  }else{ ?>新增<?php  } ?>分类</h3>
<form action="" method="post" name="sqlform" enctype="multipart/form-data" class="form-horizontal" onsubmit="return toVaild()">
	
	<input type="hidden" name="parentid" value="<?php  echo $parent['id'];?>" />
	  		<?php  if(!empty($parentid)) { ?>
	   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 上级分类</label>

										<div class="col-sm-9">
														<?php  echo $parent['name'];?>
										</div>
									</div>
		<?php  } ?>
		
		   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 排序</label>

										<div class="col-sm-9">
														<input type="text" name="displayorder" class="col-xs-10 col-sm-2" value="<?php  echo $category['displayorder'];?>" />
										</div>
									</div>
	
			   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 分类名称</label>

										<div class="col-sm-9">
												
									<input type="text" name="catename" class="col-xs-10 col-sm-2" value="<?php  echo $category['name'];?>" />
										</div>
									</div>
									
								    <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 分类图标</label>

										<div class="col-sm-9">
												
											 <div class="fileupload fileupload-new" data-provides="fileupload">
												<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
												 <?php  if(!empty($category['thumb'])) { ?>
													<img style="width:100%" src="<?php  echo $category['thumb'];?>" alt="" onerror="$(this).remove();">
												  <?php  } ?>
												</div>
												<div>
													<input name="thumb" id="thumb" type="file"  />
													<a href="#" class="fileupload-exists" data-dismiss="fileupload">移除图片</a>
													 <?php  if(!empty($category['thumb'])) { ?>
														<input name="thumb_del" id="thumb_del" type="checkbox" value="1" />删除已上传图片
													 <?php  } ?>
												</div>
											  </div>
												
											
										</div>
									</div>


								<!--

									 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 分类广告图【电脑端】</label>

										<div class="col-sm-9">
												
															 <div class="fileupload fileupload-new" data-provides="fileupload">
			                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
			                        	 <?php  if(!empty($category['adv'])) { ?>
			                            <img style="width:100%" src="<?php  echo $category['adv'];?>" alt="" onerror="$(this).remove();">
			                              <?php  } ?>
			                            </div>
			                        <div>
			                         <input name="adv" id="adv" type="file"  />
			                            <a href="#" class="fileupload-exists" data-dismiss="fileupload">移除图片</a>
			                            	 <?php  if(!empty($category['adv'])) { ?>
			                              <input name="adv_del" id="adv_del" type="checkbox" value="1" />删除已上传图片
			                                 <?php  } ?>
			                        </div>
			                    </div>
												
											
										</div>
									</div>

                                   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 分类广告图【手机端】</label>

										<div class="col-sm-9">
												
															 <div class="fileupload fileupload-new" data-provides="fileupload">
			                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
			                        	 <?php  if(!empty($category['adv_wap'])) { ?>
			                            <img style="width:100%" src="<?php  echo $category['adv_wap'];?>" alt="" onerror="$(this).remove();">
			                              <?php  } ?>
			                            </div>
			                        <div>
			                         <input name="adv_wap" id="adv_wap" type="file"  />
			                            <a href="#" class="fileupload-exists" data-dismiss="fileupload">移除图片</a>
			                            	 <?php  if(!empty($category['adv_wap'])) { ?>
			                              <input name="adv_wap_del" id="adv_wap_del" type="checkbox" value="1" />删除已上传图片
			                                 <?php  } ?>
			                        </div>
			                    </div>
												
											
										</div>
									</div>




									<div class="form-group">
                                          <label class="col-sm-2 control-label no-padding-left">品牌推荐</label>
										  <div class="col-sm-4">
										            <label>已经添加</label>
                                                    <select id="sqlquery" class="form-control" name="sql_query[]" size="13" multiple="multiple" ondblclick="insertValueQuery('left')">
                                                            <?php 
															    if ( is_array($brands) ){
                                                                      foreach ( $brands as $best_id_value ){
                                                                           echo '<option value="'.$best_b[$best_id_value]['id'].'">'.$best_b[$best_id_value]['brand'].'</option>';
																	  }
													             }
															?>  
													 </select>
										  </div>
                                          <div class="col-sm-4">
                                                     <label>未添加</label>
                                                     <select id="tablefields" class="form-control" name="dummy" size="13" multiple="multiple" ondblclick="insertValueQuery('right')">
                                                            <?php 
															    if ( is_array($best_id) ){
                                                                      foreach ( $best_id as $best_id_value ){
                                                                           echo '<option value="'.$best_b[$best_id_value]['id'].'">'.$best_b[$best_id_value]['brand'].'</option>';
																	  }
													             }
															?>
													 </select>
										  </div>
									</div>
	
	  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 分类描述</label>

										<div class="col-sm-9">
											
						<input type="text" name="description" class="col-xs-10 col-sm-5" value="<?php  echo $category['description'];?>" />
												</div>
									</div>

									-->
									
									 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 首页推荐</label>

										<div class="col-sm-9">
					
                        <input type='radio' name='isrecommand' value='1' <?php  if($category['isrecommand']==1) { ?>checked<?php  } ?> /> 是&nbsp;
                        <input type='radio' name='isrecommand' value='0' <?php  if($category['isrecommand']==0) { ?>checked<?php  } ?> /> 否
												</div>
									</div>



	
			 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 是否显示</label>

										<div class="col-sm-9">
                        <input type='radio' name='enabled' value='1' <?php  if($category['enabled']==1) { ?>checked<?php  } ?> /> 是&nbsp;
                        <input type='radio' name='enabled' value='0' <?php  if($category['enabled']==0) { ?>checked<?php  } ?> /> 否
												</div>
									</div>
									
											 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > </label>

										<div class="col-sm-9">
                       
					<input name="submit" type="submit" value="提交" o class="btn btn-primary span3">
												</div>
									</div>
</form>
<script>
    function toVaild(){
        $("#sqlquery option").attr("selected","selected");
		return true;
	}
</script>
<?php  include page('footer');?>
