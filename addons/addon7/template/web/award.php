<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<h3 class="header smaller lighter blue">
		<?php 
		if ($_GET['do'] == 'editaward'){ 
			echo '编辑礼品';
		}else{
			echo '添加礼品';
		}
		?>
</h3>

 <form action="" method="post" class="form-horizontal" enctype="multipart/form-data" onsubmit="return fillform()">
        <input type="hidden" name="id" value="<?php  echo $award['id'];?>" />
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 商品类型：</label>
			<div class="col-sm-9">
				<select name="award_type" class="award_type">
					<?php foreach($award_info as $key => $one){
						$sel = '';
						if($key == $award['award_type']){
							$sel = 'selected';
						}
						echo "<option value='{$key}' {$sel}>{$one}</option>";
					} ?>
				</select>
				<span style="display:none" class="choose-again btn btn-primary btn-sm">重新选择</span>
				<input name="gid" type="hidden" id="gid" value="<?php  echo $award['gid'];?>">
			</div>
		</div>
		<div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" > 商品名称：</label>
				<div class="col-sm-9">
						<input type="text" name="title" id="title" maxlength="100" class="span7" style="width:320px;" value="<?php  echo $award['title'];?>" />
				</div>
		</div>		
        <div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" >特别描述</label>
				<div class="col-sm-9">
						<input type="text" name="names" id="names" value="<?php  echo $award['names'];?>"  />
				</div>
		</div>
		<div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" > 首页推荐：</label>
				<div class="col-sm-9">
				       <input type="checkbox" name="isrecommand" value="1" id="isrecommand" <?php  if($award['isrecommand'] == 1) { ?> checked <?php  } ?> /> 首页推荐	
				</div>
		</div>			
					<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 宣传图</label>

										<div class="col-sm-9">
											 				<div class="fileupload fileupload-new" data-provides="fileupload">
			                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
			                        	 <?php  if(!empty($award['logo'])) { ?>
			                            <img style="width:100%" src="<?php  echo $award['logo'];?>" alt="" onerror="$(this).remove();">
			                              <?php  } ?>
										<input type="hidden" name="choose_thumb" value="" id="choose_thumb">
			                            </div>
			                        <div>
			                         <input name="logo" id="logo" type="file"  />
			                            <a href="#" class="fileupload-exists" data-dismiss="fileupload">移除图片</a>
			                        </div>
			                    </div>
										</div>
									</div>
						 <div class="form-group">
							 <label class="col-sm-2 control-label no-padding-left" > 加入许愿池：</label>
							 <div class="col-sm-9">
								 <input type="radio" name="deleted" value="1"  <?php  if($award['deleted'] == 1) { ?> checked <?php  } ?> /> 移除许愿池
								 <input type="radio" name="deleted" value="0"  <?php  if($award['deleted'] == 0) { ?> checked <?php  } ?> /> 加入许愿池
							 </div>
						 </div>

						<?php if($config['open_gift_change'] == 1){ ?>
						 <div class="form-group">
							 <label class="col-sm-2 control-label no-padding-left" > 加入积分兑换：</label>
							 <div class="col-sm-9">
								 <input type="radio" name="add_jifen_change" value="0"  <?php  if($award['add_jifen_change'] == 0) { ?> checked <?php  } ?> /> 移除积分兑换
								 <input type="radio" name="add_jifen_change" value="1"  <?php  if($award['add_jifen_change'] == 1) { ?> checked <?php  } ?> /> 加入积分兑换
							 </div>
						 </div>

						<div class="form-group">
							<label class="col-sm-2 control-label no-padding-left" > 积分兑换值 </label>

							<div class="col-sm-9">
								<input type="number" name="jifen_change"  value="<?php  echo $award['jifen_change'];?>" />
							</div>
						</div>
						<?php } ?>

						 <div class="form-group" style="display:none;">
							 <label class="col-sm-2 control-label no-padding-left" > 兑换类型</label>

							 <div class="col-sm-9">
								 <input type="radio" name="awardtype" value="0" <?php  if($award['awardtype'] == 0) { ?>checked="true"<?php  } ?> /> 人民币  &nbsp;&nbsp;

								 <input type="radio" name="awardtype" value="1"  <?php  if($award['awardtype'] == 1) { ?>checked="true"<?php  } ?> /> 积分 <input type="hidden" name="gold"  value="<?php  echo $award['gold'];?>" />
							 </div>
						 </div>
									
									
									
								
									
									
									     <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 开始日期</label>

										<div class="col-sm-9">
											 <input name="endtime" id="endtime" type="text" value="<?php  echo empty($award['endtime'])?date('Y-m-d H:i',time()):date('Y-m-d H:i',$award['endtime']);?>" readonly="readonly"  /> 
													
										</div>
									</div>
									
									
									     <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >市场价格</label>

										<div class="col-sm-9">
											 <input type="text" id="price" name="price"  value="<?php  echo $award['price'];?>"/>
										</div>
									</div>
									   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 份数切割</label>

										<div class="col-sm-9">
											 <input type="text" name="amount"  value="<?php  echo $award['amount'];?>" />
										</div>
									</div>
									
									    <div class="form-group">
										<?php if($config['active_type'] == 1){  ?>
										<label class="col-sm-2 control-label no-padding-left" > 心愿数目 </label>
										<?php }else{ ?>
										<label class="col-sm-2 control-label no-padding-left" > 积分许愿值 </label>
										<?php } ?>
										<div class="col-sm-9">
											 <input type="text" name="credit_cost"  value="<?php  echo $award['credit_cost']==0?1:$award['credit_cost'];?>"/>
										</div>
									</div>
									
									
										    <div class="form-group" style="display:none;">
										<label class="col-sm-2 control-label no-padding-left" > 简介</label>

										<div class="col-sm-9">
											 		<textarea name="content" id="content" cols="60" rows="8"><?php  echo $award['content'];?></textarea>
										</div>
									</div>
									
									
								  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

										<div class="col-sm-9">
										<input name="submit" type="submit" value=" 提 交 " class="submit btn btn-info"/>
										
										</div>
									</div>

    </form>

	<div class="modal fade" id="goodsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
		<div class="modal-dialog" style="width:800px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">选择一个宝贝&nbsp;
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-6">
							<input type="text" class="form-control" id="good_name" placeholder="请输入宝贝名称">
						</div>
						<div class="col-xs-6">
							<button type="button" class="btn btn-info" onclick="search_goods(this)">搜索</button>
						</div>
					</div>
					<table class="table table-striped">
						<thead>
						<tr>
							<th>选择</th>
							<th>名称</th>
							<th>价格</th>
							<th>缩略图</th>
						</tr>
						</thead>
						<tbody id="show_good_box">
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
					<button type="button" class="btn btn-primary" onclick="sure_choose_good()">确认选择</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal -->
	</div>



<Script>
  function fillform()
{
	var deleted_val = $("input[name='deleted']:checked").val();
	var add_jifen_change = $("input[name='add_jifen_change']:checked").val();
	if( deleted_val == 0 ){
		if($("input[name='credit_cost']").val()==""){
			alert("请输入心愿数目");
			return false;
		}
	}
	if ( add_jifen_change == 1 ){
		if($("input[name='jifen_change']").val()==""){
			alert("请输入积分兑换值");
			return false;
		}
	}
	if ( $('#c_goods').val() == 0)
	{   
		alert('请选择产品');
		return false;
	}
	return true;
}
laydate({
    elem: '#endtime',
    istime: true, 
    event: 'click',
    format: 'YYYY-MM-DD hh:mm:ss',
    istoday: true, //是否显示今天
    start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
});
laydate.skin("molv"); 
$(".award_type").on('change',function(){
	var award_type = $(this).val();
	if(award_type == 2){
		//抓取优惠卷
		$(".choose-again").show();
		var url = "<?php  echo web_url('addaward',array('op'=>'get_bonus'));?>";
		$.ajaxLoad(url,{},function(){
			//加载远端的一个页面地址  ajaxload_get_bonus.php
			$('#alterModal').modal('show');
		});
	}else if(award_type ==3){
		//抓取自有商品
		$(".choose-again").show();
		$("#goodsModal").modal('show');
	}else if(award_type == 1){
		$("#gid").val(0);
	}
})
$(".choose-again").on("click",function(){
	$(".award_type").trigger('change');
})
//回车触发搜索
$("body").keydown(function(){
	if( event.keyCode == "13" ){
		search_goods();
	}
})
//商品搜索功能
function  search_goods(){
	var title = $("#good_name").val();
	if(title){
		var url = "<?php  echo web_url('addaward',array('op'=>'get_goods'));?>";
		$.post(url,{'title':title},function(data){
			$("#show_good_box").html(' ');
			if(data.errno == 200){
				var info = data.message;
				var html = '';
				for(var i=0;i<info.length;i++){
					var row = info[i];
					html +="<tr>" +
						"<td><input type='radio' name='get_good' class='get_good' value='"+ row.id +"'/></td>"+
						"<td class='get_good_title'>"+ row.title +"</td>"+
						"<td class='get_good_price'>"+ row.marketprice +"</td>"+
						"<td class='get_good_img'> <img src='"+ row.thumb +"' width='22'></td>"+
						"</tr>";
				}
			}else{
				var html = "<p>"+data.message+"</p>";
			}
			$("#show_good_box").html(html);
		},'json');
	}
}
//商品确认选择功能
function sure_choose_good(){
	$(".get_good").each(function(){
		if(this.checked){
			var gid = $(this).val();
			var title = $(this).parent().siblings('.get_good_title').text();
			var price = $(this).parent().siblings('.get_good_price').text();
			var thumb = $(this).parent().siblings('.get_good_img').find("img").attr('src');
			$("#gid").val(gid);
			$("#title").val(title);
			$("#names").val(title);
			$("#price").val(price);
			$("#choose_thumb").val(thumb);
			$("#goodsModal").modal('hide');
			if( $(".thumbnail").find("img").length == 0 ){
				var html = "<img src='"+ thumb +"'/>"
				$(".thumbnail").append(html);
			}else{
				$(".thumbnail").find("img").attr("src",thumb)
			}
		}
	})
}
</script>
<?php  include page('footer');?>