<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $cfg['shop_title']; ?></title>
<meta charset="utf-8">
<link rel="shortcut icon" href="favicon.ico"/>
<link type="text/css" href="<?php echo WEBSITE_ROOT . 'themes/' . $theme . '/__RESOURCE__'; ?>/recouse/bootstrap3/css/bootstrap.min.css" rel="stylesheet" />
<link href="<?php echo WEBSITE_ROOT . 'themes/' . $theme . '/__RESOURCE__'; ?>/recouse/css/bjcommon.css" rel="stylesheet"	type="text/css" />
<script type="text/javascript"	src="<?php echo WEBSITE_ROOT . 'themes/' . $theme . '/__RESOURCE__'; ?>/recouse/js/jquery-1.11.0.js"></script>
<script type="text/javascript"	src="<?php echo WEBSITE_ROOT . 'themes/' . $theme . '/__RESOURCE__'; ?>/recouse/js/jquery.lazyload.min.js"></script>
<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/' . $theme . '/__RESOURCE__'; ?>/recouse/js/fbi.js"></script>
<link rel='stylesheet' type='text/css' href='<?php echo WEBSITE_ROOT . 'themes/' . $theme . '/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
</head>
<style type="text/css">
.article .title{
	float:left;
	width:auto;
	padding:5px;
	color:#002050;
}
.child-div{
	display: none;
	font-size: 14px;
	color: #333;
	line-height: 35px;
}
.child-div div:hover a{color: #da4a62;}
.child-div div.li_active a{
	color: #da4a62;
}
.right-icon{
	position: absolute;
	display: block;
    right: 18px;
    top: 14px;
	background: url(<?php echo WEBSITE_ROOT . 'themes/' . $theme . '/__RESOURCE__'; ?>/recouse/images/GrayRight.png) no-repeat center;
	width: 18px;
	height: 18px;
	background-size: contain;
}
.down-icon{
	background: url(<?php echo WEBSITE_ROOT . 'themes/' . $theme . '/__RESOURCE__'; ?>/recouse/images/GrayDown.png) no-repeat center;
	background-size: contain;
}
	.article-right p{
		line-height: 24px;
	}
	.recom_fix{
		position: fixed;right:355px;top:400px;
	}
	.relate_fix{
		position: fixed;right:355px;top:0px;
	}
	.content dl dd img{		
		margin: 10px 0;
	}
	/*为了避免后台导入的文章都是图片堆砌而成的，取消图片之间的空隙*/
	.content dl dd table td img{
		margin: 0;
		display: block;
	}
	/*同上*/
	.content dl dd p img{
		margin: 0;
		display: block;
	}
	.content dl dd p{
		font-size: 16px;
		line-height: 1.875;
	}
	.content dl dd .item{
		background: #f5f5f5 none repeat scroll 0 0;
		clear: none !important;
		display: block;
		margin: 5px 0;
		padding: 20px;
		position: relative;
		text-decoration: none;
		width: 470px;
		cursor: pointer;
	}
	.content dl dd .item:hover{
		background: #f9f9f9;
	}
	.content dl dd .item .item-pic{
		float: left;
	    height: 140px;
	    margin-right: 20px;
	    overflow: hidden;
	    width: 140px;
	}
	.content dl dd .item .item-pic img{
		width: 140px;
		height: 140px;
		border: none;
	}
	.content dl dd .item .item-info{
		 height: 140px;
	    overflow: hidden;
	    position: relative;
	}
	.content dl dd .item .item-info .item-title{
		overflow: hidden;
		height: 59px;
		display: inline-block;
	}
	.content dl dd .item .item-info .item-btn{
		border: 1px solid #da0d15;
	    border-radius: 4px;
	    bottom: 5px;
	    color: #da0d15;
	    height: 30px;
	    line-height: 30px;
	    position: absolute;
	    right: 0;
	    text-align: center;
	    width: 100px;
	}
	.content dl dd .item .item-info .item-price{
		 bottom: 5px;	    
	    left: 0;
	    position: absolute;
	}
	.content dl dd .item .item-info .item-price .price-new{
		font-size: 16px;
		color: #f50;
	}
	.content dl dd .item .item-info .item-price .price-old{
		margin-left: 10px;
	}
	.content dl dd .item .item-info .item-price .price-new strong{
		font-size: 24px;
	}
</style>
<body>
    <?php  include page('h'); ?>	
    <?php if ( $article['state'] == 4 ){ ?>
	<!--	自定义页面	-->
	    <div class="viewport" style="width:100%;">
		     <div class="main2" style="width:1100px;position:relative;margin:0 auto;">
			     <div class="mains" style="width:1920px;overflow:hidden;position:absolute;left:50%;top:0;margin-left:-960px;">
                  <?php echo $article['content']; ?>
				 </div>
			 </div>
		</div>
		<script>
             $(document).ready(function(){
                   $('.main2').height($('.mains').height());
             });
		</script>
	<?php }else if ($article['state'] == 1){ ?>
		<!--	底部展示的文章	-->
	<input type="hidden" class="hide_article" value='<?php echo $json_artile_tree;?>'>
	<div class="viewport article">
		<div class="viewport" style="width:1190px;">
			<div class="re title"><img src="<?php echo WEBSITE_ROOT . 'themes/' . $theme . '/__RESOURCE__'; ?>/recouse/images/prompt_icon.png" style="margin-bottom:2px;"> 温馨提示： 觅海不会以订单异常、系统升级为由，要求您提供银行卡号、密码等信息，或让您支付额外费用，请谨防钓鱼链接和诈骗电话！ </div>
			<div class="re" style="width:100%;border:none;">
	            <div class="content article-right">
	                  <dl>
	                       <dt><?php echo $article['title']; ?></dt>
						   <dd><?php echo $article['content']; ?></dd>
					  </dl>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function(){
			function initFun(){
				var returnId = request("id");
				var hide_article = JSON.parse($(".hide_article").val());
				var article_name ;
				var chili_div_name;
				var init_parent_name,
					init_child_name,
					init_child_id;
				$.each(hide_article,function(index,element){
					$.each(element.son,function(i,e){
						if( i == returnId ){
							init_parent_name = element.name;
							init_child_name = e;
							init_child_id = i;
						}
					})
				});
				$(".article-name").each(function(index,ele){
					article_name = $(this).text();
					if( article_name == init_parent_name){
						$(ele).siblings(".right-icon").addClass("down-icon");
						$(ele).siblings(".child-div").slideDown();
					}
				});
				$(".child-div div").each(function(){
					chili_div_name = $(this).find("a").text();
					if( chili_div_name == init_child_name){
						$(this).addClass("li_active");
					}
				})
			}
			initFun();

			$(".article-left li").on("click",function(event){
				$(this).siblings("li").find(".right-icon").removeClass("down-icon");
				$(this).find(".right-icon").toggleClass("down-icon");
				$(this).siblings("li").find(".child-div").slideUp("slow");
				$(this).find(".child-div").slideToggle("slow");
			});
			function request(paras){
				var url = location.href;
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

		});
	</script>
	<?php }else if($article['state'] == 6){ ?>
	<!--  健康系列文章	-->
		<div class="hr"></div>
		<div class="viewport">			
			<!--左侧的文章-->
			<div class="re" style="width:833px;border:none;float: left;">
				<div class="content" style="border: solid 1px #eee; padding: 20px;">
					<dl>
						<dt style="padding: 0;"><?php echo $article['title']; ?> <span class="time"><?php echo date("Y-m-d H:i",$article['createtime']); ?></span></dt>
						<dd style="padding: 0;">
							<?php echo $article['content']; ?>							
						</dd>
					</dl>
				</div>
			</div>
			<!--右侧的相关产品,显示三个就好了-->			
				<div class="shopinfo relate" style="width: 333px;float: right;margin-top: 15px;border: solid 1px gainsboro;border-left: none;border-right: none;height: auto;overflow: hidden;">
		            <div id="cshopBox" class="proinfo-side" style="height: auto;overflow: hidden;">
		                <div id="seeAgainTile" class="customer-rec" style="height: auto;overflow: hidden;">
		                    <div class="customer-rec-title" style="width: 331px;background: none;position: relative;border-bottom: solid 1px #eee;border-right: solid 1px gainsboro;">		                    		                    		
	                    		<span style="height: 2px;width: 50px;background: #eee;display: inline-block;position: absolute;left: 85px;top: 20px;"></span>
	                    		<span style="position: absolute;left: 145px;top: 10px;">看了又看</span>
	                    		<span style="height: 2px;width: 50px;background: #eee;display: inline-block;position: absolute;right: 70px;top: 20px;"></span>	                    		                    	
		                    </div>
							<div id="seeAndsee" class="customer-rec-list" style="width: 100%;">
								<ul style="border-bottom: solid 1px gainsboro;">
									<?php if(!empty($tuijian_shop)){
										foreach($tuijian_shop as $shop){
											$price = $shop['marketprice'];
											if(time()<$shop['timeend']){
												$price = $shop['timeprice'];
											}

									?>
				                    <li style="padding-left: 5px;border-right: solid 1px gainsboro;">
				                        <!--产品图片-->
				                        <a target="_blank" title="<?php echo $shop['title']; ?>" href="<?php  echo create_url('mobile', array('id' => $shop['id'],'op'=>'dish','name'=>'shopwap','do'=>'detail'))?>" target="_blank" class="product-img">
										    <img  alt="<?php echo $shop['title']; ?>" data-original="<?php echo download_pic($shop['thumb'],100,100); ?>" class="lazy" src="images/loading.gif" height="80" />
				                        </a>
				                        <p style="width: 210px;">	           
					                        <!--产品名称-->
					                        <a href="<?php  echo create_url('mobile', array('id' => $shop['id'],'op'=>'dish','name'=>'shopwap','do'=>'detail'))?>" title="<?php echo $shop['title']; ?>"  target="_blank" class="title">
					                        	<?php echo $shop['title'];?>
					                        </a>
							               <!--产品价格-->
							                <span class="price">
						                		<i>¥</i>
						                		<em><?php echo $price;?></em>
						                	</span>
											<span class="product_price" style="color: #B2B2B2;font-size: 14px;">
						                		<i>¥</i>
						                		<del><?php echo $shop['productprice'];?></del>
						                	</span>
				                        </p>
									</li>
									<?php }} ?>
								</ul>
							</div>		
						</div>
					</div>
					
					 <!--推荐文章，显示四篇-->
				<div class="shopinfo recommend" style="width:333px;float: right;margin-top: 42px;border: none;height: auto;overflow: hidden;">
					<div class="proinfo-side" style="height: auto;overflow: hidden;">
						<div id="seeAgainTile" class="customer-rec" style="height: auto;overflow: hidden;border-left: none;">
		                    <div class="customer-rec-title" style="width: 100%;line-height: 43px;font-weight: bold;font-size:16px;color: black;background: none;position: relative;border-bottom: solid 1px #eee;">	                   		                    			                  		
	                    		精彩推荐
		                    </div>
							<div id="seeAndsee" class="customer-rec-list" style="width: 100%;">
								<ul>
									<?php if(!empty($tuijian_article)){ foreach($tuijian_article as $article){ ?>
				                    <li style="padding-left: 5px;">
				                       <!--文章图片-->
				                        <a  style="width: 130px;height: 85px;" title="<?php echo $article['title']; ?>" href="<?php  echo mobile_url('article', array('id' => $article['id']))?>" target="_blank" class="product-img">
										    <img style="width: 130px;height: 85px;" alt="<?php echo $article['title']; ?>" data-original="<?php echo download_pic($article['thumb'],130,85); ?>" class="lazy" src="images/loading.gif" height="80" />
				                        </a>
				                        <p style="margin-top: 5px;width: 170px;margin-left: 5px;">
					                        <a style="height: auto;margin-top: 5px;" href="<?php  echo mobile_url('article', array('id' => $article['id']))?>" class="title">
					                        	<!--文章标题-->
					                        	<span style="font-size: 14px;font-weight: bold;overflow: hidden;text-overflow: width: 100%;height:52px;display: inline-block;"><?php echo $article['title'];?></span>
					                        </a>
											<!--文章内容-->
											<span style="display: block;text-align: left">
												<img style="width: 15px;display: inline-block;vertical-align: middle" src="<?php echo WEBSITE_ROOT . 'themes/default/__RESOURCE__'; ?>/recouse/images/seenum.png">
												<span style="font-size: 12px;"><?php echo $article['readcount'];?></span>
											</span>
				                        </p>
									</li>
									<?php }} ?>
								</ul>
							</div>
		
						</div>
					</div>
				</div>				
			</div>				
			
		</div>
		<script>
			$(".content dl dd .item").click(function(){
				var url = $(this).data('url');
				console.log(url);
				if(url.length >0){
					window.open(url);
				}
			})
		</script>
	<?php }else{ ?>
	<div class="hr"></div>
		<div class="viewport">
	    <div class="viewport" style="width:1190px;">
	    <div class="re" style="float:left;width:auto;padding:5px 15px;background:#F37384;color:#fff;"><i class="icon-warning-sign"></i>温馨提示： 觅海不会以订单异常、系统升级为由，要求您提供银行卡号、密码等信息，或让您支付额外费用，请谨防钓鱼链接和诈骗电话！ </div>
		<div class="re" style="width:100%;border:none;">
            <div class="content">
                  <dl>
                       <dt><?php echo $article['title']; ?></dt>
					   <dd><?php echo $article['content']; ?></dd>
				  </dl>
			</div>
			</div>
		</div>
	</div>
	<?php } ?>

	 <?php  include page('f'); ?>	
</body>
<script>
	$(document).ready(function(){  
		$('.f_category').mouseenter(function(){
			$('.catitmlst').show();				
		});
		$('.catitmlst').mouseleave(function(){
            $('.catitmlst').hide();
		});
	});
	
//	$(window).scroll(function(){		
//		if($(document).scrollTop() > 300){
//			$(".recommend").addClass("recom_fix");
//			$(".relate").addClass("relate_fix");
//		}else{
//			$(".recommend").removeClass("recom_fix");
//			$(".relate").removeClass("relate_fix");
//		}
//	})

	//健康文化图片大小设置,为避免获取的宽度是0 ，先绑定一个load事件	
	$(function(){
		var _maxw = $(".content").width(); //获取最外层的宽度
		//如果用onload,图片有缓存或，就不会触发了
		$(".content img").one("load",function(){
			
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
</html>
