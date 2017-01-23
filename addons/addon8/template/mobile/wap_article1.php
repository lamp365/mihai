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
	<meta name="format-detection" content="email=no" />
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
	    width: 50px;
	    height: 50px;
	    border-radius: 50%;
	    float: left;
	    margin-top: 6px;	
	}
	.health-content .health-men .info .name{
		float: left;
		overflow: hidden;
		//padding: 10px 0 0 10px;
	}
	.health-content .health-men .info .name .realname{
		width: 100%;
		overflow: hidden;
		text-overflow: ellipsis;
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
	.health-detail video{
		width: 100%;
		height: auto;
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
	/*.content img{
		width: 100%!important;
		height: auto !important;
		display: block;
		text-indent: 0;
	}*/

	.comment-area .health-men{
		//margin-left: -10px;
	}
	.comment-area .info{
		width: 100%!important;
		border-bottom: solid 1px #eee;
	}
	.comment-time{
		color: #999;font-size: 14px;margin-top: 5px;
		margin-bottom: 5px;
	}
	.comment-con{
		clear: both;
		//margin-left: 70px;
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
	.click_pinlun{
	float: left;
    width: 78%;
    padding: 0 10px;
    box-sizing: border-box;
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
					<p class="name" style="padding: 10px 0 0  10px;width: 70%;">
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
				<?php foreach($article_comment as $comment){   ?>
	        	<div class="health-men" comment_id="<?php echo $comment['comment_id']; ?>">
					<!--头像-->
					<div class="info">
						<img src="<?php if(!empty($comment['avatar'])){ echo $comment['avatar'];}else{ echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__/recouse/images/userface2.png'; } ?>"  openid="<?php echo $comment['openid']?>" at_openid="<?php echo $comment['at_openid']?>" />
						<div class="click_pinlun" onclick="pinlun(this)">
							<p class="name" >
								<span class="comment-name" comment_id ="<?php echo $comment['comment_id'];?>" ><?php  echo $comment['nickname'];  ?></span>
								<!--发布时间-->
								<span class="comment-time" >发布于<?php echo date("Y/m/d H:i",$comment['createtime']);?>
							</p>

							<?php if(!empty($comment['reply_nickname'])){ ?>
							<div class="comment-con" comment_id ="<?php echo $comment['comment_id'];?>">
								<span class='at_nickname'><?php  echo $comment['nickname'];  ?></span>
								回复:
								<span><?php  echo $comment['reply_nickname']; ?></span>
								<?php echo $comment['comment'];?>
							</div>
							<?php }else{   ?>
							<div class="comment-con" comment_id ="<?php echo $comment['comment_id'];?>"><?php echo $comment['comment'];?></div>
							<?php }   ?>
						</div>
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
			openid_val = msg.openid;
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
			obj.openid = openid_val;
			if( openid_val!="" ){
				if( ua == "ios" ){
						$.post(url,{'openid':openid_val,'article_openid':article_openid_val},function(data){
							if(data.errno == 200){
//								$(".attention").addClass("check-attention");
								$(".attention span").text("已关注");
								tip(data.message,1);
							}else{
								tip(data.message,1);
							}
						},'json');
					
				}else if( ua=="android" ){
					androidFollowCallback(obj);
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

	function androidFollowCallback(msg){
		openid_val = msg.openid;
		var url = "<?php echo mobile_url('article',array('op'=>'guanzhu'));?>";
		$.post(url,{'openid':openid_val,'article_openid':article_openid_val},function(data){
			if(data.errno == 200){
//				$(".attention").addClass("check-attention");
				$(".attention span").text("已关注");
				tip(data.message,'1');
			}else{
				tip(data.message,'1');
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
		var ua = browserFun();
		if ( ua == "android"){
			openid_val = msg.openid;
		}
		if( isNullVal(openid_val)=="false" ){
			var comment_html = "";
			var commentTime = getNowFormatDate();

			var comment_length = $(".comment-area .health-men").length;
			var at_openid_val = isNullVal(msg.at_openid);
			var at_nickname_val = isNullVal(msg.at_nickname);
			var at_avatar = isNullVal(msg.avatar);
			//返回的at_nickname和at_openid为空表示是新增的评论
			if( at_avatar == "true" ){
				at_avatar = "<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/userface2.png";
			}
			if( at_openid_val == "true"&& at_nickname_val == "true"){
				//评论
				//里面的拼接的字段后续要替换成请求返回的数据 

					if(comment_length<3){
						comment_html = "<div class='health-men' openid="+openid_val+" comment_id="+msg.comment_id+"><div class='info'><img src="+msg.avatar+" openid="+openid_val+" at_openid="+ msg.at_openid +"><div class='click_pinlun' onclick='pinlun(this)'><p class='name'><span class='comment-name' comment_id="+msg.comment_id+">"+msg.nickname+"</span>"+
										"<span class='comment-time'>发布于"+commentTime+"</span></p><div class='comment-con' comment_id="+msg.comment_id+">"+msg.comment+"</div></div></div></div>";
						$(".comment-area").prepend(comment_html);
					}else{
						$(".comment-area .health-men:last-child").remove();
						comment_html = "<div class='health-men' openid="+openid_val+" comment_id="+msg.comment_id+"><div class='info'><img src="+msg.avatar+" openid="+openid_val+" at_openid="+ msg.at_openid +"><div class='click_pinlun' onclick='pinlun(this)'><p class='name'><span class='comment-name' comment_id="+msg.comment_id+">"+msg.nickname+"</span>"+
										"<span class='comment-time'>发布于"+commentTime+"</span></p><div class='comment-con' comment_id="+msg.comment_id+">"+msg.comment+"</div></div></div></div>";
						$(".comment-area").prepend(comment_html);
					}
			}else{
				//回复
				//里面的拼接的字段后续要替换成请求返回的数据

					if(comment_length<3){
						comment_html = "<div class='health-men' openid="+msg.at_openid+" comment_id="+msg.comment_id+"><div class='info'><img src="+msg.avatar+" openid="+openid_val+" at_openid="+ msg.at_openid +"><div class='click_pinlun' onclick='pinlun(this)'><p class='name'><span class='comment-name' comment_id="+msg.comment_id+">"+msg.nickname+"</span>"+
										"<span class='comment-time'>发布于"+commentTime+"</span></p><div class='comment-con' comment_id="+msg.comment_id+"><span class='at_nickname'>"+msg.nickname+"</span>回复:<span>"+msg.at_nickname+"</span>"+msg.comment+"</div></div></div></div>";
						$(".comment-area").prepend(comment_html);
					}else{
						$(".comment-area .health-men:last-child").remove();
						comment_html = "<div class='health-men' openid="+msg.at_openid+" comment_id="+msg.comment_id+"><div class='info'><img src="+msg.avatar+" openid="+openid_val+" at_openid="+ msg.at_openid +"><div class='click_pinlun' onclick='pinlun(this)'><p class='name'><span class='comment-name' comment_id="+msg.comment_id+">"+msg.nickname+"</span>"+
										"<span class='comment-time'>发布于"+commentTime+"</span></p><div class='comment-con' comment_id="+msg.comment_id+"><span class='at_nickname'>"+msg.nickname+"</span>回复:<span>"+msg.at_nickname+"</span>"+msg.comment+"</div></div></div></div>";
						$(".comment-area").prepend(comment_html);
					}
			}
			
		}else{
			if( ua == "ios" ){
				window.webkit.messageHandlers.mihaiapp.postMessage({login:""});
			}else if ( ua == "android"){
				window.JsInterface.login("comment");
			}
			
		}
		
	}
	//页面刷新的方法
	function reloadFun(){
		var url = "<?php  echo mobile_url('article', array('op'=>'ajax_articleComment','id'=>$_GP['id']));?>";
		var ua = browserFun();
		$.post(url,{},function(data_msg){
			var obj = data_msg.message;
			$(".comment-area").html(' ');
			for(var i=0 ; i<obj.length;i++){
				var data = obj[i];
				var at_openid = isNullVal(data.at_openid);
				var face = data.avatar;
				if(face == '' || face == null){
					face = "<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/userface2.png";
				}

				if(at_openid == "true" ){
					comment_html = "<div class='health-men' openid="+data.openid+" comment_id="+data.comment_id+">" +
										"<div class='info'><img src="+face+" openid="+data.openid+" at_openid="+ data.at_openid +">" +
											"<div class='click_pinlun' onclick='pinlun(this)'><p class='name'><span class='comment-name' comment_id="+data.comment_id+">"+data.nickname+"</span>"+
												"<span class='comment-time'>发布于"+ Stringtotime(data.createtime) +"</span>" +
											"</p>" +
											"<div class='comment-con'>"+data.comment+"</div>" +
										"</div></div>" +
								 "</div>";
					$(".comment-area").append(comment_html);
				}else{
					comment_html = "<div class='health-men' openid="+data.openid+" comment_id="+data.comment_id+"><div class='info'><img src="+face+" openid="+data.openid+" at_openid="+ data.at_openid +"><div class='click_pinlun' onclick='pinlun(this)'><p class='name '><span class='comment-name' comment_id="+data.comment_id+">"+data.nickname+"</span>"+
						"<span class='comment-time'>发布于"+ Stringtotime(data.createtime) +"</span></p><div class='comment-con'><span class='nickname'>"+data.nickname+"</span>回复:<span>"+data.reply_nickname+"</span>"+data.comment+"</div></div></div></div>";
					$(".comment-area").append(comment_html);
				}
			}
			if( ua == "ios" ){
				window.webkit.messageHandlers.mihaiapp.postMessage({comment_subtract_one:""});
			}
		},'json')
	}

	//点击头像传对应的openid给app
	$(document).on('click','.comment-area .info img',function(){
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
	function pinlun(obj){
		var ua = browserFun();
		var at_nickname = $(obj).closest('.info').find(".comment-name").text();
		var at_openid   = $(obj).closest('.info').find('img').attr("openid");
		var sel_openid  = $(obj).closest('.info').find('img').attr("openid");
		var comment_id = $(obj).closest('.health-men').attr("comment_id");
		var obj = {};
		var obj2 = {};
			obj.article_id = articleid_val;
			obj.at_openid = at_openid;
			obj.at_nickname = at_nickname;
			obj2.feedback = obj;
		if( openid_val == ''){
			//登录
			if( ua == "ios" ){
				window.webkit.messageHandlers.mihaiapp.postMessage({follow:""});
			}else if( ua=="android" ){
				window.JsInterface.login("isLogin");
			}
		}else if( openid_val == sel_openid ){
			//删除
			if( ua == "ios" ){
				if( confirm("确定删除评论？") ){
					var url = "<?php  echo mobile_url('article', array('op'=>'del_comment'));?>";
					$.post(url,{comment_id:comment_id,table:'article'},function(data){
						tip(data.message,'1');
						if(data.errno == 200){
							//what to do
							reloadFun();
						}
					},'json');
				}else{
					return;
				}
				
			}else if( ua=="android" ){
				var obj3 = {commentId:comment_id};
				var jsonString = JSON.stringify(obj3);
				window.JsInterface.delete(jsonString);
			}
		}else{
		//新增评论或者回复
			if( ua == "ios" ){
				window.webkit.messageHandlers.mihaiapp.postMessage(obj2);
			}else if( ua=="android" ){
				var jsonString = JSON.stringify(obj);
				window.JsInterface.comment(jsonString);
			}
		}
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


	//点击立即下载，调用下载APP的方法
	$("#downapp p").on("click",function(){
		$("#downapp").hide();
		$(".iframe").hide();
		appDownLoad("<?php echo create_url('mobile', array('name'=>'shopwap','do'=>'appdown','op'=>'get_appversion'));?>");
	})	

	$(".wap_guanzhu").click(function(){
		tipUserToDown("<?php echo create_url('mobile', array('name'=>'shopwap','do'=>'appdown','op'=>'get_appversion'));?>",1);
	})

	function tip(msg,autoClose){
		var div = $("#poptip");
		var content =$("#poptip_content");
		if(div.length<=0){
			div = $("<div id='poptip'></div>").appendTo(document.body);
			content =$("<div id='poptip_content'>" + msg + "</div>").appendTo(document.body);
		}
		else{
			content.html(msg);
			content.show(); div.show();
		}
		if(autoClose) {
			setTimeout(function(){
				content.fadeOut(500);
				div.fadeOut(500);

			},1000);
		}
	}
	function tip_close(){
		$("#poptip").fadeOut(500);
		$("#poptip_content").fadeOut(500);
	}
	//将后台返回的时间戳格式化为时间格式
	function Stringtotime(time){
		time = time*1000;
		var datetime = new Date();
		datetime.setTime(time);
		var year = datetime.getFullYear();
		var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1;
		var date = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate();
		var hour = datetime.getHours() < 10 ? "0" + datetime.getHours() : datetime.getHours();
		var minute = datetime.getMinutes() < 10 ? "0" + datetime.getMinutes() : datetime.getMinutes();
		var sec = datetime.getSeconds() < 10 ? "0" + datetime.getSeconds() :  datetime.getSeconds();
		return year + "/" + month + "/" + date + " &nbsp " + hour + ":" + minute + "";
	}
	
	//健康文化图片大小设置,为避免获取的宽度是0 ，先绑定一个load事件	
	$(function(){
		console.log("进来了")	
		var _maxw = $(".health-detail .content").width(); //获取最外层的宽度
		//如果用onload,图片有缓存或，就不会触发了
		$(".health-detail .content img").one("load",function(){
			
		}).each(function(){				
				var _imgw = $(this).width();  //获取图片的宽度
				if( !$(this).parent().hasClass("item-pic")){    //过滤掉商品的图片
					if( _imgw >= _maxw){									
						$(this).attr("width","100%");						
					}
				}
		})
	})
</script>	
<?php if($notApp){ ?>
<?php include themePage('footer'); ?>
<?php } ?>
