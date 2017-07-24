<?php  include page('header');?>
<style type="text/css">
	.form-group{
		overflow: hidden;
	}
	.no-padding-left{
		text-align: right;
	}
	.fileupload-exists{
		margin-top: 20px;
	}
	.template{
		//display: none;
	}
	.template-list{
		border: 1px solid #ddd;
		border-radius: 4px;
		padding: 15px 15px 0 15px;
	}
	.template-list ul{
		margin: 0;
		padding: 0;
		overflow: hidden;
	}
	.template-list ul li{
		position: relative;
		cursor: pointer;
		float: left;
		margin-right: 15px;
		margin-bottom: 15px;
	}
	.template-list ul img{
		width: 200px;
    	height: 300px;
	}
	.template-list ul .havecheck{
		position: absolute;
		top: 5px;
		right: 5px;
		width: 20px;
		height: 20px;
	}
	.new-upload{
		display: none;
	}
</style>
<h3 class="header smaller lighter blue">物料管理</h3>
<form class="" method="post" action="" name="">
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-left"> 距离顶部的像素</label>
		<div class="col-sm-2">
			<input type="text" class="form-control" placeholder="距离顶部的像素">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-left"> 距离左边的像素</label>
		<div class="col-sm-2">
			<input type="text" class="form-control" placeholder="距离左边的像素">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-left"> 二维码宽度</label>
		<div class="col-sm-2">
			<input type="text" class="form-control" placeholder="二维码宽度(最大280px)">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-left"> 选择照片</label>
		<div class="col-sm-2">
			<select class="form-control" onchange="selectChange(this)">
				<option value="0">模板照片</option>
				<option value="1">重新上传</option>
			</select>
		</div>
	</div>
	<div class="template form-group">
		<label class="col-sm-1 control-label no-padding-left"> 模板照片</label>
		<div class="col-sm-10">
			<input type="hidden" name="" class="template-val">
			<div class="template-list">
				<ul>
					<li>
						
						<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201706/20170614103059409fe2f0846.jpg">
					</li>
					<li>
						<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201706/20170614103059409fe2f0846.jpg">
					</li>
					<li>
						<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201706/20170614103059409fe2f0846.jpg">
					</li>
					<li>
						<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201706/20170614103059409fe2f0846.jpg">
					</li>
					<li>
						<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201706/20170614103059409fe2f0846.jpg">
					</li>
					<li>
						<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201706/20170614103059409fe2f0846.jpg">
					</li>
					<li>
						<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201706/20170614103059409fe2f0846.jpg">
					</li>
					<li>
						<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201706/20170614103059409fe2f0846.jpg">
					</li>
					<li>
						<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201706/20170614103059409fe2f0846.jpg">
					</li>
					<li>
						<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201706/20170614103059409fe2f0846.jpg">
					</li>
					<li>
						<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201706/20170614103059409fe2f0846.jpg">
					</li>
					<li>
						<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201706/20170614103059409fe2f0846.jpg">
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="new-upload form-group">
		<label class="col-sm-1 control-label no-padding-left">重新上传</label>
		<div class="col-sm-2">
			<div class="fileupload fileupload-new" data-provides="fileupload">
				<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
					<img src="">
				</div>
				<div>
					<input name="thumb" id="thumb" type="file" />
					<a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">移除图片</a>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-left">&nbsp;</label>
		<div class="col-sm-2">
			<a href="javascript:;" class="btn btn-primary">保存</a>
		</div>
	</div>
</form>
<script type="text/javascript">
	$(function(){
		$(".template-list li img").on("click",function(){
			var imgurl = $(this).attr("src");
			var html = '<img class="havecheck" src="<?php echo WEBSITE_ROOT;?>/themes/wap/__RESOURCE__/recouse/images/shopping_habits_ok.png">';
			$(".havecheck").remove();
			$(this).parent("li").append(html);
			$(".template-val").val(imgurl);
		});
	})
	function selectChange(obj){
		if($(obj).val()==0){
			$(".template").show();
			$(".new-upload").hide();
		}else{
			$(".template").hide();
			$(".new-upload").show();
		}
	}
</script>
<?php  include page('footer');?>
