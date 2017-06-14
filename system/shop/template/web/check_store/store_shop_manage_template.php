<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<style type="text/css">
#myTab{
	padding-top: 20px;
}
#myTabContent th,#myTabContent td{
	text-align: center;
}
.select{
	margin-right: 10px;
    width: 150px;
    height: 30px;
    line-height: 28px;
    padding: 2px 0;
}
#myTabContent .search-area{
	margin-top: 15px;
	border: 1px solid #ddd;
    padding: 8px;
}
.productAdd label{
	cursor: pointer;
	font-weight: 400;
}
</style>
<ul id="myTab" class="nav nav-tabs">
    <li  <?php if($_GP['op']=='check_shop'){?> class="active" <?php } ?> >
        <a href="<?php  echo web_url('store_shop_manage',array('op'=>'check_shop') )?>" >
            审核产品
        </a>
    </li>
    <li <?php if($_GP['op']=='check_gtype'){?> class="active" <?php } ?> >
		<a href="<?php  echo web_url('store_shop_manage',array('op'=>'check_gtype') )?>">
			审核模型
		</a>
	</li>

</ul>
<div id="myTabContent" class="tab-content">
    <?php if($_GP['op']=='check_gtype'){ ?>
    <!-- 模型库tab -->
    <div class="tab-pane fade in active" id="admin">
		<form action="" method="post" name="" >
<!--			<div class="search-area">
				<select class="select">
					<option value="0">-请选择产品-</option>
					<option value="1">1</option>
					<option value="0">2</option>
				</select>
				<button class="btn btn-primary  btn-sm" style="margin-right:10px;"><i class="icon-search icon-large"></i> 搜索</button>
			</div>-->
			
			<table class="table table-striped table-bordered table-hover" style="margin-top: 15px;">
				<thead >
					<tr>
						<th>序号</th>
						<th>模型名称</th>
					
						<th>所属分类</th>
						<th>规格</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
                      <?php foreach($tab1_result as $gtype){ ?>
					<tr id="tab1_tr_<?php echo $gtype['id'] ?>">	
						<td><?php echo $gtype['id']; ?></td>
					 	<td><?php echo $gtype['name']; ?></td>
                        <td><?php echo category_func_getNameByID($gtype['p1'])."-". category_func_getNameByID($gtype['p2']); ?></td>
						<td><button type="button" class="btn btn-primary btn-xs" onclick="lookSpec(<?php echo $gtype['id']; ?>)">查看规格</button></td>
						<td>
                            <button type="button" class="btn btn-success btn-xs" onclick="pass(  <?php echo $gtype['p2']; ?>,<?php echo $gtype['id']; ?> )">加入模型库</button>&nbsp;
                            <!--<button type="button" class="btn btn-danger btn-xs">移除模型库</button>-->
                        </td>
					</tr>
                      <?php }?>
				</tbody>
			</table>
			<!-- 模型库查看规格弹窗 -->
			<div class="modal fade look-spec" tabindex="-1" role="dialog">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title">模型规格列表</h4>
			      </div>
			      <div class="modal-body">
			        	<table width="100%" class="table table-striped table-bordered table-hover" id="data_table">
			                <thead id="table_head">
				                <tr>
				                    <th width="15%" class="text-center">规格名称</th>
				                    <th class="text-center">规格项</th>
				                </tr>
			                </thead>
			                <tbody class="spec_main" id="model_tbody_spec">
			                    <tr>
			                        <td class="text-center" spec_id="28">有效时长</td>
			                        <td class="text-center">
			                            <span class="btn btn-success btn-xs">半个月</span>  
			                            <span class="btn btn-success btn-xs">半年</span>  
			                            <span class="btn btn-success btn-xs">一年</span>
			                        </td>
			                    </tr>
			                    <tr>
			                        <td class="text-center" spec_id="27">剂量</td>
			                        <td class="text-center">
			                            <span class="btn btn-success btn-xs">1.5升</span>  
			                            <span class="btn btn-success btn-xs">2.5升</span>  
			                            <span class="btn btn-success btn-xs">3.5升</span>
			                        </td>
			                    </tr>
			                </tbody>
			            </table>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			      </div>
			    </div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
		</form>
	</div>
    <?php }else{ ?>
    <!-- 产品库tab -->
	<div class="tab-pane fade in active" id="home">
        <form action="<?php echo web_url('store_shop_manage',array('op'=>'check_shop'))?>" method="post" >
            <input type="hidden" name="tab" value="2">
			<div class="search-area">
                <select  id="cate_1" class="select"  name="cate_1"  onchange="fetchChildCategory(this, this.options[this.selectedIndex].value)"  autocomplete="off">
                    <option value="0">请选择一级行业</option>
                    <?php if (is_array($indu_list)) {  foreach ($indu_list as $row) { ?>
                        <option value="<?php echo $row['gc_id']; ?>"  <?php if ($row['gc_id'] == $_GP['cate_1']) { ?> selected="selected"<?php } ?> ><?php echo $row['gc_name']; ?></option>
                    <?php }}?>
                </select>

                    <select  id="cate_2" name="cate_2" class="select"  onchange="fetchChildCategory2(this, this.options[this.selectedIndex].value)" autocomplete="off">
                        <option value="-1">请选择二级行业</option>
                            <?php if (is_array($indu_list[$_GP['cate_1']]['sub'])) {
                                foreach ( $indu_list[$_GP['cate_1']]['sub'] as $row) {
                                    ?>
                                    <option  value="<?php echo $row['gc_id']; ?>" <?php if ($row['gc_id'] == $_GP['cate_2']) { ?> selected="selected"<?php } ?>><?php echo $row['gc_name']; ?></option>
                            <?php }}           ?>
                    </select>
                            
                            <select  id="cate_3" class="select"  name="cate_3" autocomplete="off" >
                                <option value="0">请选择店铺</option>
                            <?php if (is_array($shop_list[$_GP['cate_2']])) {
                                foreach ( $shop_list[$_GP['cate_2']] as $row) {
                                        ?>
                <option value="<?php echo $row['sts_id']; ?>" <?php if ($row['sts_id'] == $_GP['cate_3']) { ?> selected="selected"<?php } ?>><?php echo $row['sts_name']; ?></option>
        <?php }} ?>
                            </select>
				<button class="btn btn-primary  btn-sm" style="margin-right:10px;"><i class="icon-search icon-large"></i> 搜索</button>
			</div>
		<table class="table table-striped table-bordered table-hover" style="margin-top: 15px;">
			<thead>
				<tr>
                    <th style="text-align:center;min-width:60px;">行业</th>
					<th style="text-align:center;min-width:100px;">首图</th>
					<th style="text-align:center;min-width:120px;">商品名称</th>
					<th style="text-align:center;min-width:100px;">货号</th>
					<th style="text-align:center;min-width:60px;">价格</th>
                    <th style="text-align:center;min-width:60px;">销量</th>
					<th style="text-align:center; min-width:60px;">库存</th>
					<th style="text-align:center; min-width:60px;">商品属性</th>
					<th style="text-align:center; min-width:60px;">状态</th>
					<th style="text-align:center; min-width:150px;">操作</th>
				</tr>
			</thead>
			<tbody>
                <?php foreach($result as $dish){ ?>
                <tr id="tr_<?php echo $dish['id']; ?>">
                   
                    <td class="text-center"><?php echo $dish['gc_name']; ?></td>
				 	<td>
				 		<p style="text-align:center"> 
                            <img src="<?php echo download_pic($dish['thumb']); ?>" height="60" width="60">
				 		</p>
				 	</td>
					<td style="text-align:center;"><?php echo $dish['title']; ?></td>
					<td style="text-align:center;"><?php echo $dish['goodssn']; ?></td>
                     <td class="text-center"><?php echo $dish['sales_num']; ?></td>
					<td style="text-align:center;"><?php echo $dish['productprice']; ?></td>
					<td style="text-align:center;"><?php echo $dish['store_count']; ?></td>
                    <td style="text-align:center;">
<?php if($dish['gtype_att']){ foreach ( $dish['gtype_att']  as $g_value) { ?>
        <p>
            <?php 
            $tmp = array_column($g_value['child_item'], 'item_name');
            $tmp_str= implode(",", $tmp);
            echo $g_value['spec_name'].":".$tmp_str; 
            ?>
        </p>
        <?php } ?>
    <?php  }  ?>
                    </td>
					<td style="text-align:center;"><span data="1" class="label label-success" style="cursor:pointer;"><?php echo $dish['status']==1?"已上架":"已下架"; ?></span>
<!--&nbsp;<span class="label label-info">虚拟商品</span></td>-->
                    <td style="text-align:center;">
						<a class="btn btn-xs btn-info" href="javascript:;" onclick="productLookSpec(<?php echo $dish['gtype_id']; ?>,<?php echo $dish['id']; ?>)"><i class="icon-eye-open"></i>查看模型</a>&nbsp;&nbsp;
						<a class="btn btn-xs btn-info" href="javascript:;"><i class="icon-edit"></i>预览商品</a>&nbsp;&nbsp;
						<a class="btn btn-xs btn-info" href="javascript:;" onclick="productAdd(<?php echo $dish['sts_category_p2_id']; ?>,<?php echo $dish['id']; ?>,<?php echo $dish['gtype_id']; ?>)"><i class="icon-edit"></i>加入模型库</a>&nbsp;&nbsp;
						<!--<a class="btn btn-xs btn-info" href="javascript:;"  onclick="productAdd(<?php echo $dish['sts_category_p2_id']; ?>,<?php echo $dish['id']; ?>,<?php echo $dish['gtype_id']; ?>)" ><i class="icon-edit"></i>移除模型库</a>&nbsp;&nbsp;-->
					</td>
                </tr>
      <?php } ?>
			</tbody>
		</table><!--
		<!-- 产品库模型查看规格弹窗 -->
			<div class="modal fade product-look-spec" tabindex="-1" role="dialog">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title">模型规格列表</h4>
			      </div>
			      <div class="modal-body">
			        	<table width="100%" class="table table-striped table-bordered table-hover" id="data_table">
			                <thead id="spec_table_head">
				                <tr>
				                    <th width="15%" class="text-center">规格名称</th>
				                    <th class="text-center">规格项</th>
				                </tr>
			                </thead>
			                <tbody class="spec_main" id="spec_table_body">
			                    <tr>
			                        <td class="text-center" spec_id="28">有效时长</td>
			                        <td class="text-center">
			                            <span class="btn btn-success btn-xs">半个月</span>  
			                            <span class="btn btn-success btn-xs">半年</span>  
			                            <span class="btn btn-success btn-xs">一年</span>
			                        </td>
			                    </tr>
			                    <tr>
			                        <td class="text-center" spec_id="27">剂量</td>
			                        <td class="text-center">
			                            <span class="btn btn-success btn-xs">1.5升</span>  
			                            <span class="btn btn-success btn-xs">2.5升</span>  
			                            <span class="btn btn-success btn-xs">3.5升</span>
			                        </td>
			                        
			                    </tr>
			                </tbody>
			            </table>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			      </div>
			    </div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
        </form>
            <?php echo $pager;?>
	</div>
    <?php } ?>
</div>

<!-- 加入模型弹窗 -->
<div class="modal fade pass-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">模型规格列表</h4>
      </div>
      <div class="modal-body">
      	<p>请将模型分配到指定的分类</p>
        <select class="select" id="pass_sel_1" onchange="pass_sel1_change( this.options[this.selectedIndex].value )">
      		<option>分类一</option>
      	</select>  
      	<select class="select"  id="pass_sel_2">
      		<option>分类二</option>
      	</select>   
      	<select class="select"  id="pass_group_sel_3">
      		<option>选择分组</option>
             <?php
                    if (is_array($group_data)) { foreach ($group_data as $row) {    ?>
     <option value="<?php echo $row['group_id']; ?>"  ><?php echo $row['group_name']; ?></option>
                        <?php  }          }  ?>
      	</select>   
      </div>
      <div class="modal-footer">
        <input type="hidden" name="pass_gtype_id"></input>
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" onclick="save()">保存</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- 加入产品库弹窗 -->
<div class="modal fade productAdd" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">加入产品库</h4>
      </div>
      <div class="modal-body">
      	<p>请选择加入产品库的方式</p>
      	<label><input type="radio" name="ruku" value="1">整套加入</label>
      	<label><input type="radio" name="ruku" value="2">只加入产品</label>  
      </div>
        <div class="modal-body" >
            <select  id="dcate_1" style="margin-right:15px;" onchange="scateFetch(this.options[this.selectedIndex].value)"  name="dcate_1"  autocomplete="off">
                    <option value="0">请选择一级分类</option>
            </select>
            <select  id="dcate_2"  lay-filter="dcate_2"    name="dcate_2"  autocomplete="off">
                <option value="-1">请选择二级分类</option>
            </select>
            <select  id="sys_group" name="sys_group"  style="display:none" autocomplete="off">
                <option value="-1">请选择分组</option>
                <?php
                    if (is_array($group_data)) { foreach ($group_data as $row) {    ?>
     <option value="<?php echo $row['group_id']; ?>"  ><?php echo $row['group_name']; ?></option>
                        <?php  }          }  ?>
            </select>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="dish_id" value=""></input>
        <input type="hidden" name="gtype_id" value=""></input>
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" onclick="addSave()">保存</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script language="javascript">
	var n2_Scate_result = <?php  echo json_encode($n2_Scate_result)?>;
    var shop_cate = <?php  echo json_encode($shop_cate)?>;
    $(function(){
        $("input[name=ruku]").on("change",function(){
//            console.log(  $("input[name=ruku]:checked").val() ); 
                var vla =  $("input[name=ruku]:checked").val() ;
                if(vla == 1){
                    $("#sys_group").show();
                }else{
                    $("#sys_group").hide();
                }
            })
        });
    
    function scateFetch(cid){
        var html = '<option value="0">请选择</option>';
//        console.log(shop_cate);
        if (!shop_cate || !shop_cate[cid]) {	}
        else{
            var sub_data = shop_cate[cid];
             for (i in sub_data ) {
                 html += '<option value="'+sub_data[i]['id']+'">'+sub_data[i]['name']+'</option>';
             }
        }
//           console.log(html);
        $('#dcate_2').html(html);
    }
    
//加入产品库
function productAdd(p2_id,dish_id,gtype_id){
    var html = '<option value="0">请选择</option>';
//        console.log(p2_id); console.log(n2_Scate_result);
        if (!n2_Scate_result || !n2_Scate_result[p2_id]) {	}
        else{
            var sub_data = n2_Scate_result[p2_id];
             for (i in sub_data ) {
                 html += '<option value="'+sub_data[i]['id']+'">'+sub_data[i]['name']+'</option>';
             }
        }
//           console.log(html);
    $('#dcate_1').html(html);
    $('input[name=dish_id]').val(dish_id);
    $('input[name=gtype_id]').val(gtype_id);
	$(".productAdd").modal();
}
// 查看规格
function lookSpec(gtype_id){
    var fdata=  {
        gtype_id: gtype_id 
    };
    var  url = '<?php echo web_url('store_shop_manage',array('op'=>'getDishSpec'))?>';  
    $.get(url,fdata,function(data){
        if( data.errno == 1 ){
            var sub_data = data.data.spec;
            var html = '';
            for (i in sub_data ) {
                html += '<tr><td class="text-center" >'+sub_data[i]['spec_name']+'</td><td class="text-center">';
                var item =     sub_data[i]['child_item'];                    
                for (y in item ) {    
                    html += '<span class="btn btn-success btn-xs">'+item[y]['item_name']+'</span>  ';
                }
                html +='</td></tr>'
            }
            $("#model_tbody_spec").html(html);
            $(".look-spec").modal();
        }else{
            alert(data.message);
        }
	},"json");
	
}
// 审核通过
function pass(p2_id,id){
    var html = '<option value="0">请选择</option>';
//        console.log(p2_id); console.log(n2_Scate_result);
        if (!n2_Scate_result || !n2_Scate_result[p2_id]) {
            alert('未找到此行业里的分类');
        }else{
            var sub_data = n2_Scate_result[p2_id];
             for (i in sub_data ) {
                 html += '<option value="'+sub_data[i]['id']+'">'+sub_data[i]['name']+'</option>';
             }
            $('#pass_sel_1').html(html);
            $('input[name=pass_gtype_id]').val(id);
            $(".pass-modal").modal();
        }
}
function pass_sel1_change(cid){
        var html = '<option value="0">请选择</option>';
//        console.log(shop_cate);
        if (!shop_cate || !shop_cate[cid]) {	}
        else{
            var sub_data = shop_cate[cid];
             for (i in sub_data ) {
                 html += '<option value="'+sub_data[i]['id']+'">'+sub_data[i]['name']+'</option>';
             }
        }
//           console.log(html);
        $('#pass_sel_2').html(html);
    }
// 保存分类
function save(){
    var id=  $("input[name=pass_gtype_id]").val();
    var fdata=  {
        id: id,
        p1:  $("#pass_sel_1 option:selected").val(), 
        p2:  $("#pass_sel_2 option:selected").val(), 
        system_group_id:$("#pass_group_sel_3 option:selected").val(), 
    };
//    console.log(dish_id); return false;
    var  url = '<?php echo web_url('store_shop_manage',array('op'=>'postGoodsTypeToGroup'))?>';      
    $.post(url,fdata,function(data){
//        console.log(data);return false;
            if( data.errno == 1 ){
                $(".pass-modal").modal('hide');
                $("#tab1_tr_"+id).fadeOut(300);
            }else{
                alert(data.message);
            }
	},"json");
	
}
// 产品库查看模型
function productLookSpec(gtype_id,dish_id){
     var fdata=  {
        gtype_id: gtype_id ,
        dish_id:  dish_id 
    };
    var  url = '<?php echo web_url('store_shop_manage',array('op'=>'getDishSpec'))?>';  
    $.get(url,fdata,function(data){
        if( data.errno == 1 ){
            var sub_data = data.data.spec;
            var html = '';
            for (i in sub_data ) {
                html += '<tr><td class="text-center" spec_id="28">'+sub_data[i]['spec_name']+'</td><td class="text-center">';
                var item =     sub_data[i]['child_item'];                    
                for (y in item ) {    
                    html += '<span class="btn btn-success btn-xs">'+item[y]['item_name']+'</span>  ';
                }
                html +='</td></tr>'
            }
            $("#spec_table_body").html(html);
            $(".product-look-spec").modal();
        }else{
            alert(data.message);
        }
	},"json");
//	$(".product-look-spec").modal();
}
function addSave(){
    var dish_id =  $('input[name=dish_id]').val() ;
    var fdata=  {
        ruku_type: $("input[name=ruku]:checked").val() ,
        gtype_id: $("input[name=gtype_id]").val() ,
        dish_id:  dish_id ,
        p1:  $("#dcate_1 option:selected").val(), 
        p2:  $("#dcate_2 option:selected").val(), 
        system_group_id:$("#sys_group option:selected").val(), 
    };
//    console.log(dish_id); return false;
    var  url = '<?php echo web_url('store_shop_manage',array('op'=>'postGoodsTypeToGroup'))?>';      
    $.post(url,fdata,function(data){
//        console.log(data);return false;
            if( data.errno == 1 ){
                $(".productAdd").modal('hide');
                $("#tr_"+dish_id).fadeOut(300);
            }else{
                alert(data.message);
            }
	},"json");
	
}
</script>
<script language="javascript">
    var category = <?php echo json_encode($indu_list) ?>;
    var shop_list = <?php echo json_encode($shop_list) ?>;
    
    function fetchChildCategory(o_obj, cid) {
        var html = '<option value="0">请选择二级分类</option>';
//        console.log(2);
        if (!category || !category[cid]|| !category[cid]['sub']) {	}
        else{
            var sub_data = category[cid]['sub'];
             for (i in sub_data ) {
                 html += '<option value="'+sub_data[i]['gc_id']+'">'+sub_data[i]['gc_name']+'</option>';
             }
        }
//           console.log(html);
        $('#cate_2').html(html);
    }
    function fetchChildCategory2(o_obj, cid) {
        var html = '<option value="0">请选择店铺</option>';
//        console.log(cid);
        if (!shop_list || !shop_list[cid]) {
         
        }else{
            for (i in shop_list[cid]) {
                html += '<option value="' + shop_list[cid][i]['sts_id'] + '">' + shop_list[cid][i]['sts_name'] + '</option>';
            }
        }
//        console.log(html);
        $('#cate_3').html(html);
    }
</script>
<?php  include page('footer');?>
