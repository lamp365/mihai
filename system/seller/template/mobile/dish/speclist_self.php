<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('seller_header');?>

<body style="padding:10px;">
<!-- 商品规格 -->
    <blockquote class="layui-elem-quote">选择模型</blockquote>
    <div class="layui-form-item layui-form">
        <label class="layui-form-label">选择分组</label>
        <div class="layui-input-inline">
            <select name="interest" id='group_id' lay-filter="group_id">
                <?php  foreach($selfgroup as $group){  $sel =''; if($current_gtype['group_id'] == $group['group_id']){ $sel ='selected'; } ?>
                <option value="<?php echo $group['group_id']; ?>" <?php echo $sel;?>><?php echo $group['group_name']; ?></option>
               <?php } ?>
            </select>
        </div>
        <label class="layui-form-label">选择模型</label>
        <div class="layui-input-inline">
            <select name="interest" id="gtype_id" lay-filter="gtype_id">
                <?php  foreach($gtype_list as $gtype){  $sel =''; if($current_gtype['id'] == $gtype['id']){ $sel ='selected'; } ?>
                    <option value="<?php echo $gtype['id']; ?>" <?php echo $sel;?>><?php echo $gtype['name']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <form class="layui-form " action="">
        <!-- 商品规格 -->
        <blockquote class="layui-elem-quote">商品规格
            <span>&nbsp;&nbsp;
                <?php if(count($specAndItem) < 2){  ?>
                <button type="button" onclick="addSpec(this)" class="btn btn-info btn-md add_spec_click">添加规格</button>
                <?php } ?>
            </span>
        </blockquote>

        <div class="spec_box">
            <?php foreach($specAndItem as $spec_list){ ?>
                <div class="layui-form-item spec_list" data-spec_id="<?php echo $spec_list['spec_id']; ?>">
                    <label class="layui-form-label" data-spec_id="<?php echo $spec_list['spec_id']; ?>"><b><?php echo $spec_list['spec_name']; ?></b></label>
                    <div class="layui-input-block">
                        <span class="modal-span-01 show_item">
                            <?php foreach($spec_list['child_item'] as $spec_item){  ?>
                                <?php if($spec_item['status'] == 1){  ?>
                                    <span title='点击禁用' data-item_name="<?php echo $spec_item['item_name']; ?>" class="layui-btn layui-btn-small btn-success one_item" data-item_id="<?php echo $spec_item['id']; ?>" id="item<?php echo $spec_item['id']; ?>"><?php echo $spec_item['item_name']; ?><i class="layui-icon spec-remove" onclick="item_remove(this,<?php echo $spec_item['id']; ?>)">&#xe640;</i></span>
                                <?php }else{  ?>
                                    <span title='点击启用' data-item_name="<?php echo $spec_item['item_name']; ?>" class="layui-btn layui-btn-small btn-default one_item" data-item_id="<?php echo $spec_item['id']; ?>" id="item<?php echo $spec_item['id']; ?>"><?php echo $spec_item['item_name']; ?><i class="layui-icon spec-remove" onclick="item_remove(this,<?php echo $spec_item['id']; ?>)">&#xe640;</i></span>
                                <?php }  ?>
                            <?php } ?>
                        </span>
                        &nbsp;&nbsp;<div class="layui-btn layui-btn-small" lay-submit="" onclick="addSpecItem(this,<?php echo $spec_list['spec_id']; ?>)">添加规格项</div>
                    </div>
                </div>
            <?php } ?>
            <div class="layui-form-item spec_name_list" style="display:none">
                <label  class="layui-form-label">规格名称</label>
                <div class="layui-input-inline">
                    <input type="text" class="form-control" name="spec_name" id='spec_name' placeholder="请输入规格名称" required>
                </div>
                <button type="button" class="btn btn-primary" onclick="sure_add_spec(this)">确认添加</button>
            </div>
        </div>
    </form>


</body>
<script>
layui.use(['form','element'], function() {
    var form = layui.form();
    var layer = layui.layer;
    var element = layui.element();
    //监听提交
    form.on('submit(demo)', function(data) {
        return false;
    });

    //选择分组的change监听
    form.on('select(group_id)', function(data){
        var group_id = data.value;
        layui.use('form', function() {
            var form = layui.form();
            var url = "<?php echo mobile_url('product',array('op'=>'ajaxGtypeBygroupid')) ?>";
            $.post(url,{group_id:group_id},function(data){
                var html = '<option value="0">选择模型</option>';
                if(data.errno == 1){
                    var optionObj = data.data;
                    for(var i=0;i<optionObj.length;i++){
                        var this_data = optionObj[i];
                        html = html +"<option value='"+this_data.id+"'>"+this_data.name+"</option>";
                    }
                    $("#gtype_id").html(html);
                    form.render();//重新渲染layui框架
                    $(".spec_box").remove();
                }else{
                    $("#gtype_id").html(html);
                    form.render();//重新渲染layui框架
                    $(".spec_box").remove();
                    layer.alert(data.message);
                }
            },'json');
        });
    });

    //选择分组的change监听
    form.on('select(gtype_id)', function(data){
        var gtype_id = data.value;
        var url = "<?php echo mobile_url('product',array('op'=>'speclist')); ?>";
        url = url +"?id="+gtype_id;
        window.location.href = url;
    });
});
function addSpec(obj){
    if($('.spec_list').length < 2){
        $(".spec_name_list").show();
    }
}
//添加规格值
function addSpecItem(thisObj){
    if( $(thisObj).parent("div").find(".save-btn").length == 0 ){
        var input_html = '<input type="text" style="margin-left: 10px;height:30px;margin-right:6px;border:1px solid #e2e2e2;padding:4px 0;border-radius:3px;display: inline-block;"><span class="layui-btn layui-btn-small save-btn" onclick="specitemSave(this)">保存</span>&nbsp;&nbsp;';
        $(thisObj).siblings(".modal-span-01").append(input_html);
    }
}
//插入规格dom节点
function specitemSave(thisObj){
    var thisval = $(thisObj).prev().val();
    if(thisval == ''){
        layer.open({
            title: '提示'
            ,content: '规格项名称不能为空！'
        });
        return false;
    }
    var isok = true;
    $(".show_item .one_item").each(function(){
        if($(this).data('item_name') == thisval) {
            isok = false;
        }
    });
    if(!isok){
        layer.open({
            title: '提示'
            ,content: '规格项名称已经存在！'
        });
        return false;
    }
    var url = "<?php  echo mobile_url('product',array('op'=>'addspecitem'));?>";
    var spec_id = $(thisObj).closest('.spec_list').data('spec_id');
    $.post(url,{'item_name':thisval,'spec_id':spec_id},function(data){
       if(data.errno == 1){
           var id = data.message;
           $(thisObj).parents(".modal-span-01").append('<span title="点击禁用" class="layui-btn layui-btn-small btn-success one_item" data-item_id="'+id+'" data-item_name="'+thisval+'" >'+thisval+'<i class="layui-icon spec-remove" onclick="item_remove(this,'+id+')">&#xe640;</i></span>');
           $(thisObj).prev().remove();
           $(thisObj).remove();
       }else{
           layer.open({
               title: '提示'
               ,content: data.message
           });
       }
    },'json');


}
//禁用规格或者使用规格
function item_remove(thisObj,item_id){

    layer.confirm('确认操作？', {
        btn: ['确认','取消'] //按钮
    }, function(){
        //确认回调
        var status = 0;
        if($(thisObj).parent().hasClass('btn-success')){
            //禁用
            status = 0;
        }else if($(thisObj).parent().hasClass('btn-default')){
            //启用
            status = 1;
        }
        
        /*
        var url = "<?php  echo mobile_url('product',array('op'=>'setitem_status'));?>";
        $.post(url,{'status':status,'item_id':item_id},function(data){
            layer.open({
                title: '提示',
                content: data.message
            });
            if(data.errno = 1){
                if($(thisObj).parent().hasClass('btn-success')){
                    $(thisObj).parent().removeClass('btn-success');
                    $(thisObj).parent().addClass('btn-default');
                    $(thisObj).parent().attr('title','点击启用');
                }else if($(thisObj).parent().hasClass('btn-default')){
                    $(thisObj).parent().removeClass('btn-default');
                    $(thisObj).parent().addClass('btn-success');
                    $(thisObj).parent().attr('title','点击禁用');
                }
            }
        },'json');
        */
        
        var url = "<?php  echo mobile_url('product',array('op'=>'delete_completely'));?>";
        $.post(url,{'item_id':item_id,'gtype_id':$('#gtype_id').val()},function(data){
            layer.open({
                title: '提示',
                content: data.message
            });
            if(data.errno = 1){
                $('#item'+item_id).remove();
            }
        },'json');
           
           
        layer.closeAll('dialog');
    }, function(){
        //取消的回调
    });
}

/**
 * 确认添加规格
 * @param obj
 * @returns {boolean}
 */
function sure_add_spec(obj){
    var spec_name = $("#spec_name").val();
    if(spec_name == '' || spec_name == null){
        layer.open({
            title: '提示'
            ,content: '规格名称不能为空'
        });
        return false;
    }
    var gtype_id = $("#gtype_id").val();
    if(gtype_id == 0 || gtype_id=='' || gtype_id==null){
        layer.open({
            title: '提示'
            ,content: '请选择模型！'
        });
        return false;
    }
    var isok = true;
    $(".spec_box .layui-form-label b").each(function(){
       if($(this).text() == spec_name) {
           isok = false;
       }
    });
    if(!isok){
        layer.open({
            title: '提示'
            ,content: '规格名称已经存在！'
        });
        return false;
    }
    var url = "<?php echo mobile_url('product',array('op'=>'addspec')); ?>";
    //append_spec(gtype_id,spec_name);
    $.post(url,{'gtype_id':gtype_id,'spec_name':spec_name},function(data){
        if(data.errno == 1){
            append_spec(data.message,spec_name);
        }
    },'json');
}

function append_spec(id,spec_name){
    var html ='<div class="layui-form-item spec_list" data-spec_id="'+id+'">'+
                '<label class="layui-form-label" data-spec_id="'+id+'"><b>'+spec_name+'</b></label>'+
                ' <div class="layui-input-block">'+
                    ' <span class="modal-span-01 show_item"></span>'+
                    '<div class="layui-btn layui-btn-small" lay-submit="" onclick="addSpecItem(this,'+id+')">添加规格项</div>'+
                '</div>'+
              '</div>';
    $(".spec_box").append(html);
   
    $(".spec_name_list").hide();
    //规格目前只能建立两个
    if($('.spec_list').length == 2){
        $(".add_spec_click").remove();
    }
}
</script>
</html>