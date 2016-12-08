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
<title><?php echo $cfg['shop_title']; ?></title>
<meta charset="utf-8">
 <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>
    </head>
<body>
	
    <?php if(empty($_GP['is_app'])){  ?>
	<div style="height: 20px;padding: 7px 15px;background: #f8f8f8;text-align: center;font-weight: bolder;">
        <a href="javascript:;" class="return"><img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/openshop/images/return.png" width="8px" height="13px" style="float: left;margin-top: 2px;"></a>
        <span style="display: inline-block;width: 90%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><?php echo $article['title'];?></span>
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
</script>

</html>
