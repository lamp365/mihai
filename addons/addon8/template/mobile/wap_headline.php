<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>觅海头条</title>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="format-detection" content="telephone=no">	
	<link rel="shortcut icon" href="favicon.ico"/>
	<link rel='stylesheet' type='text/css'href='<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>
	<link rel='stylesheet' type='text/css'href='<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/todownapp.css' />
</head>

<style type="text/css">
	*{margin: 0;padding: 0;}
	.headline-content{
		width: 90%;
		overflow: hidden;
		margin-left: 5%;		
		box-sizing: border-box;
		margin-bottom: 50px;
	}
	.headline-content .health-men{
		width: 100%;		
		overflow: hidden;
		border-bottom: solid 1px #eee;
	}
	.headline-content .health-men .info{
		float: left;
		width: 75%;
		padding: 10px 0px 10px 10px;
	}
	.headline-content .health-men .info img{
		width: 50px;
		height: 50px;
		border-radius: 50%;
		display: inline-block;
		float: left;
		
	}
	.headline-content .health-men .info .name{
		display: inline-block;
		float: left;
		overflow: hidden;	
		margin-left: 10px;
		font-size: 16px;
		margin-top: 5px;
		width: 60%;
	}
	
	.headline-content .health-men .info .name .lz{
		background: #FCB9C2;
    border-radius: 4px;
    color: #fff;
    float: left;
    font-size: 12px;
    width: 30px;
    height: 16px;
    line-height: 16px;
    padding: 3px;
    margin-top: 1px;
    text-align: center;
    margin-right: 5px;
	}
	.headline-content .health-men .attention{
		float: right;
		width: 20%;
		padding: 20px 0px 0px 0px;
		margin-right: -10px;
	}
	.headline-content .health-men .attention span{
    padding: 6px 8px;
    height: 30px;
    line-height: 30px;
    text-align: center;
    border: 1px solid #F43776;
    border-radius: 4px;
    color: #F43776;
    font-size: 12px;
	}
	
	.headline-detail .title{
		color: #333;	
		margin: 20px 0;	
		font-weight:bold;		
	}
	.headline-detail .imglist {
		width: 100%;
		overflow: hidden;		
		padding-bottom: 3%;
		box-sizing: border-box;
	}
	.headline-detail .imglist  img{
		width: 100%;
		margin-top: 3%;
	}
	.headline-detail .info{
		width: 100%;
		padding: 3% 0;
		box-sizing: border-box;
	}
	.headline-detail .info span{
		color: #999;
		font-size: 10px;
	}
	/*底部栏*/
	.heal-foot ul{
		overflow: hidden;
		margin-top: 15px;
		width: 35%;
	}
	.heal-foot ul li{
		float: left;
		width: 30%;
		padding: 0px 0px 0px 15px;
	}
	.heal-foot ul li a img{
		width: 20px;
	}
</style>

<body>
	
	<!--头部-->	
	 <div class="top_header">
	    <div class="header_left return">
	        <a href="javascript:;" class="return" id="return" style="margin-top: 4px;"><img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/openshop/images/return.png"  height="18px"></a>
	    </div>
	    <div class="header_title" style="color: #000;font-size: 16px;font-weight: bold;line-height: 45px;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;width: 90%;left: 30px;">
			<?php echo $article_headline['title'];?>
	    </div>        
	</div>
	<!--@php if(!empty($article_headline['video'])){ @-->
	<video src="<?php echo $article_headline['video'];?>" width="100%" controls="controls" poster="">

	</video>
	<!--@php } @-->
	<div class="headline-content">
		<!--发布者-->
		<div class="health-men">
			<!--头像-->
			<div class="info">
				<img src="<?php if(empty($article_member['avatar'])){ echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/912865945439541.jpg'; }else{ echo download_pic($article_member['avatar'],60,60); }?>" />
				<p class="name" style="margin-top: 15px;">
					<span style="width: 70%;display: inline-block;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;height: 22px;line-height: 22px;">
   						 <?php if(!empty($article_member['nickname'])){ echo $article_member['nickname'];}else{ echo substr_cut($article_member['mobile']); } ?>  	
   					 </span>
					<!--发布时间-->
					<!--<span style="color: #999;font-size: 14px;margin-top: 5px;">发布于<?php echo date("Y-m-d",$article_headline['createtime']);?></span>-->									
				</p>
			</div>


		</div>
		<!--文章内容-->
		<div class="headline-detail">
			
			<!--文章标题-->
			<p class="title"><?php echo $article_headline['title'];?></p>
			
			<!--文章内容-->
			<p>
				<?php echo $article_headline['description'];?>
			</p>
			
			<!--文章图片-->
			<?php if(!empty($article_headline['pic'])){  $pic_arr = explode(';',$article_headline['pic']); ?>
			<div class="imglist">
				<?php foreach($pic_arr as $pic){ ?>
				<img src="<?php echo download_pic($pic,600)?>" />
				<?php } ?>
			</div>
			<?php } ?>

			<div class="info">
				<!--发布日期-->
				<span style="float: left;"><?php echo date("Y-m-d",$article_headline['createtime']);?></span>
				<!--收藏人数-->
				<span style="float: right;"><?php echo $collect_num;?>人收藏</span>
			</div>			
					
		</div>		
	</div>
	
			
	<?php include themePage('footer'); ?>	
	
</body>
<script src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/js/appwakeup.js"></script>

<script>
	
	$("#return").click(function(){
        //没有上一页就返回首页
        if(document.referrer.length == 0){        	
			  window.location.href = "index.php";
		}else{				
			 var newHref = document.referrer;
			 $("#return").attr("href",newHref);							
		}
		window.history.back(-1);
    });

</script>
</html>
