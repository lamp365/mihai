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
									<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline.gif"/>
								<?php }else{  ?>
									<img src="<?php echo $val['thumb'];?>"/>
								<?php } ?>
								<!--文章标题-->
								<p><?php  echo $val['title']?></p>
							</a>
							<!--发布日期-->
							<div style="position: absolute;top: 0;right:2%;width: 45px;height: 41px;">
								<img class="healthy-data" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/healthy-data.png"/>
								<!--日期-->
								<span><?php  echo date("m/d",$val['createtime']);?></span>
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
					<?php if(!empty($article_list)){ ?>
					<!--一个li是一篇文章，总共4个静态文章，分别表现不同的图片数量-->
					<?php foreach($article_list as $row){  ?>
					<li>
						<div class="men">
							<!--头像-->
							<img src="<?php if(!empty($row['avatar'])){ echo $row['avatar'];}else{ echo WEBSITE_ROOT."themes/wap/__RESOURCE__/912865945439541.jpg" ;} ?>" />

						</div>
						<div class="content">
							<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>">
								<!--用户名-->
								<p class="name"><?php  echo $row['nickname']; ?></p>
								<!--文章标题-->
								<p class="title"><?php echo $row['title'] ;?></p>
								<!--文章内容-->
								<p class="detail"><?php echo $row['description'] ;?></p>
							</a>
						</div>
						<!--文章图片-->
						<?php if(!empty($row['pic'])){   $picArr = explode(';',$row['pic']);  ?>
						<div class="imglist">	
							<a href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$row['headline_id']));?>">
								<?php  foreach($picArr as $pic) { ?>
								<img src="<?php echo download_pic($pic,300);?>"/>
								<?php } ?>
							</a>
						</div>
							<?php } ?>
					</li>
					<?php }} ?>


				</ul>
			</div>
			<!--晒物笔记-->
		<?php }else { ?>	
			<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/waterfloor.js"></script>		
				<div class="wrap" id="wrap">

					<?php if (is_array($article_list)){ foreach ($article_list as $key=>$val){ ?>								
					<div>						
						<a href="<?php echo mobile_url('article',array('op'=>'note','id'=>$val['note_id']));?>">
							<?php $pic = explode(';',$val['pic']); ?>
							<img src="<?php echo download_pic($pic[0],180); ?>"/>
						</a>
						<a href="<?php echo mobile_url('article',array('op'=>'note','id'=>$val['note_id']));?>">
							<p class="title"><?php echo $val['title']; ?></p>
							<p class="detail" style="padding-top: 0;"><?php echo msubstr($val['description'],0,100); ?></p>
						</a>						
						<p class="men">
							<?php if(empty($val['avatar'])){ ?>
								<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/icon2.png" />
							<?php }else{ ?>
								<img src="<?php echo $val['avatar']; ?>" />
							<?php } ?>						
							<span><?php echo $val['nickname']; ?></span>
						</p>							
					</div>	
					<?php }}?>	
											
				</div>
		
		<?php } ?>
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
			$(".ajax_next_page").show();
			var op = getOpFromUrl();
			index = 0; //关闭开关
			var page = $("#page").val(); //第一次传的是2
			$.post("", {'page' : page,'nextpage' : 'ajax','op':op}, function(s){
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

	function getOpFromUrl(){
		var op = '';
		$(".header_title a").each(function(){
			if($(this).hasClass("art_active")){
				var url = $(this).attr('href');
				op = request('op',url);
			}
		})
		return op;
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
	
		<?php  if($_GP['op'] == 'healty'){ ?>
		//健康文化的append
		var url = "<?php echo mobile_url('article');?>";
		url = url + "&id="+art_data.id;
		var li = '<li>'+
					'<a href="'+ url +'">';
		if(art_data.thumb.length <= 0){
			li += '<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/mhheadline .gif"/>';
		}else{
			li += '<img src="'+art_data.thumb+'"/>';
		}
				li += '<p>'+art_data.title+'</p>'+
					'</a>'+
					'<div style="position: absolute;top: 0;right:2%;width: 45px;height: 41px;">'+
						'<img class="healthy-data" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/healthy-data.png"/>'+
						'<span>'+Stringtotime(art_data.createtime)+'</span>'+
						'</div>'+
				'</li>';
			$(".healthy ul").append(li);

		<?php } else if($_GP['op'] == 'headline'){ ?>
		//觅海头条的append
		var url = "<?php echo mobile_url('article',array('op'=>'headline'));?>";
		url = url + "&id="+art_data.headline_id;
		var piclist = art_data.pic;
		var perpic = piclist.split(";"); //字符串截取，成为数组
		var picurl = "";
		for(var j=0;j<perpic.length;j++){
			if(perpic[j] != ""){
				picurl += '<img src="'+perpic[j]+'"/>';
			}
		}
		var face = '';
		if(art_data.avatar == ''){
			face = "<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>" + "/912865945439541.jpg";
		}else{
			face = art_data.avatar;
		}
		var li = '<li>'+
					'<div class="men">'+
						'<img src="'+ face +'"/>'+
					'</div>'+
					'<div class="content">'+
						'<a href="'+ url +'">'+
							'<p class="name">'+ art_data.nickname +'</p>'+
							'<p class="title">'+art_data.title+'</p>'+
							'<p class="detail">'+art_data.description+'</p>'+
						'</a>'+
					'</div>';

		if(picurl != ''){
			li = li + '<div class="imglist">'+
						'<a href="'+ url +'">'+picurl+'</a>'+
					  '</div>';
		}

		var li = li +  "</li>";
		$(".mhnews-wap ul").append(li);
		hlimglist();

		<?php }else{ ?>
			//晒物笔记的append
			var url = "<?php echo mobile_url('article',array('op'=>'note'));?>";
			url = url + "&id="+art_data.note_id;
			var face = '';
			if(art_data.avatar == ''){
				face = "<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>" + "/912865945439541.jpg";
			}else{
				face = art_data.avatar;
			}
			var piclist = art_data.pic;
			var picurl = "";
			if(piclist.length > 0){
				var perpic = piclist.split(";"); //字符串截取，成为数组
				picurl     = perpic[0];
			}

			var li = '<div>'+
						'<a href="'+ url +'">'+
							'<img src="'+picurl+'"/>'+
						'</a>'+
						'<a href="'+ url +'">'+
							'<p class="title">'+art_data.title+'</p>'+
							'<p class="detail">'+art_data.description+'</p>'+
						'</a>'+
						'<p class="men">'+
							'<img src="'+ face +'" />'+
							'<span>'+ art_data.nickname +'</span>'+
						'</p>'+
					'</div>';
			$(".wrap").append(li);
			//append后按瀑布流布局排列
			waterfall();
		<?php } ?>

	}
	//将后台返回的时间戳格式化为时间格式
	function Stringtotime(time){  
	    var datetime = new Date();			    
	    datetime.setTime(time);			      
	    var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1; 			   
	    var date = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate(); 	      
	    return month + "/" + date;  
	}  
</script>
</html>
