<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>

<style type="text/css">
	.piclist{
		cursor: pointer;
	}
	.big-img-show{
		display: none;
		position: absolute;
		top: 50%;
		left: 50%;
		margin: -300px 0 0 -300px;
		width: 600px;
		height: 600px;
		cursor: pointer;
	}
	.big-img-show img{
		max-width: 100%;
	}
	.left-span{
		float: left;
	    line-height: 28px;
	    background-color: #ededed;
	    padding: 0 5px;
	    border: 1px solid #cdcdcd;
	    border-right: 0;
	    font-size: 12px;
	}
	.purchase-table-list .li-height{
	    height: 30px;
	    padding-left: 5px;
	}
	.purchase-table-list tr{
		background-color: #fff!important;
	}
	.purchase-table-list li{
		float: left;
		list-style-type: none;
		margin-right: 10px;
	}
	.img_list li{
		display: inline-block;
		width: 200px;
		height: 220px;
		margin-right:25px;
		margin-bottom:20px;
	}
	.img_list li img{
		cursor: pointer;
	}
	.img_list li p{
		text-align: center;
		font-size: 20px;
	}
	.img_list li span{
		cursor: pointer;
		font-size: 10px;
		display: none;
	}
	.next_page{
		display: inline-block;
		padding-right: 15px;
		margin-bottom:20px;
		float: left;
	}
</style>
<h3 class="header smaller lighter blue">图片列表</h3>

<?php if($has_dir){  ?>
<form action="<?php echo web_url('img_mange',array('op'=>'addDir'));?>" class="form-horizontal" method="post">
	<table class="table table-striped table-bordered table-hover purchase-table-list">
		<tbody>
		<tr>
			<td>

				<li>
					<input style="margin-right:5px;width: 300px; height:30px; line-height:28px; padding:2px 0" name="dirname" id="" type="text" placeholder="输入要创建的目录名" value="<?php echo $prefix; ?>">
				</li>
				<li >
					<button type="submit" class="btn btn-primary btn-sm" style="margin-right:10px;"><i class="icon-add icon-large"></i> 添加目录</button>
				</li>
			</td>
		</tr>
		</tbody>
	</table>
</form>
<div style="position: relative;">
	<ul class="img_list">
		<?php foreach($dir_list as $dir_item){ ?>
		<li><img style="width: 200px;" src="<?php echo RESOURCE_ROOT;?>/addons/common/image/dir.png" data-dirname="<?php echo rtrim($dir_item,"/");?>">
			<p><?php echo rtrim($dir_item,"/");?>&nbsp;<span class="btn btn-xs btn-danger del" data-dirname="<?php echo rtrim($dir_item,"/");?>">删除</span></p>
		</li>
		<?php } ?>
	</ul>
</div>

	<script>
		$(".img_list li").mousemove(function(){
			$(this).find("span").show();
		})
		$(".img_list li").mouseout(function(){
			$(this).find("span").hide();
		})
		$(".img_list li .del").click(function(){
			if(confirm("确认删除么？")){
				var dirname = $(this).data("dirname");
				var url = "<?php echo web_url('img_mange',array('op'=>'del_dir'));?>";
				url = url +"&dirname="+dirname;
				window.location.href = url;
			}

		})
		$(".img_list li img").click(function(){
			var dirname = $(this).data("dirname");
			var url = "<?php echo web_url('img_mange');?>";
			url = url +"&prefix="+dirname+"/";
			window.location.href = url;
		})
	</script>
<?php }else{ ?>
	<ul id="myTab" class="nav nav-tabs">
		<li class="active">
			<a href="#pic_search" data-toggle="tab">
				图片搜索
			</a>
		</li>
		<li><a href="#pic_upload" data-toggle="tab">图片上传</a></li>
	</ul>
	<div id="myTabContent" class="tab-content">
		<div class="tab-pane fade in active" id="pic_search">
			<table class="table table-striped table-bordered table-hover purchase-table-list">
				<tbody>
				<tr>
					<td>
						<li>
							<select name="sel_dir" id="sel_dir2" style="height: 28px;line-height: 28px;">

							</select>
						</li>
						<li>
							<input style="margin-right:5px;width: 300px; height:30px; line-height:28px; padding:2px 0" name="prefix" id="prefix" type="text" placeholder="模糊匹配图片标题" value="<?php echo $search_key; ?>">
						</li>
						<li >
							<button class="btn btn-primary btn-sm" style="margin-right:10px;" onclick="get_search()"><i class="icon-search icon-large"></i> 搜索</button>
						</li>

						<li style="float: right;">
							<a class="btn btn-primary btn-sm" style="margin-right:10px;" href="<?php echo web_url('img_mange');?>"> 返回目录 </a>
						</li>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="tab-pane fade" id="pic_upload">
			<table class="table table-striped table-bordered table-hover purchase-table-list">
				<tbody>
				<form action="<?php echo web_url('img_mange',array('op'=>"uploadPic"));?>" method="post" enctype="multipart/form-data">
				<tr>
					<td>
						<li style="line-height: 26px;">上传文件：</li>
						<li>
							<select name="sel_dir" id="sel_dir" style="height: 28px;line-height: 28px;">

							</select>
						</li>
						<li style="line-height: 26px;">文件命名：</li>
						<li>
							<select name="rename_type"  style="height: 28px;line-height: 28px;">
								<option value="1">系统随机命名</option>
								<option value="2">按照文件原名</option>
							</select>
						</li>
						<li>
							<input style="line-height: 26px;" name="picture" type="file" value="" id="picture">
						</li>

						<li>
							<button type="submit" class="btn btn-warning btn-sm">确认上传</button>
						</li>


						<li style="float: right;">
							<a class="btn btn-primary btn-sm" style="margin-right:10px;" href="<?php echo web_url('img_mange');?>"> 返回目录 </a>
						</li>
					</td>
				</tr>
				</form>
				</tbody>
			</table>
		</div>
	</div>

<div style="position: relative;">
	<table class="table table-striped table-bordered table-hover">
		<tr >
			<th class="text-center" style="width: 100px;">序号</th>
			<th class="text-center" >缩略图</th>
			<th class="text-center">URL地址</th>
			<th class="text-center" style="width: 310px;">操作</th>
		</tr>
		<?php if(!empty($pic_list)){  $img_arr = array('png','jpg','jpeg','gif') ;foreach($pic_list as $key => $pic_one){  ?>
		<tr class="one_pic">
			<td style="text-align:center;"><?php echo ++$key;?></td>
			<td style="text-align:center;" class="thumb">
				<?php $pic_explode = explode(".",$pic_one);
				      $purl = '';
						if(in_array(strtolower($pic_explode[1]),$img_arr)) {
							$purl = aliyunOSS::aliurl."/".$pic_one;
							$small_pic = download_pic($purl,50,50,2);
							echo "<img class='is_pic' src='{$small_pic}'/>";
						}else{
							echo "不是图片";
						}
				?>
			</td>
			<td style="text-align:center;">
				<?php $purl2 = aliyunOSS::aliurl."/".$pic_one; echo "<a href='{$purl2}' target='_blank'>{$purl2}</a>"; ?>&nbsp;&nbsp;
				<span class="btn btn-primary btn-xs" data-url="<?php echo $purl2;?>" onclick="copy_url(this)">复制链接</span>
			</td>
			<td style="text-align:center;">
				<span <?php echo "data-pic='{$purl}'";?> onclick="setPic(this)" style="cursor: pointer" class="btn btn-info btn-sm">设置大小</span>
				<?php if($purl){ ?>
				<span class="btn btn-primary btn-sm" data-url="<?php echo $pic_one;?>" onclick="fugai_pic(this)">覆盖原图</span>
				<?php } ?>
			</td>
		</tr>
		<?php }} ?>
	</table>
</div>

	<div class="modal fade" id="fufai_pic_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<form action="<?php echo web_url('img_mange',array('op'=>"fugai_pic"));?>" method="post" enctype="multipart/form-data">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">覆盖图片(<span class="tit"></span>)</h4>
				</div>
				<div class="modal-body">
					<div>原图：<img class="old_pic_url" src=""></div>
					<input type="hidden" name="hide_old_pic" value="">
					<div style="margin-top: 15px;">
						<input style="line-height: 26px;" name="fugai_pic" type="file" value="">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
					<button type="submmit" class="btn btn-primary">确认覆盖</button>
				</div>
			</div><!-- /.modal-content -->
			</form>
		</div><!-- /.modal -->
	</div>

	<script>
		function setPic(obj){
			var the_obj = $(obj).closest('.one_pic').find(".thumb img");
			if(the_obj.length == 0){
				alert("对不起，不是图片");
			}else{
				$("#hide_img_url").val($(obj).data('pic'));
				$("#myModal").modal("show");
			}
		}
		function get_search(){
			if($("#prefix").val() == '' && $("#sel_dir2").val() == -1){
				alert("不能为空！");
			}else{
				var url     = "<?php echo web_url('img_mange',array('op'=>'display'));?>";
				var pre_dir = $("#sel_dir2").val();
				if(pre_dir == -1){
					pre_dir = '';
				}else{
					pre_dir = pre_dir+"/";
				}
				var prefix = pre_dir+$("#prefix").val()
				url = url+ "&prefix="+prefix;
				window.location.href=url;
			}
		}

		$(function(){
			$(".getImgSize").click(function(){
				var url = "<?php echo web_url('img_mange',array('op'=>"getImgSize"));?>";
				if($("#sel_type").val() == 0){
					$(".show_res").show();
					$(".show_res").find("a").attr("href",$("#hide_img_url").val());
					$(".show_res").find("a").html($("#hide_img_url").val());
					return '';
				}else if($("#width").val() == '' && $("#height").val()==''){
					alert("请设置宽或高！");
					return '';
				}
				if($("#sel_type").val()>1 && ($("#width").val() == '' || $("#height").val()=='')){
					alert('',"固定拉伸和裁减，宽和高都要设置");
					return '';
				}
				var json_data = {
					'type'   : $("#sel_type").val(),
					'img_url': $("#hide_img_url").val(),
					'width'  : $("#width").val(),
					'height' : $("#height").val()
				};
				$.post(url,json_data,function(data){
					var img_url = data.message;
					$(".show_res").show();
					$(".show_res").find("a").attr("href",img_url);
					$(".show_res").find("a").html(img_url);
				},'json');
			});

			//获取目录
			var url = "<?php echo web_url('img_mange',array('op'=>'getDir')); ?>";
			$.post(url,{},function(data){
				var dir = data.message;
				var opt = "<option value='-1'>请选择目录</option>";
				var opt = opt+"<option value='0'>按最新时间目录</option>";
				var pre_dir = "<?php echo $pre_dir;?>";

				var opt2 = "<option value='-1'>请选择目录</option>";
				for(var i=0; i< dir.length;i++){
					var the_dir = dir[i];
					var the_dir = the_dir.replace('/','');
					opt = opt+"<option value='"+ the_dir +"'>"+ the_dir +"</option>";

					if(pre_dir == the_dir){
						opt2 = opt2+"<option value='"+ the_dir +"' selected>"+ the_dir +"</option>";
					}else{
						opt2 = opt2+"<option value='"+ the_dir +"'>"+ the_dir +"</option>";
					}

				}
				$("#sel_dir").html(opt);
				$("#sel_dir2").html(opt2);
			},'json');
		})

		function fugai_pic(obj){
			var picname = $(obj).data('url');
			var picurl  = $(obj).closest('.one_pic').find('.is_pic').attr('src');
			$("#fufai_pic_modal .tit").html(picname);
			$("#fufai_pic_modal").find("input[name='hide_old_pic']").val(picname);
			$("#fufai_pic_modal .old_pic_url").attr('src',picurl);
			$("#fufai_pic_modal").modal('show');
		}
		//copy
		function copy_url(obj){
			var url = $(obj).data('url');
			alert(url);
		}
	</script>
<?php } ?>

<div style="clear: both"></div>
<?php if(!empty($_GP['nextMarker'])){   ?>
<p class="next_page"><a href="javascript:history.back(-1);">上一页</a></p>
<?php } ?>

<?php if(!empty($nextMarker)){ ?>
<p class="next_page"><a href="<?php echo $nextMarker;?>">下一页</a></p>
<?php } ?>



<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">设置大小</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<select name="type" id="sel_type" class="form-control">
						<option value="0">选择类型</option>
						<option value="1">按宽或高等比显示</option>
						<option value="2">按宽高拉伸显示</option>
						<option value="3">按宽高裁减显示</option>
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="width" id="width" placeholder="请输入宽度">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="height" id="height" placeholder="请输入高度">
				</div>
				<input type="hidden" value="" name="img_url" id="hide_img_url">
				<p style="display: none;" class="show_res"><a href="" target="_blank"></a></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
				<button type="button" class="btn btn-primary getImgSize">获取图片</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal -->
</div>

<?php  include page('footer');?>
