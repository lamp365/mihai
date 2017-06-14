<div class="alertModal-dialog" style="width:35%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">设置密码</h4>
    </div>
    <form method="post" id='pwd_form' action="<?php echo mobile_url('shop',array('op'=>'setpwd','do_pwd'=>1));?>">
    <div class="modal-body form-horizontal">
        <div class="form-group">
            <label class="col-sm-3 control-label">短信验证</label>
            <div class="col-sm-6">
                <input name="mobilecode" id="mobilecode" class="form-control" type="password"/>
            </div>
            <div class="col-sm-3">
               <span class="btn btn-md btn-info" onclick="send_phonecode(this)">获取验证码</span>
            </div>
        </div>
        <?php if(!empty($store['sts_tran_passwd'])){  ?>
        <div class="form-group">
            <label class="col-sm-3 control-label">原始密码</label>
            <div class="col-sm-9">
                <input name="old_pwd" id="old_pwd" type="password" class="form-control" />
            </div>
        </div>
        <?php } ?>
        <div class="form-group">
            <label class="col-sm-3 control-label">支付密码</label>
            <div class="col-sm-9">
                <input name="pwd" id="pwd" type="password" class="form-control" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">确认密码</label>
            <div class="col-sm-9">
                <input name="repwd" type="password" id="repwd" class="form-control" />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary" onclick="check_parame()">确认设置</button>
    </div>
    </form>
</div>

<script>
    function send_phonecode(obj){
        var number = 120;
        var url = "<?php echo mobile_url('mobilecode',array('op'=>'index')); ?>";
        $.post(url,{},function(data){
            if(data.errno == 1){
                //倒计时120秒
                $(obj).prop('disabled',true);
                var daojishi = setInterval(function(){
                    if( number == 0 ) {
                        clearInterval(daojishi);
                        $(obj).text('获取验证码');
                        $(obj).prop('disabled',false);
                    }else{
                        --number;
                        $(obj).text('发送（'+number+'s）');
                        $(obj).prop('disabled',true);
                    }
                },1000);
            }else{
                layer.open({
                    title: '提示'
                    ,content: data.message
                });
            }
        },"json");

    }

    function check_parame()
    {
        //检查各项参数
        if($("#mobilecode").val() == ''){
            layer.alert('验证码不能为空！');
            return false;
        }
        if($("#old_pwd").length > 0 && $("#old_pwd").val() == ''){
            layer.alert('原始密码不能为空！');
            return false;
        }
        if($("#pwd").val() == '' || $('#repwd').val() == ''){
            layer.alert('密码不能为空！');
            return false;
        }
        if($("#pwd").val() != $('#repwd').val()){
            layer.alert('两次密码输入不一致');
            return false;
        }

        $("#pwd_form").submit();
    }
</script>