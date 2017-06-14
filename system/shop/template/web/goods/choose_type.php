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
		

<h3 class="header smaller lighter blue">请先选择分类 </h3>
<form action=""  class="form-horizontal" method="post" id="myform">
	<table class="table table-striped table-bordered table-hover goods-list-table">
			<tbody >
				<tr>
					<td>
						<li>
                                                    <select name="industry_p1_id" id="industry_p1_id" class="" style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0" >
                                                        <option value="">请选择一级行业分类</option>
                                                        <?php foreach($oneIndustry as $item) {
                                                                if($item['gc_id'] == $_GP['industry_p1_id']){
                                                                        $sel = "selected";
                                                                }else{
                                                                        $sel = '';
                                                                }
                                                                echo "<option value='{$item['gc_id']}' {$sel}>{$item['gc_name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <select name="industry_p2_id" id="industry_p2_id" class="" style="margin-right:10px;width: 150px; height:30px; line-height:28px; padding:2px 0">
                                                        <option value="">请选择二级行业分类</option>
                                                        <?php
                                                            foreach($twoIndustry as $item) {
                                                                if($item['gc_id'] == $_GP['industry_p2_id']){
                                                                        $sel = "selected";
                                                                }else{
                                                                        $sel = '';
                                                                }
                                                                echo "<option value='{$item['gc_id']}' {$sel}>{$item['gc_name']}</option>";
                                                            }
                                                        ?>
                                                    </select>
						</li>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
<div class="form-horizontal" style="padding: 0px 20px 0px 220px;width: 45%">
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
		<input type="hidden" name="good_id" id="good_id" value="<?php echo $_GP['id']; ?>">
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
		var industry_p1_id = $("#industry_p1_id").val();
		var industry_p2_id = $("#industry_p2_id").val();
		if(p1 == null || p1 == '' || p2 == null || p2 == ''){
			alert("请选择分类");
			return false;
		}
		var url = "<?php echo web_url('goods',array('op'=>'post_product')); ?>";
		url = url + "&p1="+p1+"&p2="+p2;
		var good_id = $("#good_id").val();
		if(good_id != '' || good_id != null){
			url = url + "&id="+good_id;
		}
                url = url + "&industry_p1_id=" + industry_p1_id;
                url = url + "&industry_p2_id=" + industry_p2_id;
                //industry_p1_id industry_p2_id
		window.location.href = url;
	}
        
        
 $(function(){
     $('#industry_p1_id').on('change',function(){
        $.post("/index.php?mod=site&name=shop&do=goods&op=twoIndustry",{id:$(this).val()},function(data){
            $('#industry_p2_id').empty();
            var indu_p2_opt = "<option value='0'>请选择</option>";
            for(var i=0;i<data.length;i++){
                var item = data[i];
                indu_p2_opt   = indu_p2_opt+"<option value='"+item['gc_id']+"'>"+item['gc_name']+"</option>";
            }
            $("#industry_p2_id").html(indu_p2_opt);
        },"json");
    });
    
     $('#industry_p2_id').on('change',function(){
        $('#myform').submit();
    });
    
 })
</script>
<?php  include page('footer');?>
