<!doctype html>
<html lang="en">
<head>
    <?php include page('seller_header');?>
    
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/layui-mz-min.js" charset="utf-8"></script>
</head>
<body  style="padding:10px;">
<blockquote class="layui-elem-quote">发放优惠券</blockquote>
<!--<form class="layui-form" action="<?php echo mobile_url('shopbonus',array('op'=>'grantCouponSub'));?>" method="post">-->

    <div class="layui clearfix">
        
            
        <div class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">用户手机号</label>
            <div class="layui-input-inline" >
                <input type="text" name="mobile" placeholder="请输入用户手机号" autocomplete="off" class="layui-input" lay-verify="required|phone" id='mobile' >
            </div>
        </div>
        
        <div class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">用户昵称(非必填)</label>
            <div class="layui-input-inline" >
                <input type="text" name="nickname" placeholder="请输入用户昵称" autocomplete="off" class="layui-input" id='nickname'>
            </div>
        </div>
            
        <div class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">发放数量</label>
            <div class="layui-input-inline" >
                <input type="text" name="grantnums" placeholder="发放数量" autocomplete="off" class="layui-input" id='grantnums' value="1">
            </div>
        </div>
        
        
        
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-inline" >
                <input type="hidden" name="do_add" value="1">
                    <button class="layui-btn" lay-submit lay-filter="formDemo" id="couponsub">提交</button>
            </div>
        </div>
    </div>
</div>   
<input type='hidden' value='<?php echo $_GP['id'];?>' name="id" id='id'>
<!--</form>-->

<?php include page('seller_footer');?>
<script>
    layui.use(['form', 'laydate'], function(){
        var form = layui.form();
        var laydate = layui.laydate;
        
        //layui.selMeltiple(layui.jquery);
        
    });
    

    layui.jquery("#couponsub").click(function(){
        var form_data_json = {};

        var form_data_json = {
            'id'       :layui.jquery('#id').val(),
            'mobile'   :layui.jquery('#mobile').val(),
            'nickname' :layui.jquery('#nickname').val(),
            'grantnums':layui.jquery('#grantnums').val()
        }
        
        var url = "<?php echo mobile_url('shopbonus',array('op'=>'grantCouponSub')); ?>";

        layui.jquery.post(url,form_data_json,function(data){
            if(data.errno == 1){
                //倒计时120秒
                layui.jquery(obj).prop('disabled',true);
            }else{
                layer.open({
                    title: '提示'
                    ,content: data.message
                    ,yes: function(index){
                        //location.href = data.data[0];
                        location.href = data.data;
                    }
                });
            }
            return false;
        });
    });

    function mobile_isreget(mobile){
        if(mobile.length == 11 && !isNaN(mobile)){
            var url = "<?php echo mobile_url('shopruler',array('op'=>'checkmobile')); ?>";
            $.post(url,{'mobile':mobile},function(data){
               if(data.errno ==1 ){
                   if(data.data.code == 1002){
                       //手机号本就不存在
                       $(".userpwd").show();
                   }else if(data.data.code == 1004){
                       //手机号已经存在
                       $(".userpwd").hide();
                   }
                   return true;
               }else{
                   $('.mobile').val('');
                   layer.open({
                       title: '提示'
                       ,content: data.message
                   });
                   return false;
               }
            },'json');
        }else{
            layer.open({
                title: '提示'
                ,content: '手机格式不对！'
            });
            return false;
        }
    }

    function send_phonecode(obj){
        var mobile = $('.mobile').val();
        if(mobile_isreget(mobile)){
            var url = "<?php echo mobile_url('shopruler',array('op'=>'adduser_code')); ?>";
            $.post(url,{'mobile':mobile},function(data){
                if(data.errno == 1){
                    //倒计时120秒
                    $(obj).prop('disabled',true);
                }else{
                    layer.open({
                        title: '提示'
                        ,content: data.message
                    });
                }
            });
        }
    }
</script>
</body>
</html>