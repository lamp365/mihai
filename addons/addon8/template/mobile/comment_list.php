<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title>Document</title>
    <link rel='stylesheet' type='text/css'href='<?php echo WEBSITE_ROOT . 'themes/' . 'wap/'. '/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
</head>
<style>
	
	*{
		margin: 0;
		padding: 0;
	}
	.top_header{
		max-width: 100% !important;
	}
	.heal-foot ul{
		overflow: hidden;
		margin-top: 15px;
		width: 40%;
	}
	.heal-foot ul li{
		float: left;
		width: 30%;
	}
	.heal-foot ul li a img{
		width: 20px;
	}
	.health-men{
		width: 90%;
		margin: 0 5%;
		overflow: hidden;
	}
	.health-men .info{
		float: left;
		width: 75%;
		padding:20px;
		box-sizing: border-box;
	}
	.health-men .info img{
		width: 60px;
		height: 60px;
		border-radius: 50%;
		display: inline-block;
		float: left;
		
	}
	.health-men .info .name{
		display: inline-block;
		float: left;
		overflow: hidden;
		padding: 10px 0 0 10px ;
		
	}
	.health-men .info .name span{
		display: block;
	}
	.health-men .attention{
		float: right;
		width: 20%;
		padding: 20px 0px 0px 0px;
		margin-top: 10px;
	}
	.health-men .attention span{
		padding: 5px;
		height: 30px;
		line-height: 30px;
		text-align: center;		
		border: 1px solid #F43776;
		border-radius: 6px;
		color: #F43776;		
	}
</style>
<body>
	<!--头部-->
		<div class="top_header" style="border-bottom: none;">
		    <div class="header_left return">
		        <a href="javascript:;" class="return" id="return" style="margin-top: 4px;">
		       		 <img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/openshop/images/return.png"  height="18px">
		        </a>
		    </div>
		    <div class="header_title" style="color: #000;font-size: 20px;font-weight: bold;line-height: 45px;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;width: 90%;left: 30px;">
				评论
		    </div>        
		</div>
	
	
		<!--评论内容-->
		
		<!--没有评论-->
		<div style="display: none;">						
			<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/health-comment@3x.png" style="position: absolute;top: 40%;left: 40%;"/>
			<p style="position: absolute;top: 40%;left:42%;font-size: 20px;margin-top: 250px;width: 182px;">沙发等待你来抢~</p>
		</div>	
		
		<!--有评论-->
		<div class="health-men">
			<!--头像-->
			<div class="info"style="width: 100%;border-bottom: solid 1px #eee;">
				<img src="<?php if(!empty($member_comment['avatar'])){ echo $member_comment['avatar'];}else{ echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/912865945439541.jpg'; } ?>" />
				<p class="name">
					<span>用户名</span>
					<!--发布时间-->
					<span style="color: #999;font-size: 14px;margin-top: 5px;">2016-12-01 14:00</p>
				</p>
				<!--评论内容-->
				<div style="clear: both;margin-left: 70px;">
					哈哈哈哈
				</div>
			</div>						
		</div>
			
		<!--底部栏 -->
		<div style="background: #F8F8F8;height: 50px;width: 100%;position: fixed;bottom: 53px;left: 0;" class="heal-foot">
			<input type="text" style="width: 40%;outline: none;background: #FFFFFF;border: 1px solid #DCDDE3;border-radius: 29px;height: 30px;margin: 10px;text-indent: 20px;" placeholder="写下评论……" value=""/>
			<ul style="float: right;list-style: none;">
				<li>
					<a>
						<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/health-comment@2x.png" />
					</a>
				</li>
				
				<li>
					<a>
						<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/clle@2x.png" />
					</a>
				</li>
				<li>
					<a>
						<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/share@2x.png" />
					</a>
				</li>
			</ul>
		</div>

<?php include themePage('footer'); ?>
<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap/'. '/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>	
<script>
	$("#return").on("click",function(){
		alert(document.referrer);		
		if(document.referrer.length == 0){
			  window.location.href = "index.php";
		}else{				
			 var newHref = document.referrer;
			 $("#return").attr("href",newHref);							
		}
	})
</script>