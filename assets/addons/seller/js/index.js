//by lin zheng yuan
//配置左侧导航
layui.use(['element', 'layer'], function() {
	var element = layui.element(),
		$ = layui.jquery,
		layer = layui.layer;
	
	//iframe自适应
	function iframeResize(){
		var $content = $('.admin-nav-card .layui-tab-content');
		$content.height($(this).height() - 147);
		$content.find('iframe').each(function() {
			$(this).height($content.height());
		});
	}
	$(window).on('resize', function() {
		iframeResize();
	}).resize();
	//左侧导航点击
	element.on('nav(side)', function(elem){
		var title = $(elem).find('.left-nav-div').html();
		var iframe_i = '<i class="layui-icon layui-unselect layui-tab-close" data-id="1">&#x1006;</i>';//关闭小图标
		var url = $(elem).find('.left-nav-div').attr("data-url");//获取当前链接
		var layid = $(elem).find('.left-nav-div').attr("lay-id");//获取当前的layid
		newTab(title,iframe_i,layid,url);
	});
	//店铺入驻
	$(".add-shop-one").on("click",function(){
		var shop_title = "店铺入驻";
		var shop_iframe_i = '<i class="layui-icon layui-unselect layui-tab-close" data-id="1">&#x1006;</i>';//关闭小图标
		var url = $(this).attr("data-url");//获取当前链接
		var layid = 9999 //暂时写死的layid
		newTab(shop_title,shop_iframe_i,layid,url);
	});
	//新开tab页title标题、iframe_i小图标、layid、url新开的tab链接地址
	function newTab(title,iframe_i,layid,url){
		// 定义一个开关
		var flag = true;
		/*遍历右侧iframe的导航，通过判断layid是否存在，设置开关*/
		$(".layui-body .layui-tab-title li").each(function(index,e){
			var li_layid = $(e).attr("lay-id");
			if( layid == li_layid ){
				flag = false;
			}
		})
		/*当flag为true打开新的tab页*/
		if ( flag == true ){
			element.tabAdd('admin-tab', {
	        	title: title+iframe_i, 
	        	content: '<iframe data-id="'+layid+'" src="'+url+'"></iframe>',
	        	id: layid 
	      	});
		}
		//iframe自适应
		iframeResize();
		//刷新iframe的内容
		$('iframe[data-id='+layid+']').attr("src",url);
		/*切换到layid对应的tab*/
		element.tabChange('admin-tab', layid);
	}
	//tab的关闭按钮，关闭对应的tab
	$("body").on("click",".layui-tab-close",function(){
		var layids = $(this).parents("li").attr("lay-id");
		element.tabDelete('admin-tab', layids);
	})
	/*左侧导航的收缩功能*/
	$('.admin-side-toggle').on('click', function() {
		var sideWidth = $('#admin-side').width();
		if(sideWidth === 200) {
			$('#admin-body').animate({
				left: '0'
			}); //admin-footer
			$('#admin-footer').animate({
				left: '0'
			});
			$('#admin-side').animate({
				width: '0'
			});
		} else {
			$('#admin-body').animate({
				left: '200px'
			});
			$('#admin-footer').animate({
				left: '200px'
			});
			$('#admin-side').animate({
				width: '200px'
			});
		}
	});
	$(".left-nav-div").hover(function(){
		var left_nav_url = $(this).attr("data-url");
		$(".left-nav-url").stop().fadeIn().text(left_nav_url);
	},function(){
		$(".left-nav-url").fadeOut();
	});
	//手机设备的简单适配
	var treeMobile = $('.site-tree-mobile'),
		shadeMobile = $('.site-mobile-shade');
	treeMobile.on('click', function() {
		$('body').addClass('site-mobile');
	});
	shadeMobile.on('click', function() {
		$('body').removeClass('site-mobile');
	});
});