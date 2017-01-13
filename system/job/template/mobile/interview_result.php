<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/style.css">
		<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/jquery-1.7.2.min.js"></script>
		<title>面试结果</title>
	</head>
	<style>
		*{
			margin: 0;padding: 0;
		}
		html,body{
			height: 100%;
		}
		.bg{
			width: 100%;
			height: 100%;
			position:fixed;
			top: 0;
		}
		.bg img{
			width: 100%;
			height: 100%;
		}
	</style>
	<body>
		<div class="cart_title">
		   <span class="return">
		   		<img src="<?php echo RESOURCE_ROOT;?>addons/common/image/job/return.png" width="10px" height="16px">
		   </span>		   
		</div>
		<!--面试成功-->
		<div class="bg">
			<img src="<?php echo RESOURCE_ROOT;?>addons/common/image/job/interview-succ.jpg" />
		</div>
		<!--面试者信息 -->
		<div style="position:relative;z-index:2;margin-top:30px;margin-left:30px;">
			<P style="margin-bottom:10px;color:#fff;font-weight:bold;">姓名：<?php echo $re['name'];?></P>
			<p style="color:#fff;font-weight:bold;">岗位：<?php echo $re['job'];?></p>
		</div>
	</body>
	
	<script>
		$(".return").click(function(){
		    window.history.back(-1);
		})
	</script>
</html>
