<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('seller_header');?>
        
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
.layui-form-label{
    width: 110px;
}
#layui-layer1.layui-layer1{

    margin-top:-300px!important;
}
.left-list,.right-list{
    float: left;
    width: 50%;
    width: 300px;
    height: 200px;
    overflow: auto;
    border: 1px solid #ddd;
    padding: 10px;
    box-sizing:border-box;
}
.right-list{
    margin-left: 10px;
}
.right-list li{
    cursor: pointer;
    display: none;
    line-height: 25px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    padding: 3px 5px;
    box-sizing:border-box;
    border: 1px solid #fff;
}
.left-list li{
    cursor: pointer;
    line-height: 25px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    padding: 3px 5px;
    box-sizing:border-box;
    border: 1px solid #fff;
}
.left-list li.left-list-li-check,.right-list li.left-list-li-check{
    background-color: #d9edf7;
    color: #4e90b5;
    border: 1px solid #bee9f1;
}
    </style>
	<body style="padding:10px;" class="step2">
    	<blockquote class="layui-elem-quote">业务及范围<span class="child-stop-info"></span></blockquote>
        <form class="layui-form " action="">
              <div class="layui-form-item layui-form">
                  <label class="layui-form-label">配送范围</label>
                  <div class="layui-input-inline">
                      <select  id="cate_1" lay-filter="cate_1" style="margin-right:15px;"  name="cate_1" class="pcates"   autocomplete="off">
                                <option value="0">请选择一级城市</option>
                                <?php if (is_array($result)) {
                                    foreach ($result as $row) {
                                        ?>
                                        <?php if ($row['parent_id'] == 0) { ?>
                                            <option value="<?php echo $row['region_id']; ?>" data-code="<?php echo $row['region_code']; ?>"  <?php if ($row['region_id'] == $extend_arr['p1']) { ?> selected="selected"<?php } ?>><?php echo $row['region_name']; ?></option>
                                        <?php } ?>
                                    <?php }
                                }
                                ?>
                        </select>
                  </div>
                  <div class="layui-input-inline">
                        <select  id="cate_2"  lay-filter="cate_2"    name="cate_2" class="cates_2"  autocomplete="off">
                            <option value="-1">请选择二级城市</option>
                            <?php if (!empty($extend_arr['p2']) && !empty($childrens[$extend_arr['p1']])) { ?>
                                <?php if (is_array($childrens[$extend_arr['p1']])) {foreach ($childrens[$extend_arr['p1']] as $row) {?>
                            <option data-code="<?php echo $row['region_code']; ?>" value="<?php echo $row['0']; ?>" <?php if ($row['0'] == $extend_arr['p2']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
                            <?php }}} ?>
                        </select>
                  </div>
                  <div class="layui-input-inline">
                        <select  id="cate_3" name="cate_3" lay-filter="cate_3"  autocomplete="off" onchange="fetchMap()">
                        <option value="0">请选择三级城市</option>
                        <?php
                        if (!empty($extend_arr['p3']) && !empty($childrens[$extend_arr['p2']])) {
                            if (is_array($childrens[$extend_arr['p2']])) {
                                foreach ($childrens[$extend_arr['p2']] as $row) {
                                    ?> 
                        <option value="<?php echo $row['0']; ?>"  data-code="<?php echo $row['0']; ?>"        <?php if ($row['0'] == $extend_arr['p3']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
                        <?php }}} ?>
                        </select>
                  </div>
              </div>
              <!-- 主营业务 -->
              <div class="layui-form-item">
                    <label class="layui-form-label">主营业务</label>
                    <div class="layui-input-block">
                        <ul class="left-list" id="industry_p1_id">
                            <?php if (is_array($catStruct)) { foreach ($catStruct as $row) { ?>
             <li list-value="<?php echo $row['gc_id']; ?>" data-value="<?php echo $row['gc_id']; ?>" <?php if ($row['gc_id'] == $extend_arr['p1']) { ?> selected="selected"<?php } ?>><?php echo $row['gc_name']; ?></li>
                            <?php }  }    ?>
                        </ul>
                        <ul class="right-list" id="industry_p2_id"></ul>
                    </div>
                </div>
<!--                <div class="layui-form-item">
                    <label class="layui-form-label">邀请码</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input" id="invite_code" name="invite_code" placeholder="请输入邀请码" >
                    </div>
                </div>-->
                
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <span class="layui-btn next-step" lay-submit="" lay-filter="demo">下一步</span>
                </div>
            </div>
            
        </form>
        <?php include page('seller_footer'); ?>
	</body>

<script type="text/javascript">
    var category = <?php echo json_encode($childrens) ?>;//    省市区JS
    var catStruct = <?php echo json_encode($catStruct) ?>;//    公用分类JS

layui.use(['form','element','layer'], function() {
    var form = layui.form();
    var element = layui.element();
    var $ = layui.jquery,
        layer = layui.layer;
    //监听提交
    form.on( 'submit(demo)', function(data) {
        var param = {
            'invitation_code': $("#invite_code").val(),
            'cat1_id'        : $(".left-list  li.left-list-li-check").attr('data-value'),
            'cat2_id'        : $(".right-list li.left-list-li-check").attr('data-value'),
            'sts_province'  :$("#cate_1 option:selected").attr('data-code'),
            'sts_city'      :$("#cate_2 option:selected").attr('data-code'),
            'region_code'   :$("#cate_3 option:selected").attr('data-code')
        };
        $.post('<?php echo mobile_url('store_shop',array('op'=>'shopRegisterStep1')) ?>',param,function(ret){
            if (ret.errno == 1) {
                location.href = '<?php  echo mobile_url('store_shop',array('op'=>'addShopTwo'))?>?id='+ret.data.id;
            } else {
                layer.alert(ret.message);
            }
            return false;
        })
     
    });
//    省市区JS   START ↓
    form.on('select(cate_1)', function(data){
        var cid = data.value;
        var html = '<option value="0">请选择二级分类</option>';
        if (!category || !category[cid]) {
           
        }else{
            for (i in category[cid]) {
            html += '<option value="' + category[cid][i][0] + '" data-code="'+category[cid][i][2]+'">' + category[cid][i][1] + '</option>';
            }
        }
        $('#cate_2').html(html);
        form.render();
    });
    form.on('select(cate_2)', function(data){
        var cid = data.value;
        var html = '<option value="0">请选择三级分类</option>';
        if (!category || !category[cid]) {
          
        }else{
            for (i in category[cid]) {
                html += '<option value="' + category[cid][i][0] + '" data-code="'+category[cid][i][2]+'">' + category[cid][i][1] + '</option>';
            }
        }
        $('#cate_3').html(html);
        form.render();
    });
    //    省市区JS   END ↑
        
    //    省市区JS   START ↓
    form.on('select(cate_3)', function(data){
        var region_code = $(data.elem).find("option:checked").attr("data-code");
//        var region_code = 350101;
        var url = '<?php echo mobile_url('store_shop',array('op'=>'appChooseIndustry')) ?>';
        $.get(url, {region_code:region_code}, function (ret) {
            if (ret.errno == 1) {
                var data_val = ret.data.shop_cat;
                var category_html = "";
                var sub_html = '';
                $.each(data_val, function (index, ele) {
                    if ( !ele['sub']) {
                    }else{
                        for (i in ele['sub']) {
                            sub_html  += '<li list-value="'+ele['sub'][i]['gc_pid']+'" data-value="'+ele['sub'][i]['gc_id']+'">' + ele['sub'][i]['gc_name'] + '('+ele['sub'][i]['gc_limit']+')</li>';
                        }
                    }
                });
                $('#industry_p2_id').html(sub_html);
                form.render();
            } else {
                alert(ret.message);
            }
        }, "json");
    });
});


$(function(){
    $(".left-list li").on("click",function(){
        var list_value = $(this).attr("list-value");
        $(".right-list li").hide().removeClass("left-list-li-check");
        $(this).addClass("left-list-li-check").siblings("li").removeClass("left-list-li-check");
        $(".right-list li[list-value="+list_value+"]").show();
    });
    $("body").on("click",".right-list li",function(){
        $(this).addClass("left-list-li-check").siblings("li").removeClass("left-list-li-check");
    });
})


</script>

</html>