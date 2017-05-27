
<!--wap-->

<!--<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<title>跳转提示</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
.system-message{ padding:0 0 48px;margin:50% auto;width:90%;border:5px solid #ccc;}
.system-message h3{ font-size: 50px; font-weight: normal; line-height: 120px; margin-bottom: 12px;border:1px solid #ccc}
.system-message .jump{ padding-top: 10px}
.system-message .jump a{ color: #333;}
.system-message .success,.system-message .error{ line-height: 1.8em; font-size: 23px ;text-align: center;}
.system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none}
</style>
</head>
<body>
<div class="system-message">
	<p style="height:35px;background:url(<?php echo RESOURCE_ROOT;?>/addons/common/image/msg_top_bg.png) #ccc;padding-left:10px;line-height:35px;color:white">系统提示</p>
	<div style="padding:24px;">				
		<div class="error">
			<img style="margin-right: 9px;padding-top:10px;" src="<?php echo RESOURCE_ROOT;?>/addons/common/image/<?php  if($type=='success') { ?>success.png<?php  } else if($type=='error') { ?>error.png<?php  } else if($type=='tips') { ?>success.png<?php  } else if($type=='sql') { ?>error.png<?php  } ?>" style="cursor:pointer;"><span style="padding-top:0px;"><?php  echo $msg;?>				
		</div>	
	</div>
	<p class="detail"></p>
	<div class="jump" id="box" style="float:right;padding-right:5px;">
		<?php  if($redirect) { ?>
			<?php  if($successAutoNext) { ?>
				页面自动 <a id="href" href="<?php  echo $redirect;?>">跳转</a> 等待时间： <b id="wait">2</b>
				<script type="text/javascript">
				(function(){
				window.scrollTo(0,0);
				var wait = document.getElementById('wait'),href = document.getElementById('href').href;
				var interval = setInterval(function(){
					var time = --wait.innerHTML;
					if(time <= 0) {
						location.href = href;
						clearInterval(interval);
					};
				}, 100000);
				})();
				</script>
			<?php  } else { ?>
			 	<a id="href" href="<?php  echo $redirect;?>">点击进入下一页</a>
			<?php  } ?>
		<?php  } else{ ?>			
				<script type="text/javascript">
					(function(){						
						
						var box = document.getElementById("box");
						if(document.referrer.length == 0){
							 box.innerHTML += '<p> </p>';
						}else{
							 box.innerHTML += '<p>[<a href="#" id="aa">点击这里返回上一页</a>] </p>';
							 var newHref = document.referrer;
							 document.getElementById("aa").setAttribute("href",newHref);							
						}											   											 
					})();
				</script>					
		<?php  } ?>
	</div>
</div>


</body>

</html>-->

<!--pc-->

<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<title>跳转提示</title>
<style type="text/css">
*{ padding: 0; margin: 0; }

.system_message{
	background: url(<?php echo RESOURCE_ROOT;?>/addons/common/image/msg_alert_bg.png) no-repeat;
	width: 300px;
	height: 175px;
    position: absolute;
    top: 30%;
    left: 50%;
    margin: -87.5px 0 0 -150px;
    z-index: 3;
}
.system_message .error{
	padding-top: 80px;
	text-align: center;
	color: #333333;
	font-size: 16px;
/*	height: 44px;*/
}
.system_message .error img{
    margin-right: 9px;
    vertical-align: middle;
    margin-top: -3px;
}
.system_message .detail{
	text-align: center;
	font-size: 14px;
	color: #717171;
/*	height: 19px;*/
}
#box .href-btn,#href{
	background-color: #fc6e51;
	padding: 5px 10px;
	border-radius: 4px;
	color: #fff;
	font-size: 14px;
	text-align: center;
	text-decoration: none;
	position: absolute;
    bottom: 10px;
    right: 20px;
}
#box{
	color: #717171;
	font-size: 14px;
}
.jump{
	text-align: center;
	margin-top: 10px;
}
.bg{
	position: fixed;
	width: 100%;
	height: 100%;
	background-color:#545454;
	opacity: 0.5;
	z-index: 2;
}
</style>
</head>
<body>
<div class="system_message">
	<div class="error">
		<img  src="<?php echo RESOURCE_ROOT;?>/addons/common/image/<?php  if($type=='success') { ?>success.png<?php  } else if($type=='error') { ?>error.png<?php  } else if($type=='tips') { ?>success.png<?php  } else if($type=='sql') { ?>error.png<?php  } ?>"><span><?php  echo $msg;?>
	</div>
	<p class="detail"></p>
	<div class="jump" id="box">
		<?php  if($redirect) { ?>
			<?php  if($successAutoNext) { ?>
				正在跳转中<b id="wait">2</b> <a id="href" href="<?php  echo $redirect;?>">立即跳转</a>
				<script type="text/javascript">
				(function(){
				window.scrollTo(0,0);
				var wait = document.getElementById('wait'),href = document.getElementById('href').href;
				var interval = setInterval(function(){
					var time = --wait.innerHTML;
					if(time <= 0) {
						location.href = href;
						clearInterval(interval);
					};
				}, 1000);
				})();
				</script>
			<?php  } else { ?>
			 	<a id="href" class="href-btn" href="<?php  echo $redirect;?>">立即跳转</a>
			<?php  } ?>
		<?php  } else{ ?>			
				<script type="text/javascript">
					(function(){						
						
						var box = document.getElementById("box");
						if(document.referrer.length == 0){
							box.innerHTML += '<p><a href="./index.php" id="aa" class="href-btn">立即跳转</a></p>';
						}else{
							 box.innerHTML += '<p><a href="#" id="aa" class="href-btn">立即跳转</a></p>';
							 var newHref = document.referrer;
							 document.getElementById("aa").setAttribute("href",newHref);							
						}											   											 
					})();
				</script>					
		<?php  } ?>
	</div>
</div>
<div class="bg"></div>
</body>

</html>