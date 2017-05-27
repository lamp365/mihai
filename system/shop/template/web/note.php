<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/kindeditor-min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/lang/zh_CN.js"></script>  
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.config.js?x=201508021"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.all.min.js?x=141"></script>
<script type="text/javascript">var ue = UE.getEditor('container');</script>

<h3 class="header smaller lighter blue">图文笔记编辑</h3>

<form action="" method="post" class="form-horizontal">
	<input type="hidden" name="op" value="post">
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 标题：</label>

		<div class="col-sm-9">
			<input type="text" name="notetitle" class="input-height" value="<?php echo $note['title'];?>">
		</div>
	</div>
	
	<div class="form-group">
									<label class="col-sm-2 control-label no-padding-left" > 图片：</label>

									<div class="col-sm-9">
			         <span id="selectimage" tabindex="-1" class="btn btn-primary"><i class="icon-plus"></i> 上传照片</span><span style="color:red;">
	        <div id="file_upload-queue" class="uploadify-queue"></div>
	        <ul class="ipost-list ui-sortable" id="fileList">
            	<?php  if(!empty($note['pic'])) { 
						$arrPic = explode(";", $note['pic']);
						foreach($arrPic as $key => $value){
							if (!empty($value)) {
				?>
	            <li class="imgbox" style="list-style-type:none;display:inline;  float: left;  position: relative;   width: 125px;  height: 130px;">
	                <span class="item_box">
	                    <img src="<?php echo $value;?>" style="width:50px;height:50px">    </span>
	               		 <a  href="javascript:;" onclick="deletepic(this, <?php  echo $key;?>);" title="删除">删除</a>
	            
	                <input type="hidden" value="<?php echo $value;?>" name="attachment[]">
	            </li>
	            <?php  } } } ?>
	        </ul>
									</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 作者：</label>
		<div class="col-sm-9">
			<?php  if($now_a == '0') { ?>
				<?php  echo get_realname($note['openid']);?>
			<?php  }else{  ?>
				<select name="author" class="author input-height">
					<option value="0" >请选择</option>
					<?php  if(is_array($author)) { 
						foreach($author as $cav) {
							if ($cav['openid'] == $note['openid']) {
								$csed = "selected";
							}else{
								$csed = "";
							}
							?>
							<option value="<?php  echo $cav['openid'];?>" <?php  echo $csed;?>><?php  echo $cav['realname'];?></option>
						<?php  } } ?>
				</select>
			<?php  }  ?>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 内容：</label>
		<div class="col-sm-9">
			<textarea style="height: 150px; margin: 0px; width: 379px;" id="preview" name="preview" cols="50"><?php echo $note['description'];?></textarea>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 是否推荐：</label>

		<div class="col-sm-9">
			<input type="radio" name="isrecommand" value="0" id="isrecommand"
				<?php  if(empty($note) || $note['isrecommand'] == 0) { ?> checked="true"
				<?php  } ?> /> 否 &nbsp;&nbsp;&nbsp; 
				<input type="radio" name="isrecommand" value="1" id="isrecommand"
				<?php  if(!empty($note) && $note['isrecommand'] == 1) { ?> checked="true"
				<?php  } ?> /> 是
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 审核通过：</label>

		<div class="col-sm-9">
			<input type="radio" name="ischeck" value="0" id="ischeck"
				<?php  if(empty($note) || $note['check'] == 0) { ?> checked="true"
				<?php  } ?> /> 否 &nbsp;&nbsp;&nbsp; 
				<input type="radio" name="ischeck" value="1" id="ischeck"
				<?php  if(!empty($note) && $note['check'] == 1) { ?> checked="true"
				<?php  } ?> /> 是
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 创建时间：</label>

		<div class="col-sm-9">
			<?php if (!empty($note['createtime'])) {
				echo date('Y-m-d H:i:s',$note['createtime']);
			} ?>
		</div>
	</div>
	
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"> 编辑时间：</label>

		<div class="col-sm-9">
			<?php if (!empty($note['modifiedtime'])) {
				echo date('Y-m-d H:i:s',$note['modifiedtime']);
			} ?>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left"
			for="form-field-1"> </label>

		<div class="col-sm-9">
			<input name="submit" type="submit" value=" 提 交 " class="btn btn-info" />
		</div>
	</div>
</form>

<script language="javascript">
	$(function(){
	var i = 0;
	$('#selectimage').click(function() {
		var editor = KindEditor.editor({
			allowFileManager : false,
			imageSizeLimit : '10MB',
			uploadJson : '<?php  echo mobile_url('upload')?>'
		});
		editor.loadPlugin('multiimage', function() {
			editor.plugin.multiImageDialog({
				clickFn : function(list) {
					if (list && list.length > 0) {
						for (i in list) {
							if (list[i]) {
								html =	'<li class="imgbox" style="list-style-type:none;display:inline;  float: left;  position: relative;  width: 125px;  height: 130px;">'+
								'<span class="item_box"> <img src="'+list[i]['url']+'" style="width:50px;height:50px"></span>'+
								'<a href="javascript:;" onclick="deletepic(this,0);" title="删除">删除</a>'+
								'<input type="hidden" name="attachment-new[]" value="'+list[i]['filename']+'" />'+
								'</li>';
								$('#fileList').append(html);
								i++;
							}
						}
						editor.hideDialog();
					} else {
						alert('请先选择要上传的图片！');
					}
				}
			});
		});
	});
	//select2下拉框初始化
	$("#brand").select2();
	});

	function deletepic(obj,oid){
		if (confirm("确认要删除？")) {
			
			var $thisob=$(obj);
			var $liobj=$thisob.parent();
			var picurl=$liobj.children('input').val();
			// if (oid == 0)
			// {
	  //           $liobj.remove();
			// 	alert("删除成功");
			// 	return;
			// }
			$liobj.remove();
			// $.post('<?php  echo create_url('site',array('name' => 'shop','do' => 'headline', 'op' => 'delpic'))?>',{ pic:picurl,ids:oid},function(m){
			// 	if(m==1) {
			// 		$liobj.remove();
			// 	} else {
			// 		alert("删除失败");
			// 	}
			// },"html");	
		}
	}
</script>

<?php include page('footer');?>