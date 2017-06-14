<!DOCTYPE html>
<html>
<head>
  <?php include page('seller_header');?>
</head>
<body style="padding:10px;">
<div class="layui-form">
  <blockquote class="layui-elem-quote"><span class="child-stop-info">模型分组有利于管理和快速找到自己的模型！</span></blockquote>
  <div class="layui-tab layui-tab-card" lay-filter="mark">
    <ul class="layui-tab-title">
      <li class="layui-this" lay-id="self">个人模型库</li>
      <li class="" lay-id="pingtai">系统模型库</li>
    </ul>
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">
        <label class="layui-form-label">选择分组</label>
        <div class="layui-input-inline">
          <select name="gtype_id" lay-filter="gtype" choose-type="self" >
            <option value="0">--查看全部--</option>
            <?php
              foreach($selfgroup as $s_group){
                $sel = '';
                if($s_group['group_id'] == $_GP['group_id'])
                  $sel = 'selected';
                echo "<option value='{$s_group['group_id']}' {$sel}>{$s_group['group_name']}</option>";
              }
            ?>
          </select>
        </div>
        <span class="layui-btn layui-btn-small add_gtype" >添加分组</span>
        <span class="layui-btn layui-btn-small" onclick="add_the_gtype(this)" data-url="<?php echo mobile_url('product',array('op'=>'add_goodstype')); ?>">添加模型</span>
        <table class="layui-table">
          <thead>
          <tr>
            <th width="80">id</th>
            <th>模型名称</th>
            <th>规格操作</th>
            <th>操作</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach($selfgtype_list as $s_list){ ?>
            <tr>
              <td><?php echo $s_list['id'];?></td>
              <td><?php echo $s_list['name'];?></td>
              <td><span class="layui-btn layui-btn-small" data-url="<?php echo mobile_url('product',array('op'=>'speclist','id'=>$s_list['id']));?>" onclick="speclist(this)">规格操作</span></td>
              <td>
                <div class="layui-btn layui-btn-small layui-btn-warm" onclick="add_the_gtype(this)"  data-url="<?php echo mobile_url('product',array('op'=>'add_goodstype','id'=>$s_list['id']));?>">编辑模型</div>
                <div class="layui-btn  layui-btn-danger layui-btn-small" onclick="sure_to_del(<?php echo $s_list['id'];?>)">删除模型</div>
              </td>
            </tr>
          <?php } ?>

          </tbody>
        </table>
      </div>

      <div class="layui-tab-item">
        <label class="layui-form-label">选择分组</label>
        <div class="layui-input-inline">
          <select name="interest" lay-filter="gtype" choose-type="pingtai">
            <option value="0">--查看全部--</option>
            <?php
            foreach($pingtaigroup as $p_group){
              $sel = '';
              if($p_group['group_id'] == $_GP['group_id'])
                $sel = 'selected';
              echo "<option value='{$p_group['group_id']}' {$sel}>{$p_group['group_name']}</option>";
            }
            ?>
          </select>
        </div>
        <table class="layui-table">
          <thead>
          <tr>
            <th width="80">id</th>
            <th>模型名称</th>
            <th>规格操作</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach($pingtaigtype_list as $s_list){ ?>
          <tr>
            <td><?php echo $s_list['id'];?></td>
            <td><?php echo $s_list['name'];?></td>
            <td><span onclick="speclist(this)" class="layui-btn layui-btn-small" data-url="<?php echo mobile_url('product',array('op'=>'showspec','id'=>$s_list['id']));?>">规格查看</span></td>
          </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<div class="modal fade" id="addGtypeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width: 30%">
    <form action="<?php echo mobile_url('product',array('op'=>'addgtype_group')) ?>" method="post" class="gtype_form form-horizontal">
    <div class="modal-content" >
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">添加分组</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label  class="col-sm-3 control-label">分组名称</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="group_name" placeholder="请输入分组名称" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">确认添加</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal -->
</div>

<?php include page('seller_footer'); ?>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
layui.use(['form','element'], function(){
  var $ = layui.jquery, form = layui.form();
  var element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块


  var layid = location.hash.replace(/^#mark=/, '');
  element.tabChange('mark', layid);

  element.on('tab(mark)', function(elem){
    location.hash = 'mark='+ $(this).attr('lay-id');
  });
  //选择分组的change监听
  form.on('select(gtype)', function(data){
    var group_id = data.value;
    var choose_type = $(data.elem).attr("choose-type");
    var url = "<?php echo mobile_url('product',array('op'=>'goodstype')); ?>";
        url = url +"?group_id="+group_id+"&type="+choose_type+"#mark="+choose_type;
        window.location.href = url;
  });

});

function speclist(obj){
  var url = $(obj).data('url');
  layer.open({
    title:'规格查看',
    type: 2,
    fixed: false, //不固定
    maxmin: true,
    area : ['800px' , '470px'],
    content: url ,//这里content是一个URL，如果你不想让iframe出现滚动条，你还可以content: ['http://sentsin.com', 'no']
  });
}

function sure_to_del(id){
  layer.confirm('该操作将同时删除使用该模板的商品价格,请慎重操作？', {icon: 3, title:'提示'}, function(index){
    if(index){
      var url = "<?php echo mobile_url('product',array('op'=>'del_gtype')); ?>";
      url = url + "?id="+id;
      window.location.href= url;
      layer.close(index);
    }
  });
}

$(function(){
   $('.add_gtype').click(function(){
     $("#addGtypeModal").modal('show');
   });

  //表单验证
  $('.gtype_form').validator();
});

function add_the_gtype(obj){
    var url = $(obj).data('url');
    $.ajaxLoad(url,{},function(){
      $('#alterModal').modal('show');
    });
}

</script>

</body>
</html>