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
    <blockquote class="layui-elem-quote">提现记录<span class="child-stop-info"></span></blockquote>
    <ul class="nav nav-tabs" >
        <li style="" <?php  if($_GP['op'] == 'outgold' ) { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('account',array('op'=>'outgold'))?>">店铺提现</a></li>
        <li style="" <?php  if($_GP['op'] == 'record') { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('account',  array('op' => 'record'))?>">提现记录</a></li>
    </ul>
        <div class="layui-form">
          <table class="layui-table">
            <thead>
              <tr>
                <th>提现单号</th>
                <th>申请日期</th>
                <th>账户类型</th>
                <th>账户</th>
                <th>金额（含手续费）</th>
                <th>状态</th>
              </tr> 
            </thead>
            <tbody>
            <?php foreach($data['cash_info'] as $one){ ?>
              <tr>
                <td><?php echo $one['ordersn']; ?></td>
                <td><?php echo date('Y-m-d H:i:s',$one['createtime']); ?></td>
                <td><?php echo $one['bank_name']; ?></td>
                <td><?php echo $one['bank_id']; ?></td>
                <td><?php echo number_format($one['fee']+$one['draw_money'],2); ?></td>
                <td><?php if($one['status'] == 0){
                        echo "<span class='layui-btn layui-btn-small'>等待审核</span>";
                    }else if($one['status'] == -1){
                        echo "<span class='layui-btn layui-btn-danger layui-btn-small'>审核失败</span>";
                    }else{
                        echo "<span class='layui-btn layui-btn-warm layui-btn-small'>提现成功</span>";
                    }

                    ?></td>
              </tr>
          <?php } ?>
            </tbody>
          </table>
           <div id="demo1" style='float:right'>
               <?php echo $data['pagehtml']; ?>
           </div>
        </div>  
	</body>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/layui.js"></script>
<script type="text/javascript">
layui.use(['element','form','laydate','laypage'], function(){
    var $ = layui.jquery;
    var element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块
    var form = layui.form();
    var laydate = layui.laydate;
   
});

</script>
</html>