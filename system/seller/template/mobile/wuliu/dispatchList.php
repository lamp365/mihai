<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <?php include page('seller_header');?>
</head>
<style type="text/css">
    label{
        font-weight: 400;
        padding: 0 15px;
        width: 180px;
        margin-top: 10px;
        cursor: pointer;
    }
    .nav-tabs li a{
        padding-left: 18px;
        padding-right: 18px;
        text-align: center;
    }
</style>
<body style="padding:10px;">
<div class="layui-form">
    <h3 class="blue" style="margin-top:5px;margin-bottom:5px;">
        <span style="font-size:18px;"><strong>配送方式</strong></span>
    </h3>
    <ul class="nav nav-tabs" >
        <li class="active"><a href="<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'index'));?>">物流选择</a></li>
        <li><a href="<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'freePrice','status'=>1));?>">免邮配置</a></li>
        <li><a href="<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'returnAddress','status'=>2));?>">退货地址配置</a></li>
    </ul>
<form method="post" action="<?php echo mobile_url('wuliu',array('name'=>'seller','op'=>'addDispatch'));?>">
	<?php  if(is_array($result)) { foreach($result as $item) { ?>
        <label>
            <input style="display:inline" type="checkbox" name="dispatchId[]" value="<?php echo $item['id'];?>" <?php if (in_array($item['id'], $dispatchId)){?>checked="checked"<?php }?>/><?php echo $item['name'];?>
        </label>
    <?php }}?>
    <div style="padding: 15px 15px;">
        <button class="btn btn-sm btn-info" type="submit">
            确定提交
        </button>
    </div>
</form>

</div>
</body>
</html>

