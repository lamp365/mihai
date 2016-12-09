<?php defined('SYSTEM_IN') or exit('Access Denied');?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>健康文化</title>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
	<link rel='stylesheet' type='text/css'href='<?php echo WEBSITE_ROOT . 'themes/' . 'wap/'. '/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap/'. '/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>	
<style type="text/css">
	*{
		margin: 0;
		padding: 0;
	}
	.health-content{
		width: 100%;
		height: 100%;
	}
	.health-content .health-men{
		width: 100%;
	}
	.health-content .health-men .info{
		float: left;
		width: 75%;
		padding: 10px 0px 10px 10px;
	}
	.health-content .health-men .info img{
		width: 60px;
		height: 60px;
		border-radius: 50%;
		display: inline-block;
		float: left;
		
	}
	.health-content .health-men .info .name{
		display: inline-block;
		float: left;
		overflow: hidden;
		padding: 10px 0 0 10px ;
		
	}
	.health-content .health-men .info .name span{
		display: block;
	}
	.health-content .health-men .attention{
		float: right;
		width: 20%;
		padding: 20px 0px 0px 0px;
		margin-right: -10px;
	}
	.health-content .health-men .attention span{
		padding: 5px;
		height: 30px;
		line-height: 30px;
		text-align: center;		
		border: 1px solid #F43776;
		border-radius: 6px;
		color: #F43776;	
		font-size: 12px;	
	}
	.health-detail{
		width: 90%;
		padding: 10px 5%;
	}
	.item{
		background: #f7f7f7 none repeat scroll 0 0;
	    clear: none !important;
	    display: block;
	    margin: 10px 0;
	    padding: 10px;
	    position: relative;
	    text-decoration: none;
	   
	}
	.item .item-pic{
		float: left;
	    height: 70px;
	    margin-right: 20px;
	    overflow: hidden;
	    width: 70px;
	    
	}
	.item .item-pic img{
		width: 70px;
		height: 70px;
		border: none;
	}
	.item .item-info{
		height: 70px;
	    overflow: hidden;
	    position: relative;
	}
	.item .item-info .item-title{
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		width: 100%;
		display: inline-block;
	}
	.item .item-info .item-btn{
		border: 1px solid #da0d15;
	    border-radius: 4px;
	    bottom: 0px;
	    color: #da0d15;
	    height: 20px;
	    line-height: 20px;
	    position: absolute;
	    right: 0;
	    text-align: center;
	    padding: 0 3px;
	    font-size: 12px;
	}
	.item .item-info .item-price{
		bottom: 5px;	    
	    left: 0;
	    position: absolute;
	    
	}
	.item .item-info .item-price .price-new{
		font-size: 16px;
		color: #f50;
		text-decoration: none;
	}
	.item .item-info .item-price .price-old{
		color: #B2B2B2;
		font-size: 10px;
	}
	.item .item-info .item-price .price-new strong{
		font-size: 16px;
	}
	.heal-foot ul{
		overflow: hidden;
		margin-top: 15px;
		width: 35%;
	}
	.heal-foot ul li{
		float: left;
		padding: 0px 0px 0px 15px;
	}
	.heal-foot ul li a img{
		width: 20px;
	}
	.content p{
		line-height: 32px;
	}
	.content img{width: 100%;height: 100%;}
	
	/*下载app的图片*/
	#downapp{
		position:fixed;
		z-index: 100;
		margin-top: -160px;
		display: none;
		width: 100%;
		height: 300px;
		background: url(<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/downapp.png) center center no-repeat;
		background-size: 100% 300px;
	}
	#downapp img{
		width: 100px;
		position: absolute;
		bottom: 10%;
		right: 10%;
	}
	#downapp span{
		position: absolute;
		bottom: 14%;
		left: 15%;
		color: rgb(252,100,150);
		font-weight: bold;
	}
	#downapp p{
		position: absolute;
		bottom: 14%;
		right: 15%;
		color: #fff;
		font-weight: bold;
	}
</style>
</head>
<body>
	<div id="downapp">
		<span>下次下载</span>		
		<img  src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/downapp-btn2x.png" />
		<p>立即下载</p>
	</div>		
	
	<!--头部-->
	<?php if($notApp){ ?>
	 <div class="top_header" style="border-bottom: none;">
	    <div class="header_left return">
	        <a href="javascript:;" class="return" style="margin-top: 4px;"><img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/openshop/images/return.png"  height="18px"></a>
	    </div>
	    <div class="header_title" style="color: #000;font-size: 16px;font-weight: bold;line-height: 45px;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;width: 90%;left: 30px;">
			<?php echo $article['title'];?>
	    </div>        
	</div>

	<!--内容-->
	<div class="health-content">
		<!--发布者-->
		<div class="health-men">
			<!--头像-->
			<div class="info">
				<?php if(!empty($article['openid'])){ $author = member_get($article['openid']);    ?>
					<img src="<?php if(empty($author['avatar'])){ echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/912865945439541.jpg'; }else{ echo download_pic($author['avatar'],60,60); }?>" />
					<p class="name">
						<span><?php echo $author['realname'];?></span>
						<!--发布时间-->
						<span style="color: #999;font-size: 14px;margin-top: 5px;">发布于<?php echo date("Y-m-d H:i",$author['createtime']);?></span>
					</p>
				<?php }else{  ?>
					<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/912865945439541.jpg" />
					<p class="name">
						<span>觅海小妹</span>
						<!--发布时间-->
						<span style="color: #999;font-size: 14px;margin-top: 5px;">发布于2016-12-2</span>
					</p>
				<?php } ?>
			</div>
			<!--关注按钮-->
			<div class="attention">
				<span class="guanzhu <?php if($notApp){ echo " wap_guanzhu";}else{ echo " app_guanzhu";}?>">+关注</span>
			</div>
		</div>
		<?php }  ?>
		<!--文章大图-->
		<?php if(!empty($article['thumb'])){ ?>
		<img style="width: 100%;display: block;" src="<?php echo $article['thumb'];?>" />
		<?php } ?>
		<!--文章内容详情开始-->
		<div class="health-detail">
			<!--文章标题-->
			<p style="color: #000;font-weight: bold;margin: 10px 0 20px 0;">
				<?php echo $article['title'];?>
			</p>
			<!--文章内容-->
			<div class="content">
				<?php echo $article['content'];?>
			</div>

        	<!--评论-->
        	<div style="padding:5% 5% 0 5%;">
        		  <img style="width: 90%;" src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/comment@3x.png" />
        	</div>
			<?php foreach($article_comment as $comment){   $member_comment = member_get($comment['openid']); ?>
        	<div class="health-men" style="margin-left: -10px;">
				<!--头像-->
				<div class="info"style="width: 100%;border-bottom: solid 1px #eee;">
					<img src="<?php if(!empty($member_comment['avatar'])){ echo $member_comment['avatar'];}else{ echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/912865945439541.jpg'; } ?>" />
					<p class="name">
						<span><?php echo $member_comment['realname'];?></span>
						<!--发布时间-->
						<span style="color: #999;font-size: 14px;margin-top: 5px;">发布于<?php echo $comment['createtime'];?></p>
					</p>
					<!--评论内容-->
					<div style="clear: both;margin-left: 70px;"><?php echo $comment['comment'];?></div>
				</div>						
			</div>
			<?php } ?>
			
			<!--更多评论-->
			<div style="text-align: center;margin-top: 30px;clear: both;color: #999;padding: 15px 0px 10px 0px;" <?php if($notApp){ echo "class='wap_more'";}else{ echo "class='app_more'";}?> >
				<a href="javascript:;" style="color: #999;">
					查看更多评论
				</a>
			</div>

		</div>		
		<div style="height: 50px;"></div>
		<?php if($notApp){ ?>
		<!--底部栏 -->
		<div style="background: #F8F8F8;height: 50px;width: 100%;position: fixed;bottom: 53px;left: 0;" class="heal-foot">
			<input type="text" readOnly="true"  style="outline: none;background: #FFFFFF;border: 1px solid #DCDDE3;border-radius: 29px;height: 30px;margin: 10px;text-indent: 20px;width: 57%;" placeholder="写下评论……" value="" id="put_comment"/>

			<ul style="float: right;list-style: none;">
				<li>
					<a>
						<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/health-comment@2x.png"  <?php if($notApp){ echo "class='wap_more'";}else{ echo "class='app_more'";}?> />
					</a>
				</li>
				
				<li>
					<a>
						<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/clle@2x.png" />
					</a>
				</li>
			</ul>
		</div>
		<?php } ?>
</div>

<?php if($notApp){ ?>
<script src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/js/appwakeup.js"></script>
<script>
	window.onload = function(){
		appWakeUp("<?php echo create_url('mobile', array('name'=>'addon8','do'=>'article','op'=>'get_appversion'));?>",1);
	}
</script>
<?php } ?>

<script>
	$(".return").click(function(){
        //没有上一页就返回首页
        if(document.referrer.length == 0){
			  window.location.href = "index.php";
		}else{				
			 var newHref = document.referrer;
			 $(".return").attr("href",newHref);							
		}
		
    })
	$(".wap_more").click(function(){
		var url = "<?php echo create_url('mobile', array('id' => $id,'op'=>'comment_list','name'=>'addon8','do'=>'article')); ?>"
		window.location.href = url;
	})
	$(".app_more").click(function(){
		//需要正源写，跳到app的评论页显示更多
	})
	<?php if($notApp){ ?>
		$(".content .item").click(function(){
			var url = $(this).data('url');
			window.location.href = url;
		})
	<?php }else{ ?>
		$(".content .item").click(function(){
			//需要正源写，跳到app的详情页
			var id = $(this).data('id');
		})
	<?php } ?>
	$("#put_comment").focus(function(){
		//并且不让输入，不一定要用focus事件，反正wap不给评论，一评论就提示,引导下载
		$("#downapp").show();
	})
</script>
<script>	
	var Top = $(window).height() * 0.5;			
	$("#downapp").css("top",Top);
	
	//点击下次下载，图片消失
	$("#downapp span").on("click",function(){
		$("#downapp").hide();
	})
	
	//点击立即下载，调用下载APP的方法
	$("#downapp p").on("click",function(){
		$("#downapp").hide();
		appDownLoad("<?php echo create_url('mobile', array('name'=>'addon8','do'=>'article','op'=>'get_appversion'));?>");
	})
	
	//关注的点击事件，wap
	$(".wap_guanzhu").on("click",function(){
		
	})
	//关注的点击事件，app
	$(".app_guanzhu").on("click",function(){
		
	})
</script>	
<?php if($notApp){ ?>
<?php include themePage('footer'); ?>
<?php } ?>
