
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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
.system-message{ padding:0 0 48px;margin:150px auto;width:400px;border:5px solid #ccc}
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
				}, 1000);
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

</html>