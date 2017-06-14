<?php  include page('h'); ?>
<link rel="stylesheet" type="text/css" href="http://cdn.bootcss.com/font-awesome/4.6.0/css/font-awesome.min.css">

  <div class="beg-login-bg">
		<!-- 登录框背景图 -->
		<!-- 随鼠标滚动的banner<div class="login-area">
            <div class="move-area">首页动画</div>
            <div class="top-logo"><img src="images/logos.png"  style="max-height:56px; width:auto; margin-bottom:7px;" /></div>
        </div> -->
        <div class="banner">      
		    <div class="banner_area">
                  <div class="banner_area_title">
                       <div class="ali-main-head ali-main-partner-head">
						  <h1 class="y-row-title">换个角度看世界</h1>
						  <div class="index-info">小城市，简单的有点可怕</div>
						</div>
				  </div>
			</div>
        </div>
		<!-- 登录弹出框 -->
     	<?php if(!$isLogin){ ?>
            <div class="login-alert">
            	<div class="login-alert-close">&times;</div>
            	<div class="code-tab-bg qrcode-target-show"></div>
            	<div class="qrcode-show">
					<div class="beg-login-box">
						<div class="header">
							<h1>登录</h1>
						</div>
						<div class="beg-login-main">
								<input name="submit" type="hidden" value="submit" />
				                    <div class="layui-form-item">
									<label class="beg-login-icon">
				                        <i class="layui-icon">&#xe612;</i>
				                    </label>
									<input type="text" name="mobile" lay-verify="mobile"  placeholder="这里输入登录名" class="mobile-input layui-input">
									<span class="remove"><img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/remove_input_val.png"></span>
								</div>
								<div class="layui-form-item" style="margin-bottom: 10px;">
									<label class="beg-login-icon">
					                        <i class="layui-icon">&#xe642;</i>
					                    </label>
									<input type="password" name="pwd" lay-verify="pwd"  placeholder="这里输入密码" class="layui-input login-pwd">
									<span class="remove"><img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/remove_input_val.png"></span>
								</div>
								<div class="layui-form-item">
									<a class="forget-pwd" href="javascript:;" onclick="forgetPwd()">忘记登录密码？</a>
									<div >
										<div class="layui-btn layui-btn-normal" style="width:100%;" lay-submit lay-filter="login" onclick="loginFun('<?php echo mobile_url("login",array("name"=>"shopwap","op"=>"do_login")); ?>')">
					                            登录
					                    </div>
									</div>
									<div class="login-a-area clearfix">
				                        <a class="register" href="javascript:;" onclick="register()">免费注册</a>
									</div>
									<div class="beg-clear"></div>
								</div>
							
						</div>
					</div>
				</div>
				<div class="qrcode-hide">
					<div class="beg-login-box">
						<div class="header">
							<h1>扫码登录</h1>
						</div>
						<div class="qrcode-img">
							<img src="http://www.hinrc.com/images/weixin.jpg">
						</div>
						<div class="qrcode-titles">请使用手机小城市扫码</div>
						<div class="qrcode-a">
							<a href="" target="_blank">使用帮助</a>
							<em>|</em>
							<a href="" target="_blank">下载手机小城市</a>
						</div>
					</div>
				</div>
			    <?php } ?>
			</div>
			
		<!-- 注册弹出框 -->
		<div class="regedit-alert-bg"></div>
		<div class="regedit-alert">
			<div class="position:relative">
				<i class="layui-icon regedit-alert-close">&#x1006;</i>
				<div class="regedit-title"><div>欢迎注册</div></div>
				<div class="layui-form">
			        <input type="hidden" name="member_type" value="2">
			        <div class="layui-form-item">
						<div class="layui-input-block">
							<i class="regedit-icon"><img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/phone.png"></i>
							<input type="tel" name="mobile" lay-verify="phone" autocomplete="off" placeholder="请输入手机号码" class="layui-input phone-number">
							<div class="send-code" onclick='phoneCheck(this,"<?php echo  create_url('mobile', array('name' => 'shopwap','do' => 'regedit','op'=>'regedit_sms')); ?>",".phone-number")'>发送验证码</div>
						</div>
					</div>
					<div class="layui-form-item">
						<div class="layui-input-block">
							<i class="regedit-icon"><img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/code.png"></i>
							<input type="number" name="mobilecode" lay-verify="number" placeholder="请输入验证码" autocomplete="off" class="layui-input mobilecode">
						</div>
					</div>
					<div class="layui-form-item">
						<div class="layui-input-block">
							<i class="regedit-icon"><img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/password.png"></i>
							<input type="password" name="pwd" lay-verify="pass" placeholder="请输入登录密码" autocomplete="off" class="layui-input first-pass">
						</div>
						<div class="layui-form-mid layui-word-aux">请填写6到12位密码，英文与数字结合</div>
					</div>
					<div class="layui-form-item">
						<div class="layui-input-block">
							<i class="regedit-icon"><img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/password.png"></i>
							<input type="password" name="pwd" lay-verify="pass2" placeholder="再次输入登录密码" autocomplete="off" class="layui-input confirm-pass">
						</div>
					</div>
					<div class="layui-form-item xieyi">
						<label class="layui-form-mid layui-word-aux">
						<input type="checkbox" name="like1[game]" lay-skin="primary" disabled=""  checked="">用户创建账户即代表同意<a href="#">《使用条款》</a> 和<a href="#">《隐私声明》</a></label>
					</div>
					<div class="regedit-error-msg">错误提示</div>
					<div class="layui-form-item">
						<div class="layui-input-block">
                            <div class="layui-btn regedit-sub-btn" lay-submit="" onclick='registerFun("<?php echo mobile_url('regedit', array('op' => 'signin','submit'=>'submit')); ?>")'>立即提交</div>
							<div type="reset" class="layui-btn layui-btn-primary regedit-reset-btn">重置</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- 找回密码开始 -->
		<div class="new-password-alert">
			<i class="layui-icon regedit-alert-close">&#x1006;</i>
			<div class="regedit-title"><div>找回密码</div></div>
			<div class="layui-form">
				<div class="layui-form-item">
					<div class="layui-input-block">
						<i class="new-pwd-icon"><img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/phone.png"></i>
						<input type="tel" name="mobile" lay-verify="phone" autocomplete="off" placeholder="请输入手机号码" class="layui-input new-pwd-phone">
						<div class="send-code" onclick='phoneCheck(this,"<?php echo  create_url('mobile', array('name' => 'shopwap','do' => 'regedit','op'=>'regedit_sms','is_already_member'=>2)); ?>",".new-pwd-phone")'>发送验证码</div>
					</div>
				</div>
				<div class="layui-form-item">
					<div class="layui-input-block">
						<i class="new-pwd-icon"><img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/code.png"></i>
						<input type="number" name="mobilecode" lay-verify="number" placeholder="请输入验证码" autocomplete="off" class="layui-input new-pwd-code">
					</div>
				</div>
				<div class="layui-form-item">
					<div class="layui-input-block">
						<i class="new-pwd-icon"><img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/password.png"></i>
						<input type="password" name="pwd" lay-verify="pass" placeholder="请输入新密码" autocomplete="off" class="layui-input new-pwd-pass">
					</div>
				</div>
				<div class="layui-form-item">
					<div class="layui-input-block">
                        <div class="layui-btn" style="width:100%" lay-submit="" onclick='modifyPwd("<?php echo mobile_url('login',array('op'=>'resetPasswordByPhone'));?>")'>立即修改</div>
					</div>
				</div>
			</div>
		</div>
		<!-- 找回密码结束 -->
	</div>	
	<div class="brand">
        <div class="y-row">
		    <img src="images/banner1.jpg" width="100%" />
			<ul class="brand-lists clearfix">
			
			</ul>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/login.js"></script>

<?php  include page('f'); ?>