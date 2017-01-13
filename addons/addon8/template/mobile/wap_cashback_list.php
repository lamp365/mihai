<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>返现记录</title>
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

	/*返现记录*/
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
</style>

<body>
<div style="height: 100%;background: white;position: relative;">

	<div class="mhnews-wap">
		<!--返现记录-->
			<div class="healthy">
				<ul>
					<!--一个li是一篇文章-->
					<?php if (is_array($article_list)){ foreach ($article_list as $val){ ?>
						<li>
							<a href="<?php echo mobile_url('cashback',array('id'=>$val['id']));?>">
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
		})
	})

		
	function Refresh(){
		if( index == 1){
			$(".ajax_next_page").show();
			index = 0; //关闭开关
			var page = $("#page").val(); //第一次传的是2
			var url = "<?php echo mobile_url('cashback',array('op'=>'list'));?>";
			$.post(url, {'page' : page,'nextpage' : 'ajax'}, function(s){
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



function Load(art_data){
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
