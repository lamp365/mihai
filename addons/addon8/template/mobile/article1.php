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
	<link rel='stylesheet' type='text/css' href='<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
	<link rel='stylesheet' type='text/css' href='<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/todownapp.css' />
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>

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
		overflow: auto;
	}
	.health-content .health-men .info{
		float: left;
		width: 80%;
		padding: 10px 0px 10px 10px;
		box-sizing: border-box;
	}
	.health-content .health-men .info img{
		width: 60px;
		height: 60px;
		border-radius: 50%;
		float: left;		
	}
	.health-content .health-men .info .name{
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
		box-sizing: border-box;
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
	.health-content .health-men .check-attention span{
		border: 1px solid #d8d8d8;
		color: #d8d8d8;	
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
		width: 30%;
		padding: 0px 0px 0px 15px;
	}
	.heal-foot ul li a img{
		width: 20px;
	}
	.content p{
		line-height: 32px;
	}
	.content img{width: 100%!important;display: block;}

	.comment-area .health-men{
		margin-left: -10px;
	}
	.comment-area .info{
		width: 100%!important;
		border-bottom: solid 1px #eee;
	}
	.comment-time{
		color: #999;font-size: 14px;margin-top: 5px;
	}
	.comment-con{
		clear: both;
		margin-left: 70px;
		color: #6d6d6d;
	}
	/*app下载页样式*/
	.appdownload-hasfooter{position:fixed;left:0;z-index:999;bottom:45px;width:100%;display:block}
	.appdownload-nofooter{position:fixed;left:0;z-index:999;bottom:0;width:100%;display:block}
	#appdownload img{width: 100%;max-width: 100%;display: block;}
	#appdownloadlink{
		position: absolute;
	    right: 0;
	    top: 0;
	    width: 28%;
	    height: 100%;
	}
	#closeLoad{
		position: absolute;
	    left: 0;
	    top: 0;
	    width: 15%;
	    height: 100%;
	}
	.huifu{
		color: #000;
	}
</style>
</head>
<body>
	<!-- openid 和 文章id的隐藏域 -->
	<input type="hidden" class="openid" name="openid" value="<?php echo $_GP['openid'];?>">
	<input type="hidden" class="articleid" name="articleid" value="<?php echo $_GP['id'];?>">
	<input type="hidden" class="article_openid" name="article_openid" value="<?php echo $article['openid'];?>">

	
	<!--头部-->
	<?php if($notApp){ ?>
	 <div class="top_header" style="border-bottom: none;">
	    <div class="header_left return">
	        <a href="javascript:;" class="return" id="return" style="margin-top: 4px;"><img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/openshop/images/return.png"  height="18px"></a>
	    </div>
	    <div class="header_title" style="color: #000;font-size: 16px;font-weight: bold;line-height: 45px;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;width: 90%;left: 30px;">
			<?php echo $article['title'];?>
	    </div>        
	</div>
	<?php }  ?>

	<!--内容-->
	<div class="health-content">
		<!--发布者-->
		<div class="health-men">
			<!--头像-->
			<div class="info">
				<?php if(!empty($article['openid'])){ $author = member_get($article['openid']);    ?>
					<img class="head-img" src="<?php if(empty($author['avatar'])){ echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/912865945439541.jpg'; }else{ echo download_pic($author['avatar'],60,60); }?>" />
					<p class="name">
						<span class="realname"><?php echo $author['realname'];?></span>
						<!--发布时间-->
						<span style="color: #999;font-size: 14px;margin-top: 5px;">发布于<?php echo date("Y-m-d H:i",$article['createtime']);?></span>
					</p>
				<?php }else{  ?>
					<img class="head-img" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/912865945439541.jpg" />
					<p class="name">
						<span>觅海小妹</span>
						<!--发布时间-->
						<span style="color: #999;font-size: 14px;margin-top: 5px;">发布于<?php echo date("Y-m-d H:i",$article['createtime']);?></span>
					</p>
				<?php } ?>
			</div>
			<!--关注按钮-->
			<div class="attention">
				<span class="guanzhu <?php if($notApp){ echo " wap_guanzhu";}else{ echo " app_guanzhu";}?>">
					<?php if($is_guanzhu) { echo '已关注'; }else{ echo '+关注'; }?>
				</span>
			</div>
		</div>

		<!--文章大图-->
		<?php if(!empty($article['thumb'])){ ?>
			<img style="width: 100%;display: block;" src="<?php echo $article['thumb'];?>" />
		<?php }else { ?>
			<!--如果这篇文章没有大图，就放一个空的撑开高度，以免文章标题跑上去-->
			<div style="height: 70px;"></div>
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
        		  <img style="width: 100%;" src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/comment@3x.png" />
        	</div>
        	<div class="comment-area">
				<?php if(!empty($article_comment)){ ?>
				<?php foreach($article_comment as $comment){   $member_comment = member_get($comment['openid']); ?>
	        	<div class="health-men">
					<!--头像-->
					<div class="info">
						<img src="<?php if(!empty($member_comment['avatar'])){ echo $member_comment['avatar'];}else{ echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/912865945439541.jpg'; } ?>"  openid="<?php echo $comment['openid']?>"/>
						<p class="name">
							<span class="comment-name" comment_id ="<?php echo $comment['comment_id'];?>" ><?php if(!empty($member_comment['nickname'])){ echo $member_comment['nickname'];}else{ echo substr_cut($member_comment['mobile']); } ?></span>
							<!--发布时间-->
							<span class="comment-time" >发布于<?php echo date("Y-m-d H:i",$comment['createtime']);?>
						</p>
						<!--评论内容-->
						<div class="comment-con" comment_id ="<?php echo $comment['comment_id'];?>"><?php echo $comment['comment'];?></div>
					</div>						
				</div>
				<?php } ?>
			</div>
			<!--更多评论-->
			<div style="text-align: center;margin-top: 30px;clear: both;color: #999;padding: 15px 0px 10px 0px;" <?php if($notApp){ echo "class='wap_more'";}else{ echo "class='app_more'";}?> >
				<a href="javascript:;" style="color: #999;">
					查看更多评论
				</a>
			</div>
			<?php } ?>

		</div>		

		<?php if($notApp){ ?>
		<div class="ajax_next_page_foot"></div>
		<!--底部栏 -->
		<div style="background: #F8F8F8;height: 50px;width: 100%;position: fixed;bottom: 49px;left: 0;" class="heal-foot">
			<input type="text" readOnly="true"  style="outline: none;background: #FFFFFF;border: 1px solid #DCDDE3;border-radius: 29px;height: 30px;margin: 10px;text-indent: 20px;width: 57%;" placeholder="写下评论……" value="" class="wap_guanzhu"/>

			<ul style="float: right;list-style: none;">
				<li>
					<a href="javascript:;">
						<img src="<?php echo WEBSITE_ROOT . 'themes/' . 'wap' . '/__RESOURCE__'; ?>/recouse/images/health-comment@2x.png"  <?php if($notApp){ echo "class='wap_more'";}else{ echo "class='app_more'";}?> />
					</a>
				</li>
				
				<li>
					<a  href="javascript:;" class="wap_guanzhu">
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
		appWakeUp("<?php echo create_url('mobile', array('name'=>'shopwap','do'=>'appdown','op'=>'get_appversion'));?>",1);
	}
</script>
<?php } ?>

<script>
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
	$(".wap_more").click(function(){
		var url = "<?php echo create_url('mobile', array('id' => $_GP['id'],'op'=>'comment_list','name'=>'addon8','do'=>'article','table'=>'article')); ?>"
		window.location.href = url;
	})

	<?php if($notApp){ ?>
		$(".content .item").click(function(){
			var url = $(this).data('url');
			window.location.href = url;
		})
	<?php }else{ ?>
		$(".content .item").click(function(){
			//需要正源写，跳到app的详情页
			var id = $(this).attr('data-id');
			product_id_obj.product_id = id;
			var obj = {product_id_obj:product_id_obj}
			var ua = browserFun();
			if( ua == "ios" ){
				window.webkit.messageHandlers.mihaiapp.postMessage(product_id_obj);
			}else if( ua=="android" ){
				var obj_android = {product_id:id}
				var jsonString = JSON.stringify(obj_android);
				window.JsInterface.gotoProductDetail(jsonString);
			}
			
		})
	<?php } ?>
	//app判断是否登录
	var openid_val         = $(".openid").val();
	var articleid_val      = $(".articleid").val();
	var article_openid_val = $(".article_openid").val();
	var product_id_obj = {};

	function isLogin(msg){
        //处理app登录
        var ua = browserFun();
        //is_login  0未登录 1已登录
        if( ua == "ios" ){
        	//msg no不需要再发请求，msg yes 继续发请求

            if( msg.openid !=""){
                openid_val = msg.openid;
            }else{
                return;
            }

        }else if( ua=="android" ){
            window.JsInterface.login("");
        }
    }
	//判断iOS还是Android系统
	function browserFun(){
		var ua = navigator.userAgent.toLowerCase();
		if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
			return 'ios';
		}
		if(navigator.userAgent.match(/android/i)){
			return 'android';
		}
	}
	
	function appMore(msg){
		$(".app_more").click(function(){
			//需要正源写，跳到app的评论页显示更多
			var obj = {articleid_val:articleid_val};
			var ua = browserFun();
			if( ua == "ios" ){
				window.webkit.messageHandlers.mihaiapp.postMessage({articleid:articleid_val});
			}else if( ua=="android" ){
				var jsonString = JSON.stringify(obj);
				window.JsInterface.gotoCommentList(jsonString);
			}
		})
	}
	appMore();

	//关注的点击事件，app
	function follow(msg){
		$(".app_guanzhu").on("click",function(){
			//这里需要正源写 提交给app操作
			//先判断是否登录
			var url = "<?php echo mobile_url('article',array('op'=>'guanzhu'));?>";
			var ua = browserFun();
			var obj = {}
			obj.follow = openid_val;
			if( openid_val!="" ){
				if( ua == "ios" ){
						$.post(url,{'openid':openid_val,'article_openid':article_openid_val},function(data){
							if(data.errno != 200){
								$(".attention").addClass("check-attention");
								$(".attention span").text("已关注");
								alert(data.message);
							}else{
								alert(data.message);
							}
						},'json');
					
				}else if( ua=="android" ){
					androidFollowCallback();		
				}

			}else{
				if( ua == "ios" ){
					window.webkit.messageHandlers.mihaiapp.postMessage(obj);
				}else if( ua=="android" ){
					window.JsInterface.login("androidFollowCallback");		
				}
			}
			
		})
	}
	follow();

	function androidFollowCallback(){
		var url = "<?php echo mobile_url('article',array('op'=>'guanzhu'));?>";
		alert("article_openid="+article_openid_val);
		$.post(url,{'openid':openid_val,'article_openid':article_openid_val},function(data){
			if(data.errno != 200){
				$(".attention").addClass("check-attention");
				$(".attention span").text("已关注");
				alert(data.message);
			}else{
				alert(data.message);
			}
		},'json');
	}
	function isNullVal(para){
		//true 代表空 //false代表有值
		if(para==""){
			return "true";
		}else if(para==undefined){
			return "true";
		}else if(para==null){
			return "true";
		}else{
			return "false";
		}
	}
	//评论 回复
	function comment(msg){
		if( openid_val!="" ){
			var comment_html = "";
			var commentTime = getNowFormatDate();
			var head_img = $(".head-img").attr("src");
			var realname = $(".realname").text();
			var comment_length = $(".comment-area .health-men").length;
			var at_nickname_val = isNullVal(msg.at_nickname);
			var at_openid_val = isNullVal(msg.at_openid);
			//返回的at_nickname和at_openid为空表示是新增的评论
			if( at_nickname_val == "true" && at_openid_val =="true"){
				//评论
				//里面的拼接的字段后续要替换成请求返回的数据 
					if(comment_length<3){
						comment_html = "<div class='health-men' at_openid="+openid_val+" comment_id="+msg.comment_id+"><div class='info'><img src="+head_img+"><p class='name'><span>"+realname+"</span>"+
										"<span class='comment-time'>发布于"+commentTime+"</span></p><p></p><div class='comment-con'>"+msg.comment+"</div></div></div>";
						$(".comment-area").prepend(comment_html);
					}else{
						$(".comment-area .health-men:last-child").remove();
						comment_html = "<div class='health-men' at_openid="+openid_val+" comment_id="+msg.comment_id+"><div class='info'><img src="+head_img+"><p class='name'><span>"+realname+"</span>"+
										"<span class='comment-time'>发布于"+commentTime+"</span></p><p></p><div class='comment-con'>"+msg.comment+"</div></div></div>";
						$(".comment-area").prepend(comment_html);
					}
			}else{
				//回复
				//里面的拼接的字段后续要替换成请求返回的数据
					if(comment_length<3){
						comment_html = "<div class='health-men' at_openid="+msg.at_openid+" comment_id="+msg.comment_id+"><div class='info'><img src="+head_img+"><p class='name'><span>"+msg.at_nickname+"</span>"+
										"<span class='comment-time'>发布于"+commentTime+"</span></p><p></p><div class='comment-con'><span class='at_nickname'>"+msg.at_nickname+"</span><span class='huifu'>回复</span>"+msg.comment+"</div></div></div>";
						$(".comment-area").prepend(comment_html);
					}else{
						$(".comment-area .health-men:last-child").remove();
						comment_html = "<div class='health-men' at_openid="+msg.at_openid+" comment_id="+msg.comment_id+"><div class='info'><img src="+head_img+"><p class='name'><span>"+msg.at_nickname+"</span>"+
										"<span class='comment-time'>发布于"+commentTime+"</span></p><p></p><div class='comment-con'><span class='at_nickname'>"+msg.at_nickname+"</span><span class='huifu'>回复</span>"+msg.comment+"</div></div></div>";
						$(".comment-area").prepend(comment_html);
					}
			}
			
		}else{
			var ua = browserFun();
			if( ua == "ios" ){
				window.webkit.messageHandlers.mihaiapp.postMessage({login:""});
			}else if ( ua == "android"){
				window.JsInterface.login("comment");
			}
			
		}
		
	}
	//点击头像传对应的openid给app
	$(".comment-area .info img").click(function(){
		var this_openid = $(this).attr("openid");
		var ua = browserFun();
		var obj = {};
		if( ua == "ios" ){
			obj.profile = this_openid;
			window.webkit.messageHandlers.mihaiapp.postMessage(obj);
		}else if( ua=="android" ){
			obj.openid = this_openid;
			var jsonString = JSON.stringify(obj);
			window.JsInterface.gotoUserDetail(jsonString);
		}
	});
	//点击评论调用comment
	$(".comment-area .comment-con").click(function(){
		var ua = browserFun();
		var at_nickname = $(this).siblings(".name").find(".comment-name").text();
		var at_openid = $(this).siblings("img").attr("openid");
		var comment_id = $(this).attr("comment_id");
		var obj = {};
		var obj2 = {};
			obj.article_id = articleid_val;
			obj.at_openid = at_openid;
			obj.at_nickname = at_nickname;
			obj2.feedback = obj;
		if( ua == "ios" ){
			window.webkit.messageHandlers.mihaiapp.postMessage(obj2);
		}else if( ua=="android" ){
			var jsonString = JSON.stringify(obj);
			window.JsInterface.comment(jsonString,"comment");
		}
	});
	var comment_id = "";
	$(".comment-area .name").click(function(){
		var ua = browserFun();
		var at_nickname = $(this).find(".comment-name").text();
		var at_openid = $(this).siblings("img").attr("openid");
		comment_id = $(this).attr("comment_id");
		var obj = {};
		var obj2 = {};
			obj.article_id = articleid_val;
			obj.at_openid = at_openid;
			obj.at_nickname = at_nickname;
			obj2.feedback = obj;
		//删除评论操作
		if( openid_val == at_openid ){
			if( ua == "ios" ){
				if( confirm("确定删除评论？") ){
					$.post("",{comment_id:comment_id},function(){

					},'json');
				}else{
					return;
				}
				
			}else if( ua=="android" ){
				window.JsInterface.delete("comment_id","deleteComment");
			}
		}else{
		//新增评论或者回复
			if( ua == "ios" ){
				window.webkit.messageHandlers.mihaiapp.postMessage(obj2);
			}else if( ua=="android" ){
				var jsonString = JSON.stringify(obj);
				window.JsInterface.comment(jsonString,"comment");
			}
		}
		
	});
	//安卓删除评论功能
	function deleteComment(){
		$.post("",{comment_id:comment_id},function(){

		},'json');
	}
	//获取当前时间yyyy-mm-dd tt:mm
	function getNowFormatDate() {
	    var date = new Date();
	    var seperator1 = "-";
	    var seperator2 = ":";
	    var month = date.getMonth() + 1;
	    var strDate = date.getDate();
	    if (month >= 1 && month <= 9) {
	        month = "0" + month;
	    }
	    if (strDate >= 0 && strDate <= 9) {
	        strDate = "0" + strDate;
	    }
	    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
	            + " " + date.getHours() + seperator2 + date.getMinutes();
	    return currentdate;
	}

	//页面刷新的方法
	function reloadFun(){
		window.location.reload();
	}
	//点击立即下载，调用下载APP的方法
	$("#downapp p").on("click",function(){
		$("#downapp").hide();
		$(".iframe").hide();
		appDownLoad("<?php echo create_url('mobile', array('name'=>'shopwap','do'=>'appdown','op'=>'get_appversion'));?>");
	})	

	$(".wap_guanzhu").click(function(){
		tipUserToDown("<?php echo create_url('mobile', array('name'=>'shopwap','do'=>'appdown','op'=>'get_appversion'));?>",1);
	})

</script>	
<?php if($notApp){ ?>
<?php include themePage('footer'); ?>
<?php } ?>
