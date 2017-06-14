
<div class="alertModal-dialog" style="width:30%">
  
    <form  action="<?php  echo mobile_url('shop',array('name'=>'seller','op'=>'add_zhanghu')) ?>" method="post" class="gtype_form form-horizontal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">
                <?php echo intval($_GP['id'])>0?"编辑":"添加" ?>账户
            </h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label  class="col-sm-3 control-label">账户类型</label>
                <div class="col-sm-9">
                    <select name="type" id="type"  class="form-control">
                        <option value="1" <?php if($edit_bank['type'] == 1){ echo "selected";} ?>>银行卡</option>
                        <option value="2" <?php if($edit_bank['type'] == 2){ echo "selected";} ?>>支付宝</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label  class="col-sm-3 control-label">账户号码</label>
                <div class="col-sm-9">
                    <input type="text" name="bank_number" value="<?php echo $edit_bank['bank_number'] ?>" id="bank_number"  class="form-control"  placeholder="账户号码" required>
                </div>
            </div>
            <div class="form-group cart_own" style="display: none">
                <label  class="col-sm-3 control-label">持卡人姓名</label>
                <div class="col-sm-9">
                    <input type="text" name="card_own" value="<?php echo $edit_bank['card_own'] ?>" id="card_own"  class="form-control"  placeholder="持卡人姓名">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">短信验证</label>
                <div class="col-sm-6">
                    <input name="mobilecode" id="mobilecode" class="form-control" type="text" placeholder="短信验证码"/>
                </div>
                <div class="col-sm-3">
                    <span class="btn btn-md btn-info"  onclick="send_phonecode(this)">获取验证码</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="action" value="1">
            <input type="hidden" name="id" id="id" value="<?php echo $edit_bank['id']; ?>">
            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            <button type="submit"  class="btn btn-primary" >确认<?php echo intval($_GP['id'])>0?"编辑":"添加" ?></button>
        </div>
    </form>
</div>

<script>
    //监听表单验证  input框 需要的规则 可以查看bootstrap
    $(".gtype_form").validator();
    function send_phonecode(obj){
        var number = 120;
        var url = "<?php echo mobile_url('mobilecode',array('op'=>'index')); ?>";
        var type =  $("#type").val();
        if(type == 1){
            var parame = {action:'bank'};
        }else {
            var parame = {action:'ali'};
        }
        $.post(url,parame,function(data){
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

    $("#type").change(function(){
        var id = $(this).val();
        if(id == 1){
            $(".cart_own").show();
        }else{
            $(".cart_own").hide();
        }
    });
    $("#type").trigger('change');
</script>