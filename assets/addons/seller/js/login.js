$(document).ready(function() {
	//layui初始化
	layui.use(['layer'], function() {
		var layer = layui.layer,
			$ = layui.jquery;	
	});
	//轮播代码
	var index = 0;
	var slide_length = $(".img-wrap .slide-wrap li").length;
    var timer = setInterval(function(){
        if( index < slide_length ){
        	index = index + 1;
        }
        if( index == slide_length ){
        	index = 0;
        }
        $(".img-wrap .slide-wrap li").fadeOut(600).eq(index).fadeIn(600);                
    }, 3000);
    //滑动到视频的位置时播放视频
   /* $(window).scroll(function(){
    	var scroll_height = $(window).scrollTop();
    	var page09_height = $(".page09").offset().top-$(".page09").height();
    	if( scroll_height > page09_height ) {
    		$(".J_playAfterOver").get(0).play();
    	}
    });*/
    //输入框的删除按钮控制
    $(".layui-form-item input").each(function(){
		if($(this).val()){
			$(this).siblings(".remove").show();
		}else{
			$(this).siblings(".remove").hide();
		}
	});
	$(".layui-form-item input").on("input propertychange",function(){
		if($(this).val()){
			$(this).siblings(".remove").show();
		}else{
			$(this).siblings(".remove").hide();
		}
	});
	$(".layui-form-item input").blur(function(){
		if($(this).val()){
			$(this).siblings(".remove").show();
		}else{
			$(this).siblings(".remove").hide();
		}
	});
	$(".layui-form-item .remove").on("click",function(){
		$(this).siblings("input").val("");
		$(this).hide();
	});

	
	//手机号码验证
/*	$(".send-code").on("click",function(){
		phoneCheck();
	});*/
	$(".regedit-alert-close").on("click",function(){
		$(".regedit-alert-bg,.regedit-alert,.new-password-alert").hide();
	});
	//清除自动填充
	$(".beg-login-main .mobile-input").val("");
	$(".beg-login-main .login-pwd").val("");
	//重置
	$(".regedit-reset-btn").on("click",function(){
		$(".regedit-alert .layui-form-item input").each(function(){
			$(this).val("");
		})
	});
	$(".login-alert-close").on("click",function(){
		$(".regedit-alert-bg,.login-alert").hide();
	});
	$(".code-tab-bg").on("click",function(){
		if( $(this).hasClass("qrcode-target-show") ){
			$(this).removeClass("qrcode-target-show").addClass("qrcode-target-hide");
			$(".qrcode-show").hide();
			$(".qrcode-hide").show();
		}else{
			$(this).removeClass("qrcode-target-hide").addClass("qrcode-target-show");
			$(".qrcode-show").show();
			$(".qrcode-hide").hide();
		}
	})
});
//登录
function loginFun(url){
	var login_mobile = $.trim($(".mobile-input").val());
	var login_pwd_val = $(".login-pwd").val();
	var top_nav_html = ''; 
	////verify.phone手机号码验证,val表示需要验证的值，dom参数是需要验证的节点
	///verify.password密码验证,val表示需要验证的值，dom参数是需要验证的节点
	var flag = verify.phone(login_mobile,".mobile-input")&&verify.password(login_pwd_val,".login-pwd");
	if( flag == true ){
		$.post(url,{mobile:login_mobile,pwd:login_pwd_val},function(ret){
			if(ret.errno==1){
				location.href=ret.data.url;
				$(".sjrz").show();
			}else{
				if(ret.data.error_location==1){
                    layer.tips(ret.message, '.mobile-input');
				}
				if(ret.data.error_location==2){
                    layer.tips(ret.message, '.login-pwd');
				}
			}
		},"json");
	}
}
//注册
function registerFun(url){
	var register_mobile = $(".phone-number").val();
	var register_code = $(".mobilecode").val();
	var register_first_pwd = $(".first-pass").val();
	var register_confirm_pwd = $(".confirm-pass").val();
	var top_nav_html = ''; 
	////verify.phone手机号码验证,val表示需要验证的值，dom参数是需要验证的节点
	///verify.password密码验证,val表示需要验证的值，dom参数是需要验证的节点
	//////verify.mobilecode手机验证码验证,val表示需要验证的值，dom参数是需要验证的节点
	var flag = verify.phone(register_mobile,".phone-number")&&verify.mobilecode(register_code,".mobilecode")&&verify.password(register_first_pwd,".first-pass")&&verify.password(register_confirm_pwd,".confirm-pass");
	if( flag == true ){
		$.post(url,{mobile:register_mobile,mobilecode:register_code,pwd:register_first_pwd},function(data){
			if(data.errno==1){
				$(".regedit-alert-bg,.regedit-alert").hide();
				window.location.reload();
			}else{
                            var loc=data.data.error_location; console.log(data.data.error_location);
				if(loc==1){
                                    layer.tips(data.message, '.phone-number');
				}
				if(loc==2){
					layer.tips(data.message, '.mobilecode');
				}
				if(loc==3){
					layer.tips(data.message, '.first-pass');
				}
				if(loc==4){
					layer.tips(data.message, '.confirm-pass');
				}
			}
		},"json");
	}
}
//注册弹出框
function register(){
	$(".login-alert").hide();
	$(".regedit-alert-bg,.regedit-alert").show();
}
function loginAlert(){
	$(".regedit-alert-bg,.login-alert").show();
}
//手机号码验证和发送验证码
function phoneCheck(obj,url,elem){
	var number = 60;
	var cellphone = $(elem).val();
	var flag = verify.phone(cellphone,elem);
	if( flag==true ){
		if(!$(obj).hasClass("layui-btn-disabled")){
			$.post(url,{accout:cellphone},function(data){
				if( data.errno == 1 ){
					var daojishi = setInterval(function(){
						if( number == 0 ) {
							clearInterval(daojishi);
							$(obj).text('发送验证码').removeClass("layui-btn-disabled");
						}else{
							--number;
							$(obj).text('重新发送（'+number+'s）').addClass("layui-btn-disabled");
						}
					},1000);
				}else{
					alert(data.message);
				}
			},'json');
		}
	}
}
//忘记密码
function forgetPwd(){
	$(".login-alert").hide();
	$(".regedit-alert-bg,.new-password-alert").show();
}
//密码重置验证
function modifyPwd(url){
	var new_pwd_phone = $(".new-pwd-phone").val();
	var new_pwd_code = $(".new-pwd-code").val();
	var new_pwd_pass = $(".new-pwd-pass").val();
	var flag = verify.phone(new_pwd_phone,".new-pwd-phone")&&verify.mobilecode(new_pwd_code,".new-pwd-code")&&verify.password(new_pwd_pass,".new-pwd-pass");
	var fdata=  {
                mobilecode:new_pwd_code,
                mobile: new_pwd_phone,
                pwd: new_pwd_pass
            };
        if( flag == true ){
		$.post(url,fdata,function(ret){
                    if(ret.errno==1){
                        $(".new-password-alert").hide();
                    }else{
                        alert(ret.message);
                    }
		},"json");
	}
}

//首页动画匿名函数
(function() {
	//过滤掉IE8 IE9 不支持transform动画
	var userAgent = window.navigator.userAgent.toLowerCase(); 
	var isie8 = /msie 8\.0/i.test(userAgent)||/msie 9\.0/i.test(userAgent);
	if( !isie8 ){
		function t() {
	        var e = .03;
	        e *= Math.max(.1, Math.abs(a - i)),
	        a - i > 0 ? (i += e,
	        i = Math.min(a, i)) : a - i < 0 && (i -= e,
	        i = Math.max(a, i)),
	        s.css({
	            transform: "translateX(-" + c * i / 150 + "px) translateZ(0)"
	        }),
	        a == i ? o = !1 : requestAnimationFrame(t)
	    }
	    window.requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame || function(t) {
	        window.setTimeout(t, 1e3 / 60)
	    }
	    ;
	    var e, n, a = 50, i = 50, o = !1, r = $(".login-area"), s = $(".move-area"), c = 0;
	    $(window).resize(function() {
	        e = r.outerHeight(),
	        n = 5.146 * e,
	        c = n - $(window).width(),
	        s.css({
	            width: n + "px"
	        })
	    }).resize();
	    setTimeout(function() {
	        r.on("mousemove", function(e) {
	            var n = $(this).outerWidth()
	              , i = .07
	              , r = .01
	              , s = Math.min(n * (1 - i), Math.max(n * i, e.clientX)) / (n * (1 - 2 * r));
	            "number" != typeof s && (s = .5),
	            a = 100 * s,
	            o || (o = !0,
	            requestAnimationFrame(t))
	        })
	    }, 500),
	    requestAnimationFrame(t)
	}
    
})();
