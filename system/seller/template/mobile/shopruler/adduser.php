<!doctype html>
<html lang="en">
<head>
    <?php include page('seller_header');?>
</head>
<body  style="padding:10px;">
<blockquote class="layui-elem-quote">添加用户</blockquote>
<p>
    1、如手机已注册：只需验证短信，即可管理该店铺<br/>
    2、如手机未注册：验证短信后，并设置一个密码，即可管理该店铺
</p><br/>
<form class="layui-form" action="<?php echo mobile_url('shopruler',array('op'=>'adduser'));?>" method="post">
    <div class="layui clearfix">
        <div class="layui-form-item">
            <label class="layui-form-label">用户手机号</label>
            <div class="layui-input-inline" >
                <input type="number" name="mobile" placeholder="用户手机号" autocomplete="off" class="layui-input mobile" lay-verify="required|phone">
            </div>
            <span class="layui-btn layui-btn-warm" onclick="send_phonecode(this)">获取验证码</span>
        </div>
        <div class="layui-form-item userpwd" style="display: none">
            <label class="layui-form-label">用户密码</label>
            <div class="layui-input-inline" >
                <input type="text" name="pwd" placeholder="用户密码" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">该手机号未注册过，请设置一个密码</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">验证码</label>
            <div class="layui-input-inline" >
                <input type="text" name="checkcode" placeholder="短信验证码" autocomplete="off" class="layui-input" lay-verify="required">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">角色分组</label>
            <div class="layui-input-inline">
                <select name="group_id" lay-verify="required">
                    <option value="0">选择角色</option>
                    <?php foreach($sellergroup as $s_group){
                            echo "<option value='{$s_group['group_id']}'>{$s_group['group_name']}</option>";
                          }
                    ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-inline" >
                <input type="hidden" name="do_add" value="1">
                <button class="layui-btn" lay-submit lay-filter="formDemo">确认添加</button>
            </div>
        </div>
    </div>
</form>

<?php include page('seller_footer');?>
<script>
    function mobile_isreget(mobile){
        var isok = '';
        if(mobile.length == 11 && !isNaN(mobile)){
            var url = "<?php echo mobile_url('shopruler',array('op'=>'checkmobile')); ?>";
            $.ajax({
                'type':"POST",
                'url' : url,
                'async': false,
                'data' : {'mobile':mobile},
                'success':function(data){
                    if(data.errno ==1 ){
                        if(data.data.code == 1002){
                            //手机号本就不存在
                            $(".userpwd").show();
                        }else if(data.data.code == 1004){
                            //手机号已经存在
                            $(".userpwd").hide();
                        }
                        isok = true;
                    }else{
                        $('.mobile').val('');
                        layer.open({
                            title: '提示'
                            ,content: data.message
                        });
                        isok = false;
                    }
                }
            });
            return isok;
        }else{
            layer.open({
                title: '提示'
                ,content: '手机格式不对！'
            });
            isok =  false;
            return isok;
        }
    }
    layui.use('form', function(){
      var form = layui.form();
    });

    function send_phonecode(obj){
        var number = 120;
        var mobile = $('.mobile').val();
        if(mobile_isreget(mobile)){
            var url = "<?php echo mobile_url('shopruler',array('op'=>'adduser_code')); ?>";
            $.post(url,{'mobile':mobile},function(data){

                if(data.errno == 1){
                    //倒计时120秒
                    $(obj).prop('disabled',true);
                    var daojishi = setInterval(function(){
                      if( number == 0 ) {
                        clearInterval(daojishi);
                        $(obj).text('获取验证码').removeClass("layui-btn-disabled");
                      }else{
                        --number;
                        $(obj).text('重新发送（'+number+'s）').addClass("layui-btn-disabled"  );
                      }
                    },1000);
                }else{
                    layer.open({
                        title: '提示'
                        ,content: data.message
                    });
                }
            },"json");
        }else{
            console.log('dddd');
        }
    }
</script>
</body>
</html>