<style type="text/css">
    .choosetype-list li{
        position: relative;
        line-height: 30px;
        padding: 0 5px;
        box-sizing: border-box;
        border:1px solid #fff;
    }
    .choosetype-list li .parent-type-val{
        cursor: pointer;
    }
    .child-type{
        display: none;
    }
    .choosetype-list .edit-icon{
        color:red;
    }
    .choosetype-list .edit-username-input{
        display: block;
        line-height: 30px;
        height: 30px;
        padding: 0;
        width: 81px;
        top: 0px;
    }
    .choosetype-list .save-btn{
        position: absolute;
        top: 0px;
        left: 82px;
    }
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
    }
    .form-horizontal .form-group{
        margin-left: 0;
        margin-right: 0;
    }
    .li-check{
        background-color: #d9edf7;
        color: #4e90b5;
        border:1px solid #bee9f1;
    }
    .right-list .type-show{
        display: block;
    }
    .error-msg{
        display: none;
        margin-left: 20px;
        font-size: 12px;
        color: red;
    }
</style>
<div class="alertModal-dialog" style="width:30%">
    <div action="" method="post" class="form-horizontal gtype_form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">商品分类<span class="error-msg">请选择分类</span></h4>
    </div>
    <div class="modal-body">
        <div class="form-group choosetype-list">
            
            
            <div class="left-list">
                <ul class="parent-type">
                <?php
                    foreach($storyShopClass as $item)
                    {  $v = $item['main'];
                ?>
                    <li><span class="parent-type-val" type-id="<?php echo $v['id'];?>"><?php echo $v['name'];?></span><i class="fa fa-pencil-square-o edit-icon" onclick="editFun(this)" data-pid="<?php echo $v['parentid'];?>" data-fieldid="<?php echo $v['id'];?>"></i></li>
                <?php
                    }
                ?>
                </ul>
                <div class="layui-btn layui-btn-small" onclick="addType(this)"><i class="layui-icon">&#xe654;</i></div>
            </div>
            
            <div class="right-list">
                <?php
                  $i = 0;
                  foreach($storyShopClass as $item2)
                  {
                ?>
                <ul class="child-type <?php echo $i==0?'type-show':'';?>">
                    <?php
                        foreach($item2['child'] as $row)
                        {   $vv =  $row['main'];
                    ?>
                    <li><span class="parent-type-val" type-id="<?php echo $vv['id'];?>"><?php echo $vv['name'];?></span><i class="fa fa-pencil-square-o edit-icon" onclick="editFun(this)" data-pid="<?php echo $vv['parentid'];?>" data-fieldid="<?php echo $vv['id'];?>"></i></li>
                    <?php
                        }
                    ?>
                </ul>
                <?php
                    $i++;
                  }
                ?>
                <div class="layui-btn layui-btn-small" onclick="addChlidType(this)"><i class="layui-icon">&#xe654;</i></div>
            </div>
            
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary" onclick="save()">确认</button>
    </div>
    </div>
</div>
<script>
$(function(){
    var type_index = 0;
    //一级分类
    $("body").on("click",'.parent-type .parent-type-val',function(){
        //把子类的 默认被选中清除掉
        $(".choosetype-list .child-type").each(function(){
            $(this).find("li").removeClass("li-check");
        });
        type_index = $(this).parent("li").index();
        $(this).parent("li").addClass("li-check").siblings("li").removeClass("li-check");
        if( $(".child-type").eq(type_index).length == 0 ){
            $(".child-type").removeClass("type-show");
        }else{
            $(".child-type").eq(type_index).addClass("type-show").siblings().removeClass("type-show");
        }
        $(".error-msg").hide();
    });
    //二级分类
    $("body").on("click",".child-type .parent-type-val",function(){
        $(".error-msg").hide();
        $(".child-type li").removeClass("li-check");
        $(this).parent("li").addClass("li-check");
    });

});
function editFun(obj){
    var type_input = '<input type="text" class="edit-username-input"><span class="layui-btn layui-btn-small save-btn" onclick="typeSave(this)">保存</span>';
        $(obj).parent("li").append(type_input);
        $(obj).parent("li").siblings("").find(".edit-username-input,.save-btn").remove();
        $(".edit-username-input").focus();
}
function typeSave(obj){
    var catname = $(obj).siblings(".edit-username-input").val();
    var save_url = "<?php echo mobile_url('product',array('op'=>'savecate')); ?>";
    var id  = $(obj).closest('li').find("i").data('fieldid');
    var pid = $(obj).closest('li').find("i").data('pid');
    if( catname != "" ){
        $.post(save_url,{'cat_name':catname,'id':id,'pid':pid},function(data){
            if(data.errno == 1){
                var cat_id = data.message;
                $(obj).siblings("i").attr('data-fieldid',cat_id);
                $(obj).siblings(".parent-type-val").attr('type-id',cat_id);
                $(obj).siblings(".parent-type-val").text(catname);
                $(".edit-username-input,.save-btn").remove();
            }else{
                $(".error-msg").html(data.message);
                $(".error-msg").show();
            }
        },'json');

    }
    
}

//添加父类
function addType(obj){
    if ($(".parent-type .save-btn").length==0){
        var add_html = '<li><span class="parent-type-val" type-id="0"></span><i class="fa fa-pencil-square-o edit-icon" onclick="editFun(this)" data-fieldid="0" data-pid="0"></i><input type="text" class="edit-username-input"><span class="layui-btn layui-btn-small save-btn" onclick="typeSave(this)">保存</span></li>';
        $(".parent-type").append(add_html);
    }
}

//添加子类
function addChlidType(obj){
    //获取父类的pid
    var p_obj = $(".left-list").find(".li-check");
    var pid = 0;
    if(p_obj.length < 1){
        $(".error-msg").html("请选择一个父分类");
        $(".error-msg").show();
        return false;
    }else{
        pid = $(p_obj).find("i").data('fieldid');
    }

    if ($(".type-show .save-btn").length==0){
        var add_html = '<li><span class="parent-type-val" type-id="0"></span><i class="fa fa-pencil-square-o edit-icon" onclick="editFun(this)" data-fieldid="0" data-pid="'+pid+'"></i><input type="text" class="edit-username-input"><span class="layui-btn layui-btn-small save-btn" onclick="typeSave(this)">保存</span></li>';
        if( $(".type-show").length == 0 ){
            var child_type = '<ul class="child-type type-show">'+add_html+'</ul>';
            $(".child-type:last").after(child_type);
        }else{
            $(".type-show").append(add_html);
        }  
    }
}
function save(){
    if( $(".parent-type .li-check").length ==0 ){
        $(".error-msg").html('请选择一级分类');
        $(".error-msg").show();
        return false;
    }
    if($(".child-type .li-check").length ==0){
        $(".error-msg").html('请选择二级分类');
        $(".error-msg").show();
        return false;
    }
    var store_p1 = $(".parent-type .li-check").find(".parent-type-val").attr('type-id');
    var store_p2 = $(".child-type .li-check").find(".parent-type-val").attr('type-id');
    $(".type-area").show();
    $(".chooseType").text("修改分类");
    $(".type-area-1").text($(".parent-type .li-check span").text());
    $(".type-area-2").text($(".child-type .li-check span").text());
    $("#store_p1").val(store_p1);
    $("#store_p2").val(store_p2);
    $("#alterModal").modal('hide');
}
</script>