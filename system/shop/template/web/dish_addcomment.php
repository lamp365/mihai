<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">添加评论 <span style="padding-left: 15px;font-size: 12px;color: red">头像不给请放空，PC、wap不显示头像，但是APP显示头像</span></h3>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/webuploader.css" />
<script src="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/webuploader.js" type="text/javascript" charset="utf-8"></script>

<style>
	.show_pic>div{
		width: 49px;
		height: 27px;
	}
	.upload_pic{width: 90px;height: 90px;float: left;margin-right:6px;border: 1px solid #F1F1F1;padding: 1px;background: #ffffff;position: relative}
	.upload_pic img{width: 88px;height: 88px;}
	.upload_button_close{
		position: absolute;
		top: -8px;
		right: -8px;
		width: 17px;
		height: 17px;
		background: url('images/close_icon.png') no-repeat -25px 0;
		cursor: pointer;
	}
	.s_upload{
		height: 24px;
		width: 47px;
		background: #C4C1C1;
		border-radius: 6px;
		color: #ffffff;
		display: block;
		text-align: center;
		cursor: pointer;
		line-height: 24px;
	}
	.level img{
		height: 20px;
		width: 20px;
		cursor: pointer;
	}
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

</style>

<form role="form" class="form-horizontal" action="<?php echo web_url('dish',array('op'=>'addcomment'));?>" method="post" enctype="multipart/form-data">
	 <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 宝贝id：</label>
			<div class="col-sm-9">
				<input type="text" name="dishid" id="dishid" maxlength="30" style="padding-left: 6px;" class="span7" value="<?php echo $dishid; ?>">
				&nbsp;&nbsp;<span class="dish_title"><?php if(!empty($dish)) echo $dish['title']; ?></span>
			</div>
	  </div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 用户名：</label>
		<div class="col-sm-9">
			<input type="text" name="username" id="username" maxlength="60" style="padding-left: 6px;" class="span7" value="" placeholder="用户名或者手机号">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 头像：</label>
		<div class="col-sm-9">
			<input type="file" name="face" id="face">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 等级：</label>
		<div class="col-sm-9 level">
			<img src="images/level_over.png" alt=""/>
			<img src="images/level_over.png" alt=""/>
			<img src="images/level_over.png" alt=""/>
			<img src="images/level_over.png" alt=""/>
			<img src="images/level_none.png" alt=""/>
			<input class="level-val" type="hidden" name="rate" value="4">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 评论：</label>
		<div class="col-sm-9">
			<textarea name="comment" style="width:35%;height:100px;border: 1px solid #0a95a6;padding: 10px;" type="text" id="comment"></textarea>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 图片：</label>
		<div class="col-sm-9">
			<div class="show_pic">
				<span class="s_upload">上传</span>
			</div>
			<div class="show_pic_list"></div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" > 系统设备：</label>
		<div class="col-sm-9">
			<select name="system" style="margin-right:10px;margin-top:10px;width: 100px; height:30px; line-height:28px; padding:2px 0">
				<option value="0">系统设备</option>
				<option value="3">IOS</option>
				<option value="2">Android</option>
				<option value="1">PC</option>
			</select>
			<span>不选系统会随机分配</span>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-2 control-label no-padding-left" >&nbsp; </label>
		<div class="col-sm-9">
			<button class="btn btn-md btn-primary" type="submit" name="add_sub" value="sub">确定添加</button>
		</div>
	</div>
</form>

<br/>
<h4>评论列表:<span style="padding-left: 8px;font-size: 14px;color: red"><?php if(!empty($dish)){ echo $dish['title'];}?></span><span style="font-size: 14px;">（共：<?php echo $total;?>）</span></h4>
<div style="position: relative;">
	<table class="table table-striped table-bordered table-hover">
		<tr >
			<th class="text-center" >序号</th>
			<th class="text-center" >宝贝id</th>
			<th class="text-center">用户名</th>
			<th class="text-center">评论内容</th>
			<th class="text-center">系统设备</th>
			<th class="text-center" style="width: 260px">操作</th>
		</tr>
		<?php $index=0; if(!empty($list)) { foreach($list as $item) { ?>
			<tr>
				<td style="text-align:center;"><?php echo  ++$index ?></td>
				<td style="text-align:center;"><?php echo  $item['did']; ?></td>
				<td style="text-align:center;"><?php  echo $item['username']; ?></td>
				<td style="text-align:center;">
					<?php  echo $item['comment'];?>
					<div class="piclist">
						<?php if(!empty($item['piclist'])){ ?>
							<?php foreach($item['piclist'] as $picurl){ ?>
								<span imghref="<?php echo download_pic($picurl['img'],600,600);?>" class="onepic"><img src="<?php echo  download_pic($picurl['img'],50,50);;?>" style="width: 50px;height: 50px;border: 1px solid #C6C6C6;background: #ffffff;padding: 1px;"/></span>
							<?php } ?>
						<?php } ?>
					</div>
				</td>
				<td style="text-align:center;">来自 <b><?php  echo getSystemType($item['system']);?></b></td>
				<td style="text-align:center;" style="width: 260px">
					<?php if(isHasPowerToShow('shop','dish','delcomment','delete')){ ?>
						<a  class="btn btn-xs btn-info" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'delcomment'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
					<?php } ?>
					<?php if(isHasPowerToShow('shop','dish','replycomment','delete')){ ?>
						<a data-id="<?php echo $item['id']?>" class="btn btn-xs btn-info reply" href="javascript:;"><i class="icon-edit"></i>&nbsp;回&nbsp;复&nbsp;</a>
					<?php } ?>
					<?php if(isHasPowerToShow('shop','dish','topcomment')){ ?>
						<a  class="btn <?php if($item['istop'] == 1){ echo "btn-warning"; }else{ echo "btn-primary"; }?> btn-xs" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'topcomment','istop'=>$item['istop']))?>" ><?php if($item['istop'] == 1){ echo "取消置顶";}else{ echo "置顶评论"; } ?></a>
					<?php } ?>
					<?php if(isHasPowerToShow('shop','dish','downcomment')){ ?>
						<a  class="btn btn-xs btn-danger" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'downcomment','gid'=>$item['goodsid']))?>" >&nbsp;下&nbsp;沉&nbsp;</a>
					<?php } ?>
				</td>
			</tr>
		<?php  } } ?>
	</table>
	<div class="big-img-show">
		<img src="">
	</div>
</div>
<?php  echo $pager;?>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<form action="" method="post" class="reply_form">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">评论回复</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="name">内容：</label>
						<textarea type="text" class="form-control" id="name" placeholder="请输入名称"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
					<button type="submit" class="btn btn-primary">确定回复</button>
				</div>
			</div><!-- /.modal-content -->
		</form>
	</div><!-- /.modal -->
</div>

<br/>
<br/>
<br/>
<script>
$("#dishid").blur(function(){
	var dishid = $(this).val();
	if(isNaN(dishid)){
		alert('请输入数字id');
		return false;
	}
	var url = "<?php echo web_url('dish',array('op'=>'addcomment'));?>";
	url += "&dishid="+dishid;
	window.location.href=url;
})

var host = 'http://'+window.location.host+'/';
var uploader = WebUploader.create({

	// 选完文件后，是否自动上传。
	auto: true,

	swf: '__RESOURCE__/recouse/js/webuploader/Uploader.swf',

	// 文件接收服务端。
	server: host+'fileupload.php?savelocal=0',

	// 选择文件的按钮。可选。
	// 内部根据当前运行是创建，可能是input元素，也可能是flash.

	pick: '.show_pic',

	//可以重复上传
	duplicate: true,

	// 只允许选择图片文件。
	accept: {
		title: 'Images',
		extensions: 'gif,jpg,jpeg,bmp,png',
		mimeTypes: 'image/jpg,image/jpeg,image/png'
	}
});
// 当有文件被添加进队列的时候
uploader.on( 'fileQueued', function( file ) {
	uploader.makeThumb(file, function(error, src) {
		if(error) {
			tip('不能预览图片',1);
			return;
		}
	}, 50, 50);
});
// 文件上传成功，给item添加成功class, 用样式标记上传成功。
uploader.on('uploadSuccess', function(file, response) {
	var data = eval("(" +response._raw+ ")");
	if(data.hasOwnProperty('error')){
		tip('上传失败',1);
	}else{
		var html = "<div class='upload_pic'>"+
			"<input type='hidden' name='picurl[]' value='"+ data.name +"'>"+
			"<img src='"+data.name+"' /><span class='upload_button_close' title='删除图' onclick='del(this);'></span>"+
			"</div>";
		$('.show_pic_list').append(html);

		$('#' + file.id).addClass('upload-state-done');
	}

});
// 文件上传失败，显示上传出错。
uploader.on('uploadError', function(file) {
	tip('上传失败',1);
});



function del(ele){
	$(ele).parent('.upload_pic').remove();
}
$(function(){
	$(".piclist img").on("click",function(){
		var bigImg = $(this).parent(".onepic").attr("imghref");
		$(".big-img-show").fadeIn();
		$(".big-img-show").find("img").attr("src",bigImg);
	})
	$(".big-img-show").on("click",function(){
		$(this).fadeOut();
	})
	$(".level img").each(function(index){
		var star='images/level_none.png';
		var starRed='images/level_over.png';
		$(this).on("mouseover click",function(){
			$('.level img').attr('src',star);
			$(this).attr('src',starRed);
			$(this).prevAll().attr('src',starRed);
			$(".level-val").val(parseInt(index)+1);
		});
	});
})

$(".reply").click(function(){
	var id = $(this).data('id');
	var url = "<?php echo web_url('dish',array('op'=>'replycomment')); ?>";
	url = url + '&id='+id;
	$("#myModal").modal('show');
	$(".reply_form").attr('action',url);
})

</script>

<?php  include page('footer');?>
