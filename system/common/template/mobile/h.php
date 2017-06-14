<?php defined('SYSTEM_IN') or exit('Access Denied'); ?>
<?php
$h_member = get_member_account();
//查看该法人 是否有正在申请的店铺
$onChaekStore = mysqld_select("select * from ".table('store_shop_apply')." where sts_openid='{$h_member['openid']}'");
$onCheckStore = mysqld_select("select * from ".table('store_shop')." where sts_openid='{$h_member['openid']}'");
$cfg  =  globaSetting();
if(!empty($h_member['store_sts_id'])){
    //之前已经成功申请过店铺
    $has_store = 1;
}else{
    //之前都没有过申请店铺
    $has_store = 0;
}
?>
<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link rel="shortcut icon" href="favicon.ico"/>
        <title><?php echo empty($title)?$cfg['shop_title']:$title.'你就在城市之心' ?></title>
        <link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/css/layui.css" media="all" />
        <link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/seller/css/login.css" />
        <link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/seller/css/jquery.fullpage.min.css" />
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/layui.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/jquery.fullpage.min.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/jquery.easings.min.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/verify.js"></script>
</head>
<style>
::-ms-clear{display: none;}
::-ms-reveal{display: none;}
body{
	min-width: 1200px;
	margin:0 auto;
}
.top-nav{
	color: #fff;
}
#main{
	width: 1200px;
	height: 700px;
	margin:0 auto;
}
</style>
<body>
<div class="beg-login-bg">
       <div class="top-nav">
			<div class="top-nav-area">
				<div class="top-nav-div">
					<ul class="top-nav-left">
					    <li><img src="<?php echo $cfg['shop_logo']; ?>" height="30" /></li>
					</ul>
					<ul class="top-nav-list">
                        <?php if(empty($h_member)){ ?>
                            <li><a href="javascript:;" onclick="loginAlert()">登录</a></li>
                            <li class="point"><a href="javascript:;" onclick="register()">免费注册</a></li>
                        <?php }else{ ?>
                        <!--  之前申请过有店铺   -->
                            <?php if($has_store){ ?>
                             <li class="shop_control"> <a href="<?php echo mobile_url('index',array('name'=>'seller'));?>">店铺管理</a></li>
                            <?php } ?>
                            <?php if(empty($onChaekStore) && empty($onCheckStore) ){ ?>
                             <li><a href="<?php echo mobile_url('index',array('name'=>'shopwap','op'=>'apply')); ?>">商家入驻</a></li>
                            <?php }elseif (!empty($onChaekStore)) {    ?>
                             <li> <span style="color: #333;cursor: pointer" onclick="get_store_jindu()">审核进度</></li>
                            <?php } ?>

                        <?php } ?>
						<?php if(!empty($h_member)){ ?>
                        <li class="shop-logo">
                            <img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/0.jpg">
                            <div class="shop-hover-div">
                                <div class="shop-hover-head" >
                                    <div class="big-logo"><img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/0.jpg"></div>
                                    <div class="shop-info">
                                        <div>店铺名称</div>
                                        <div>会员等级：L0会员</div>
                                        <div>余额：0.00</div>
                                    </div>
                                </div>
                                <div class="shop-hover-content">
                                    <div class="clearfix">
                                        <div class="shop-hover-l"><a href="" target="_blank" style="border-right:1px solid #4f4f4f"><i class="fa fa-commenting" aria-hidden="true"></i>产品续费</a></div>
                                        <div class="shop-hover-l"><a href="" target="_blank"><i class="fa fa-commenting" aria-hidden="true"></i>实名认证</a></div>
                                    </div>
                                    <div class="clearfix" style="border-top:1px solid #4f4f4f">
                                        <div class="shop-hover-l"><a href="" target="_blank" style="border-right:1px solid #4f4f4f"><i class="fa fa-commenting" aria-hidden="true"></i>未支付订单</a></div>
                                        <div class="shop-hover-l"><a href="" target="_blank"><i class="fa fa-commenting" aria-hidden="true"></i>未读消息</a></div>
                                    </div>
                                </div>
                                <div class="shop-hover-footer" >
                                    <a class="shop-gong-dang" href="" target="_blank">工单管理</a>
                                    <a class="shop-login-out" href="">退出</a>
                                </div>
                            </div>
                        </li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="bottom-nav"></div>
</div>

<script>
    function get_store_jindu(){
        <?php if($onChaekStore['sts_info_status'] == 1 || $onChaekStore['sts_info_status'] == 12 ){ ?>
        layer.confirm('您有店铺信息未完善!', {
            btn: ['完善信息', '取消'] //可以无限个按钮
        }, function(index, layero){
            var url = "<?php echo mobile_url('index',array('op'=>'apply','sts_id'=>$onChaekStore['sts_id'])); ?>";
            window.location.href = url;
        }, function(index){
            //按钮【按钮二】的回调
        });

        <?php }else if($onChaekStore['sts_info_status'] == 2  ){ ?>
        layer.alert('您有店铺信息还在审核中!');

        <?php }else if($onChaekStore['sts_info_status'] == 3){  ?>
        layer.confirm("审核失败，<?php echo  $onChaekStore['fail_reason'];?>", {
            btn: ['修改信息', '取消'] //可以无限个按钮
        }, function(index, layero){
            var url = "<?php echo mobile_url('index',array('op'=>'apply','sts_id'=>$onChaekStore['sts_id'])); ?>";
            window.location.href = url;
        }, function(index){
            //按钮【按钮二】的回调
        });
        <?php } ?>

    }

</script>