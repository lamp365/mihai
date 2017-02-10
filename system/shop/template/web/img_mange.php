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

<form action="<?php echo web_url('img_mange',array('op'=>'display'));?>" class="form-horizontal" method="post">
	<table class="table table-striped table-bordered table-hover purchase-table-list">
		<tbody>
		<tr>
			<td>
				<li>
					<input style="margin-right:5px;width: 300px; height:30px; line-height:28px; padding:2px 0" name="prefix" id="" type="text" placeholder="模糊匹配图片标题" value="<?php echo $prefix; ?>">
				</li>
				<li >
					<button class="btn btn-primary btn-sm" style="margin-right:10px;"><i class="icon-search icon-large"></i> 搜索</button>
				</li>

				<li style="float: right;">
					<a class="btn btn-primary btn-sm" style="margin-right:10px;" href="<?php echo web_url('img_mange');?>"> 返回目录 </a>
				</li>
			</td>
		</tr>
		</tbody>
	</table>
</form>
<div style="position: relative;">
	<table class="table table-striped table-bordered table-hover">
		<tr >
			<th class="text-center" style="width: 100px;">序号</th>
			<th class="text-center" >缩略图</th>
			<th class="text-center">URL地址</th>
			<th class="text-center" style="width: 310px;">操作</th>
		</tr>

		<tr>
			<td style="text-align:center;">88</td>
			<td style="text-align:center;">88</td>
			<td style="text-align:center;">88</td>
			<td style="text-align:center;">88</td>
		</tr>

	</table>
	<div class="big-img-show">
		<img src="">
	</div>
</div>
<?php } ?>


<?php if(!empty($nextMarker)){ $curl = WEBSITE_ROOT.$_SERVER['REQUEST_URI']; $curl = $curl."&nextMarker={$nextMarker}";?>
<p class="next_page"><a href="<?php echo $curl;?>">下一页</a></p>
<?php } ?>



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
					<label for="reply">内容：</label>
					<textarea type="text" class="form-control" id="reply" name="reply" placeholder="请输入回复"></textarea>
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

<script>

$(function(){
	$(".piclist img").on("click",function(){
		var bigImg = $(this).parent(".onepic").attr("imghref");
		$(".big-img-show").fadeIn();
		$(".big-img-show").find("img").attr("src",bigImg);
	});
	$(".big-img-show").on("click",function(){
		$(this).fadeOut();
	});
	$(".sel_system").change(function(){
		var system = $(this).val();
		if(system != 0){
			var url = "<?php echo web_url('dish',array('op'=>'comment'));?>";
			url += "&system="+system;
			window.location.href= url;
		}
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
