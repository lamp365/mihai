<!DOCTYPE html>
<html>
<head>
    <?php include page('seller_header');?>

</head>
<body style="padding:10px;">
<div class="layui-form">
    <blockquote class="layui-elem-quote"> <a class="layui-btn layui-btn-small" href="<?php echo mobile_url('shopruler',array('op'=>'addgroup')); ?>">添加角色</a><span class="child-stop-info"></span></blockquote>
    <table class="layui-table" style="width: 50%">
        <thead>
        <tr>
            <th>序号</th>
            <th>角色名称</th>
            <th>简介</th>
            <!--
            <th>创建时间</th>
            <th>操作</th>
            -->
        </tr>
        </thead>
        <tbody>
        <?php foreach($sellergroup as $key => $s_group){ ?>
        <tr>
            <td><?php echo ++$key; ?></td>
            <td><?php echo $s_group['group_name']; ?></td>
            <td><?php echo $s_group['description']; ?></td>
            <!--
            <td><?php echo date("Y-m-d H:i:s",$s_group['createtime']); ?></td>
            <td>
                <a class="layui-btn layui-btn-small" href="<?php echo mobile_url('shopruler',array('op'=>'editgroup','group_id'=>$s_group['group_id'])); ?>">修改角色</a>
                <div data-url="<?php echo mobile_url('shopruler',array('op'=>'delgroup','group_id'=>$s_group['group_id'])); ?>" class="layui-btn layui-btn-warm layui-btn-small" onclick="del_group(this)">删除角色</div>
            </td>
            -->
        </tr>
       <?php } ?>
        </tbody>
    </table>
</div>
<?php include page('seller_footer');?>
<script>
    layui.use('form', function(){
        var $ = layui.jquery, form = layui.form();

        //全选
        form.on('checkbox(allChoose)', function(data){
            var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
            child.each(function(index, item){
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });

    });

    function del_group(obj){
        var url = $(obj).data('url');
        layer.confirm('确认删除么？', {
            btn: ['确认删除', '取消删除'] //可以无限个按钮
        }, function(index, layero){
            //按钮【按钮一】的回调
            window.location.href = url;
        }, function(index){
            //按钮【按钮二】的回调
        });
    }
</script>

</body>
</html>