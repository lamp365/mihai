<!DOCTYPE html>
<html>
	<head>
        <?php include page('seller_header');?>
	</head>
	<body style="padding:10px;">
    	<blockquote class="layui-elem-quote">子账号信息<span class="child-stop-info">*新增、修改角色并分配给子账号后，子账号的角色约需要十分钟才能生效，请稍作等待</span></blockquote>
        <div class="layui-tab layui-tab-card">
            <ul class="layui-tab-title">
                <li class="layui-this">子账号管理</li>
                <li>角色管理</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <div class="layui-btn layui-btn-small newChild">新建子账号</div>
                    <div class="newChildLayer">
                        <div class="layui-form-item">
                            <label class="layui-form-label">账号名</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="请输入标题" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">角色</label>
                            <div class="layui-input-block">
                                <input type="text" name="username" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">备注</label>
                            <div class="layui-input-block">
                                <input type="text" name="username" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <div class="layui-btn">提交</div>
                            </div>
                        </div>
                    </div>
                    <table class="layui-table">
                        <thead>
                            <tr>
                                <th>账号名</th>
                                <th>角色</th>
                                <th>备注</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>类型</td>
                                <td>专业买手</td>
                                <td>1</td>
                                <td>1</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="layui-tab-item">
                    <div class="layui-btn layui-btn-small newRole">新建角色</div>
                    <div class="newRoleLayer">
                        <div class="layui-form-item">
                            <label class="layui-form-label">角色</label>
                            <div class="layui-input-block">
                                <input type="text" name="title" lay-verify="title" autocomplete="off" placeholder="请输入标题" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">模块权限</label>
                            <div class="layui-input-block">
                                <input type="text" name="username" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">分配该角色的子账号</label>
                            <div class="layui-input-block">
                                <input type="text" name="username" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <div class="layui-btn" >提交</div>
                            </div>
                        </div>
                    </div>
                    <table class="layui-table">
                        <thead>
                            <tr>
                                <th>角色</th>
                                <th>模块权限</th>
                                <th>分配该角色的子账号</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>1</td>
                                <td>1</td>
                                <td>1</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
	</body>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/layui.js"></script>
<script type="text/javascript">
layui.use(['element','layer'], function(){
  var $ = layui.jquery;
  var element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块
  var layer = layui.layer;
  var newChildLayer = $(".newChildLayer");
  var newRoleLayer = $(".newRoleLayer");
  $(".newChild").on("click",function(){
    layer.open({
      type: 1, 
      area : ['320px' , '300px'],
      content: newChildLayer
    });
  });
  $(".newRole").on("click",function(){
    layer.open({
      type: 1, 
      area : ['320px' , '300px'],
      content: newRoleLayer
    });
  });
});

</script>
</html>