<!DOCTYPE html>
<html>
<head>
  <?php include page('seller_header');?>
    <style>
        .nav-tabs li a{
            padding-left: 18px;
            padding-right: 18px;
            text-align: center;
        }
        .set .icon_phone{
            margin-left:35px;
            margin-top:8px;
            width: 65px;
            height:65px;
            float: left;
            background: url('<?php WEB_ROOT ?>/themes/wap/__RESOURCE__/recouse/images/mobile.png') no-repeat;
        }
        .set .icon_pwd{
            margin-left:35px;
            margin-top:8px;
            width: 65px;
            height:65px;
            float: left;
            background: url('<?php WEB_ROOT ?>/themes/wap/__RESOURCE__/recouse/images/safe_lock.png') no-repeat;
        }
        .set .right{
            margin-top:14px;
            display: inline-block;
            font-size: 18px;
            float: left;
        }
        .set .right span{
            font-size: 12px;
            display: block;
            text-align: center;
        }
        .set .show{
            width: 100px;
            float: left;
            margin-left:160px;
            color:#999999;
            margin-top:20px;
        }
        .set .tips{
            width: 500px;
            float: left;
            margin-left:50px;
            color:#999999;
            margin-top:10px;
        }

    </style>
</head>
<body style="padding:10px;">
<div class="layui-form">
    <blockquote class="layui-elem-quote">安全设置 <span style="margin-left: 10px;font-size:12px; ">以下只有最高管理员可操作</span></blockquote>
    <ul class="nav nav-tabs" >
        <li  <?php  if( $_GP['op'] == 'safe' ) { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('shop',array('op'=>'safe'))?>">安全设置</a></li>
        <li  <?php  if($_GP['op'] == 'zhanghu') { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('shop',  array('op' => 'zhanghu'))?>">提现账户</a></li>
    </ul>
    <br/>
    <div class="">
        <table class="layui-table">
            <tr>
                <th>提款密码 <span style="font-size: 12px;color: #F63;margin-left: 10px;">（开启提款密码，以保障账户及资金安全）</span></th>
                <th style="width: 300px;"></th>
            </tr>
            <tbody class="set">
            <tr>
                <td>
                    <div class="icon_phone"></div>
                    <div class="right">
                        手机绑定
                        <span>已绑定</span>
                    </div>
                    <div class="show">
                        <?php echo $apply_man['mobile']; ?>
                    </div>
                    <div class="tips">
                        手机号，可用于接收敏感操作的身份验证信息，以及敏感行为操作的验证确认，非常有助于保护您的账号和账户财产安全。
                    </div>
                </td>
                <td>
                    <span class="layui-btn layui-btn-warm">已经绑定</span>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="icon_pwd"></div>
                    <div class="right">
                        支付密码
                        <?php if(empty($store['sts_tran_passwd'])){ ?>
                        <span style="color:#F60 ">未设置</span>
                        <?php }else{  ?>
                        <span>已设置</span>
                        <?php } ?>
                    </div>
                    <div class="show">
                        xxxxxx
                    </div>
                    <div class="tips">
                        设置支付密码后，在对账户中余额的先关操作时，需输入支付密码。
                    </div>
                </td>
                <td>
                    <?php if(empty($store['sts_tran_passwd'])){ ?>
                    <span class="layui-btn" onclick="setpwd(this)" data-url="<?php echo mobile_url('shop',array('op'=>'setpwd')); ?>">设置密码</span>
                    <?php }else{  ?>
                    <span class="layui-btn layui-btn-warm" data-url="<?php echo mobile_url('shop',array('op'=>'setpwd')); ?>" onclick="setpwd(this)">修改密码</span>
                    <?php } ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include page('seller_footer');?>
<script>
    layui.use("form",function(){
        var form = layui.form();
        /*分类联动*/
    })
    function setpwd(obj){
        var is_admin = "<?php echo $member['store_is_admin']; ?>";
        if(is_admin){
            var url = $(obj).data('url');
            $.ajaxLoad(url,{},function(){
                $('#alterModal').modal('show');
            });
        }else{
            layer.alert("只有最高管理员可操作！");
        }

    }
</script>
</body>
</html>