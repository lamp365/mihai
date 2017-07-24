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

<div id="myTabContent" class="tab-content">
    <!-- 产品库tab -->
	<div class="tab-pane fade in active" id="home">
        <form action="<?php echo web_url('store_shop_manage',array('op'=>'check_shop'))?>" method="post" onsubmit="return checkParame();">
            <input type="hidden" name="tab" value="2">
			<div class="search-area">
                <select  id="indu_p1" class="select"  name="indu_p1"  onchange="get_indu_p2(this)"  autocomplete="off">
                    <option value="0">请选择一级行业</option>
                    <?php foreach ($indu_parent as $row) { ?>
                        <option value="<?php echo $row['gc_id']; ?>"  <?php if ($row['gc_id'] == $_GP['indu_p1']) { ?> selected="selected"<?php } ?> ><?php echo $row['gc_name']; ?></option>
                    <?php }?>
                </select>

                    <select  id="indu_p2" name="indu_p2" class="select"  onchange="get_shops(this)" autocomplete="off">
                        <option value="-1">请选择二级行业</option>
                            <?php
                            if(empty($indu_second)){
                                echo "<option value='0'>请选择第二级行业</option>";
                            }else{
                                foreach ( $indu_second as $row) {
                            ?>
                                    <option  value="<?php echo $row['gc_id']; ?>" <?php if ($row['gc_id'] == $_GP['indu_p2']) { ?> selected="selected"<?php } ?>><?php echo $row['gc_name']; ?></option>
                            <?php } } ?>
                    </select>
                            
                            <select  id="show_store" class="select"  name="sts_id" autocomplete="off" >

                            <?php if (empty($store_instry)) {
                                echo '<option value="0">请选择店铺</option>';
                            }else{
                                foreach($store_instry as $this_store) {
                                    echo "<option value='{$this_store['sts_id']}'>{$this_store['sts_name']}</option>";
                                }
                            } ?>
                            </select>
				<button class="btn btn-primary  btn-sm" style="margin-right:10px;"><i class="icon-search icon-large"></i> 搜索</button>
				<button class="btn btn-primary  btn-sm" style="margin-right:10px;" id="bat_addgoods" data-cate2="<?php echo $dish['sts_category_p2_id']; ?>">全部加入产品库</button>
			</div>
		<table class="table table-striped table-bordered table-hover" style="margin-top: 15px;">
			<thead>
				<tr>
                                    <th style="text-align:center;min-width:20px;"><input type="checkbox" class="btn btn-xs btn-info choose_all"><span class="box_zi">全选</span></th>
                    <th style="text-align:center;min-width:60px;">店铺名称</th>
					<th style="text-align:center;min-width:100px;">首图</th>
					<th style="text-align:center;min-width:120px;">商品名称</th>
					<th style="text-align:center;min-width:100px;">货号</th>
					<th style="text-align:center;min-width:60px;">促销价</th>
                    <th style="text-align:center;min-width:60px;">销量</th>
					<th style="text-align:center; min-width:60px;">库存</th>
					<th style="text-align:center; min-width:60px;">状态</th>
					<th style="text-align:center; min-width:150px;">操作</th>
				</tr>
			</thead>
			<tbody>
                <?php foreach($result as $dish){ ?>
                <tr id="tr_<?php echo $dish['id']; ?>" >
                    <td style="text-align:center;"><input type="checkbox" class="child_box" name="id[]" value="<?php echo $dish['id'];?>"></td>
                    <td class="text-center"><?php echo $dish['store_name']; ?></td>
				 	<td>
				 		<p style="text-align:center"> 
                            <img src="<?php echo download_pic($dish['thumb'],60,60); ?>" height="60" width="60">
				 		</p>
				 	</td>
					<td style="text-align:center;"><?php echo $dish['title']; ?></td>
					<td style="text-align:center;"><?php echo $dish['goodssn']; ?></td>
					<td style="text-align:center;"><?php echo $dish['marketprice']; ?></td>
                    <td class="text-center"><?php echo $dish['sales_num']; ?></td>
                    <td style="text-align:center;"><?php echo $dish['store_count']; ?></td>
					<td style="text-align:center;"><span data="1" class="label label-success" style="cursor:pointer;"><?php echo $dish['status']==1?"已上架":"已下架"; ?></span>

                    <td style="text-align:center;">
						<a class="btn btn-xs btn-info ylsp" href="javascript:;" data-id="<?php echo $dish['id']; ?>"><i class="icon-edit"></i>预览商品</a>&nbsp;&nbsp;
						<a class="btn btn-xs btn-info" href="javascript:;" onclick="productAdd(<?php echo $dish['sts_category_p2_id']; ?>,<?php echo $dish['id']; ?>)"><i class="icon-edit"></i>加入产品库</a>&nbsp;&nbsp;
					</td>
                </tr>
                 <?php } ?>
			</tbody>
		</table>

        </form>
            <?php echo $pager;?>
	</div>
</div>

<input type="hidden" name="dish_id" id="dish_id" >
<input type="hidden" name="dish_ids" id="dish_ids" >
<input type="hidden" name="types" id="types" >
<!-- 加入产品库弹窗 -->
<div class="modal fade productAdd" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">加入产品库 <span style="font-size: 12px;color: red;margin-left: 20px;" class="show_tip"></span></h4>
      </div>

        <div class="modal-body" >
            <select  id="pcate_1" style="margin-right:15px;" onchange="get_cate_p2(this)"  name="dcate_1"  autocomplete="off">
                <option value="0">请选择一级分类</option>
            </select>
            <select  id="pcate_2"  lay-filter="dcate_2"    name="dcate_2"  autocomplete="off">
                <option value="-1">请选择二级分类</option>
            </select>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="dish_id" value=""/>
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" onclick="addSave()">保存</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script language="javascript">
$(function(){
    $(".ylsp").on('click',function(){
        var url = "<?php  echo web_url('store_shop_manage',array('op'=>'getDishContent'));?>";
        $.ajaxLoad(url,{dish_id:$(this).attr('data-id')},function(){
            //加载远端的一个页面地址  ajaxload_get_bonus.php
            $('#alterModal').modal('show');
        });
    })
    
    $(".choose_all").click(function(){
        if(this.checked){
            $('.child_box').each(function(){
                this.checked = true;
            })
        }else{
            $('.child_box').each(function(){
                this.checked = false;
            })
        }
    });
    
    $('#bat_addgoods').click(function(){
        var ids = '';
        $(".child_box:checked").each(function(){
           ids +=  $(this).val()+',';
        });
        if(ids == '')
        {
            alert('请选择一个产品');
            return false;
        }
        $('#dish_ids').val(ids);
        $('#types').val('2');
        $("#pcate_2").empty();
        var indu_bat_p2 = $(this).attr('data-cate2');
        var show_opt = '<option value="0">请选择</option>';
        var url = "<?php echo web_url('store_shop_manage',array('op'=>'ajaxGet_cate')); ?>";
        $.post(url,{'indu_p2':indu_bat_p2},function(data){
            if(data.errno == 1){
                var list = data.data;
                for(var i=0;i<list.length;i++){
                    var item = list[i];
                    show_opt   = show_opt+"<option value='"+item['id']+"'>"+item['name']+"</option>";
                }
            }

            $('#pcate_1').html(show_opt);
            //$('input[name=dish_id]').val(dish_id);
            $(".productAdd").modal();
        },'json');
        
        $(".productAdd").modal();
        return false;
    })
})

//加入产品库
function productAdd(indu_p2,dish_id){
    var show_opt = '<option value="0">请选择</option>';
    var url = "<?php echo web_url('store_shop_manage',array('op'=>'ajaxGet_cate')); ?>";
    $("#pcate_2").empty();
    $.post(url,{'indu_p2':indu_p2},function(data){
        if(data.errno == 1){
            var list = data.data;
            for(var i=0;i<list.length;i++){
                var item = list[i];
                show_opt   = show_opt+"<option value='"+item['id']+"'>"+item['name']+"</option>";
            }
        }

        $('#pcate_1').html(show_opt);
        $('#types').val('1');
        //$('input[name=dish_id]').val(dish_id);
        $('#dish_id').val(dish_id);
        $(".productAdd").modal();
    },'json');

}

function get_cate_p2(obj) {
    var cate_p1 = $(obj).val();
    if(cate_p1 == 0){
        var show_opt = "<option value='0'>请选择二级分类</option>";
        $("#pcate_2").html(show_opt);
    }else{
        var show_opt = '<option value="0">请选择</option>';
        var url = "<?php echo web_url('store_shop_manage',array('op'=>'ajaxGet_cate2')); ?>";
        $.post(url,{'cate_p1':cate_p1},function(data){
            if(data.errno == 1){
                var list = data.data;
                for(var i=0;i<list.length;i++){
                    var item = list[i];
                    show_opt   = show_opt+"<option value='"+item['id']+"'>"+item['name']+"</option>";
                }
                $("#pcate_2").html(show_opt);
            }else{
                var show_shop_opt = "<option value='0'>请选择二级分类</option>";
                $("#pcate_2").html(show_shop_opt);
            }
        },'json');
    }
}


function addSave(){
    var dish_id =  $('#dish_id').val() ;
    var dish_ids =  $('#dish_ids').val() ;
    var p1 =  $("#pcate_1 option:selected").val();
    var p2 =  $("#pcate_2 option:selected").val();
    var types = $('#types').val();
    if(p1 == 0 || p2 == 0){
        $(".show_tip").html('分类不能为空！');
        return false;
    }
    var fdata=  {
        dish_id:  dish_id ,
        dish_ids:  dish_ids ,
        types: types,
        p1: p1,
        p2: p2
    };

    var  url = '<?php echo web_url('store_shop_manage',array('op'=>'postGoodsTypeToGroup'))?>';    
    
    $.post(url,fdata,function(data){
            if( data.errno == 1 ){
                $(".productAdd").modal('hide');
                if(types == 1)
                {
                    $("#tr_"+dish_id).fadeOut(300);
                }
                else{
                    location.reload();
                }
            }else{
                $(".show_tip").html(data.message);
            }
	},"json");
	
}

function get_indu_p2(obj){
    var indu_p1 = $(obj).val();
    if(indu_p1 == 0){
        var indu_p2_opt   = "<option value='0'>请选择二级行业</option>";
        var show_shop_opt = "<option value='0'>请选择店铺</option>";
        $("#indu_p2").html(indu_p2_opt);
        $("#show_store").html(show_shop_opt);
    }else{
        var url = "<?php echo web_url('store_shop_manage',array('op'=>'ajaxGet_indulist')); ?>";
        $.post(url,{'indu_p1':indu_p1},function(data){
            if(data.errno == 1){
                var list = data.data;
                var indu_p2_opt   = "<option value='0'>请选择</option>";
                for(var i=0;i<list.length;i++){
                    var item = list[i];
                    indu_p2_opt   = indu_p2_opt+"<option value='"+item['gc_id']+"'>"+item['gc_name']+"</option>";
                }
                $("#indu_p2").html(indu_p2_opt);
            }else{

//                alert(data.message);
            }
            var show_shop_opt = "<option value='0'>请选择店铺</option>";
            $("#show_store").html(show_shop_opt);
        },'json');
    }
}
function get_shops(obj){
    var indu_p2 = $(obj).val();
    if(indu_p2 == 0){
        var show_shop_opt = "<option value='0'>请选择店铺</option>";
        $("#show_store").html(show_shop_opt);
    }else{
        var url = "<?php echo web_url('store_shop_manage',array('op'=>'ajaxGet_shop')); ?>";
        $.post(url,{'indu_p2':indu_p2},function(data){
            if(data.errno == 1){
                var list = data.data;
                var show_shop_opt = '';
                for(var i=0;i<list.length;i++){
                    var item = list[i];
                    show_shop_opt   = show_shop_opt+"<option value='"+item['sts_id']+"'>"+item['sts_name']+"</option>";
                }
                $("#show_store").html(show_shop_opt);
            }else{
                var show_shop_opt = "<option value='0'>请选择店铺</option>";
                $("#show_store").html(show_shop_opt);
//                alert(data.message);
            }
        },'json');
    }
}
function checkParame(){
    var sts_id = $("#show_store").val();
    if(sts_id == 0 ){
        //alert('请选择店铺！');
        //return false;
    }else{
        return true;
    }
}
</script>
<script language="javascript">

</script>
<?php  include page('footer');?>
