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
		<div class="cart_title" >
		   <span class="return">
		   		<img src="<?php echo RESOURCE_ROOT;?>addons/common/image/job/return.png" width="10px" height="16px">
		   </span>
		    面试人的名字
		</div>
		<!--面试失败-->
		<div class="bg">
			<img src="<?php echo RESOURCE_ROOT;?>addons/common/image/job/interview-fail.jpg" />
		</div>
	</body>
	
	<script>
		$(".return").click(function(){
		    window.history.back(-1);
		})
	</script>
</html>
