<?php defined('SYSTEM_IN') or exit('Access Denied');?>
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
    </style>
</head>
<body style="padding:10px;">
<div class="layui-form">
    <h3 class="blue" style="margin-top:5px;margin-bottom:5px;">
        <span style="font-size:18px;"><strong>邮费配置</strong></span>
    </h3>
    <ul class="nav nav-tabs" >
        <li><a href="<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'index'));?>">物流选择</a></li>
        <li class="active"><a href="<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'freePrice','status'=>1));?>">邮费配置</a></li>
        <li><a href="<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'returnAddress','status'=>2));?>">退货地址配置</a></li>
    </ul>
<form method="post" action="<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'freePrice','type'=>'add','status'=>1));?>">
    <br >
    <div class="layui-form">
        <label class="layui-form-label">配送运费</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="express_fee" placeholder="配送运费；单位：元" class="layui-input"  value="<?php echo $express_fee;?>">
        </div>
        <p style="padding: 15px 38px;"></p>

        <label class="layui-form-label">免邮配置</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="free_dispatch" placeholder="免邮配置；单位：元" class="layui-input"  value="<?php echo $free_dispatch;?>">
        </div>
        <div class="layui-input-inline" style="width: 300px;">
            例如满 70 包邮
        </div>

        <p style="padding: 15px 38px;"></p>

        <label class="layui-form-label">最低多少配送</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="limit_send" placeholder="最低多少配送；单位：元" class="layui-input"  value="<?php echo $limit_send;?>">
        </div>
        <div class="layui-input-inline" style="width: 300px;">
            例如20块起 才配送
        </div>
    </div>
    <br >
    <button class="btn btn-sm btn-info" type="submit" style="margin-left: 30px;">
        确定提交
    </button>
</form>

</div>
</body>
</html>

