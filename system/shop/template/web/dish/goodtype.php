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
</style>
<!-- 商品模型-->
<div class="ncap-form-default tab_div_3">
    <dl class="row">
	    <div class="tab-pane" id="tab_goods_spec">
	        <table class="table table-bordered" id="goods_spec_table">                                
	            <tr>
	                <td width="24%">商品模型:（按产品分类进行获取）</td>
	                <td>                                        
	                  <select name="goods_type" id="goods_type" class="form-control choose_gtype" style="width:250px;" >
	                    <option value="0">选择商品模型</option>
						  <?php foreach($gtype_list as $one_gtype){
							  $sel = '';
							  if($item['gtype_id'] == $one_gtype['id']){
								  $sel = "selected";
							  }
							  echo "<option value='{$one_gtype['id']}' {$sel}>{$one_gtype['name']}</option>";
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
					               	<td><b>SKU</b></td>
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
<script>
/** 以下 商品规格相关 js*/
$(document).ready(function(){
    // 商品模型切换时 ajax 调用  返回不同的属性输入框
    $("#goods_type").change(function(){
        var goods_id = '<?php echo $item['id']; ?>';
        var gtype_id = $(this).val();
		getGoodsAttrAndSpec(gtype_id,goods_id);
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
		var goods_id = '<?php echo $item['id']; ?>';
		getGoodsSpecInputInfo(goods_id); // 显示下面的输入框
	});

});
</script>