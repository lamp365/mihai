<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue"><?php if(empty($_GP['id'])){ echo '添加晒单';}else{ echo '修改晒单';} ?>&nbsp;&nbsp;&nbsp;</h3>
<?php if(empty($draw_goods)){ echo "<font color='red'>暂无中奖商品，还不能发布晒单</font>"; }?>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
		<input type="hidden" name="id" class="col-xs-10 col-sm-2" value="<?php echo $article['id'];?>" />


	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 中奖商品</label>

		<div class="col-sm-9">
			<select  id="award_id" name="award_id" onchange="get_zuozhe(this)">
				<option value="0">请选择中奖商品</option>
				<?php if(!empty($draw_goods)){
							foreach($draw_goods as $item){
								if($article['award_id'] == $item['id']){
									$sel = "selected";
								}else{
									$sel = '';
								}
								echo "<option value='{$item['id']}' {$sel}>{$item['title']}</option>";
							}
				       }
				?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 中奖作者</label>

		<div class="col-sm-9">
			<select  id="openid" name="openid" >
				<?php if($article['openid']){ $member_people = member_get($article['openid']); ?>
					<option value="<?php echo $article['openid'];?>"><?php echo empty($member_people['realname'])? $member_people['mobile'] : $member_people['realname'];?></option>
				<?php }else{ ?>
					<option value="0">请选择中奖作者</option>
				<?php } ?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 标题</label>

		<div class="col-sm-9">
			<input type="text" name="title" class="col-xs-10 col-sm-3" value="<?php echo $article['title'];?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 点赞次数</label>

		<div class="col-sm-9">
			<input type="text" name="zan_num" class="col-xs-10 col-sm-2" value="<?php echo $article['zan_num'];?>" />
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
		<label class="col-sm-2 control-label no-padding-left" >内容</label>

		<div class="col-sm-9">
			<textarea name="content" class="span7" style="height:400px;width:800px" id="content"><?php echo $article['content'];?></textarea>
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
 function get_zuozhe(obj){
	 var award_id = $(obj).val();
	 if(award_id != 0){
		 var url = "<?php echo web_url('shaidan',array('op'=>'get_zuozhe'));?>";
		 $.post(url,{'award_id':award_id},function(data){
			if(data.errno == 200){
				var zuozhe  = data.message;
				var html = '';
				for(var i=0;i<zuozhe.length;i++){
					var item = zuozhe[i];
					html = html+"<option value='"+ item.openid +"' >"+ item.nickname +"</option>";
				}
				$("#openid").html(html);
			}else{
				no_zuozhe();
			}
		 },'json');
	 }else{
		 no_zuozhe();
	 }
 }

	function no_zuozhe(){
		var html ="<option value='0'>请选择中奖作者</option>";
		$("#openid").html(html);
	}
</script>

<?php  include page('footer');?>
