<html>
<head>
</head>
<body>
    <?php if (is_array($couponsList) && !empty($couponsList)){foreach ($couponsList as $key=>$v){?>
    <ul>
        <li><a href="<?php echo mobile_url('coupons',array('name'=>'shopwap','op'=>'getCoupons','stsid'=>$stsid,'scid'=>$v['scid'])); ?>"><?php echo $v['coupon_name'];?></a></li>
    </ul>
    <?php }}?>
</body>
</html>