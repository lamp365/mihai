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
</style>
<h3 class="header smaller lighter blue">商品评论管理</h3>

<form action="<?php echo web_url('dish',array('op'=>'comment'));?>" class="form-horizontal" method="post">
	<table class="table table-striped table-bordered table-hover">
		<tbody>
		<tr>
			<td>
				<li style="float:left;list-style-type:none;padding-top: 10px;margin-right: 15px;">
					时间范围:
					<input type="text" id="datepicker_timestart" name="timestart" value="" readonly="readonly" />
					<script type="text/javascript">
						laydate({
					        elem: '#datepicker_timestart',
					        istime: true, 
					        event: 'click',
					        format: 'YYYY-MM-DD hh:mm:ss',
					        istoday: true, //是否显示今天
					        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
					    });
					    laydate.skin("molv"); 
					</script> -
					<input type="text"  id="datepicker_timeend" name="timeend" value="" readonly="readonly" />
					<script type="text/javascript">
						laydate({
					        elem: '#datepicker_timeend',
					        istime: true, 
					        event: 'click',
					        format: 'YYYY-MM-DD hh:mm:ss',
					        istoday: true, //是否显示今天
					        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
					    });
					    laydate.skin("molv");
					</script>
				</li>

				<li style="float:left;list-style-type:none;">
					<select class="sel_system" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
						<option value="0">系统设备</option>
						<option value="3" <?php if($_GP['system'] == 3) echo "selected"; ?>>IOS</option>
						<option value="2" <?php if($_GP['system'] == 2) echo "selected"; ?>>Android</option>
						<option value="1" <?php if($_GP['system'] == 1) echo "selected"; ?>>PC</option>
					</select>
				</li>

				<li style="float:left;list-style-type:none;">
					<input style="margin-right:5px;margin-top:10px;width: 300px; height:34px; line-height:28px; padding:2px 0" name="keyword" id="" type="text" placeholder="模糊匹配标题或者具体宝贝id" value="<?php echo $keyword; ?>">
				</li>
				<li style="float:left;list-style-type:none;">
					<button class="btn btn-primary" style="margin-right:10px;margin-top:10px;"><i class="icon-search icon-large"></i> 搜索</button>
				</li>

				<li style="list-style-type:none;float: right;">
					<a class="btn btn-primary" style="margin-right:10px;margin-top:10px;" href="<?php echo web_url('dish',array('op'=>'addcomment','type'=>'new'));?>"> 添加评论 </a>
				</li>
			</td>
		</tr>
		</tbody>
	</table>
</form>

<div style="position: relative;">
<table class="table table-striped table-bordered table-hover">
  <tr >
 <th class="text-center" >序号</th>
 <th class="text-center" >宝贝id</th>
<th class="text-center">用户名</th>
	  <th class="text-center">评论商品</th>
	  <th class="text-center">评论内容</th>
	  <th class="text-center">商家回复</th>
	  <th class="text-center">系统设备</th>
	  <th class="text-center" style="width: 310px;">操作</th>
  </tr>
		<?php $index=0; if(!empty($list)) { foreach($list as $item) { ?>
				<tr>
				 <td style="text-align:center;"><?php echo  ++$index ?></td>
				 <td style="text-align:center;"><?php echo  $item['did']; ?></td>
				 <td style="text-align:center;"><?php  echo $item['username']; ?></td>
                 <td style="text-align:center;"><a href="<?php echo create_url('mobile',array('name'=>'shopwap','do'=>'detail','op'=>'dish','id'=>$item['did']));?>" target="_blank"><?php  echo $item['title'];?></a></td>
                 <td style="text-align:center;">
					 <?php  echo $item['comment'];?>
					 <div class="piclist">
						 <?php if(!empty($item['piclist'])){ ?>
						 <?php foreach($item['piclist'] as $picurl){ ?>
							 <span imghref="<?php echo download_pic($picurl['img'],600,600);?>" class="onepic"><img src="<?php echo  download_pic($picurl['img'],50,50);?>" style="width: 50px;height: 50px;border: 1px solid #C6C6C6;background: #ffffff;padding: 1px;"/></span>
						 <?php } ?>
						 <?php } ?>
					 </div>
				 </td>
				  <td style="text-align:center;"><?php echo  $item['reply']; ?></td>
                 <td style="text-align:center;">来自 <b><?php  echo getSystemType($item['system']);?></b></td>
				 <td style="text-align:center;" style="width: 310px;">
					 <?php if(isHasPowerToShow('shop','dish','addcomment','add')){ ?>
						 <a  class="btn btn-xs btn-info" href="<?php  echo web_url('dish', array('dishid' => $item['did'], 'op' => 'addcomment'))?>" ><i class="icon-edit"></i>&nbsp;添&nbsp;加&nbsp;</a>
					 <?php } ?>
					 <?php if(isHasPowerToShow('shop','dish','replycomment','delete')){ ?>
						 <a data-id="<?php echo $item['id']?>" class="btn btn-xs btn-info reply" href="javascript:;"><i class="icon-edit"></i>&nbsp;回&nbsp;复&nbsp;</a>
					 <?php } ?>
					 <?php if(isHasPowerToShow('shop','dish','delcomment','delete')){ ?>
						 <a  class="btn btn-xs btn-info" href="<?php  echo web_url('dish', array('id' => $item['id'], 'op' => 'delcomment'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a>
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
