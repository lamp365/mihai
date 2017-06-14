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
    <h3 class="blue" style="margin-top:5px;margin-bottom:5px;">	<span style="font-size:18px;"><strong>退换件数：<?php echo $total;?></strong></span></h3>
    <ul class="nav nav-tabs" >
        <li style="" <?php  if($status != 4 ) { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('order',  array('op' => 'return_list'))?>">退换处理中</a></li>
        <li style="" <?php  if($status == 4  ) { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('order',  array('op' => 'return_list','status'=>4))?>">退换完成</a></li>
    </ul>

    <table class="layui-table">
        <thead>
        <tr>
            <th>订单号</th>
            <th>商品名称</th>
            <th>商品金额</th>
            <th>买家</th>
            <th>类型</th>
            <th>下单时间</th>
            <th>申请时间</th>
            <th>处理状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($returnList && is_array($returnList)){foreach ($returnList as $v){?>
        <tr>
            <td><?php echo $v['ordersn']?></td>
            <td><?php echo $v['title']?></td>
            <td><?php echo $v['marketprice']?>元</td>
            <td><?php echo $v['nickname']?></td>
            <td><?php echo $v['type_name'];?></td>
            <td><?php echo date("Y:m:d H:i:s",$v['createtime']);?></td>
            <td><?php echo date("Y:m:d H:i:s",$v['reply_return_time']);?></td>
            <td><?php echo $v['status_name'];?></td>
            <td><a href="<?php echo mobile_url('order',array('name'=>'seller','op'=>'detail','id'=>$v['orderid'])); ?>" class="layui-btn layui-btn-small">详情</a></td>
            <?php }}?>
        </tr>
        <?php  echo $pager;?>
        </tbody>
    </table>
</div>

<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    layui.use('form', function(){
        var $ = layui.jquery, form = layui.form();



    });
</script>

</body>
</html>