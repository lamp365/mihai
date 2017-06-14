<!DOCTYPE html>
<html>
    <head>
        <?php include page('seller_header'); ?>

        <link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/select2.min.css" />
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/select2.min.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/zh-CN.js"></script>
    </head>
    <style>
        .select-area{
            margin: 33px 0;
        }
        .select-area select{
            padding: 3px;
        }
        .spec-p1,.spec-p2{
            padding: 3px;
        }
        .choose-before{
            color: #b0b0b0;
            font-size: 20px;
            cursor: pointer;
        }
        .choose-checked{
            color: #60a75a;
            font-size: 20px;
            cursor: pointer;
        }
        .good-table th,.good-table td{
            text-align: center;
        }
        .spec-name,.spec-detail{
            margin-bottom: 5px;
        }
        .choose-all i{
            vertical-align: middle;
            margin-left: 3px;
        }
        .choose-all{
            display: none;
            cursor: pointer;
        }
        .save-btn{
            float: right;
            margin-bottom: 30px;
        }
        .js-data-example-ajax{
            width: 395px;
        }
        .dataTables_paginate{
            float: left;
        }
        .save-sure-btn{
            float: left;
            margin-top: 21px;
            margin-left: 40px;
        }
        .select2-container .select2-selection--single{
            height: 38px;
            line-height: 38px!important;
        }
        .select2-container--default .select2-selection--single{
            border: 1px solid #D2D2D2;border-radius: 2px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
             height: 38px;
            line-height: 38px!important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow{
            top:6px;
        }
    </style>
    <body style="padding:10px;">
        <div >
            <blockquote class="layui-elem-quote"><?php if (empty($_GP['choose_one'])) {
            echo '批量导入';
        } else {
            echo '从产品库添加';
        } ?> <span></span></blockquote>
            <?php if (empty($_GP['choose_one'])) { ?>
                <ul class="nav nav-tabs" >
                    <li style="width:7%" <?php if (empty($_GP['op']) || $_GP['op'] == 'index') { ?> class="active"<?php } ?>><a href="<?php echo mobile_url('product_bat') ?>">分类导入</a></li>
                    <li style="width:7%" <?php if ($_GP['op'] == 'bat_add') { ?> class="active"<?php } ?>><a href="<?php echo mobile_url('product_bat', array('op' => 'bat_add')) ?>">快速导入</a></li>
                </ul>
            <?php } ?>
            <form action="" method="" class="select-area">
                <div class="layui-form " >
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">选择分类</label>
                            <div class="layui-input-inline">
                                <select class="p1" name="pcate" id="p1" lay-filter="oneCategory">
                                    <option value="">请选择一级行业分类</option>
                                    <?php
                                    foreach ($oneShopCate as $v) {
                                        ?> 
                                        <option value="<?php echo $v['id']; ?>" <?php if ($v['id'] == $_GP['pcate']) {
                                        echo 'selected';
                                    } ?>><?php echo $v['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="layui-inline" id="twoClass">
                                <select class="p2" name="ccate" id="p2">
                                    <option value="">请选择</option>
                                    <?php
                                    if (count($twoShopCate) > 0) {
                                        foreach ($twoShopCate as $v) {
                                            ?>
                                            <option value="<?php echo $v['id']; ?>" <?php if ($v['id'] == $_GP['ccate']) {
                                        echo 'selected';
                                    } ?>><?php echo $v['name']; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">上下架</label>
                            <div class="layui-input-inline">
                                <select name="status"  lay-search="">
                                    <option value="1" <?php echo $_GP['status']==1?'selected':'';?>>上架</option>
                                    <option value="0" <?php echo isset($_GP['status'])&&$_GP['status']==0?'selected':'';?>>下架</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">价格范围</label>
                            <div class="layui-input-inline" style="width: 100px;">
                                <input type="text" name="marketprice_less" placeholder="￥" value="<?php echo  $_GP['marketprice_less'];?>" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-form-mid">-</div>
                            <div class="layui-input-inline" style="width: 100px;">
                                <input type="text" name="marketprice_many" placeholder="￥" value="<?php echo$_GP['marketprice_many'];?>" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">选择品牌</label>
                    <div class="layui-input-inline">
                     <select class="js-data-example-ajax" name="brand" id="brand">
                        <option value="<?php echo $brand['id'];?>" selected="selected"><?php echo $brand['brand']!=''?$brand['brand']:'select2/select2';?></option>
                      </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label" style="margin-left: 20px;">搜索关键字</label>
                    <div class="layui-input-inline" style="width: 190px;">
                        <input type="text" name="search_key" placeholder="搜索关键字" autocomplete="off" class="layui-input" value="<?php echo$_GP['search_key'];?>" >
                    </div>
                </div>
                <div class="layui-input-inline">
                    <label class="layui-form-label" style="margin-left: 20px;">
                        <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1">搜索</button>
                    </label>
                </div>
                <div class="layui-form">
                    <table class="layui-table">
                        <!-- col根据需要设置tr对应的宽度 -->
                        <colgroup>
                            <col width="50">
                            <col>
                            <col>
                            <col>
                            <col>
                        </colgroup>
                        <thead>
                            <tr>
                                <th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
                                <th>产品名称</th>
                                <th>缩略图</th>
                                <th>促销价</th>
                                <th>市场价</th>
                            </tr> 
                        </thead>
                        <tbody>
                            <?php foreach ($goodsPage['goodslist'] as $v) { ?>   
                            <tr>
                                <td><input type="checkbox" name="" value="<?php echo $v['id']; ?>" lay-skin="primary" class="category"></td>
                                <td><?php echo $v['title']; ?></td>
                                <td><img src="<?php echo $v['thumb']; ?>" width="60" height="60"></td>
                                <td><?php echo $v['marketprice']; ?></td>
                                <td><?php echo $v['productprice']; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </form>


            <div id="demo1"><!-- 分页的div -->
                <?php echo $pager; ?>
                <button class="layui-btn layui-btn-normal layui-btn-small save-sure-btn" onclick="save()">确定导入</button>
            </div>


        </div>
        <input type="hidden" value="<?php echo $brand['brand'];?>" id="brand_name" name="brand_name" class="layui-input">
        <!-- url-hidden隐藏域用来存储跳转的URL -->
<input type="hidden" class="url-hidden" value="">
<input type="hidden" value="" name="ids" id="ids">
<div class="p1p2-modal modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">选择店铺分类</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <select class="spec-p1 form-control" name="store_p1" id="store_p1">
                                <option value="">请选择一级行业分类</option>
                                <?php
                                foreach ($shopStoreCateData['oneCategory'] as $v) {
                                ?>
                                    <option value="<?php echo $v['id'];?>"><?php echo $v['cat_name'] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select class="spec-p2 form-control" name="store_p2" id="store_p2">
                                <option value="">请选择分类</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="close_button">关闭</button>
                <button type="button" class="save-btn-2 btn btn-primary" id="substore">确定导入</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

        
<?php include page('seller_footer'); ?>
<script>
<?php if (empty($_GP['choose_one'])) { ?>
    var flag = 2;//1选择产品跳转过来的,2代表批量导入。
<?php } else { ?>
    var flag = 1;//1选择产品跳转过来的,2代表批量导入。
<?php } ?>
var id_array = [];//储存选中的id数组

layui.use(['form'], function () {
    //全选
    var $ = layui.jquery, form = layui.form();
    form.on('checkbox(allChoose)', function (data) {
        var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
        child.each(function (index, item) {
            item.checked = data.elem.checked;
        });
        form.render('checkbox');
    });
    
    form.on('select(oneCategory)', function(data){
        var val = parseInt(data.value);
        //var weburl = '/seller/product/parCategory.html';
        var weburl = "<?php echo mobile_url('product_bat',array('op'=>'cate_tow')); ?>";
        var twoCategoryHtml = '';
        $.post(weburl,{'pid':val},function(data){
            twoCategoryHtml = '<select name="ccate" lay-filter="twoCategory"><option value="">请选择分类</option>';
            for(var i in data){
                if (data.hasOwnProperty(i)) { //filter,只输出man的私有属性
                    //console.log(i,":",data[i]);
                    twoCategoryHtml = twoCategoryHtml + '<option value="'+ data[i]['id'] +'">'+ data[i]['name'] +'</option>';
                };
            }
            twoCategoryHtml = twoCategoryHtml + '</select>';
            $('#twoClass').html(twoCategoryHtml);
            form.render();
        },"json");
    });
    
})

$(function () {
    $('#substore').on('click',function(){
        var store_p1 = $("#store_p1").val();
        var store_p2 = $("#store_p2").val();
        

        var url = "<?php echo mobile_url('product_bat',array('op'=>'bat_dish_add')); ?>";
        $.post(url,{goods_ids:$('#ids').val(),store_p1:store_p1,store_p2:store_p2},function(data){
            $('#close_button').trigger("click");
            alert(data['message']);
            location.reload();
        },"json");
    })

     $('#store_p1').change(function(){
        var url = "<?php echo mobile_url('product_bat',array('op'=>'store_cate_two')); ?>";
        $.ajaxLoad(url,{'pid':$(this).val()},function(data){
            $('#store_p2').empty();
            $('#store_p2').append("<option value='0'>请选择分类</option>");
            var da = JSON.parse(data);
            for(var i in da){
                $('#store_p2').append("<option value='" + da[i]['id'] + "'>" + da[i]['cat_name'] + "</option>");
            }
        });
    })

    $(".js-data-example-ajax").select2({
        placeholder: '请选择品牌',
        language: 'zh-CN',
        allowClear: true,
        ajax: {
          url: "/seller/product/searcgbrand.html",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              key: params.term, // search term
              page: params.page
            };
          },
          processResults: function (data, params) {
            params.page = params.page || 1;
            return {
              results: data.brand,
              pagination: {
                more: (params.page * 30) < data.total_count
              }
            };
          },
          cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepoDefault, // omitted for brevity, see the source of this page
        templateSelection: formatRepoProvince // omitted for brevity, see the source of this page
    });

    function formatRepoDefault(repo){
        if($('#brand_name').val() != '')
        {
            var markup = "<div>"+$('#brand_name').val()+"</div>";
        }
        else
        {
            var markup = "<div>品牌搜索</div>";
        }
        return repo.brand;
    }

    function formatRepoProvince(repo) {
        if(repo.brand != undefined)
        {
            var markup = "<div>"+repo.brand+"</div>";
        }
        else{
            if($('#brand_name').val() != '')
            {
                var markup = "<div>"+$('#brand_name').val()+"</div>";
            }
            else
            {
                var markup = "<div>品牌搜索</div>";
            }
        }
        return markup;
    }
})

function save(){
    var ids = '';
    var i = 0;
    $(".category:checked").each(function(){
       ids +=  $(this).val()+',';
       i++;
    });
    $('#ids').val(ids);
    $(".p1p2-modal").modal();
}
        </script>
    </body>
</html>