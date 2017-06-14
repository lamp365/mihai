 var index = 0;
			$(document).ready(function(){
               if ( index == 0)
               {
				$('.f_category').mouseenter(function(){
					$('.catitmlst').show();
				});
				$('.f_category').mouseleave(function(){
					$('.catitmlst').mouseenter(function(){
						$('.catitmlst').show();
					});			
				});
				$('.f_category').mouseleave(function(){
					$('.catitmlst').hide();
				});
				$('.catitmlst').mouseleave(function(){
	                $('.catitmlst').hide();
				});
			   }
			});
timer_length = 200; // Milliseconds
border_opacity = false; // Use opacity on borders of rounded-corner elements? Note: This causes antialiasing issues


// supportsVml() borrowed from http://stackoverflow.com/questions/654112/how-do-you-detect-support-for-vml-or-svg-in-a-browser
function supportsVml() {
	if (typeof supportsVml.supported == "undefined") {
		var a = document.body.appendChild(document.createElement('div'));
		a.innerHTML = '<v:shape id="vml_flag1" adj="1" />';
		var b = a.firstChild;
		b.style.behavior = "url(#default#VML)";
		supportsVml.supported = b ? typeof b.adj == "object": true;
		a.parentNode.removeChild(a);
	}
	return supportsVml.supported
}


// findPos() borrowed from http://www.quirksmode.org/js/findpos.html
function findPos(obj) {
	var curleft = curtop = 0;

	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	}

	return({
		'x': curleft,
		'y': curtop
	});
}

function createBoxShadow(element, vml_parent) {
	var style = element.currentStyle['iecss3-box-shadow'] || element.currentStyle['-moz-box-shadow'] || element.currentStyle['-webkit-box-shadow'] || element.currentStyle['box-shadow'] || '';
	var match = style.match(/^(\d+)px (\d+)px (\d+)px/);
	if (!match) { return(false); }


	var shadow = document.createElement('v:roundrect');
	shadow.userAttrs = {
		'x': parseInt(RegExp.$1 || 0),
		'y': parseInt(RegExp.$2 || 0),
		'radius': parseInt(RegExp.$3 || 0) / 2
	};
	shadow.position_offset = {
		'y': (0 - vml_parent.pos_ieCSS3.y - shadow.userAttrs.radius + shadow.userAttrs.y),
		'x': (0 - vml_parent.pos_ieCSS3.x - shadow.userAttrs.radius + shadow.userAttrs.x)
	};
	shadow.size_offset = {
		'width': 0,
		'height': 0
	};
	shadow.arcsize = element.arcSize +'px';
	shadow.style.display = 'block';
	shadow.style.position = 'absolute';
	shadow.style.top = (element.pos_ieCSS3.y + shadow.position_offset.y) +'px';
	shadow.style.left = (element.pos_ieCSS3.x + shadow.position_offset.x) +'px';
	shadow.style.width = element.offsetWidth +'px';
	shadow.style.height = element.offsetHeight +'px';
	shadow.style.antialias = true;
	shadow.className = 'vml_box_shadow';
	shadow.style.zIndex = element.zIndex - 1;
	shadow.style.filter = 'progid:DXImageTransform.Microsoft.Blur(pixelRadius='+ shadow.userAttrs.radius +',makeShadow=true,shadowOpacity='+ element.opacity +')';

	element.parentNode.appendChild(shadow);
	//element.parentNode.insertBefore(shadow, element.element);

	// For window resizing
	element.vml.push(shadow);

	return(true);
}

function createBorderRect(element, vml_parent) {
	if (isNaN(element.borderRadius)) { return(false); }

	element.style.background = 'transparent';
	element.style.borderColor = 'transparent';

	var rect = document.createElement('v:roundrect');
	rect.position_offset = {
		'y': (0.5 * element.strokeWeight) - vml_parent.pos_ieCSS3.y,
		'x': (0.5 * element.strokeWeight) - vml_parent.pos_ieCSS3.x
	};
	rect.size_offset = {
		'width': 0 - element.strokeWeight,
		'height': 0 - element.strokeWeight
	};
	rect.arcsize = element.arcSize +'px';
	rect.strokeColor = element.strokeColor;
	rect.strokeWeight = element.strokeWeight +'px';
	rect.stroked = element.stroked;
	rect.className = 'vml_border_radius';
	rect.style.display = 'block';
	rect.style.position = 'absolute';
	rect.style.top = (element.pos_ieCSS3.y + rect.position_offset.y) +'px';
	rect.style.left = (element.pos_ieCSS3.x + rect.position_offset.x) +'px';
	rect.style.width = (element.offsetWidth + rect.size_offset.width) +'px';
	rect.style.height = (element.offsetHeight + rect.size_offset.height) +'px';
	rect.style.antialias = true;
	rect.style.zIndex = element.zIndex - 1;

	if (border_opacity && (element.opacity < 1)) {
		rect.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(Opacity='+ parseFloat(element.opacity * 100) +')';
	}

	var fill = document.createElement('v:fill');
	fill.color = element.fillColor;
	fill.src = element.fillSrc;
	fill.className = 'vml_border_radius_fill';
	fill.type = 'tile';
	fill.opacity = element.opacity;

	// Hack: IE6 doesn't support transparent borders, use padding to offset original element
	isIE6 = /msie|MSIE 6/.test(navigator.userAgent);
	if (isIE6 && (element.strokeWeight > 0)) {
		element.style.borderStyle = 'none';
		element.style.paddingTop = parseInt(element.currentStyle.paddingTop || 0) + element.strokeWeight;
		element.style.paddingBottom = parseInt(element.currentStyle.paddingBottom || 0) + element.strokeWeight;
	}

	rect.appendChild(fill);
	element.parentNode.appendChild(rect);
	//element.parentNode.insertBefore(rect, element.element);

	// For window resizing
	element.vml.push(rect);

	return(true);
}

function createTextShadow(element, vml_parent) {
	if (!element.textShadow) { return(false); }

	var match = element.textShadow.match(/^(\d+)px (\d+)px (\d+)px (#?\w+)/);
	if (!match) { return(false); }


	//var shadow = document.createElement('span');
	var shadow = element.cloneNode(true);
	var radius = parseInt(RegExp.$3 || 0);
	shadow.userAttrs = {
		'x': parseInt(RegExp.$1 || 0) - (radius),
		'y': parseInt(RegExp.$2 || 0) - (radius),
		'radius': radius / 2,
		'color': (RegExp.$4 || '#000')
	};
	shadow.position_offset = {
		'y': (0 - vml_parent.pos_ieCSS3.y + shadow.userAttrs.y),
		'x': (0 - vml_parent.pos_ieCSS3.x + shadow.userAttrs.x)
	};
	shadow.size_offset = {
		'width': 0,
		'height': 0
	};
	shadow.style.color = shadow.userAttrs.color;
	shadow.style.position = 'absolute';
	shadow.style.top = (element.pos_ieCSS3.y + shadow.position_offset.y) +'px';
	shadow.style.left = (element.pos_ieCSS3.x + shadow.position_offset.x) +'px';
	shadow.style.antialias = true;
	shadow.style.behavior = null;
	shadow.className = 'ieCSS3_text_shadow';
	shadow.innerHTML = element.innerHTML;
	// For some reason it only looks right with opacity at 75%
	shadow.style.filter = '\
		progid:DXImageTransform.Microsoft.Alpha(Opacity=75)\
		progid:DXImageTransform.Microsoft.Blur(pixelRadius='+ shadow.userAttrs.radius +',makeShadow=false,shadowOpacity=100)\
	';

	var clone = element.cloneNode(true);
	clone.position_offset = {
		'y': (0 - vml_parent.pos_ieCSS3.y),
		'x': (0 - vml_parent.pos_ieCSS3.x)
	};
	clone.size_offset = {
		'width': 0,
		'height': 0
	};
	clone.style.behavior = null;
	clone.style.position = 'absolute';
	clone.style.top = (element.pos_ieCSS3.y + clone.position_offset.y) +'px';
	clone.style.left = (element.pos_ieCSS3.x + clone.position_offset.x) +'px';
	clone.className = 'ieCSS3_text_shadow';


	element.parentNode.appendChild(shadow);
	element.parentNode.appendChild(clone);

	element.style.visibility = 'hidden';

	// For window resizing
	element.vml.push(clone);
	element.vml.push(shadow);

	return(true);
}

function ondocumentready(classID) {
	if (!supportsVml()) { return(false); }

  if (this.className.match(classID)) { return(false); }
	this.className = this.className.concat(' ', classID);

	// Add a namespace for VML (IE8 requires it)
	if (!document.namespaces.v) { document.namespaces.add("v", "urn:schemas-microsoft-com:vml"); }

	// Check to see if we've run once before on this page
	if (typeof(window.ieCSS3) == 'undefined') {
		// Create global ieCSS3 object
		window.ieCSS3 = {
			'vmlified_elements': new Array(),
			'update_timer': setInterval(updatePositionAndSize, timer_length)
		};

		if (typeof(window.onresize) == 'function') { window.ieCSS3.previous_onresize = window.onresize; }

		// Attach window resize event
		window.onresize = updatePositionAndSize;
	}


	// These attrs are for the script and have no meaning to the browser:
	this.borderRadius = parseInt(this.currentStyle['iecss3-border-radius'] ||
	                             this.currentStyle['-moz-border-radius'] ||
	                             this.currentStyle['-webkit-border-radius'] ||
	                             this.currentStyle['border-radius'] ||
	                             this.currentStyle['-khtml-border-radius']);
	this.arcSize = Math.min(this.borderRadius / Math.min(this.offsetWidth, this.offsetHeight), 1);
	this.fillColor = this.currentStyle.backgroundColor;
	this.fillSrc = this.currentStyle.backgroundImage.replace(/^url\("(.+)"\)$/, '$1');
	this.strokeColor = this.currentStyle.borderColor;
	this.strokeWeight = parseInt(this.currentStyle.borderWidth);
	this.stroked = 'true';
	if (isNaN(this.strokeWeight) || (this.strokeWeight == 0)) {
		this.strokeWeight = 0;
		this.strokeColor = fillColor;
		this.stroked = 'false';
	}
	this.opacity = parseFloat(this.currentStyle.opacity || 1);
	this.textShadow = this.currentStyle['text-shadow'];

	this.element.vml = new Array();
	this.zIndex = parseInt(this.currentStyle.zIndex);
	if (isNaN(this.zIndex)) { this.zIndex = 0; }

	// Find which element provides position:relative for the target element (default to BODY)
	vml_parent = this;
	var limit = 100, i = 0;
	do {
		vml_parent = vml_parent.parentElement;
		i++;
		if (i >= limit) { return(false); }
	} while ((typeof(vml_parent) != 'undefined') && (vml_parent.currentStyle.position != 'relative') && (vml_parent.tagName != 'BODY'));

	vml_parent.pos_ieCSS3 = findPos(vml_parent);
	this.pos_ieCSS3 = findPos(this);

	var rv1 = createBoxShadow(this, vml_parent);
	var rv2 = createBorderRect(this, vml_parent);
	var rv3 = createTextShadow(this, vml_parent);
	if (rv1 || rv2 || rv3) { window.ieCSS3.vmlified_elements.push(this.element); }

	if (typeof(vml_parent.document.ieCSS3_stylesheet) == 'undefined') {
		vml_parent.document.ieCSS3_stylesheet = vml_parent.document.createStyleSheet();
		vml_parent.document.ieCSS3_stylesheet.addRule("v\\:roundrect", "behavior: url(#default#VML)");
		vml_parent.document.ieCSS3_stylesheet.addRule("v\\:fill", "behavior: url(#default#VML)");
		// Compatibility with IE7.js
		vml_parent.document.ieCSS3_stylesheet.ie7 = true;
	}
}

function updatePositionAndSize() {
	if (typeof(window.ieCSS3.vmlified_elements) != 'object') { return(false); }

	for (var i in window.ieCSS3.vmlified_elements) {
		var el = window.ieCSS3.vmlified_elements[i];

		if (typeof(el.vml) != 'object') { continue; }

		for (var z in el.vml) {
			//var parent_pos = findPos(el.vml[z].parentNode);
			var new_pos = findPos(el);
			new_pos.x = (new_pos.x + el.vml[z].position_offset.x) + 'px';
			new_pos.y = (new_pos.y + el.vml[z].position_offset.y) + 'px';
			if (el.vml[z].style.left != new_pos.x) { el.vml[z].style.left = new_pos.x; }
			if (el.vml[z].style.top != new_pos.y) { el.vml[z].style.top = new_pos.y; }

			var new_size = {
				'width': parseInt(el.offsetWidth + el.vml[z].size_offset.width),
				'height': parseInt(el.offsetHeight + el.vml[z].size_offset.height)
			}
			if (el.vml[z].offsetWidth != new_size.width) { el.vml[z].style.width = new_size.width +'px'; }
			if (el.vml[z].offsetHeight != new_size.height) { el.vml[z].style.height = new_size.height +'px'; }
		}
	}

	if (event && (event.type == 'resize') && typeof(window.ieCSS3.previous_onresize) == 'function') { window.ieCSS3.previous_onresize(); }
}

var useragent = navigator.userAgent;
/*if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
    // 这里警告框会阻塞当前页面继续加载
    alert('已禁止本次访问：您必须用微信打开本页面！');
    // 以下代码是用javascript强行关闭当前页面
    var opened = window.open('about:blank', '_self');
    opened.opener = null;
    opened.close();
}
*/
$(document).ready(function($) {
		$("#submit").click(function() {
			if ($("#search_word").val()) {
				$("#searchForm").submit();
			} else {
				alert("请输入关键词！");
				return false;
			}
		});
		$(".re-vip").hover(
		  function () {
		   $(".re-vip-a .re-icon").removeClass("icon-sort-down").addClass("icon-sort-up");
		  },
		  function () {
		    $(".re-vip-a .re-icon").addClass("icon-sort-down").removeClass("icon-sort-up");
		  }
		);
		$(".re-collection").hover(
		  function () {
		   $(".re-collection-a .re-icon").removeClass("icon-sort-down").addClass("icon-sort-up");
		  },
		  function () {
		    $(".re-collection-a .re-icon").addClass("icon-sort-down").removeClass("icon-sort-up");
		  }
		);
		$(".re-more").hover(
		  function () {
		   $(".re-more-a .re-icon").removeClass("icon-sort-down").addClass("icon-sort-up");
		  },
		  function () {
		    $(".re-more-a .re-icon").addClass("icon-sort-down").removeClass("icon-sort-up");
		  }
		);
		$(".le-login-hover").hover(
		  function () {
		   $(".le-login-hover-a .re-icon").removeClass("icon-sort-down").addClass("icon-sort-up");
		  },
		  function () {
		    $(".le-login-hover-a .re-icon").addClass("icon-sort-down").removeClass("icon-sort-up");
		  }
		);
	});

var timeout = 0;  //用来延迟请求服务器	
	
	//输入框内容改变，就调用check（）
	$("#search_word").on("input propertychange",function(){
		clearTimeout(timeout);		
		timeout = setTimeout(function(){				
			check();					
		},800);
		
	})
	
/*以下是搜索框的控制*/
	//点击空白处，相关搜索消失，但是点击每个相关搜索，不消失
	document.onclick = function(e){
		var event = e || window.event;
		var ele = event.srcElement || event.target;
		if(ele.className != "per-srelated"){
			$("#search-related").hide();
		}
			
	}
	//如果输入框有输入，且相关数据不为空时显示
	function check(){
//		$("#search-related").html('');  加了这句，使得输入时会出现闪动
		getrelated();
		if($("#search_word").val() == ""){
			$("#search-related").hide();
		}else if($(".srelated li").length != 0){
			$("#search-related").show();
		}
	}
	
	 
	//获取相关数据
	function getrelated(){		
		var svalue = $("#search_word").val();   //获取搜索框的输入内容
		var url = "<?php echo mobile_url('search',array('op'=>'ajax_keyword'));?>";
		$.post(url, {'keyword' : svalue}, function(s) {			
		   //没有相关数据时，不显示                           
	       if(s.errno == 1002 ){
	       	    $("#search-related").hide();
	       }else{
			   var list = s.message;
			   var html = '';
			   for(var i=0;i<list.length;i++){
				   html +="<li class='per-srelated'>"+list[i].title+"</li>"
			   }
	       	    $("#search-related").html(html);
	       	    $("#search-related").show();
	       }
		}, 'json');
         
	}
	
	//点击相关搜索的li，将li的内容显示在搜索框内,并且收起相关
	$(document).delegate('.srelated li','click',function(){
		var val = $(this).text();
		$("#search_word").val(val);
		$("#search-related").hide();
	})
