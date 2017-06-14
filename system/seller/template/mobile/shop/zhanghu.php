<!DOCTYPE html>
<html>
<head>
  <?php include page('seller_header');?>
    <style>
        .nav-tabs li a{
            padding-left: 18px;
            padding-right: 18px;
            text-align: center;
        }
    </style>
</head>
<body style="padding:10px;">
<div class="layui-form">
    <blockquote class="layui-elem-quote">提现账户<span style="margin-left: 10px;font-size:12px; ">以下只有最高管理员可操作</span></blockquote>
   <!-- <ul class="nav nav-tabs" >
        <li  <?php /* if( $_GP['op'] == 'safe' ) { */?> class="active"<?php /* } */?>><a href="<?php /* echo mobile_url('shop',array('op'=>'safe'))*/?>">安全设置</a></li>
        <li  <?php /* if($_GP['op'] == 'zhanghu') { */?> class="active"<?php /* } */?>><a href="<?php /* echo mobile_url('shop',  array('op' => 'zhanghu'))*/?>">提现账户</a></li>
    </ul>
    <br/>-->
    <p>
        <span class="layui-btn layui-btn-warm" onclick="add_bank(this)" data-url="<?php echo mobile_url('shop',array('op'=>'add_zhanghu')) ?>">添加账户</span>
    </p>

    <div class="">
        <table class="layui-table">
            <tr>
                <th>序号</th>
                <th>账户名称</th>
                <th>账户号码</th>
                <th>账户类型</th>
                <th>持卡人姓名</th>
                <th>操作</th>
            </tr>
            <tbody class="set">
            <?php foreach($bank_list['all'] as $key=>$bank){ ?>
            <tr>
                <td><?php echo ++$key; ?></td>
                <td><?php echo $bank['bank_name']; ?></td>
                <td><?php echo $bank['bank_number']; ?></td>
                <td><?php echo $bank['card_kind']; ?></td>
                <td><?php echo $bank['card_own']; ?></td>
                <td>
                    <span class="layui-btn layui-btn-warm" onclick="add_bank(this)" data-url="<?php echo mobile_url('shop',array('op'=>'edit_zhanghu','id'=>$bank['id'])) ?>">修改账户</span>
                    <span class="layui-btn layui-btn-danger"  onclick="del_bank(this)" data-url="<?php echo mobile_url('shop',array('op'=>'del_zhanghu','id'=>$bank['id'])) ?>">删除账户</span>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include page('seller_footer');?>
<script>
    layui.use("form",function(){
        var form = layui.form();
        /*分类联动*/
    })

    function add_bank(obj){
        var url = $(obj).data('url');
        $.ajaxLoad(url,{},function(){
            $('#alterModal').modal('show');
        });
    }

    function del_bank(obj){
        var url = $(obj).data('url');
        layer.confirm('确认删除么？', {icon: 3, title:'提示'}, function(index){
            if(index){
                window.location.href= url;
                layer.close(index);
            }
        });
    }
</script>
</body>
</html>