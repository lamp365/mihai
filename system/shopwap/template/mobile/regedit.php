<?php  include page('h'); ?>
<!-- 头部导航 -->
<div class="top-nav">
	<div class="top-nav-area">
		<div class="top-logo">小城市</div>
		<div class="top-nav-div">
			<ul class="top-nav-list clearfix">
				<li><a href="<?php echo mobile_url('seller_check_in',array('name'=>'shopwap')); ?>">商家入住</a></li>
			</ul>
		</div>
	</div>
</div>
<div class="regedit-area">
	<form action="<?php echo  mobile_url('regedit', array('name' => 'shopwap','op'=>'signin')); ?>" name="" method="post" class="layui-form">
        <input type="hidden" name="member_type" value="1">
        <div class="layui-form-item">
			<label class="layui-form-label"><span class="red">*</span>验证手机</label>
			<div class="layui-input-inline">
				<input type="tel" name="mobile" lay-verify="phone" autocomplete="off" placeholder="请输入手机号码" class="layui-input phone-number">
			</div>
			<div class="layui-input-inline"><div class="layui-btn layui-btn-normal send-code">发送验证码</div></div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label"><span class="red">*</span>验证码</label>
			<div class="layui-inline">
				<div class="layui-input-inline">
					<input type="number" name="mobilecode" lay-verify="number" placeholder="请输入验证码" autocomplete="off" class="layui-input">
				</div>
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label"><span class="red">*</span>登录密码</label>
			<div class="layui-input-inline">
				<input type="password" name="pwd" lay-verify="pass" placeholder="请输入登录密码" autocomplete="off" class="layui-input first-pass">
			</div>
			<div class="layui-form-mid layui-word-aux">请填写6到12位密码，英文与数字结合</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label"><span class="red">*</span>确认密码</label>
			<div class="layui-input-inline">
				<input type="password" name="repwd" lay-verify="pass2" placeholder="再次输入登录密码" autocomplete="off" class="layui-input confirm-pass">
			</div>
		</div>
		<div class="layui-form-item">
			<div class="layui-input-block">
				<button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
				<button type="reset" class="layui-btn layui-btn-primary">重置</button>
			</div>
		</div>
	</form>
</div>
<script>
$(document).ready(function() {
	layui.use(['form', 'layedit', 'laydate'], function() {
		var form = layui.form(),
			layer = layui.layer;

		//自定义验证规则
		form.verify({
			title: function(value) {
				if(value.length < 5) {
					return '标题至少得5个字符啊';
				}
			},
			phone:function(){
				var cellphone = $(".phone-number").val();
				if( !(/^1[34578]\d{9}$/.test(cellphone)) ){
					return '请输入正确的手机号码';
				}
			},
			pass: [/(.+){6,12}$/, '密码必须6到12位'],
			pass2:function(){
				if($(".confirm-pass").val()!=$(".first-pass").val()){
					return "两次输入的密码不一致";
				}
			}
		});

		//监听提交
		form.on('submit(demo1)', function(data) {
//			layer.alert(JSON.stringify(data.field), {
//				title: '最终的提交信息'
//			})
//			return false;
		});
	});
	//手机号码验证和发送验证码
	function phoneCheck(){
		var number = 60;
		var cellphone = $(".phone-number").val();
		var url = "<?php echo  mobile_url('regedit', array('name' => 'shopwap','op' => 'regedit_sms')); ?>";
		if( !(/^1[34578]\d{9}$/.test(cellphone)) ){
			alert('请输入正确的手机号码');
		}else{
			$.post(url,{'phone':cellphone},function(data){
               
				if( data.code == 1 ){
					var daojishi = setInterval(function(){
						if( number == 0 ) {
							clearInterval(daojishi);
							$(".send-code").text('发送验证码').removeClass("layui-btn-disabled");
						}else{
							--number;
							$(".send-code").text('重新发送（'+number+'s）').addClass("layui-btn-disabled");
						}
					},1000);
				}else{
					alert(data.message);
				}
			},'json');
		}
	}
	//手机号码验证
	$(".send-code").on("click",function(){
		phoneCheck();
	});

	//下拉框多级联动

});
</script>
<?php  include page('f'); ?>