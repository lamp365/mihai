<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>晒物笔记</title>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="format-detection" content="telephone=no">	
	<link rel="shortcut icon" href="favicon.ico"/>
	<link href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjcommon.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/js/TouchSlide.1.1.js"></script>
</head>

<style type="text/css">
	.return{
		position: absolute;top: 10%;left: 3%;		
	}
	*{margin: 0;padding: 0;font-family: "微软雅黑";}
	body{
		background: #fff;
	}
	.headline-content{
		width: 90%;
		overflow: hidden;
		margin-left: 5%;			
		box-sizing: border-box;
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
	.headline-content .health-men .info .name .men{
		width: 70%;
		display: inline-block;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		
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
		padding: 3px;
		height: 30px;
		line-height: 30px;
		text-align: center;		
		border: 1px solid #F43776;
		border-radius: 4px;
		color: #F43776;	
		font-size: 12px;	
	}
	
	/*文章内容*/
	.headline-detail .title{
		color: #333;	
		margin: 15px 0;
		font-size: 16px;
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
	
	.headline-detail .info span{
		color: #999;
		font-size: 10px;
		
	}
	/*收藏人数*/
	.headline-detail .info{
		width: 100%;
		padding: 3% 0;
		box-sizing: border-box;
		border-top: solid 1px #eee;
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
	/*下载app的图片*/
	#downapp{
		position:fixed;
		top: 50%;
		left: 50%;
		margin: -160px 0 0 -160px;
		z-index: 2;		
		display: none;
		width: 320px;	
		height: 304px;								
	}
	#downapp .bg{
		position: absolute;
		width: 100%;
		box-shadow:0 0 10px #000;
	}
	#downapp .btn{
		width: 100px;
		position: absolute;
		bottom: 11%;
		right: 10%;
	}
	#downapp span{
		position: absolute;
		bottom: 15%;
		left: 15%;
		color: rgb(252,100,150);
		font-weight: bold;
		z-index: 3;
	}
	#downapp p{
		position: absolute;
		bottom: 15%;
		right: 15%;
		color: #fff;
		font-weight: bold;
	}
	html,body{
		height: 100%;
	}
	.bd{
		height: 30%;
	}
	.bd img{height: 100%;}
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
	
	<!--文章图片轮播-->
	<div class="slider card card-nomb" style="visibility: visible;position: relative;">			
		<div id="focus" class="focus">
			<div class="hd">
				<ul>
				</ul>
			</div>
			<div class="bd">
				<ul>
					<!--一个li是一张轮播的图片-->
					<?php $pic_list = explode(';',$article_note['pic']); ?>
					<?php foreach($pic_list as $pic){ ?>
					<li>							
						<img src="<?php echo $pic;?>" />
					</li>	
					<?php } ?>
				</ul>
			</div>
		</div>
		<!--返回的按钮-->	
		 <a href="javascript:;" class="return" id="return">
		 	<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/openshop/images/return.png"  height="18px">
		 </a>	
	</div>
	
	<div class="headline-content">
		<!--发布者-->
		<div class="health-men">
			<!--头像-->
			<div class="info">

					<img src="<?php if(empty($article_member['avatar'])){ echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/912865945439541.jpg'; }else{ echo download_pic($article_member['avatar'],60,60); }?>" />
					<p class="name" style="margin-top: 13px;">
						<span class="men"><?php if(!empty($article_member['nickname'])){ echo $article_member['nickname'];}else{ echo substr_cut($article_member['mobile']); } ?></span>
						<span class="lz">楼主</span>
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
			<p class="title"><?php echo $article_note['title'];?></p>
			
			<!--文章内容-->
			<p style="margin-bottom: 5px;">
				<?php echo $article_note['description'];?>
			</p>
			<!--定位-->
			<?php if(!empty($article_note['ddress'])){ ?>
			<div style="margin: 3% 0;">
				<img style="width: 15px;" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/location.png" />
				<span style="color: #999;font-size: 10px;"><?php echo $article_note['ddress'];?></span>
			</div>
			<?php } ?>
			<div class="info" style="position: relative;">
				<!--发布日期-->
				<span><?php echo date("Y-m-d",$article_note['createtime']);?></span>
				<!--收藏人数-->
				<span style="position: absolute;top:30%;right: 0;display: inline-block;"><?php echo $collect_num;?>人收藏</span>
			</div>	
			
			<!--评论-->
        	<div style="padding:0 5%;">
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


			<?php if(!empty($article_comment)){ ?>
			<!--更多评论-->
			<div style="text-align: center;margin-top: 15px;clear: both;color: #999;padding: 15px 0px 10px 0px;" class='wap_more' >
				<a href="javascript:;" style="color: #999;font-size: 14px;">
					查看更多评论
				</a>
			</div>
			<?php } ?>
		</div>	
	</div>
	<h3 style="height: 50px;"></h3>
		<!--底部栏 -->
		<div style="background: #F8F8F8;height: 50px;width: 100%;position: fixed;bottom: 49px;left: 0;" class="heal-foot">
			<input type="text" readOnly="true"  style="outline: none;background: #FFFFFF;border: 1px solid #DCDDE3;border-radius: 29px;height: 30px;margin: 10px;text-indent: 20px;width: 57%;" placeholder="写下评论……" value="" class="put_comment"/>

			<ul style="float: right;list-style: none;">
				<li>
					<a>
						<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/health-comment@2x.png"  class='wap_more' />
					</a>
				</li>
				
				<li>
					<a class="put_comment" href="javascript:;">
						<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/clle@2x.png" />
					</a>
				</li>
			</ul>
		</div>	
			
	<?php include themePage('footer'); ?>
</body>

<script type="text/javascript">
		TouchSlide({
			slideCell : "#focus",
			titCell : ".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
			mainCell : ".bd ul",
			delayTime : 600,
			interTime : 4000,
			effect : "leftLoop",
			autoPlay : true,//自动播放
			autoPage : true, //自动分页
			switchLoad : "_src" //切换加载，真实图片路径为"_src" 
		});
		
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
    
    
    $(".put_comment").focus(function(){
		//并且不让输入，不一定要用focus事件，反正wap不给评论，一评论就提示,引导下载
		$("#downapp").show();
		$(".iframe").show();
	})
	//点击下次下载，图片消失
	$("#downapp span").on("click",function(){
		$("#downapp").hide();
		$(".iframe").hide();
	})
	
	//点击立即下载，调用下载APP的方法
	$("#downapp p").on("click",function(){
		$("#downapp").hide();
		$(".iframe").hide();
		appDownLoad("<?php echo create_url('mobile', array('name'=>'shopwap','do'=>'appdown','op'=>'get_appversion'));?>");
	})

	$(".wap_more").click(function(){
		var url = "<?php echo create_url('mobile', array('id' => $_GP['id'],'op'=>'comment_list','name'=>'addon8','do'=>'article','table'=>'note')); ?>"
		window.location.href = url;
	})
</script>
</html>
