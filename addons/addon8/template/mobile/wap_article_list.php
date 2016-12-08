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
.mhnews-wap ul{
	overflow: hidden;
}
.mhnews-wap ul li{
	width: 90%;border-top: solid 1px gainsboro;padding:20px 5%;overflow: hidden;
}
.mhnews-wap ul li a{
	float: left;
	display: inline-block;
	overflow: hidden;
	/*height: 80%;*/
	width: 100%;
	margin-bottom: 3px;	
}
.mhnews-wap ul li a p{		
	font-size: 16px;
	color: #333333;
	width: 100%;
	/*height: 100%;*/
}
.mhnews-wap ul li span{
	width:40%;
	float: left;
	display: inline-block;
}

.mhnews-wap .hottag{
	width: 28px;
	height: 16px;
	line-height: 16px;
	font-size: 12px;
	text-align: center;
	border: 1px solid #DC0200;
	border-radius: 6px;
	color: #DC0200;
	margin-left: 0;
}
.hot{margin-left: 10px !important;}
.tip .jiazai{
	margin: 0 auto;
	display: block;
}
.header_title .art_active{
	color: red;
}
</style>

<body>
     <div style="height: 100%;background: white;position: relative;">
     	
     	<div class="top_header" style="border-bottom: none;">
	        <div class="header_title" style="color: #E53A57;font-size: 20px;font-weight: bold;line-height: 45px;">
				<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'healty'))?>"  <?php if($_GP['op']=='healty' || empty($_GP['op'])){ echo "class='art_active'"; } ?> >健康文化</a> |
				<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'headline'))?>"  <?php if($_GP['op']=='headline'){ echo "class='art_active'"; } ?>>觅海头条</a> |
				<a href="<?php  echo mobile_url('article_list',array('name'=>'addon8','op'=>'note'))?>"  <?php if($_GP['op']=='note'){ echo "class='art_active'"; } ?>>觅海笔记</a>
	        </div>
	    </div>
     	
     	<div class="mhnews-wap">
			<?php if($_GP['op'] == 'healty' || empty($_GP['op'])){  ?>
					健康文化模块的文章需要按照ui图重写


			<?php }else{  ?>
     		<ul>
     			<?php if (is_array($article_list)){ foreach ($article_list as $val){ ?>	
     			<li> 
     				<div  style="overflow: hidden; float: left;width: 57%;">    			  
	     				<a href="<?php  echo mobile_url('article',array('name'=>'addon8','id'=>$val['id']))?>">
	     					<p><?php  echo $val['title']?></p>     					
	     				</a>   				     				
						<?php if($val['iscommend'] == 1 && $val['ishot'] == 0) {?>			    				
		    				<span class="hottag">推荐</span>
		    			<?php }elseif($val['ishot'] == 1 && $val['iscommend'] == 0){ ?>
		    				<span class="hottag">热门</span>
		    			<?php }elseif($val['iscommend'] == 1 && $val['ishot'] == 1){ ?>
		    				<span class="hottag">推荐</span>
		    				<span class="hottag hot">热门</span>
		    			<?php } ?>
						<span style="line-height: 20px;color: #999999;">
							<img style="margin-top: 5px;" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/seenum.png"/>
							<?php  echo $val['readcount']?>
						</span>													
 					</div>
     				<!--右边的图片-->
     				<span style="float:right;">
     					<img style="width: 100%;border: none;" data-original="<?php  echo $val['thumb']?>" class="lazy" src="images/loading.gif"/>
     				</span>    				    				
     			</li>
     			<?php }}?>    			
     		</ul>

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
