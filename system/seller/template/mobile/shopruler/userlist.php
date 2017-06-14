<!DOCTYPE html>
<html>
<head>
    <?php include page('seller_header');?>

</head>
<body style="padding:10px;">
<div class="layui-form">
    <blockquote class="layui-elem-quote"> <a class="layui-btn layui-btn-small" href="<?php echo mobile_url('shopruler',array('op'=>'adduser')); ?>">添加管理员</a><span class="child-stop-info"></span></blockquote>
    <table class="layui-table">
        <thead>
        <tr>
            <th>序号</th>
            <th>用户昵称</th>
            <th>用户手机</th>
            <th>用户属组</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($userlist as $key => $item){ ?>
        <tr>
            <td style=""><?php  echo ++$key; ?></td>
            <td><?php echo $item['nickname'];?></td>
            <td><?php echo $item['mobile'];?></td>
            <td <?php if($item['is_admin']){  echo "style='font-weight: bolder'";  } ?>>
                <?php echo $item['group_name'];?>
            </td>
            <td>
                <?php echo date('Y-m-d H:i',$item['createtime']);?>
            </td>
            <td>
                <?php if(!$item['is_admin']){ ?>
                <div class="layui-btn layui-btn-small" onclick="edit_user(<?php echo $item['rid']; ?>)">修改用户</div>
                <div class="layui-btn layui-btn-warm layui-btn-small" onclick="del_user(<?php echo $item['rid']; ?>)">删除用户</div>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include page('seller_footer');?>
<script>
layui.use(['form','element'], function(){

});
function edit_user(id){
    var url = "<?php echo mobile_url('shopruler',array('op'=>'edituser')); ?>";
    $.ajaxLoad(url,{'id':id},function(){
        $('#alterModal').modal('show');
    });
}

function del_user(id){
    var url = "<?php echo mobile_url('shopruler',array('op'=>'deluser')); ?>";
    url = url +"?id="+id;
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