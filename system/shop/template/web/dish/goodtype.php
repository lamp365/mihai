<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<style>
.ncap-form-default {
    padding: 10px 0;
    overflow: hidden;
}
.ncap-form-default dl.row, .ncap-form-all dd.opt {
    color: #777;
    background-color: #FFF;
    padding: 12px 0;
    margin-top: -1px;
    border-style: solid;
    border-width: 1px 0;
    border-color: #F0F0F0;
    position: relative;
    z-index: 1;
}
.table-bordered {
    width: 100%;
}
table {
    border-collapse: collapse;
}
.row .table-bordered td {
    padding: 8px;
    line-height: 1.42857143;
}
.table-bordered tr td {
    border: 1px solid #f4f4f4;
}
.row{
	margin:0;
	padding:0;
}
.set_bat{
	cursor: pointer;
}
</style>
<!-- 商品模型-->
<div class="ncap-form-default tab_div_3">
    <dl class="row">
	    <div class="tab-pane" id="tab_goods_spec">
	        <table class="table table-bordered" id="goods_spec_table">                                
	            <tr>
	                <td width="24%">规格模型:</td>
	                <td>                                        
	                  <select name="gtype_id" id="goods_type" class="form-control choose_gtype" style="width:250px;" >
	                    <option value="0">选择规格模型</option>
						  <?php foreach($gtype_list as $one_gtype){
							  $sel = '';
							  if($item['gtype_id'] == $one_gtype['id']){
								  $sel = "selected";
							  }
							  echo "<option value='{$one_gtype['id']}' {$sel}>{$one_gtype['gtype_name']}</option>";
						  } ?>
	                  </select>

	                </td>
	            </tr>                            
	        </table>
	        <div class="row">
	        	<!-- ajax 返回规格-->
	        	<div id="ajax_spec_data" class="col-xs-8">
	        		<table class="table table-bordered" id="goods_spec_table1">                                
					    <tbody>
						    <tr>
						        <td colspan="2"><b>商品规格:</b></td>
						    </tr> 
						</tbody>
					</table>
					<div id="">
						<table class="table table-bordered" id="goods_spec_table2">
							<tbody>
								<tr> 
									<td><b></b></td>
									<td><b>价格</b></td>
					               	<td><b>库存</b></td>
					               	<td><b>货号</b></td>
					               	<td><b>操作</b></td>
				             	</tr>
			             	</tbody>
			            </table>
		             </div>
	 			</div>
	        	<div id="" class="col-xs-4" >
	        	    <table class="table table-bordered" id="goods_attr_table">                                
	                    <tr>
	                        <td colspan="2"><b>商品属性</b>：</td>
	                    </tr>                                
	                </table>
	        	</div>
	        </div>
	    </div>
    </dl>             
</div>   
<!-- 商品模型-->

<div class="modal fade" id="setBate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 28%">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel"><span  class="tit">批量设置</span>
					<span style="font-size: 12px;margin-left: 20px;color: red" class="error_tip"></span>
				</h4>
			</div>
			<div class="modal-body">
				<div class="form-group" style="padding: 0 25px;">
					<label for="name"></label>
					<input type="text" class="form-control" id="set_bat_input" placeholder="批量设置">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
				<input type="hidden" class="to_set_class" value="">
				<input type="hidden" class="put_type" value=""> <!-- 1数字 2字符串-->
				<button type="button" class="btn btn-primary" onclick="sure_set()">确认设置</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal -->
</div>

<script>
/** 以下 商品规格相关 js*/
$(document).ready(function(){
    // 商品模型切换时 ajax 调用  返回不同的属性输入框
    $("#goods_type").change(function(){
        var dish_id = '<?php echo $item['id']; ?>';
        var gtype_id = $(this).val();
		getGoodsAttrAndSpec(gtype_id,dish_id);
    });
	// 触发商品规格
	$("#goods_type").trigger('change');

	// 按钮切换 class
	$(document).delegate("#goods_spec_table1 button",'click',function(){
		if($(this).hasClass('btn-success')) {
			$(this).removeClass('btn-success');
			$(this).addClass('btn-default');
		} else {
			$(this).removeClass('btn-default');
			$(this).addClass('btn-success');
		}
		var dish_id = '<?php echo $item['id']; ?>';
		getGoodsSpecInputInfo(dish_id); // 显示下面的输入框
	});

});

function set_bat_conf(_class){
	//批量设置
	$("#setBate").modal('show');
	$(".error_tip").html('');
	$(".to_set_class").val(_class);
	if(_class == 'set_productprice'){
		$("#setBate .tit").html('批量设置【市场价】');
		$(".put_type").val(1);
	}else if(_class == 'set_marketprice'){
		$("#setBate .tit").html('批量设置【促销价】');
		$(".put_type").val(1);
	}else if(_class == 'set_total'){
		$("#setBate .tit").html('批量设置【库存】');
		$(".put_type").val(1);
	}else if(_class == 'set_productsn'){
		$("#setBate .tit").html('批量设置【货号】');
		$(".put_type").val(2);
	}
}

function sure_set(){
	var set_bat_input = $("#set_bat_input").val();
	var to_set_class = $(".to_set_class").val();
	var put_type = $(".put_type").val()  //1必须是数字   2可以是其他
	if($.trim(set_bat_input) == ''){
		$(".error_tip").html('请输入对应的值！');
		return false;
	}
	if(put_type == 1){
		if(isNaN(set_bat_input)){
			$(".error_tip").html('请输入数字！');
			return false;
		}
	}
	$("."+to_set_class).val(set_bat_input);
	$("#setBate").modal('hide');

}
</script>