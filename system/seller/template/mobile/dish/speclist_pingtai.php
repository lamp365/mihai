<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('seller_header');?>

<body style="padding:10px;">
<!-- 商品规格 -->
    <!--<blockquote class="layui-elem-quote">选择模型</blockquote>
    <div class="layui-form-item layui-form">
        <label class="layui-form-label">商品模型</label>
        <div class="layui-input-inline">
            <select name="interest" lay-filter="aihao">
                <option value="0">请选择分组</option>
                <option value="0">写作</option>
                <option value="1" selected="">阅读</option>
                <option value="2">游戏</option>
                <option value="3">音乐</option>
                <option value="4">旅行</option>
            </select>
        </div>
        <div class="layui-input-inline">
            <select name="interest" lay-filter="aihao">
                <option value="0"></option>
                <option value="0">写作</option>
                <option value="1" selected="">阅读</option>
                <option value="2">游戏</option>
                <option value="3">音乐</option>
                <option value="4">旅行</option>
            </select>
        </div>
    </div>-->

    <form class="layui-form " action="">
        <!-- 商品规格 -->
        <blockquote class="layui-elem-quote">商品规格 <span></span></blockquote>
        <?php
          foreach($goodsTypeData['spec'] as $k=>$v){
        ?>
        <div class="layui-form-item">
            <label class="layui-form-label"><?php echo $v['spec_name'];?></label>
            <div class="layui-input-block">
                <span class="modal-span-01">
                  <?php
                    foreach($v['item'] as $kk=>$vv){
                  ?>
                    <span class="layui-btn layui-btn-small btn-success" ><?php echo $vv['item_name'];?></span>
                  <?php
                    }
                  ?>
                </span>
            </div>
        </div>
        <?php
          }
        ?>

        
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
});

</script>
</html>