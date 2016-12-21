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
	<script src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/js/todownapp.js" type="text/javascript" charset="utf-8"></script>
</head>

<style type="text/css">
	*{margin: 0;padding: 0;}
	.headline-content{
		width: 90%;
		overflow: hidden;
		margin-left: 5%;		
		box-sizing: border-box;
	}
	.headline-content .health-men{
		width: 100%;		
		overflow: hidden;
		
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
	}
	
	.headline-content .health-men .info .name .lz{
		background: #FCB9C2;
		border-radius: 4px;
		color: #fff;
		font-size: 12px;
		width: 30px;
		height: 18px;
		line-height: 18px;
		text-align: center;
		margin-left: 5px;
	}
	.headline-content .health-men .attention{
		float: right;
		width: 20%;
		padding: 20px 0px 0px 0px;
		margin-right: -10px;
	}
	.headline-content .health-men .attention span{
		padding: 3px;
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
	
	<!--遮罩层-->
	<div style="width: 100%;height: 100%;position:fixed;background: #000;opacity: 0.5;z-index: 1;display: none;" class="iframe"></div>
	<!--弹出框-->			
	<div id="downapp">
		<span>下次下载</span>	
		<!--背景-->
		<img class="bg" src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/downapp.png" /> 
		<!--立即下载--> 	
		<img  class="btn" src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/downapp-btn2x.png" />
		<p>立即下载</p>
	</div>	
	
	<!--头部-->	
	 <div class="top_header">
	    <div class="header_left return">
	        <a href="javascript:;" class="return" id="return" style="margin-top: 4px;"><img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/openshop/images/return.png"  height="18px"></a>
	    </div>
	    <div class="header_title" style="color: #000;font-size: 16px;font-weight: bold;line-height: 45px;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;width: 90%;left: 30px;">
			<?php echo $article_headline['title'];?>
	    </div>        
	</div>
	
	
	<div class="headline-content">
		<!--发布者-->
		<div class="health-men">
			<!--头像-->
			<div class="info">
				<img src="<?php if(empty($article_member['avatar'])){ echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/912865945439541.jpg'; }else{ echo download_pic($article_member['avatar'],60,60); }?>" />
				<p class="name" style="margin-top: 15px;">
					<span><?php if(!empty($article_member['nickname'])){ echo $article_member['nickname'];}else{ echo substr_cut($article_member['mobile']); } ?></span>
					<!--发布时间-->
					<span style="color: #999;font-size: 14px;margin-top: 5px;">发布于<?php echo date("Y-m-d",$article_headline['createtime']);?></span>
					<span class="lz" style="float: right;">楼主</span>
				</p>
			</div>
			<!--关注按钮-->
			<div class="attention">
				<span class="guanzhu put_comment">+关注</span>
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
					
			<!--评论-->
        	<div style="padding:3% 5% 0 5%;">
        		  <img style="width: 100%;" src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/comment@3x.png" />        		 
        	</div>
			<?php if(empty($article_comment)){ ?>
				<!--没有评论-->
				<div style="width: 100%;line-height: 100px;height: 100px;text-align: center;border-bottom: solid 1px #eee;">
					勾搭评论别害羞，聊骚要做第一人~
				</div>
			<?php }else{ ?>
				<!--有评论-->
				<?php foreach($article_comment as $row){  ?>
					<?php $member_comment = member_get($row['openid']); ?>
					<div class="health-men" style="margin-left: -10px;border-bottom: none;">
						<!--一条评论内容-->
						<div class="info"style="width: 100%;border-bottom: solid 1px #eee;border-top: none;">
							<!--头像-->
							<img src="<?php if(!empty($member_comment['avatar'])){ echo $member_comment['avatar'];}else{ echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/912865945439541.jpg'; } ?>" />
							<p class="name">
							<span style="color: #4c4c4c;font-size: 14px;">
								<?php if(!empty($member_comment['nickname'])){ echo $member_comment['nickname'];}else{ echo substr_cut($member_comment['mobile']); } ?>
							</span>
								<!--发布时间-->
								<span style="color: #999;font-size: 12px;margin-top: 5px;display: block;"><?php echo date("Y-m-d H:i",$article_comment['createtime']);?></p>
							</p>
							<!--评论内容-->
							<div style="clear: both;margin-left: 60px;"><?php echo $article_comment['comment'];?></div>
						</div>
					</div>
				<?php } ?>
			<?php } ?>

			
			<!--更多评论-->
			<div style="text-align: center;margin-top: 30px;clear: both;color: #999;padding: 15px 0px 10px 0px;" class='wap_more' >
				<a href="javascript:;" style="color: #999;font-size: 14px;">
					查看更多评论
				</a>
			</div>
			
		</div>		
	</div>
		<h3 style="height: 50px;"></h3>
		<!--底部栏 -->
		<div style="background: #F8F8F8;height: 50px;width: 100%;position: fixed;bottom: 49px;left: 0;" class="heal-foot">
			<input type="text" readOnly="true"  style="outline: none;background: #FFFFFF;border: 1px solid #DCDDE3;border-radius: 29px;height: 30px;margin: 10px;text-indent: 20px;width: 57%;" placeholder="写下评论……" value="" id="put_comment" class="put_comment"/>

			<ul style="float: right;list-style: none;">
				<li>
					<a class="wap_more" href="javascript:;">
						<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/health-comment@2x.png"  <?php if($notApp){ echo "class='wap_more'";}else{ echo "class='app_more'";}?> />
					</a>
				</li>
				
				<li>
					<a href="javascript:;" class="put_comment">
						<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/clle@2x.png" />
					</a>
				</li>
			</ul>
		</div>	
			
	<?php include themePage('footer'); ?>	
	
</body>
<script src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/js/appwakeup.js"></script>
<script>
	window.onload = function(){
		appWakeUp("<?php echo create_url('mobile', array('name'=>'shopwap','do'=>'appdown','op'=>'get_appversion'));?>",1);
	}
</script>
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
    })

	$(".wap_more").click(function(){
		var url = "<?php echo create_url('mobile', array('id' => $_GP['id'],'op'=>'comment_list','name'=>'addon8','do'=>'article','table'=>'headline')); ?>"
		window.location.href = url;
	})
	//点击立即下载，调用下载APP的方法
	$("#downapp p").on("click",function(){
		$("#downapp").hide();
		$(".iframe").hide();
		appDownLoad("<?php echo create_url('mobile', array('name'=>'shopwap','do'=>'appdown','op'=>'get_appversion'));?>");
	})	
</script>
</html>
