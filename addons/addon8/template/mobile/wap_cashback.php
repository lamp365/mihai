<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <style>
        *{
            padding: 0;
            margin: 0;
        }
        .content{
            padding: 0 10px;
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
    <!--引入懒加载的js文件-->
    <script type="text/javascript"	src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/js/jquery.lazyload.min.js"></script>
    <script>
		//懒加载的初始化
		$(function(){
			$("img.lazy").lazyload({
				 threshold : 50,
				 failure_limit : 10,
				 effect : "fadeIn"
			});
		})	

    </script>
</head>
<body>


<div style="height: 20px;padding: 7px 15px;background: #f8f8f8;text-align: center;font-weight: bolder;">
    <a href="javascript:;" class="return"><img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/openshop/images/return.png" width="8px" height="13px" style="float: left;margin-top: 2px;"></a>
    <span style="display: inline-block;width: 90%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"></span>
</div>
<div style="height: 2px;background: #EEEEEE;margin-bottom: 1px;"></div>

<div class="content">
    <?php echo $article['content'];?>
</div>
<?php include themePage('footer');?>
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
