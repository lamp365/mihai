<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <style>
        *{
            padding: 0;
            margin: 0;
        }
        .content img{
            display: block;
            width: 100%;
        }
        p{
            line-height:24px;
        }
    </style>
<title><?php echo $article['title'];?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjdetail.css" rel="stylesheet"  type="text/css" />
    <script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>
    <script>
        //is_login 0或者1 0未登录 1已登录
        var is_login = "<?php echo checkIsLogin();?>";
        var get_ajax_url = "";
        <?php if($_GP['is_app']){  ?>
            var is_app   = true;
        <?php }else{ ?>
            //wap的判断是否登录，可以不做判断，请求服务器的时候会判断。
            var is_app   = false;
        <?php } ?>

        function browserFun(){
            var ua = navigator.userAgent.toLowerCase();
            if(navigator.userAgent.match(/(iPhone|iPod|iPad);?/i)){
                return 'ios';
            }
            if(navigator.userAgent.match(/android/i)){
                return 'android';
            }
        }

        function isLogin(msg){
            if(is_app){
                //处理app登录
                var ua = browserFun();
                //is_login  0未登录 1已登录

                if( ua == "ios" ){
                    window.webkit.messageHandlers.mihaiapp.postMessage({login:""});
                    var msgObj = JSON.parse(msg);
                    if( msgObj.openid !=""){
                        is_login = 1;
                        one_honus();
                    }else{
                        return;
                    }
                }else if( ua=="android" ){
                    window.JsInterface.login("get_ajax_android");
                }

            }else{
                tip('请先登录！');
                setTimeout(function(){
                    var url = "<?php echo mobile_url('login',array('name'=>'shopwap'));?>";
                    window.location.href = url;
                },2000)
            }
        }
        function get_ajax_android(msg){
            var msgObj = JSON.parse(msg);
                if( msgObj.openid !=""){
                    is_login = 1;
                    one_honus();
                }else{
                    return;
                }
        }
        function one_honus(msg){
            $(".one_honus a").click(function(){
                var _this = $(this);
                get_ajax_url = _this.attr("data-url");
                if( is_login == 0){
                    isLogin();
                }else{
                    $.get(get_ajax_url,{},function(data){
                        if(data.errno==200){
                            tip(data.message,"autoClose")
                        }else{
                            tip(data.message,"autoClose")
                        }
                    },"json");
                }
            });
        }
        one_honus();
    </script>
</head>
<body>
	
    <?php if(empty($_GP['is_app'])){  ?>
	<div style="height: 20px;padding: 7px 15px;background: #f8f8f8;text-align: center;font-weight: bolder;">
        <a href="javascript:;" class="return"><img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/openshop/images/return.png" width="8px" height="13px" style="float: left;margin-top: 2px;"></a>
        <span style="display: inline-block;width: 90%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"></span>
    </div>
    <div style="height: 2px;background: #EEEEEE;margin-bottom: 1px;"></div>
    <?php } ?>
    <div class="content">
        <?php echo $article['content'];?>
    </div>

</body>
<script>
	$(".return").click(function(){
        //没有上一页就返回首页
        if(document.referrer.length == 0){
			  window.location.href = "index.php";
		}else{				
			 var newHref = document.referrer;
			 $(".return").attr("href",newHref);							
		}
		
    })
    //自定义页面有时候是一个宣传页面有时候是一个内容
    //宣传页不需要间隙但是内容需要间隙
    var obj = document.getElementsByTagName("p")[0];
    $(obj).css('margin-top','8px');

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

</script>

</html>
