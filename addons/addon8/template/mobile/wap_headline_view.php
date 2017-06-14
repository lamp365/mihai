<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title>视频中心</title>
    <link href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjcommon.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjdetail.css" rel="stylesheet"  type="text/css" />
    <link href="<?php echo RESOURCE_ROOT;?>addons/common/beAlert/css/BeAlert.css" type="text/css" rel="stylesheet" />
    <link href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/coupons.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript"  src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/beAlert/js/beAlert.js"></script>
    <style type="text/css">
    	.top_header{
    		background-color: #fff;
    	}
    	.video-list{
    		margin-bottom: 10px;
    	}
    	.video-list li{
    		float: left;
    		width: 45.5%;
    		background-color: #fff;
    		margin-top: 3%;
    		box-shadow: 1px 1px 5px 1px #eaeaea;
    	}
    	.video-list li:nth-child(odd){
		    margin-left: 3%;
		    margin-right: 1.5%;
		}
		.video-list li:nth-child(even){
		    margin-left: 1.5%;
		    margin-right: 3%;
		}
		.video-list .video-img{
			position: relative;
		}
		.video-list .video-img img{
			display: block;
			width: 100%;
			max-width: 100%;
		}
		.video-list .video-img .paly-icon{
			width: 25px;
			max-width: 25px;
			position: absolute;
			bottom: 10px;
			left: 10px;
		}
		.video-title{
			color: #000;
			padding: 5px;
			overflow: hidden;
		    text-overflow: ellipsis;
		    display: -webkit-box;
		    -webkit-line-clamp: 2;
		    -webkit-box-orient: vertical;
		    height: 50px;
		    box-sizing: border-box;
		    font-size: 14px;
		}
		.video-detail{
			color: #999;
			padding: 5px;
			overflow: hidden;
		    text-overflow: ellipsis;
		    display: -webkit-box;
		    -webkit-line-clamp: 2;
		    -webkit-box-orient: vertical;
		    height: 46px;
		    box-sizing: border-box;
		    font-size: 13px;
		}
		.video-user-img{
			float: left;
			width: 25px;
		}
		.video-user-img img{
			width: 100%;
			display: block;
			max-width: 100%;
			border-radius: 100px;
		}
		.video-user-name{
			float: left;
			width: 43%;
			font-size: 12px;
    		color: #666;
    		margin-top: 4px;
    		overflow: hidden;
    		white-space: nowrap;
		    text-overflow: ellipsis;
		}
		.video-collect{
			width: 41%;
		    float: right;
		    text-align: right;
		    margin-top: 2px;
		}
		.video-collect img{
			width:16px;
			vertical-align: middle;
		}
		.video-collect span{
			color: #666;
			font-size: 12px;
		}
		@media only screen and (min-width: 320px) and (max-width: 374px){
			.video-user-img{
				width: 20px;
			}
			.video-user-name{
				margin-top: 2px;
			}
			.video-collect{
				margin-top: 0;
			}
		}
		@media only screen and (min-width: 374px){
			.video-user-name{
				margin-left: 2px;
				width: 47%;
			}
			.video-collect{
				width: 36%;
			}
		}
		.video-user-info{
			padding: 5px;overflow: hidden;padding-bottom: 10px;
		}
    </style>
</head>
<body class="freepay body-gray" style="min-width: 320px;max-width: 640px;margin:0 auto;">
	<div class="top_header">
        <div class="header_left">
            <a href="javascript:;" class="return" style="margin-top: 2px;"><img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/openshop/images/return.png"  height="18px"></a>
        </div>
        <div class="header_title">
            热门视频
        </div>
    </div>
    <ul class="video-list clearfix">
		<?php if(!empty($video_list)){ foreach($video_list as $list){  ?>
    	<li>
    		<a class="video-img" href="<?php echo mobile_url('article',array('op'=>'headline','id'=>$list['headline_id'])); ?>">
			<!--视频上传的图片比例290:204 -->
				<?php if(empty($list['video_img'])){ ?>
    			<img src="http://odozak4lg.bkt.clouddn.com/20161013151057ff335c3585d.jpg">
				<?php }else{ ?>
				<img src="<?php echo download_pic($list['video_img'],300,210,2);?>" alt="">
				<?php } ?>
    			<img class="paly-icon" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/video-play.png">
    		</a>
    		<div class="video-title">
    			<?php echo $list['title'];?>
    		</div>
    		<div class="video-detail">
				<?php echo $list['preview'];?>
    		</div>
    		<div class="video-user-info">
    			<div class="video-user-img">
    				<img src="<?php if(empty($list['avatar'])){ echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/recouse/images/userface.png'; }else{ echo download_pic($list['avatar'],60,60,2); }?>" />
    			</div>
    			<div class="video-user-name"><?php if(empty($list['nickname'])){ echo "觅海掌门人"; }else{ echo $list['nickname'];} ?></div>
    			<div class="video-collect">
    				<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/clle@2x.png">
    				<span><?php echo $list['collent_num'];?></span>
    			</div>
    		</div>
    		
    	</li>
    	<?php }} ?>
    </ul>
<!--用来存当前page-->
<input type="hidden" value="2" id="page"/>
<div class="ajax_next_page">
	<img class="jiazai" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/ajax-loader.gif"/>
	正在加载
</div>
<?php include themePage('footer');?>
</body>
<script>
//返回按钮
$(function(){
    var index = 0;
    $(".return").click(function(){
        var refer = document.referrer;//没有来源url
        if(refer.length == 0){
            var url = "index.php";
            window.location.href = url;
        }else{
            window.history.back(-1);
        }
    });
})
</script>
<script>
	var page_index = 1; //默认开关状态是打开

	$(function(){
		//滚动条到底部时就加载剩下数据
		$(window).scroll(function(){			
			if ($(document).scrollTop() >= $(document).height() - $(window).height()) {
				Refresh();
			}
		})
	})
	function Refresh(){
		if( page_index == 1){
			$(".ajax_next_page").show();
			page_index = 0; //关闭开关
			var page = $("#page").val(); //第一次传的是2
			$.post("", {'page' : page,'nextpage' : 'ajax','op':'headline_view'}, function(s){
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
					page_index = 1;  //加载完后重新打开开关
					$(".ajax_next_page").hide();
				}
			}, 'json');

		}
	}
	function Load(art_data){	
		//觅海头条的append
		var url = "<?php echo mobile_url('article',array('op'=>'headline')); ?>";
		url = url + "&id="+art_data.headline_id;
		var pic_img  = art_data.video_img,
		    avatar   = art_data.avatar,
		    nickname = art_data.nickname;
		if( !avatar ){
			avatar = "<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/userface.png";
		}
		if( !nickname ){
			nickname = "觅海掌门人";
		}
		var li ='<li>'+
					'<a class="video-img" href="'+url+'">'+
						'<img src="'+pic_img+'">'+
						'<img class="paly-icon" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/video-play.png">'+
					'</a>'+
					'<div class="video-title">'+art_data.title+'</div>'+
					'<div class="video-detail">'+art_data.preview+'</div>'+
					'<div class="video-user-info">'+
						'<div class="video-user-img">'+
							'<img src="'+avatar+'">'+
						'</div>'+
						'<div class="video-user-name">'+nickname+'</div>'+
						'<div class="video-collect">'+
							'<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/clle@2x.png">'+
							'<span>'+parseInt(art_data.collent_num)+'</span>'+
						'</div>'+
					'</div>'+
				'</li>';
		$(".video-list").append(li);
	}
</script>
</html>