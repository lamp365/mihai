<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="format-detection" content="telephone=no">
	<title>评论列表</title>
	<link rel='stylesheet' type='text/css'href='<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>
	<link rel='stylesheet' type='text/css'href='<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/todownapp.css' />
</head>
<style>

	*{
		margin: 0;
		padding: 0;
	}
	.top_header{
		max-width: 100% !important;
	}
	.heal-foot ul{
		overflow: hidden;
		margin-top: 15px;
		width: 35%;
	}
	.heal-foot ul li{
		float: left;
		width: 30%;
		padding: 0 0 0 15px;
	}
	.heal-foot ul li a img{
		width: 20px;
	}
	.health-men{
		width: 90%;
		margin: 0 5%;
		overflow: hidden;
	}
	.health-men .info{
		float: left;
		width: 75%;
		padding:20px;
		box-sizing: border-box;
	}
	.health-men .info img{
		width: 50px;
		height: 50px;
		border-radius: 50%;
		display: inline-block;
		float: left;

	}
	.health-men .info .name{
		display: inline-block;
		float: left;
		overflow: hidden;
		padding: 7px 0 0 10px ;
		margin-bottom: 10px;

	}
	.health-men .info .name span{
		display: block;
	}
	.health-men .attention{
		float: right;
		width: 20%;
		padding: 20px 0px 0px 0px;
		margin-top: 10px;
	}
	.health-men .attention span{
		padding: 5px;
		height: 30px;
		line-height: 30px;
		text-align: center;
		border: 1px solid #F43776;
		border-radius: 6px;
		color: #F43776;
	}
	/*没有评论*/
	.no-comment{
		width: 60%;
		height: 50%;
		margin: 0 auto;
		margin-top: 45%;
	}
	.no-comment img{
		width: 50%;
		height: 50%;
		margin-left: 23%;
	}
	.no-comment p{		
		font-size: 14px;
		color: #666666;	
		margin-top: 5%;	
	}
</style>
<body>

<!--头部-->
<div class="top_header" style="border-bottom: none;">
	<div class="header_left return">
		<a href="javascript:;" class="return" id="return" style="margin-top: 4px;">
			<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/openshop/images/return.png"  height="18px">
		</a>
	</div>
	<div class="header_title" style="color: #000;font-size: 20px;font-weight: bold;line-height: 45px;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;width: 90%;left: 30px;">
		评论
	</div>
</div>


<!--评论内容-->
<div class="commentlist">
	<!--没有评论-->
	<?php if(empty($comment_list)){ ?>
		<div class="no-comment">
			<img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/health-comment@3x.png"/>
			<p>勾搭评论别害羞，聊骚要做第一人~</p>
		</div>
	<?php }else{  ?>
		<?php foreach($comment_list as $row) {  ?>
			<!--有评论-->
			<div class="health-men">
				<!--头像-->
				<div class="info"style="width: 100%;border-bottom: solid 1px #eee;">
					<img src="<?php if(!empty($row['avatar'])){ echo $row['avatar'];}else{ echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/912865945439541.jpg'; } ?>" />
					<p class="name">
						<span><?php echo $row['nickname'];  ?></span>
						<!--发布时间-->
						<span style="color: #999;font-size: 14px;margin-top: 5px;"><?php echo date("Y-m-d H:i:s",$row['createtime']); ?></p>
					</p>
					<!--评论内容-->
					<div style="clear: both;margin-left: 60px;line-height: 22px;">
						<?php echo $row['comment'];?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
</div>
<!--用来存当前page-->
<input type="hidden" value="2" id="page"/>
<div class="ajax_next_page">
	<img class="jiazai" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/ajax-loader.gif"/>
	正在加载
</div>
<div class="ajax_next_page_foot"></div>
<!--底部栏 -->
<div style="background: #F8F8F8;height: 50px;width: 100%;position: fixed;bottom: 49px;left: 0;" class="heal-foot">
	<input type="text" readOnly="true"  style="outline: none;background: #FFFFFF;border: 1px solid #DCDDE3;border-radius: 29px;height: 30px;margin: 10px;text-indent: 20px;width: 57%;" placeholder="写下评论……" value="" class="wap_guanzhu"/>
	<ul style="float: right;list-style: none;">
		<li>
			<a href="javascript:;">
				<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/health-comment@2x.png"  class='wap_more' />
			</a>
		</li>

		<li>
			<a class="wap_guanzhu" href="javascript:;">
				<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/clle@2x.png" />
			</a>
		</li>
	</ul>
</div>

<input type="hidden" name="hide_table" value="<?php echo $_GP['table'];?>" id="hide_table">
<?php include themePage('footer'); ?>
<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>
<script src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/js/appwakeup.js"></script>
<script>
	window.onload = function(){
		appWakeUp("<?php echo create_url('mobile', array('name'=>'shopwap','do'=>'appdown','op'=>'get_appversion'));?>",1);
	}
</script>
<script>
	$("#return").on("click",function(){
		if(document.referrer.length == 0){
			window.location.href = "index.php";
		}else{
			var newHref = document.referrer;
			$("#return").attr("href",newHref);
		}
		window.history.back(-1);
	})

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
			var table = $("#hide_table").val();
			$.post("", {'page' : page,'nextpage' : 'ajax','op':'comment_list','table':table}, function(s){
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
		//拼接html 然后追加
		if(art_data.avatar == ''){
			var face = "<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/912865945439541.jpg'; ?>";
		}else{
			var face = art_data.avatar;
		}
		var html = '<div class="health-men">'+
			'<!--头像-->'+
			'<div class="info"style="width: 100%;border-bottom: solid 1px #eee;">'+
				'<img src="'+ face +'" />'+
				'<p class="name">'+
					'<span>'+ art_data.nickname +'</span>'+
					'<!--发布时间-->'+
					'<span style="color: #999;font-size: 14px;margin-top: 5px;">'+Stringtotime(art_data.createtime)+'</p>'+
				'</p>'+
				'<!--评论内容-->'+
				'<div style="clear: both;margin-left: 60px;line-height: 22px;">'+art_data.comment+'</div>'+
			'</div>'+
		'</div>';
		$(".commentlist").append(html);
	}
	
	//格式化时间
	function Stringtotime(time){  
	    var datetime = new Date();  
	    datetime.setTime(time);  
	    var year = datetime.getFullYear();  
	    var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1;  
	    var date = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate();  
	    var hour = datetime.getHours()< 10 ? "0" + datetime.getHours() : datetime.getHours();  
	    var minute = datetime.getMinutes()< 10 ? "0" + datetime.getMinutes() : datetime.getMinutes();  
	    var second = datetime.getSeconds()< 10 ? "0" + datetime.getSeconds() : datetime.getSeconds();  
	    return year + "-" + month + "-" + date+" "+hour+":"+minute+":"+second;  
	}  

	$(".wap_more").click(function(){
		var url = "<?php echo create_url('mobile', array('id' => $_GP['id'],'op'=>'comment_list','name'=>'addon8','do'=>'article','table'=>$_GP['table'])); ?>"
		window.location.href = url;
	})

	$(".wap_guanzhu").click(function(){
		tipUserToDown("<?php echo create_url('mobile', array('name'=>'shopwap','do'=>'appdown','op'=>'get_appversion'));?>",1);
	})
</script>