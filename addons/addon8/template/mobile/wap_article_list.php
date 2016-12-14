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
	<link href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjcommon.css" rel="stylesheet"  type="text/css" />
	<link rel='stylesheet' type='text/css'href='<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
	<link rel="shortcut icon" href="favicon.ico"/>
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>
	
</head>

<style type="text/css">

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
		width: 55px;
		position: absolute;
		top: 0;
		right:0;
	}
	.healthy ul li span{
		position: absolute;
		top: 9px;
		right:22px;
		width: 30px;
		font-size: 8px;
		color: #fff;
		display: inline-block;
	}

	/*晒物笔记*/	
	div.wrap{
		width: 100%;								
		position: relative;
		background: #DDD;
	}
	div.wrap div{				
		border: 1px solid #eee;
		position: absolute;
		background: #fff;
		padding-bottom: 1%;
		box-sizing: border-box;
	}
	div.wrap div p{
		padding:3% 3% 0 3%;
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
	}
	.mhnews-wap .mhheadline {
		background: #fff;
	}

	.tip .jiazai{
		margin: 10px auto;
		display: block;
		width: 20px;
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
	}
	.mhheadline ul li .imglist .imgtwo{
		width: 49%;			
	}
	.mhheadline ul li .imglist .imgthree{
		width: 32%;
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
					<?php if (is_array($article_list)){ foreach ($article_list as $val){ ?>
						<li>
							<a href="<?php echo mobile_url('article',array('id'=>$val['id']));?>">
								<!--文章大图-->
								<?php if(empty($val['thumb'])){ ?>
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline .gif"/>
								<?php }else{  ?>
									<img src="<?php echo $val['thumb'];?>"/>
								<?php } ?>
								<!--文章标题-->
								<p><?php  echo $val['title']?></p>
							</a>
							<!--发布日期-->
							<div style="position: absolute;top: 0;right:5%;width: 45px;height: 41px;">
								<img class="healthy-data" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/healthy-data.png"/>
								<!--日期-->
								<span><?php  echo date("Y/m",$val['createtime']);?></span>
							</div>
						</li>
					<?php }}?>
					<!--底部-->
				</ul>
				<div style="position: relative;height: 30px;">
					<span style="width: 20%;height: 2px;background: #ccc;display: inline-block;position: absolute;top: 10px;left:5%;"></span>
					<span style="width: 40%;display: inline-block;position: absolute;top: 0px;left: 30%;color: #666;text-align: center;">我是有底线的哦~</span>
					<span style="width: 20%;height: 2px;background: #ccc;display: inline-block;position: absolute;top: 10px;right: 5%;"></span>
				</div>
			</div>
			<!--觅海头条-->
		<?php }else if($_GP['op'] == 'headline'){  ?>			
			<div class="mhheadline">
				<h3></h3>
				<ul>
					<!--一个li是一篇文章，总共4个静态文章，分别表现不同的图片数量-->					
					<li>
						<div class="men">
							<!--头像-->							
							<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/912865945439541.jpg" />														
						</div>
						<div class="content">
							<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$val['headline_id']));?>">
								<!--用户名-->
								<p class="name">北城少女南城情</p>
								<!--文章标题-->
								<p class="title">对美物的追求，是永远没有尽头的</p>
								<!--文章内容-->
								<p class="detail">厌倦了周末不是逛街，看电影，就是吃吃喝喝喝。直到今天去了新天地的一家名叫洗衣船</p>
							</a>
						</div>
						<!--文章图片-->
						<div class="imglist">	
							<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$val['headline_id']));?>">						
								<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline .gif"/>
								<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline .gif"/>
								<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline .gif"/>
							</a>
						</div>
					</li>	
					<li>
						<div class="men">
							<!--头像-->
							<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/912865945439541.jpg" />							
						</div>
						<div class="content">
							<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$val['headline_id']));?>">
								<!--用户名-->
								<p class="name">北城少女南城情</p>
								<!--文章标题-->
								<p class="title">对美物的追求，是永远没有尽头的</p>
								<!--文章内容-->
								<p class="detail">厌倦了周末不是逛街，看电影，就是吃吃喝喝喝。直到今天去了新天地的一家名叫洗衣船</p>
							</a>
						</div>
						<!--文章图片-->
						<div class="imglist">	
							<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$val['headline_id']));?>">						
								<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline .gif"/>							
								<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline .gif"/>
							</a>
						</div>
					</li>	
					<li>
						<div class="men">
							<!--头像-->
							<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/912865945439541.jpg" />							
						</div>
						<div class="content">
							<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$val['headline_id']));?>">
								<!--用户名-->
								<p class="name">北城少女南城情</p>
								<!--文章标题-->
								<p class="title">对美物的追求，是永远没有尽头的</p>
								<!--文章内容-->
								<p class="detail">厌倦了周末不是逛街，看电影，就是吃吃喝喝喝。直到今天去了新天地的一家名叫洗衣船</p>
							</a>
						</div>
						<!--文章图片-->
						<div class="imglist">
							<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$val['headline_id']));?>">							
								<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline .gif"/>	
							</a>											
						</div>
					</li>	
					<li>
						<div class="men">
							<!--头像-->
							<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/912865945439541.jpg" />							
						</div>
						<div class="content">
							<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$val['headline_id']));?>">
								<!--用户名-->
								<p class="name">北城少女南城情</p>
								<!--文章标题-->
								<p class="title">对美物的追求，是永远没有尽头的</p>
								<!--文章内容-->
								<p class="detail">厌倦了周末不是逛街，看电影，就是吃吃喝喝喝。直到今天去了新天地的一家名叫洗衣船</p>
							</a>
						</div>
						<!--文章图片-->
						<div class="imglist">							
							<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$val['headline_id']));?>">
							</a>												
						</div>
					</li>						
				</ul>
			</div>
			<!--晒物笔记-->
		<?php }else { ?>	
			<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/waterfloor.js"></script>		
				<div class="wrap" id="wrap">
					<!--这块div是静态数据，为了让你看下瀑布流的布局-->
					<div>						
						<a href="">							
							<img src="<?php echo $pic[0]; ?>"/>
						</a>
						<a href="">
							<p class="title">hhh</p>
							<p class="detail">slkdjgkajdgka</p>
						</a>						
						<p class="men">							
							<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/icon2.png" />												
							<span>gsdh</span>
						</p>							
					</div>	
					<!--接下来的是正式的，一个div是一条文章-->	
					<?php if (is_array($article_list)){ foreach ($article_list as $key=>$val){ ?>								
					<div>						
						<a href="<?php echo mobile_url('article',array('op'=>'note','id'=>$val['note_id']));?>">
							<?php $pic = explode(';',$val['pic']); ?>
							<img src="<?php echo $pic[0]; ?>"/>
						</a>
						<a href="<?php echo mobile_url('article',array('op'=>'note','id'=>$val['note_id']));?>">
							<p class="title"><?php echo $val['title']; ?></p>
							<p class="detail"><?php echo msubstr($val['description'],0,100); ?></p>
						</a>						
						<p class="men">
							<?php $member = member_get($val['openid']); ?>
							<?php if(empty($member['avatar'])){ ?>
								<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/icon2.png" />
							<?php }else{ ?>
								<img src="<?php echo $member['avatar']; ?>" />
							<?php } ?>						
							<span><?php if(!empty($member['nickname'])) {  echo $member['nickname']; }else{ echo substr_cut($member['mobile']); } ; ?></span>
						</p>							
					</div>	
					<?php }}?>	
											
				</div>
		
		<?php } ?>
	</div>
</div>
<!--用来存当前page-->
<input type="hidden" value="2" id="page"/>
<div class="tip" style="display: none;text-align: center">
	<img class="jiazai" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/ajax-loader.gif"/>
	正在加载
</div>
<?php include themePage('footer');?>

</body>
<script>
	var index = 1; //默认开关状态是打开

	$(function(){
		hlimglist();//调用觅海头条图片样式控制的方法
		//滚动条到底部时就加载剩下数据
		$(window).scroll(function(){			
			if ($(document).scrollTop() >= $(document).height() - $(window).height()) {
				Refresh();
			}
		})
	})
	//根据觅海头条图片数量，动态控制样式
	function hlimglist(){
		$(".mhheadline .imglist").each(function(){
			var length = $(this).find("img").length;
			if(length == 1){			
				$(this).find("img").attr("class", "imgone");
			}else if(length == 2){
				$(this).find("img").attr("class", "imgtwo");
			}else if(length == 3){
				$(this).find("img").attr("class", "imgthree");
			}else{
				$(this).find("img").attr("class", " ");
			}
		})
	}
		
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
						var readcount = s.result[i].readcount;//阅读人数
						var title = s.result[i].title;//文章标题
						var img = s.result[i].thumb;//文章图片
													//文章内容
													//用户头像
													//用户名
						Load(title,readcount,img);
					}
					index = 1;  //加载完后重新打开开关
					$(".tip").hide();
				}
			}, 'json');

		}
	}

	function Load(title,readcount,img){
		//健康文化的append
		<?php  if($_GP['op'] == 'healty'){ ?>
		var li = '<li>'+
					'<a href="<?php echo mobile_url('article',array('id'=>$val['id']));?>">'+								
						'<?php if(empty($val['thumb'])){ ?>'+
							'<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline .gif"/>'+
						'<?php }else{  ?>'+
							'<img src="'+img+'"/>'+
						'<?php } ?>'+								
						'<p>'+title+'</p>'+
					'</a>'+							
					'<div style="position: absolute;top: 0;right:5%;width: 45px;height: 41px;">'+
						'<img class="healthy-data" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/healthy-data.png"/>'+								
						'<span><?php  echo date("Y/m",$val['createtime']);?></span>'+
					'</div>'+
				'</li>';
			$(".healthy ul").append(li);
		<?php } else if($_GP['op'] == 'headline'){ ?>
		//觅海头条的append
		var li = '<li>'+
					'<div class="men">'+
						'<!--头像-->'+							
						'<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/912865945439541.jpg" />'+														
					'</div>'+
					'<div class="content">'+
						'<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$val['headline_id']));?>">'+
							'<!--用户名-->'+
							'<p class="name">hhh</p>'+
							'<!--文章标题-->'+
							'<p class="title">对美物的追求，是永远没有尽头的</p>'+
							'<!--文章内容-->'+
							'<p class="detail">厌倦了周末不是逛街，看电影，就是吃吃喝喝喝。直到今天去了新天地的一家名叫洗衣船</p>'+
						'</a>'+
					'</div>'+
					'<!--文章图片-->'+
					'<div class="imglist">'+	
						'<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$val['headline_id']));?>">'+						
							'<img src="'+img+'"/>'+					
						'</a>'+	
					'</div>'+	
				'</li>';
		$(".mhnews-wap ul").append(li);
		hlimglist();
		<?php }else{ ?>
			//晒物笔记的append			
			var li = '<div>'+						
						'<a href="">'+							
							'<img src="'+img+'"/>'+
						'</a>'+
						'<a href="">'+
							'<p class="title">'+title+'</p>'+
							'<p class="detail">slkdjgkajdgka</p>'+
						'</a>'+						
						'<p class="men">'+							
							'<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/icon2.png" />'+												
							'<span>gsdh</span>'+
						'</p>'+							
					'</div>';
			$(".wrap").append(li);
			//append后按瀑布流布局排列
			waterfall();
		<?php } ?>

	}
	
</script>
</html>
