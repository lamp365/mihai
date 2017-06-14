<!DOCTYPE html>
<html>
	<head>
        <?php include page('seller_header');?>
        <style>
        .layui-form-label{
            width: 90px;
        }
        .layui-input-block{
            margin-left: 95px;
        }
        .send-code{
            position: absolute;
            top: 0;
            right: 0;
            z-index: 99999;
        }
        </style>
	</head>
	<body>
        <div style="margin: 15px;">
            <form class="layui-form" id="pwd_form" action="<?php echo mobile_url('password', array('op' => 'rePassword','name' => 'seller','submit'=>'submit'));?>">
                <div class="layui-form-item">
                    <label class="layui-form-label">验证码</label>
                    <div class="layui-input-block" style="position:relative">
                        <input type="text" name="mobilecode" lay-verify="code" autocomplete="off" placeholder="请输入验证码" class="layui-input">
                        <input type="hidden" name="phone-number" value="<?php echo $info['mobile'] ?>" lay-verify="phone-number" class="phone-number">
                        <div class="layui-btn send-code" onclick='phoneCheck(this,"<?php echo  create_url('mobile', array('name' => 'shopwap','do' => 'regedit','op'=>'regedit_sms')); ?>",".phone-number")'>发送验证码</div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">新密码</label>
                    <div class="layui-input-block">
                        <input type="text" name="new_pwd1" lay-verify="pass" placeholder="请输入新密码" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">确认密码</label>
                    <div class="layui-input-block">
                        <input type="text" name="new_pwd2" lay-verify="pass" placeholder="请确认密码" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit="" lay-filter="demo1">提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/layui.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/verify.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/login.js"></script>
        <script>
            layui.use(['form', 'layedit', 'laydate','layer'], function() {
                var form = layui.form(),
                    layer = layui.layer,
                    layedit = layui.layedit,
                    laydate = layui.laydate;

                //创建一个编辑器
                var editIndex = layedit.build('LAY_demo_editor');
                //自定义验证规则
                form.verify({
                    pass: [/(.+){6,12}$/, '请输入新密码'],
                    content: function(value) {
                        layedit.sync(editIndex);
                    }
                });

                //监听提交
                form.on('submit(demo1)', function(form) {
                    $("#pwd_form").ajaxSubmit({
                        type: "post",
                        url: "<?php echo mobile_url('password', array('op' => 'rePassword','name' => 'seller','submit'=>'submit'));?>",
                        dataType: "json",
                        success: function(ret){
                            //返回提示信息       
                            if(ret.errno==1){
                                parent.layer.closeAll();
//                                $('#avatar-modal').modal('hide');
                            }else{
                                layer.open({title: '提示',content: ret.message});
                            }
                        }
                    });
                    return false;
//                    layer.alert(JSON.stringify(data.field), {
//                        title: '最终的提交信息'
//                    })
//                    return false;
                });
            });
        </script>
	</body>
</html>