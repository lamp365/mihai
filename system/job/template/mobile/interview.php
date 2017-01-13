<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title>查询结果</title>
		<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/style.css">
		<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/jquery-1.7.2.min.js"></script>
	</head>
	<style>
		*{margin: 0;padding: 0;}
		html,body{height: 100%;}
		.bg{
			width: 100%;
			height: 100%;
		}
		.box{
			width: 80%;
			height: 50%;			
			position:absolute;
			top: 40%;
			left: 10%;			
			z-index: 1;	
			color: #fff;
			text-align: center;	
		}
		.box div{
			width: 100%;
			height: 100%;
			background: #fff;
			opacity: 0.5;
			position: absolute;
		}
		.box h3{				
			margin-top: 25%;			
		}
		.box input#num{						
			width: 80%;
			height: 50px;
			margin-top:10%;
			background: none;
			border: none;
			border-bottom: solid 1px #fff;
			outline: none;
			font-size: 20px;
			letter-spacing: 5px;
			text-indent: 15%;
			color: #fff;
		}
		.box input#sub{
			
			width: 60%;
			height: 50px;
			margin-top: 20%;
			background: none;			
			border: solid 1px #fff;
			outline: none;
			border-radius: 5px;
			font-size: 20px;
			font-weight: bold;
			color:#fff;
		}
	</style>
	<body>
		<div class="cart_title" >
		   <span class="return">
		   		<img src="<?php echo RESOURCE_ROOT;?>addons/common/image/job/return.png" width="10px" height="16px">
		   </span>
		</div>
		<div style="position: fixed;width: 100%;overflow: hidden;top: 0;">
			<img class="bg" src="<?php echo RESOURCE_ROOT;?>addons/common/image/job/interview.png"/>
			<!--输入框-->
			<div class="box">				
				<h3>请输入您的手机号</h3>
				<form action="" method="post" role="form" class="form-horizontal mobile_form">
					<input type="text"  id="num" autocomplete="off"/>
					<input type="submit" value="确认" id="sub" onclick="check()"/>
				</form>
			</div>
		</div>
	</body>
	<script>
		function check(){
			var url = "<?php  echo mobile_url('job',array('op' => 'check'));?>";
			
			var tel = $("#num").val();		
			var reg = /^0?1[3|4|5|8][0-9]\d{8}$/;				
			if(!tel){
				alert("请输入手机号！");
				return false;
			}else if(!reg.test(tel)) {
				alert('请输入正确的手机号码！');
				return false;
			}else{		
				url = url + '&mobile='+tel;
				$(".mobile_form").attr('action',url);
			}
		}
		
		$(".return").click(function(){
		    window.history.back(-1);
		})
	</script>
</html>
