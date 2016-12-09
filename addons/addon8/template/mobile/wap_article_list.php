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
	<link href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjcommon.css" rel="stylesheet"	type="text/css" />
	<link rel='stylesheet' type='text/css'href='<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
	<link rel="shortcut icon" href="favicon.ico"/>
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>
</head>

<style type="text/css">

/*wap的觅海头条二级页*/
.mhnews-wap{
	width: 100%;overflow: hidden;
}
.mhnews-wap .mhheadline {
	background: #eee;
}
.mhnews-wap .mhheadline ul{
	overflow: hidden;
}
.mhnews-wap .mhheadline ul li{
	width: 100%;border-top: solid 1px gainsboro;
	overflow: hidden;padding:3%;
	background: #fff;	
	box-sizing: border-box;
}
.mhnews-wap .mhheadline ul li a{
	float: left;
	display: inline-block;
	overflow: hidden;
	/*height: 80%;*/
	width: 100%;
	margin-bottom: 3px;	
}
.mhnews-wap .mhheadline ul li a p{		
	font-size: 14px;
	color: #333333;
	width: 100%;
	height: 40px;
	overflow: hidden;
}
.mhnews-wap .mhheadline ul li span{
	width:47%;
	float: left;
	display: inline-block;
}

.mhnews-wap .mhheadline .hottag{
	width: 28px;
	display: inline-block;
	height: 16px;
	line-height: 16px;
	font-size: 8px;
	text-align: center;
	border: 1px solid #DC0200;
	border-radius: 4px;
	color: #DC0200;
	margin-left: 0;
	margin-right: 5px;
	box-sizing: border-box;
}

.tip .jiazai{
	margin: 0 auto;
	display: block;
}
.header_title .art_active{
	color:#FF2D4B;
}
.header_title a{
	width: 30%;
	display: inline-block;
	font-family: "微软雅黑";
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
}
.healthy ul li .healthy-data{
	width: 45px;	
	position: absolute;
	top: 0;
	right:0;
}
.healthy ul li span{
	position: absolute;
	top: 6px;
	right:10px;
	width: 30px;
	font-size: 8px;
	color: #fff;
	display: inline-block;
}

/*晒物笔记*/
.notelist{
	background: rgb(243,243,243);
	width: 100%;
	overflow: hidden;
	padding: 3% 2% 0% 0%;
}

.column{
	width: 47%;
	overflow: hidden;				
	float: left;
	margin-left: 2%;		
	
}
.column .pernote{
	margin-bottom: 3.5%;
	border: solid 1px gainsboro;
	background: #fff;
	background: #FFFFFF;
	
	box-shadow: 0 0 5px 0 rgba(0,0,0,0.20);
}
.column .pernote img{
	width: 100%;
}
.column .pernote .note-content{
	width: 100%;
	padding: 3%;
	box-sizing: border-box;
}

.column .pernote .note-content .note-men{
	margin: 3% 0;
}
.column .pernote .note-content .note-men img{
	width: 30px;
	border-radius: 50%;
}
.column .pernote .note-content .note-men span{
	display: inline-block;
	margin: 5px 0 0 0;
	font-size: 8px;
}
</style>

<body>
     <div style="height: 100%;background: white;position: relative;">
     	
     	<div class="top_header" style="border-bottom: none;background: #fff;">
	        <div class="header_title" style="font-size: 16px;line-height: 45px;">
				<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'healty'))?>"  <?php if($_GP['op']=='healty' || empty($_GP['op'])){ echo "class='art_active'"; } ?> >健康文化</a> 
				<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline'))?>"  <?php if($_GP['op']=='headline'){ echo "class='art_active'"; } ?>>觅海头条</a> 
				<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'note'))?>"  <?php if($_GP['op']=='note'){ echo "class='art_active'"; } ?>>晒物笔记</a>
	        </div>
	    </div>
     	
     	<div class="mhnews-wap">
     		<!--健康文化-->
			<?php if($_GP['op'] == 'healty' || empty($_GP['op'])){  ?>
					<div class="healthy">
						<ul>
							<!--一个li是一篇文章-->
							<li>
								<a href="#">
									<!--文章大图-->
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline .gif"/>
									<!--文章标题-->
									<p>我带上你你带上钱咱来场说走就走的旅行</p>
								</a>
								<!--发布日期-->
								<div style="position: absolute;top: 0;right:5%;width: 45px;height: 41px;">
									<img class="healthy-data" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/healthy-data.png"/>
									<!--日期-->
									<span>12/09</span>
								</div>
							</li>
							
							<!--底部-->
							<div style="position: relative;height: 30px;">
								<span style="width: 20%;height: 2px;background: #ccc;display: inline-block;position: absolute;top: 10px;left:5%;"></span>
								<span style="width: 40%;display: inline-block;position: absolute;top: 0px;left: 30%;color: #666;text-align: center;">我是有底线的哦~</span>
								<span style="width: 20%;height: 2px;background: #ccc;display: inline-block;position: absolute;top: 10px;right: 5%;"></span>
							</div>												
						</ul>
					</div>
			<!--觅海头条-->
			<?php }else if($_GP['op'] == 'headline'){  ?>
				<div class="mhheadline">
		     		<ul>
		     			<?php if (is_array($article_list)){ foreach ($article_list as $val){ ?>	
		     			<li> 
		     				<div  style="overflow: hidden; float: left;width: 50%;">    			  
			     				<a href="<?php  echo mobile_url('article',array('name'=>'addon8','id'=>$val['id']))?>">
			     					<p><?php  echo $val['title']?></p>     					
			     				</a> 
			     				<div style="width: 100%;overflow: hidden;"> 				     				
									<?php if($val['iscommend'] == 1 && $val['ishot'] == 0) {?>			    				
					    				<span class="hottag">推荐</span>
					    			<?php }elseif($val['ishot'] == 1 && $val['iscommend'] == 0){ ?>
					    				<span class="hottag">热门</span>
					    			<?php }elseif($val['iscommend'] == 1 && $val['ishot'] == 1){ ?>
					    				<span class="hottag">推荐</span>
					    				<span class="hottag hot">热门</span>
					    			<?php } ?>
									<span style="line-height: 20px;color: #999999;font-size: 10px;">
										<img style="margin-top: 5px;width: 12px;" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/seenum.png"/>
										<?php  echo $val['readcount']?>
									</span>	
								</div> 												
		 					</div>
		     				<!--右边的图片-->
		     				<span style="float:right;">
		     					<!--<img style="width: 100%;border: none;" data-original="<?php  echo $val['thumb']?>" class="lazy" src="images/loading.gif"/>-->
		     					<img style="width: 100%;border: none;" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline .gif"/>
		     				</span>    				    				
		     			</li>
		     			<?php }}?>    			
		     		</ul>
	     		</div>
			<!--晒物笔记-->
			<?php }else { ?>
				<div class="notelist">
							<!--第一列，因为要有瀑布流的效果-->
							<div class="column one">
								<!--第一列的第一条笔记-->
								<div class="pernote">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/bg3.png" />
									<div class="note-content">
										<!--笔记标题-->
										<h3>想不到瘦腿肿的最难瘦的瘦肌肉腿爽分享给你啊</h3>
										<!--笔记内容-->
										<p>
											这些写了好久
											感觉自己balabala的像个老太婆 ,但是都是  自己瘦腿心得也希望能帮助你们一起
										</p>
										<!--发布人-->
										<div class="note-men">
											<!--头像-->
											<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/icon2.png" />
											<!--用户名-->
											<span>觅海掌门人</span>
										</div>
									</div>
								</div>
								<!--第二条笔记-->
								<div class="pernote">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/bg3.png" />
									<div class="note-content">
										<!--笔记标题-->
										<h3>想不到瘦腿肿的最难瘦的瘦肌肉腿爽分享给你啊</h3>
										<!--笔记内容-->
										<p>
											这些写了好久
											感觉自己balabala的像个老太婆 ,但是都是  自己瘦腿心得也希望能帮助你们一起
										</p>
										<!--发布人-->
										<div class="note-men">
											<!--头像-->
											<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/icon2.png" />
											<!--用户名-->
											<span>觅海掌门人</span>
										</div>
									</div>		
								</div>
							</div>
							<!--第二列-->
							<div class="column two">
								<div class="pernote">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/bg333.png" />
									<div class="note-content">
										<!--笔记标题-->
										<h3>想不到瘦腿肿的最难瘦的瘦肌肉腿爽分享给你啊</h3>
										<!--笔记内容-->
										<p>
											这些写了好久
											感觉自己balabala的像个老太婆 ,但是都是  自己瘦腿心得也希望能帮助你们一起
										</p>
										<!--发布人-->
										<div class="note-men">
											<!--头像-->
											<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/icon2.png" />
											<!--用户名-->
											<span>觅海掌门人</span>
										</div>
									</div>		
								</div>
								<div class="pernote">
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/bg3.png" />
									<div class="note-content">
										<!--笔记标题-->
										<h3>想不到瘦腿肿的最难瘦的瘦肌肉腿爽分享给你啊</h3>
										<!--笔记内容-->
										<p>
											这些写了好久
											感觉自己balabala的像个老太婆 ,但是都是  自己瘦腿心得也希望能帮助你们一起
										</p>
										<!--发布人-->
										<div class="note-men">
											<!--头像-->
											<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/icon2.png" />
											<!--用户名-->
											<span>觅海掌门人</span>
										</div>
									</div>
								</div>
							</div>
						</div>						
					</div>
			<?php } ?>
     	</div> 
     	<!--用来存当前page-->
     	<input type="hidden" value="2" id="page"/>
     	<div class="tip" style="display: none;text-align: center">
     		<img class="jiazai" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/ajax-loader.gif" />
     		正在加载
     	</div>   
     </div> 
     
	<?php include themePage('footer');?>

</body>
<script>
var index = 1; //默认开关状态是打开

$(function(){
	//滚动条到底部时就加载剩下数据
	$(window).scroll(function () {
		 if ($(document).scrollTop() >= $(document).height() - $(window).height()) {		 			 	
		 	Refresh();		 	
		 }
	})
})

function Refresh(){	
	if( index == 1){
		$(".tip").show();
		index = 0; //关闭开关
		var page = $("#page").val(); //第一次传的是2
	    $.post("", {'page' : page,'nextpage' : 'ajax'}, function(s){
	    	console.log(s.result);		
			if(s.info == 1){
				//如果没有数据
				$(".tip").hide();				
			}else{										
				$("#page").val(++page);			
				for(var i = 0;i < s.result.length;i++){					
					var readcount = s.result[0].readcount;	
					var title = s.result[0].title;	
					var img = s.result[0].thumb;	
					Load(title,readcount,img);
				}		
				index = 1;	//加载完后重新打开开关	
				$(".tip").hide();	
			}			
		}, 'json');	
		
	 }	
}

function Load(title,readcount,img){
	<?php  if($_GP['op'] == 'healty'){ ?>
        var li = '';

	<?php } else{ ?>
		var li = '<li>'+
     				'<div  style="overflow: hidden; float: left;width: 57%;height: 100%;">'+
	     				'<a target="_blank" href="">'+
	     					'<p>'+title+'</p>'+
	     				'</a>'+

						'<?php if($val['iscommend'] == 1 && $val['ishot'] == 0) {?>'+
		    				'<span class="hottag">推荐</span>'+
		    			'<?php }else if($val['ishot'] == 1 && $val['iscommend'] == 0){ ?>'+
		    				'<span class="hottag">热门</span>'+
		    			'<?php }else if($val['iscommend'] == 1 && $val['ishot'] == 1){ ?>'+
		    				'<span class="hottag">推荐</span>'+
		    				'<span class="hottag hot">热门</span>'+
		    			'<?php } ?>'+
						'<span style="line-height: 20px;color: #999999;">'+
							'<img style="margin-top: 5px;" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/seenum.png"/>'+
							readcount+
						'</span>'+

 					'</div>'+
     				'<span style="float:right;">'+
     					'<img style="width: 100%;border: none;" src="'+img+'"/>'+
     				'</span>'+
     			'</li>'

	<?php } ?>

	$(".mhnews-wap ul").append(li);
}
</script>
</html>
