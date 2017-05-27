<?php defined('SYSTEM_IN') or exit('Access Denied');?>	<div id="footer" style="float: left;overflow: hidden;padding-top: 20px;margin: 20px 15px 20px 15px;border-top: 1px #eee solid;line-height: 20px;font-size: 14px;color: #666;">
		<span style="float: left;">
			<p>CopyRight <?php echo SAPP_NAME ?>2015</i></i></p>
		</span>
	</div>
<!--用于加载远程地址 作为弹框页面  注意更新 -->
<div id="alterModal" class="alertModalBox"></div>
<script>
$(function(){
	var the_height = document.body.clientHeight;
	if(the_height < 400){
		document.body.style.height = '700px';
	}
});


</script>
</body>
</html>

