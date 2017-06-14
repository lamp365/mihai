<!DOCTYPE html>
<html>
	<head>
        <?php include page('seller_header');?>
	</head>
	<body style="padding:10px;">
    	<blockquote class="layui-elem-quote">店铺分类<span class="child-stop-info"></span></blockquote>
        <div class="layui-tab layui-tab-card">
            <div class="layui-tab-content">

                <div class="layui-tab-item layui-show">
            &nbsp;&nbsp;<div class="layui-btn layui-btn-small newChild" onclick="addedit(this)" data-url="<?php echo mobile_url('category',array('op'=>'addcate','pid'=>0) ); ?>">新建一级分类</div>
                        <table class="layui-table">
                            <thead>
                                <tr>
                                    <th>分类名</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($cate_list as $one_cate) {  $item = $one_cate['main'];  ?>
                            <tr class="first_cat" id="mytr_<?php echo $item['id']; ?>">
                                <td>
                                    <a href="javascript:void(0)" onclick="hiddens(this,<?php echo $item['id']; ?>)">
                                        <i class="fa fa-expand"></i>
                                    </a><?php echo $item['name']; ?>
                                </td>
                                <td >
                                    <span onclick="addedit(this)" data-url="<?php  echo mobile_url('category', array('op' => 'addcate', 'pid' => $item['id']))?>" class="layui-btn layui-btn-small">添加子分类</span>
                                    <span onclick="addedit(this)" data-url="<?php  echo mobile_url('category', array('op' => 'editcate', 'id' => $item['id'],'pid'=>$item['pid']))?>" class="layui-btn layui-btn-warm  layui-btn-small">编辑分类</span>
                                    <a href="<?php  echo mobile_url('category', array('op' => 'delete', 'id' => $item['id']))?>" onclick="return confirm('确认删除此分类吗？');return false;" class="layui-btn layui-btn-danger layui-btn-small">删除分类</a>
                                </td>
                            </tr>
                            <?php foreach ($one_cate['child'] as $son_cate) {  $row = $son_cate['main'];  ?>
                            <tr style="display: none" class="parent_<?php echo $row['pid'];?> parent_show">
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $row['name']; ?></td>
                                <td>
                                    <span class="layui-btn layui-btn-warm  layui-btn-small" onclick="addedit(this)" data-url="<?php  echo mobile_url('category', array('op' => 'editcate', 'id' => $row['id'],'pid'=>$row['pid']))?>">编辑分类</span>
                                    <a href="<?php  echo mobile_url('category', array('op' => 'delete', 'id' => $row['id']))?>" onclick="return confirm('确认删除此分类吗?');" class="layui-btn layui-btn-danger layui-btn-small">删除分类</a>
                                </td>
                            </tr>
                            <?php }}?>
                            </tbody>
                        </table>
                </div>

            </div>
        </div>
        <?php include page('seller_footer');?>
	</body>


<script type="text/javascript">
function addedit(obj){
    var url = $(obj).data('url');
    $.ajaxLoad(url,{},function(){
        $('#alterModal').modal('show');
    });
};
    
layui.use(['element','layer'], function(){
  var $ = layui.jquery;
  var element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块
  var layer = layui.layer;
  var newChildLayer = $(".newChildLayer");
  var newRoleLayer = $(".newRoleLayer");
  

  $(".newRole").on("click",function(){
    layer.open({
      type: 1, 
      area : ['320px' , '300px'],
      content: newRoleLayer
    });
  });
});

</script>
<script>
function hiddens(thisObj, pid) {
    $('.parent_' + pid).toggle();
}
</script>
</html>