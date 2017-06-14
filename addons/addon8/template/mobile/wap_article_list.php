<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" type="text/css" href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/swiper-3.3.1.min.css"/>
	<link href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjcommon.css" rel="stylesheet"  type="text/css" />
	<link rel='stylesheet' type='text/css' href='<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
	<link rel="shortcut icon" href="favicon.ico"/>
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/js/TouchSlide.1.1.js"></script>
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/js/swiper.js"></script>	

</head>

<style type="text/css">
	html,body{
		background: #fff;
	}
	/*健康文化*/
	.healthy{
		width: 100%;
		overflow: hidden;
		background: #EDEDED;
		padding: 5%;
		box-sizing: border-box;
	}
	.healthy ul{
		width: 100%;
		overflow: hidden;
	}
	.healthy ul li{
		width: 100%;
		padding: 3%;
		box-sizing: border-box;
		border-radius: 5px;
		background: #fff;
		border: none;
		margin-bottom: 3%;
		position: relative;
	}

	.healthy ul li a img{
		width: 100%;
	}
	.healthy ul li p{
		color: #333;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		padding: 10px 5px 0 0px;
		font-size: 14px !important;
		font-weight: bold;
	}
	.healthy ul li .healthy-data{
		width: 48px;
		position: absolute;
		top: 0;
		right:0;
	}
	.healthy ul li span{
		position: absolute;
		top: 8px;
		right:12px;
		width: 30px;
		font-size: 8px;
		color: #fff;
		display: inline-block;
	}

	/*晒物笔记*/	
	div.wrap{
		width: 100%;								
		position: relative;
		background:rgb(244,244,244);
	}
	div.wrap div{				
		border: 1px solid #eee;		
		box-shadow: 0 0 3px 0 rgba(0,0,0,0.2);
		position: absolute;
		background: #fff;
		padding-bottom: 1%;
		box-sizing: border-box;
		width: 47%;
	}
	div.wrap div.newnote{
		visibility: hidden;
	}
	div.wrap div p{
		padding:3%;
		box-sizing: border-box;
		line-height: 18px;
	}
	div.wrap div p.title{
		color: #333;
		font-size: 14px;
		font-weight: bold;
	}
	div.wrap div p.detail{
		color: #7F7F7F;
		font-size: 12px;
		height: 74px;
		overflow: hidden;
	}	
	div.wrap div p.men img{
		width: 30px;
		height: 30px;
		border-radius: 50%;
	}
	div.wrap div p.men span{
		color: #7F7F7F;
		font-size: 10px;
		line-height: 30px;
		width: 75%;
		display: inline-block;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	div.wrap div h3 {
		line-height: 35px;
	}
	div.wrap div img {
		width: 100%;
	}

	/*wap的觅海头条二级页*/
	.mhnews-wap{
		width: 100%;
		display: none;
	}
	.mhnews-wap .mhheadline {
		background: #fff;
	}

	.header_title .art_active{
		color:#FF2D4B;
	}
	.header_title a{
		width: 30%;
		display: inline-block;
		font-family: "微软雅黑";
	}
	.mhheadline h3{
		background: #eee;height: 10px;
	}	
	.mhheadline ul li{
		padding:4%;
		width: 100%;
		overflow: hidden;
		box-sizing: border-box;
		border-bottom: solid 1px #eee;
	}
	.mhheadline ul li .content{
		width: 85%;
		float: right;
	}
	.mhheadline ul li .content .name{
		color: #333;
		font-size: 10px;
		width: 100%;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	.mhheadline ul li .content .title{
		color: #333;
		font-weight: bold;
		font-family: "微软雅黑";
		font-size: 14px;		
	}
	.mhheadline ul li .content .detail{
		color: #7F7F7F;
		font-size: 12px;
		line-height: 20px;
		height: 40px;
		overflow: hidden;
		margin-top: 5px;
	}
	/*头像*/	
	.mhheadline .men{
		width: 15%;
		overflow: hidden;
		float: left;		
	}
	.mhheadline .men img{
		width: 40px;
		height: 40px;
		border-radius: 50%;
	}	
	/*文章图片*/
	.mhheadline ul li .imglist{
		width: 100%;		
		overflow: hidden;
		padding:2% 0 0 15%;
		box-sizing: border-box;
	}
	.mhheadline ul li .imglist .imgone{
		width: 100%;
		border: solid 1px #f8f8f8;
		box-sizing: border-box;
	}
	.mhheadline ul li .imglist .imgtwo{
		width: 49%;	
		border: solid 1px #f8f8f8;
		box-sizing: border-box;		
	}
	.mhheadline ul li .imglist .imgthree{
		width: 32%;
		border: solid 1px #f8f8f8;
		box-sizing: border-box;
	}
	/*热门视频*/
	.hot-video {
		width: 100%;
		overflow: hidden;
		padding: 10px 0 10px 0;
	}
	.hot-video ul{
		width: 100%;

	}	 
	.hot-video ul li{
		width: 33%;
		overflow: hidden;
		float: left;
		list-style: none;
		text-align:center;
		margin-left:3%;
		padding: 0;
		
	}
	.hot-video ul li img{
		width: 100%;  
		height:auto;
	   	vertical-align: middle;
	   	border-radius: 4px;
	}
	.hot-video ul li video{
		width: 100%;  
		height:auto;
	   	vertical-align: middle;
	}
	.hot_title{
		padding: 10px 0 0 10px;		
		font-size: 16px;
		font-weight: bold;
		line-height: 32px;
	}
	.hot_title .more-video{
		float: right;
		margin-right: 10px;
		color:#b7b7b7;
		font-size: 14px;
		line-height: 26px;
		font-weight: 400;
	}
	.swiper-slide{
		position: relative;
	}
	.swiper-wrapper .paly-icon{
	    position: absolute;
	    top: 1px;
	    right: 3px;
	    width: 25px;
	    height: 25px;
	}
	.swiper-wrapper .video-num{
    position: absolute;
    left: 6px;
    bottom: 44px;
    background: rgba(0,0,0,.5);
    color: #fff;
    padding: 2px 7px;
    border-radius: 50px;
	}
	.swiper-wrapper .video-num img{
	    float: left;
	    width: 13px;
	    margin-top: 4px;
	}
	.swiper-wrapper .video-num span{
		float: left;
		font-size: 12px;
		    margin-left: 5px;
	}
	.video-title{
		overflow: hidden;
	    text-overflow: ellipsis;
	    display: -webkit-box;
	    -webkit-line-clamp: 2;
	    -webkit-box-orient: vertical;
	    height: 40px;
	}
	.hot_title_small_title{
    padding-left: 4px;
    border-left: 2px solid #ff2741;
    line-height: 23px;
    height: 20px;
    display: inline-block;
	}
	.new-article-list{
		width: 92%;
		margin-left: 4%;
	}
	.new-article-list .list-left{
		width: 35%;
		float: right;
	}
	.new-article-list .list-left img{
		display: block;
		width: 100%;
		max-width: 100%;
		border-radius: 4px;
	}
	.new-article-list .list-right{
		width: 65%;
		float: left;
		padding-right: 2%;
    	box-sizing: border-box;
	}
	.new-article-list .article-name{
		position: absolute;
		bottom: 15px;
		left: 0;
		width: 36%;
		overflow: hidden;
		white-space: nowrap;
	    text-overflow: ellipsis;
	}
	.new-article-list .article-time{
		position: absolute;
		bottom: 15px;
		left: 37%;
		width: 25%;
		overflow: hidden;
		white-space: nowrap;
	    text-overflow: ellipsis;
	    text-align: right;
	}
	.article-title{
	    overflow: hidden;
	    text-overflow: ellipsis;
	    display: -webkit-box;
	    -webkit-line-clamp: 2;
	    -webkit-box-orient: vertical;
	    height: 50px;
	    font-weight: bold;
	    color: #333;
	    font-size: 16px;
	    font-family: "微软雅黑";
	}
	.article-detail{
		overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    height: 43px;
    margin-top: 5px;
	}
	.mhheadline .new-article-list li{
		position: relative;
		padding: 15px 0;
		border-bottom: none;
	}
	.head-banner img{
		width: 100%;
		max-height: 150px;
	}
	/*移动端1px处理*/
@media only screen and (-webkit-min-device-pixel-ratio:2),only screen and (min-device-pixel-ratio:2) {
 .good-content {
	border: none;
	background-image: -webkit-linear-gradient(90deg,#eee,#eee 50%,transparent 50%);
	background-image: -moz-linear-gradient(90deg,#eee,#eee 50%,transparent 50%);
	background-image: -o-linear-gradient(90deg,#eee,#eee 50%,transparent 50%);
	background-image: linear-gradient(0,#eee,#eee 50%,transparent 50%);
	background-size: 100% 1px;
	background-repeat: no-repeat;
	background-position: bottom
	}
}
.article-nav-list,.article-nav-more-list{
	border-bottom: 1px solid #f2f2f2;
}
.article-nav-more-list{
	position: absolute;
	top: 47px;
	left: 0;
	display: none;
	width: 100%;
    background: #fff;
    z-index: 2;
}
.article-nav-list li,.article-nav-more-list li{
	float: left;
	width: 16.6%;
	text-align: center;
	overflow: hidden;
	white-space: nowrap;
    text-overflow: ellipsis;
}
.WX_search_txt, .hd_search_txt {
    background-image: none!important;
    border: 1px solid #ddd;
    float: none;
    width: 98%;
    margin:5px auto;
}
input::-webkit-input-placeholder{
	color: #d0d0d0!important;
}
.search-down{
    position: absolute;
    top: 7px;
    left: 4%;
    font-size: 16px;
}
.search-down-list{
	display: none;
    position: absolute;
    top: 30px;
    left: 0%;
    font-size: 14px;
    z-index: 9;
    color: #fff;
    background: rgba(0,0,0,.5);
    border-radius: 3px;
}
.search-down-list div a{
	color: #fff;
}
.search-down-list div{
	padding: 5px 7px;
	border-bottom: 1px solid #b9b9b9;
}
.search-down-list div:last-child{
	border-bottom: none;
}
.search-img{
	position: absolute;
    right: 2%;
    top: 0;
    text-align: center;
    width: 30px;
    height: 30px;
}
.search-img img{
    width: 18px;
    margin-top: 5px;
}
.article-nav-list a,.article-nav-more-list li a{
	display: block;
    text-align: center;
    font-size: 14px;
    line-height: 14px;
    color: #696969;
    padding: 15px 0;
}
.article-nav-list a:visited,.article-nav-more-list li a:visited{
	color: #696969;
}
.article-nav-list .a-cur a{
    color: #00a06a;
    font-weight: bold;
}
.article-nav-list .a-cur{
	border-bottom: 2px solid #0a6;
}
    
</style>

<body>
<div style="position:relative;background: white;">
	<i class="icon-angle-down search-down"></i>
	<div class="search-down-list">
		<div><a href="#">搜索文章</a></div>
		<div><a href="#">搜索公众号</a></div>
	</div>
	<input name="keyword" id="search_word" class="WX_search_txt hd_search_txt_null" placeholder="搜索文章和公众号！" type="search" AUTOCOMPLETE="off" />
	<div class="search-img" onclick="searchFun()"><img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/icon_ss.png"></div>
</div>
<div style="height: 100%;background: white;position: relative;">
	<div id="focus" class="focus head-banner">
		<div class="hd">
			<ul>
			</ul>
		</div>
		<div class="bd">
			<ul>
				<?php if(empty($banner)){ ?>
				<li>
				<img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201702/20170214130958a29102a4b73.jpg"/>
				<?php }else{ $query = parse_url($banner['link']); $parse_arr = convertUrlQuery($query['query']); ?>
				<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$parse_arr['headline_id']));?>"><img src="<?php echo download_pic($banner['thumb'],430,150,2);?>"/></a>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
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
				
				
			</script>
	<div class="top_header" style="border-bottom: none;background: #fff;display: none;">
		<div class="header_title" style="font-size: 16px;line-height: 45px;">
			<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'healty'))?>"  <?php if($_GP['op']=='healty' || empty($_GP['op'])){ echo "class='art_active'"; } ?> >健康文化</a>
			<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline'))?>"  <?php if($_GP['op']=='headline'){ echo "class='art_active'"; } ?>>觅海头条</a>
			<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'note'))?>"  <?php if($_GP['op']=='note'){ echo "class='art_active'"; } ?>>晒物笔记</a>
		</div>
	</div>
	<div style="position:relative">
		<ul class="article-nav-list clearfix">
			<li id="wap_0" class="a-cur"><a href="javascript:;" >热门</a></li>
			<li id="wap_1"><a href="javascript:;" >nav1</a></li>
			<li id="wap_2"><a href="javascript:;" >nav2</a></li>
			<li id="wap_3"><a href="javascript:;" >nav3</a></li>
			<li id="wap_4"><a href="javascript:;" >nav4</a></li>
			<li class="more-btn"><a href="javascript:;" ><span>更多</span><i class="icon-angle-down"></i></a></li>
		</ul>
		<ul class="article-nav-more-list clearfix">
			<li id="wap_5"><a href="javascript:;" >nav5</a></li>
			<li id="wap_6"><a href="javascript:;" >nav6</a></li>
			<li id="wap_7"><a href="javascript:;" >nav7</a></li>
			<li id="wap_8"><a href="javascript:;" >nav8</a></li>
			<li id="wap_9"><a href="javascript:;" >nav9</a></li>
			<li id="wap_10"><a href="javascript:;" >nav10</a></li>
		</ul>
	</div>

	<div class="mhnews-wap wap_0" style="display:block">
第一屏
		   <?php if(!empty($video_article)){  ?>
			<p class="hot_title">
				<span class="hot_title_small_title">热门视频</span>
				<a href="<?php echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline_view'));?>" class='more-video'>更多视频 ></a>
			</p>
			<div class="swiper-container hot-video">				
				<ul class="swiper-wrapper">
					<?php foreach($video_article as $video){ ?>
					<li class="swiper-slide">	
						<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$video['headline_id']));?>">
							<img src="<?php echo download_pic($video['video_img'],250,190,2);?>" />
							<img class="paly-icon" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/video-play.png">
							<div class="video-title"><?php echo $video['title'];?></div>
						</a>		
					</li>
					<?php } ?>
				</ul>				
			</div>	
			<h3 style="background: #eee;height: 10px;"></h3>
		   <?php } ?>
			<div class="mhheadline">				
				<p class="hot_title">
					<!-- <img style="width: 20px;margin-top: 4px;" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/hot-new.png"/>
					觅海头条 -->
					<span class="hot_title_small_title">觅海头条</span>
				</p>
				<!--新头条样式-->
				<ul class="new-article-list">
					<?php foreach($article_list as $row){  $picarr = explode(';',$row['pic']); ?>
					<li class="good-content">
						
							<div class="list-left">
								<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>"><img src="<?php echo download_pic($picarr[0],250,190,2);?>"></a>
							</div>
							<div class="list-right">
								<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>"><div class="article-title"><?php echo $row['title'] ;?></div></a>
								<div class="article-name">公众号名称，文章作者</div>
								<div class="article-time">1小时前<i class="icon-angle-down" style="margin-left:5px;"></i></div>
							</div>
						
					</li>
					<?php } ?>
				</ul>
				
			</div>
	</div>
	<div class="mhnews-wap wap_1">
第2屏
		   <?php if(!empty($video_article)){  ?>
			<p class="hot_title">
				<span class="hot_title_small_title">热门视频</span>
				<a href="<?php echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline_view'));?>" class='more-video'>更多视频 ></a>
			</p>
			<div class="swiper-container hot-video">				
				<ul class="swiper-wrapper">
					<?php foreach($video_article as $video){ ?>
					<li class="swiper-slide">	
						<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$video['headline_id']));?>">
							<img src="<?php echo download_pic($video['video_img'],250,190,2);?>" />
							<img class="paly-icon" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/video-play.png">
							<div class="video-title"><?php echo $video['title'];?></div>
						</a>		
					</li>
					<?php } ?>
				</ul>				
			</div>	
			<h3 style="background: #eee;height: 10px;"></h3>
		   <?php } ?>
			<div class="mhheadline">				
				<p class="hot_title">
					<!-- <img style="width: 20px;margin-top: 4px;" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/hot-new.png"/>
					觅海头条 -->
					<span class="hot_title_small_title">觅海头条</span>
				</p>
				<!--新头条样式-->
				<ul class="new-article-list">
					<?php foreach($article_list as $row){  $picarr = explode(';',$row['pic']); ?>
					<li class="good-content">
						
							<div class="list-left">
								<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>"><img src="<?php echo download_pic($picarr[0],250,190,2);?>"></a>
							</div>
							<div class="list-right">
								<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>"><div class="article-title"><?php echo $row['title'] ;?></div></a>
								<div class="article-name">公众号名称，文章作者</div>
								<div class="article-time">1小时前<i class="icon-angle-down" style="margin-left:5px;"></i></div>
							</div>
						
					</li>
					<?php } ?>
				</ul>
				
			</div>
	</div>
	<div class="mhnews-wap wap_2">
第3屏
		   <?php if(!empty($video_article)){  ?>
			<p class="hot_title">
				<span class="hot_title_small_title">热门视频</span>
				<a href="<?php echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline_view'));?>" class='more-video'>更多视频 ></a>
			</p>
			<div class="swiper-container hot-video">				
				<ul class="swiper-wrapper">
					<?php foreach($video_article as $video){ ?>
					<li class="swiper-slide">	
						<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$video['headline_id']));?>">
							<img src="<?php echo download_pic($video['video_img'],250,190,2);?>" />
							<img class="paly-icon" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/video-play.png">
							<div class="video-title"><?php echo $video['title'];?></div>
						</a>		
					</li>
					<?php } ?>
				</ul>				
			</div>	
			<h3 style="background: #eee;height: 10px;"></h3>
		   <?php } ?>
			<div class="mhheadline">				
				<p class="hot_title">
					<!-- <img style="width: 20px;margin-top: 4px;" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/hot-new.png"/>
					觅海头条 -->
					<span class="hot_title_small_title">觅海头条</span>
				</p>
				<!--新头条样式-->
				<ul class="new-article-list">
					<?php foreach($article_list as $row){  $picarr = explode(';',$row['pic']); ?>
					<li class="good-content">
						
							<div class="list-left">
								<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>"><img src="<?php echo download_pic($picarr[0],250,190,2);?>"></a>
							</div>
							<div class="list-right">
								<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>"><div class="article-title"><?php echo $row['title'] ;?></div></a>
								<div class="article-name">公众号名称，文章作者</div>
								<div class="article-time">1小时前<i class="icon-angle-down" style="margin-left:5px;"></i></div>
							</div>
						
					</li>
					<?php } ?>
				</ul>
				
			</div>
	</div>
	<div class="mhnews-wap wap_3">
第4屏
		   <?php if(!empty($video_article)){  ?>
			<p class="hot_title">
				<span class="hot_title_small_title">热门视频</span>
				<a href="<?php echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline_view'));?>" class='more-video'>更多视频 ></a>
			</p>
			<div class="swiper-container hot-video">				
				<ul class="swiper-wrapper">
					<?php foreach($video_article as $video){ ?>
					<li class="swiper-slide">	
						<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$video['headline_id']));?>">
							<img src="<?php echo download_pic($video['video_img'],250,190,2);?>" />
							<img class="paly-icon" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/video-play.png">
							<div class="video-title"><?php echo $video['title'];?></div>
						</a>		
					</li>
					<?php } ?>
				</ul>				
			</div>	
			<h3 style="background: #eee;height: 10px;"></h3>
		   <?php } ?>
			<div class="mhheadline">				
				<p class="hot_title">
					<!-- <img style="width: 20px;margin-top: 4px;" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/hot-new.png"/>
					觅海头条 -->
					<span class="hot_title_small_title">觅海头条</span>
				</p>
				<!--新头条样式-->
				<ul class="new-article-list">
					<?php foreach($article_list as $row){  $picarr = explode(';',$row['pic']); ?>
					<li class="good-content">
						
							<div class="list-left">
								<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>"><img src="<?php echo download_pic($picarr[0],250,190,2);?>"></a>
							</div>
							<div class="list-right">
								<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>"><div class="article-title"><?php echo $row['title'] ;?></div></a>
								<div class="article-name">公众号名称，文章作者</div>
								<div class="article-time">1小时前<i class="icon-angle-down" style="margin-left:5px;"></i></div>
							</div>
						
					</li>
					<?php } ?>
				</ul>
				
			</div>
	</div>
	<div class="mhnews-wap wap_4">
第5屏
		   <?php if(!empty($video_article)){  ?>
			<p class="hot_title">
				<span class="hot_title_small_title">热门视频</span>
				<a href="<?php echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline_view'));?>" class='more-video'>更多视频 ></a>
			</p>
			<div class="swiper-container hot-video">				
				<ul class="swiper-wrapper">
					<?php foreach($video_article as $video){ ?>
					<li class="swiper-slide">	
						<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$video['headline_id']));?>">
							<img src="<?php echo download_pic($video['video_img'],250,190,2);?>" />
							<img class="paly-icon" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/video-play.png">
							<div class="video-title"><?php echo $video['title'];?></div>
						</a>		
					</li>
					<?php } ?>
				</ul>				
			</div>	
			<h3 style="background: #eee;height: 10px;"></h3>
		   <?php } ?>
			<div class="mhheadline">				
				<p class="hot_title">
					<!-- <img style="width: 20px;margin-top: 4px;" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/hot-new.png"/>
					觅海头条 -->
					<span class="hot_title_small_title">觅海头条</span>
				</p>
				<!--新头条样式-->
				<ul class="new-article-list">
					<?php foreach($article_list as $row){  $picarr = explode(';',$row['pic']); ?>
					<li class="good-content">
						
							<div class="list-left">
								<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>"><img src="<?php echo download_pic($picarr[0],250,190,2);?>"></a>
							</div>
							<div class="list-right">
								<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>"><div class="article-title"><?php echo $row['title'] ;?></div></a>
								<div class="article-name">公众号名称，文章作者</div>
								<div class="article-time">1小时前<i class="icon-angle-down" style="margin-left:5px;"></i></div>
							</div>
						
					</li>
					<?php } ?>
				</ul>
				
			</div>
	</div>
	<div class="mhnews-wap wap_5">
第6屏
		   <?php if(!empty($video_article)){  ?>
			<p class="hot_title">
				<span class="hot_title_small_title">热门视频</span>
				<a href="<?php echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline_view'));?>" class='more-video'>更多视频 ></a>
			</p>
			<div class="swiper-container hot-video">				
				<ul class="swiper-wrapper">
					<?php foreach($video_article as $video){ ?>
					<li class="swiper-slide">	
						<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$video['headline_id']));?>">
							<img src="<?php echo download_pic($video['video_img'],250,190,2);?>" />
							<img class="paly-icon" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/video-play.png">
							<div class="video-title"><?php echo $video['title'];?></div>
						</a>		
					</li>
					<?php } ?>
				</ul>				
			</div>	
			<h3 style="background: #eee;height: 10px;"></h3>
		   <?php } ?>
			<div class="mhheadline">				
				<p class="hot_title">
					<!-- <img style="width: 20px;margin-top: 4px;" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/hot-new.png"/>
					觅海头条 -->
					<span class="hot_title_small_title">觅海头条</span>
				</p>
				<!--新头条样式-->
				<ul class="new-article-list">
					<?php foreach($article_list as $row){  $picarr = explode(';',$row['pic']); ?>
					<li class="good-content">
						
							<div class="list-left">
								<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>"><img src="<?php echo download_pic($picarr[0],250,190,2);?>"></a>
							</div>
							<div class="list-right">
								<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>"><div class="article-title"><?php echo $row['title'] ;?></div></a>
								<div class="article-name">公众号名称，文章作者</div>
								<div class="article-time">1小时前<i class="icon-angle-down" style="margin-left:5px;"></i></div>
							</div>
						
					</li>
					<?php } ?>
				</ul>
				
			</div>
	</div>
</div>
<!--用来存当前page-->
<input type="hidden" value="2" id="page"/>
<div class="ajax_next_page">
	<img class="jiazai" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/ajax-loader.gif"/>
	正在加载
</div>
<?php include themePage('footer');?>

</body>
<script>
	var index = 1; //默认开关状态是打开

	$(function(){
		//滚动条到底部时就加载剩下数据
		$(window).scroll(function(){			
			if ($(document).scrollTop() >= $(document).height() - $(window).height()) {
				Refresh();
			}
		});
		$(".search-down").on("click",function(){
			$(".search-down-list").stop().slideToggle();
		});
		tabChoose();
		moreBtn();
		moreList();
	})
	//搜索的事件
	function searchFun(){

	}
	function tabChoose(){
		//tab切换
		var tab_index = 0;
		$("body").on("click",".article-nav-list li",function(){
			tab_index = $(this).index();
			var tab_id = $(this).attr("id");
			if( tab_index <= 4 ){
				$(".article-nav-list li").removeClass("a-cur");
				$(this).addClass("a-cur");
				$(".mhnews-wap").hide();
				$("."+tab_id).show();
			}
		});
	}
	function moreBtn(){
		//更多、收起按钮
		$(".more-btn").on("click",function(){
			if( !$(this).hasClass("open-more") ){
				$(this).find("span").text("收起");
				$(this).find("i").attr("class","icon-angle-up");
				$(this).addClass("open-more");
				$(".article-nav-more-list").show();
			}else{
				$(this).find("span").text("更多");
				$(this).find("i").attr("class","icon-angle-down");
				$(this).removeClass("open-more");
				$(".article-nav-more-list").hide();
			}
		});
	}
	function moreList(){
		//更多菜单的点击事件
		$("body").on("click",'.article-nav-more-list li',function(){
			$(".article-nav-list li").removeClass("a-cur");
			var nav_4_html = $(".article-nav-list li").eq(4).clone();
			var this_html = $(this).clone().addClass("a-cur");
			$(".article-nav-list li").eq(4).replaceWith(this_html);
			$(this).replaceWith(nav_4_html);
			var tab_id = $(this).attr("id");
			$(".mhnews-wap").hide();
			$("."+tab_id).show();
			$(".more-btn").trigger("click");
		})
	}
	function Refresh(){
		if( index == 1){
			$(".ajax_next_page").show();
			index = 0; //关闭开关
			var page = $("#page").val(); //第一次传的是2
			$.post("", {'page' : page,'nextpage' : 'ajax','op':'headline'}, function(s){
				if(s.errno != 200){
					//如果没有数据
					$(".ajax_next_page").hide();
				}else{
					$("#page").val(++page);
					var art_data = s.message;
					for(var i = 0;i < art_data.length;i++){
						//循环拼接 html下一页数据
						Load(art_data[i]);
					}
					index = 1;  //加载完后重新打开开关
					$(".ajax_next_page").hide();
				}
			}, 'json');

		}
	}

	//获取URL参数
	function request(paras,url)
	{
		url = decodeURI(url);
		var paraString = url.substring(url.indexOf("?")+1,url.length).split("&");
		var paraObj = {}
		for (i=0; j=paraString[i]; i++){
			paraObj[j.substring(0,j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=")+1,j.length);
		}
		var returnValue = paraObj[paras.toLowerCase()];
		if(typeof(returnValue)=="undefined"){
			return "";
		}else{
			return returnValue;
		}
	}

function Load(art_data){	
		//觅海头条的append
		var url = "<?php echo mobile_url('article',array('op'=>'headline'));?>";
		url = url + "&id="+art_data.headline_id;
		var piclist = art_data.pic;
		var perpic = piclist.split(";"); //字符串截取，成为数组
		var picurl = perpic[0];
		var li ='<li class="good-content">'+
					'<a href="'+url+'">'+
						'<div class="list-left">'+
							'<img src="'+ picurl +'"/>'+
						'</div>'+
						'<div class="list-right">'+
							'<div class="article-title">'+art_data.title+'</div>'+
							'<div class="article-name">公众号名称，文章作者</div>'+
							'<div class="article-time">1小时前<i class="icon-angle-down" style="margin-left:5px;"></i></div>'+
						'</div>'+
					'</a>'+
				'</li>';
		$(".new-article-list").append(li);
	}
	//将后台返回的时间戳格式化为时间格式
	function Stringtotime(time){ 
		time = time *1000; 
	    var datetime = new Date();			    
	    datetime.setTime(time);			      
	    var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1; 			   
	    var date = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate(); 	      
	    return month + "/" + date;  
	}  
</script>

</html>
