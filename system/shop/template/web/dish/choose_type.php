<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<style>
	.left-list{
		float: left;
		width: 48%;
		margin-right: 4%;
		border: 1px solid #ddd;
		border-radius: 2px;
		padding: 10px;
		box-sizing: border-box;
		height: 300px;
		overflow-y: auto;
	}
	.right-list{
		float: left;
		width: 48%;
		height: 300px;
		border: 1px solid #ddd;
		border-radius: 2px;
		padding: 10px;
		box-sizing: border-box;
		overflow-y: auto;
		background: #F1F1F1;
	}
	.choosetype-list ul{
		padding: 0px;
		margin: 0px;
	}
	.choosetype-list li{
		padding: 3px 7px;
		cursor: pointer;
	}
	.li-check{
		background-color: #d9edf7;
		color: #4e90b5;
		border: 1px solid #bee9f1;
	}
	.parent-type li span,.child-type li span{
		display:block;
	}
</style>
<h3 class="header smaller lighter blue">请先选择分类 <span style="font-size: 12px;margin-left: 20px;color: red">如果商品需要规则，请先创建规格模型</span></h3>
<div class="form-horizontal" style="padding: 0px 20px 0px 220px;width: 55%">
	<div class="form-group choosetype-list">
		<div class="left-list">
			<ul class="parent-type">
				<?php foreach($parent_category as $row){ ?>
				<li class="<?php if($row['id'] == $_GP['p1']) { echo 'li-check';}?>" p1="<?php echo $row['id']; ?>">
					<span class="parent-type-val" onclick="getson_cat(this,<?php echo $row['id'];?>)"><?php echo $row['name'];?></span>
				</li>
				<?php } ?>
			</ul>
		</div>

		<div class="right-list">
			<ul class="child-type type-show">
			</ul>
		</div>

	</div>

	<div style="text-align: center">
		<input type="hidden" name="dish_id" id="dish_id" value="<?php echo $_GP['id']; ?>">
		<p><span class="btn btn-md btn-info" onclick="get_next_step()">发布商品</span></p>
	</div>
</div>
<script>
	function getson_cat(obj,id){
		var url = "<?php echo web_url('goodscommon',array('op'=>'getCate'));?>";
		$.post(url,{id:id},function(data){
			if(data.errno == 200){
				var list = data.message;
				var html = '';
				for(var i=0;i<list.length;i++){
					var child = list[i];
					html = html + "<li class='' p2='"+child.id+"'>"+
									"<span class='parent-type-val'>"+child.name+"</span>"+
								  "</li>";
				}
				$(".parent-type").find('li').removeClass('li-check');
				$(obj).parent().addClass('li-check');
				$(".right-list").css({'background':"#ffffff"});
				$(".child-type").html(html);
			}
		},'json');
	}
	$(document).delegate(".child-type li",'click',function(){
		$(".child-type").find('li').removeClass('li-check');
		$(this).addClass('li-check');
	});

	//下一步发布商品
	function get_next_step(){
		var p1 = $(".parent-type").find(".li-check").attr('p1');
		var p2 = $(".child-type").find(".li-check").attr('p2');
		if(p1 == null || p1 == '' || p2 == null || p2 == ''){
			alert("请选择分类");
			return false;
		}
		var url = "<?php echo web_url('dish',array('op'=>'post_dish')); ?>";
		url = url + "&p1="+p1+"&p2="+p2;
		var dish_id = $("#dish_id").val();
		if(dish_id != '' || dish_id != null){
			url = url + "&id="+dish_id;
		}
		window.location.href = url;
	}
</script>
<?php  include page('footer');?>
