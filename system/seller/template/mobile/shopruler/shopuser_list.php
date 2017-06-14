<!DOCTYPE html>
<html>
<head>
    <?php include page('seller_header');?>
</head>
<body style="padding:10px;">
<!-- 不用form表单，直接把form改成div即可 -->
<form class="layui-form layui-form-pane" action="" method="post">
    <div class="layui-form-item">
        <label class="layui-form-label">手机号码</label>
        <div class="layui-input-inline">
            <input type="text" name="mobile" lay-verify="" autocomplete="off" placeholder="请输入手机号码" class="layui-input" value="<?php echo $_GP['mobile'];?>">
        </div>
        <label class="layui-form-label">微信号</label>
        <div class="layui-input-inline">
            <input type="text" name="weixin" lay-verify="" autocomplete="off" placeholder="请输入微信号" class="layui-input" value="<?php echo $_GP['weixin'];?>">
        </div>
        <!--<div class="layui-btn" id="sub">搜索</div>-->
            <div class="layui-inline">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">搜索</button>
            </div>
    </div>
    <div>
         <table class="layui-table">
            <thead>
                <tr>
                    <th>姓名</th>
                    <th>手机号码</th>
                    <th>微信号</th>
                    <th>积分</th>
                </tr>
            </thead>
            <tbody>
                <?php
                  foreach($data['data'] as $v)
                  {
                ?>
                <tr>
                    <td><?php echo $v['realname'];?></td>
                    <td><?php echo $v['mobile'];?></td>
                    <td><?php echo $v['weixin'];?></td>
                    <td><?php echo $v['credit'];?></td>
                </tr>
                <?php
                  }
                ?>
            </tbody>
        </table>
        
        <div id="demo1"><!-- 分页的div -->
              <?php echo $data['pager'];?>
          </div>
    </div>
</from>
<?php include page('seller_footer');?>
<script>
layui.use(['laypage', 'layer','form','element'], function(){
    var $ = layui.jquery;
    element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块
    form = layui.form();
    
    form.on('submit(formDemo)', function(data){
        return false;
        //res就是返回的结果
    });

});
</script>
</body>
</html>