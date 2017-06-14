<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>搜索的标题</title>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" type="text/css" href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/swiper-3.3.1.min.css"/>
	<link href="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjcommon.css" rel="stylesheet"  type="text/css" />
	<link rel='stylesheet' type='text/css' href='<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/css/bjdetail.css' />
	<link rel="shortcut icon" href="favicon.ico"/>
	<script type="text/javascript" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/script/jquery-1.7.2.min.js"></script>

</head>

<style type="text/css">
html,body{
	background: #fff;
}
.WX_search_txt, .hd_search_txt {
    background-image: none!important;
    border: 1px solid #ddd;
    float: none;
    width: 98%;
    margin:5px auto;
    padding-left: 5px;
}
.search-img{
	position: absolute;
    right: 2%;
    top: 0;
    width: 30px;
    height: 30px;
    text-align: center;
}
.search-img img{
    width: 18px;
    margin-top: 5px;
}
input::-webkit-input-placeholder{
	color: #d0d0d0!important;
}
.tab-top {
    line-height: 40px;
    font-size: 16px;
    overflow: hidden;
    border-top: 1px solid #eee;
}
.tab-top li {
    width: 50%;
    float: left;
    box-sizing: border-box;
    text-align: center;
    background: #fafafa;
    border: 1px solid #eee;
    border-top: 0;
}
.tab-top a {
    color: #333;
}
.tab-top li.cur {
    color: #00a06a;
    background: #fff;
    border-color: #fff;
}
.tab-top li.cur a{
    color: #00a06a;
}
.tab_list{
	display: none;
	padding: 0 19px;
    background: #fff;
}
.wx-news-list2 .gzh-box {
    overflow: hidden;
    position: relative;
    padding-bottom: 8px;
}
.wx-news-list2 .gzh-box .img-box {
    float: left;
    margin-right: 10px;
    position: relative;
}
.wx-news-list2 .gzh-box .img-box img {
    border-radius: 32px;
    border: 1px solid #eee;
}
.wx-news-list2 .gzh-box .txt-box {
    overflow: hidden;
    line-height: 21px;
    padding-right: 65px;
}
.wx-news-list2 .gzh-box .txt-box .gzh-tit {
    font-size: 18px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    word-wrap: normal;
    padding-top: 3px;
}
.wx-news-list2 .gzh-box .txt-box .gzh-name {
    font-size: 12px;
    color: #ccc;
    margin-top: -1px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    word-wrap: normal;
}
.wx-news-list2 li {
    border-top: 1px solid #f2f2f2;
    padding: 18px 0 14px;
}
.wx-news-list2 li:first-child {
    border: 0;
}
.wx-news-list2 dl {
    overflow: hidden;
    font-size: 13px;
    color: #888;
    line-height: 21px;
    margin-top: 3px;
}
.wx-news-list2 dl dt {
    float: left;
    width: 72px;
}
.wx-news-list2 dl dd {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    -webkit-box-flex: 1;
    overflow: hidden;
}
.wx-news-list2 dl dd a {
    display: block;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    word-wrap: normal;
    padding-right: 60px;
    position: relative;
}
.article-title {
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    height: 50px;
    font-weight: bold;
    color: #333;
    font-size: 16px;
    font-family: "微软雅黑";
}
.wx-news-list2 dl dd a span {
    position: absolute;
    right: 0;
    top: 0;
    font-size: 13px;
    color: #ccc;
}
.mhheadline ul li {
    padding: 4%;
    width: 100%;
    overflow: hidden;
    box-sizing: border-box;
    border-bottom: solid 1px #eee;
}
.mhheadline .new-article-list li {
    position: relative;
    padding: 15px 0;
    border-bottom: none;
}
.new-article-list .list-left {
    width: 35%;
    float: right;
}
.new-article-list .list-right {
    width: 65%;
    float: left;
    padding-right: 2%;
    box-sizing: border-box;
}
.new-article-list .list-left img {
    display: block;
    width: 100%;
    max-width: 100%;
    border-radius: 4px;
}
.new-article-list .article-name {
    position: absolute;
    bottom: 15px;
    left: 0;
    width: 36%;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.new-article-list .article-time {
    position: absolute;
    bottom: 15px;
    left: 37%;
    width: 25%;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    text-align: right;
}
@media only screen and (-webkit-min-device-pixel-ratio:2),only screen and (min-device-pixel-ratio:2) {
 .good-content {
	border: none;
	background-image: -webkit-linear-gradient(90deg,#eee,#eee 50%,transparent 50%);
	background-image: -moz-linear-gradient(90deg,#eee,#eee 50%,transparent 50%);
	background-image: -o-linear-gradient(90deg,#eee,#eee 50%,transparent 50%);
	background-image: linear-gradient(0,#eee,#eee 50%,transparent 50%);
	background-size: 100% 1px;
	background-repeat: no-repeat;
	background-position: bottom
	}
}
a em, em {
    color: #00a06a !important;
}
</style>

<body>
	<div style="position:relative;background: white;">
		<input name="keyword" id="search_word" class="WX_search_txt hd_search_txt_null" placeholder="搜索文章和公众号！" type="search" AUTOCOMPLETE="off" />
		<div class="search-img" onclick="searchFun()"><img src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/icon_ss.png"></div>
	</div>
	<div>
		<ul class="tab-top">
			<li class="cur" tab-class="tab_article" ><a href="javascript:void(0)" >相关文章</a></li>
			<li tab-class="tab_account"><a href="javascript:void(0)" >公众账号</a></li>
		</ul>
	</div>

	<div class="tab_list tab_article mhheadline" style="display:block">
	<!-- 相关文章列表 -->
		<ul class="new-article-list">
			<li class="good-content">
				<div class="list-left">
					<a href="index.php?mod=mobile&amp;op=headline&amp;id=18&amp;name=addon8&amp;do=article"><img src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201702/20170220160658aaa37024062.jpg?x-oss-process=image/resize,m_fixed,h_190,w_250"></a>
				</div>
				<div class="list-right">
					<a href="index.php?mod=mobile&amp;op=headline&amp;id=18&amp;name=addon8&amp;do=article"><div class="article-title"><em>恭喜</em>第一期513位幸运顾客，您将享受全额免单</div></a>
					<div class="article-name">公众号名称，文章作者</div>
					<div class="article-time">1小时前<i class="icon-angle-down" style="margin-left:5px;"></i></div>
				</div>
			</li>
		</ul>
	</div>

	<div class="tab_list tab_account">
	<!-- 公众账号列表 -->
		<ul class="wx-news-list2">
			<li>
				<div class="gzh-box">
					<a href="#">
						<div class="img-box">
							<img height="60" width="60" src="http://hinrc.oss-cn-shanghai.aliyuncs.com/201702/20170215140058a3ee65f3fb6.jpg" >
						</div>
						<div class="txt-box">
							<p class="gzh-tit"><em>改车</em>汇</p>
							<p class="gzh-name">微信号：gh_e653e0fbc280</p>
						</div>
					</a>
				</div>
				<dl>
					<dt>功能介绍：</dt>
					<dd>爱<em>车</em><em>改</em>装分享</dd>
				</dl>
				<dl>
					<dt>微信认证：</dt>
					<dd>昆明市西山区星宇汽车装饰用品经营部</dd>
				</dl>
				<dl>
					<dt>最近文章：</dt>
					<dd>
						<a  href="#">H6拆小屏导航换10.2寸安卓大屏<span>2016-6-21</span></a>
					</dd>
				</dl>
			</li>
		</ul>
	</div>
	<!--用来存当前page-->
	<!-- 相关文章加载更多 -->
	<input type="hidden" value="2" id="page0"/>
	<div class="ajax_next_page ajax_next_page0">
		<img class="jiazai" src="<?php echo WEBSITE_ROOT . 'themes/wap/__RESOURCE__'; ?>/recouse/images/ajax-loader.gif"/>
		正在加载
	</div>
	<!-- 公众号加载更多 -->
	<input type="hidden" value="2" id="page1"/>
	<div class="ajax_next_page ajax_next_page1">
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
		});
		tabChoose();
	})
	function tabChoose(){
		//tab切换
		var tab_index = 0;
		$("body").on("click",".tab-top li",function(){
			tab_index = $(this).index();
			var tab_class = $(this).attr("tab-class");
			$(".tab-top li").removeClass("cur");
			$(this).addClass("cur");
			$(".tab_list").hide();
			$("."+tab_class).show();
		});
	}
	//搜索的事件
	function searchFun(){

	}
	function Refresh(){
		var more_index = $(".tab-top .cur").index();//获取当前tab选中的索引值0代表相关文章，1代表公众号
		if( index == 1){
			$(".ajax_next_page"+more_index+"").show();
			index = 0; //关闭开关
			var page = $("#page").val(); //第一次传的是2

			$.post("", {'page' : page,'nextpage' : 'ajax','op':'headline','type':more_index}, function(s){
				if(s.errno != 200){
					//如果没有数据
					$(".ajax_next_page"+more_index+"").hide();
				}else{
					$("#page").val(++page);
					var art_data = s.message;
					for(var i = 0;i < art_data.length;i++){
						//循环拼接 html下一页数据
						Load(art_data[i],more_index);
					}
					index = 1;  //加载完后重新打开开关
					$(".ajax_next_page"+more_index+"").hide();
				}
			}, 'json');

		}
	}

	//获取URL参数
	function request(paras,url)
	{
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

function Load(art_data,type){	
	//type值0代表相关文章，1代表公众号
		//觅海头条的append
		if( type == 0 ){
			var url = "<?php echo mobile_url('article',array('op'=>'headline'));?>";
			url = url + "&id="+art_data.headline_id;
			var piclist = art_data.pic;
			var perpic = piclist.split(";"); //字符串截取，成为数组
			var picurl = perpic[0];
			var li ='<li class="good-content">'+
						'<a href="'+url+'">'+
							'<div class="list-left">'+
								'<img src="'+ picurl +'"/>'+
							'</div>'+
							'<div class="list-right">'+
								'<div class="article-title">'+art_data.title+'</div>'+
								'<div class="article-name">'+art_data.name+'</div>'+
								'<div class="article-time">'+art_data.time+'<i class="icon-angle-down" style="margin-left:5px;"></i></div>'+
							'</div>'+
						'</a>'+
					'</li>';
			$(".new-article-list").append(li);
		}else if(type==1){
			var url = "<?php echo mobile_url('article',array('op'=>'headline'));?>";
			url = url + "&id="+art_data.headline_id;
			var piclist = art_data.pic;
			var perpic = piclist.split(";"); //字符串截取，成为数组
			var picurl = perpic[0];
			var li ='<li>'+
				'<div class="gzh-box">'+
					'<a href="#">'+
						'<div class="img-box">'+
							'<img height="60" width="60" src="'+ picurl +'" >'+
						'</div>'+
						'<div class="txt-box">'+
							'<p class="gzh-tit">'+art_data.gzh_tit+'</p>'+
							'<p class="gzh-name">'+ art_data.gzh_name +'</p>'+
						'</div>'+
					'</a>'+
				'</div>'+
				'<dl>'+
					'<dt>功能介绍：</dt>'+
					'<dd>'+art_data.gzh_name+'</dd>'+
				'</dl>'+
				'<dl>'+
					'<dt>微信认证：</dt>'+
					'<dd>'+art_data.address+'</dd>'+
				'</dl>'+
				'<dl>'+
					'<dt>最近文章：</dt>'+
					'<dd>'+
						'<a href="'+art_data.href+'">'+art_data.title+'<span>'+art_data.time+'</span></a>'+
					'</dd>'+
				'</dl>'+
			'</li>';
			$(".wx-news-list2").append(li);
		}
		
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
