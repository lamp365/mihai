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
    <h3 class="blue" style="margin-top:5px;margin-bottom:5px;">	<span style="font-size:18px;"><strong>订单总数：<?php echo $total ? $total : 0; ?></strong></span></h3>
    <ul class="nav nav-tabs" >
        <li style="width:7%" <?php  if( !isset($_GET['status']) ) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('mobile',  array('name' => 'seller','do'=>'order','op' => 'lists', ))?>">全部</a></li>
        <li style="width:7%" <?php  if(isset($_GET['status']) && $_GET['status'] == 0) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('mobile',  array('name' => 'seller','do'=>'order','op' => 'lists', 'status' => 0))?>">待付款</a></li>
        <li style="width:7%" <?php  if($_GET['status'] == 1) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('mobile',  array('name' => 'seller','do'=>'order','op' => 'lists', 'status' => 1))?>">待发货</a></li>
        <li style="width:7%" <?php  if($_GET['status'] == 2) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('mobile',  array('name' => 'seller','do'=>'order','op' => 'lists', 'status' => 2))?>">待收货</a></li>
        <li style="width:7%" <?php  if($_GET['status'] == 3) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('mobile',  array('name' => 'seller','do'=>'order','op' => 'lists', 'status' => 3))?>">已完成</a></li>
        <li style="width:7%" <?php  if($_GET['status'] == -1) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('mobile',  array('name' => 'seller','do'=>'order','op' => 'lists', 'status' => -1))?>">已关闭</a></li>
    </ul>

  <table class="layui-table">
    <thead>
      <tr>
        <th>订单号</th>
        <th>金额</th>
        <th>运费</th>
        <th>买家</th>
        <th>支付方式</th>
        <th>下单时间</th>
        <th>订单状态</th>
        <th>操作</th>
      </tr> 
    </thead>
    <tbody>
      <?php if($order_lists){ ?>
      <?php foreach($order_lists as $order){ ?>
              <tr>
                  <td><?php echo $order['ordersn']; ?></td>
                  <td align="center" valign="middle" style="vertical-align: middle;">
                      <div><?php  echo $order['price'];?> 元 </div>
                      <div style="font-size:10px;color:#999;">(含运费:<?php  echo $order['dispatchprice'];?> 元)</div>
                  </td>
                  <td><?php echo $order['dispatchprice']; ?> 元</td>
                  <td>
                      <div>收货人：<?php  echo $order['nickname'];?></div>
                      <div>电话：<?php  echo $order['mobile'];?></div>
                  </td>
                  <td><?php echo $order['paytype_name']; ?></td>
                  <td><?php echo date('Y-m-d H:i:s',$order['createtime']); ?></td>
                  <td><?php echo $order['status_name']; ?></td>
                  <td><a href="<?php echo mobile_url('order',array('name'=>'seller','op'=>'detail','id'=>$order['id'])); ?>" class="layui-btn layui-btn-small">详情</a></td>
              </tr>
      <?php } ?>
      <?php }else{ ?>
          <tr><td colspan="8" align="center">没有记录</td></tr>
      <?php } ?>
      <?php  echo $pager;?>
    </tbody>
  </table>
</div>         

<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
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
</script>

</body>
</html>