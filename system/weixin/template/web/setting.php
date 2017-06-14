<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
     <form method="post" class="form-horizontal" enctype="multipart/form-data" >
		<h3 class="header smaller lighter blue">微信号设置</h3>

		 <div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" > 公众号名称：</label>

			 <div class="col-sm-9">
				 <input type="text" name="weixinname" class="col-xs-10 col-sm-4" value="<?php  echo $settings['weixinname'];?>" />&nbsp&nbsp;&nbsp;<a href="http://www.squdian.com" target="_blank" style="font-size:16px"><strong>配置帮助</strong></a>
			 </div>
		 </div>
		 <div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" > 访问域名：</label>

			 <div class="col-sm-9">
				 <input type="text" name="domain" class="col-xs-10 col-sm-4" value="<?php  echo $settings['domain'];?>" />&nbsp&nbsp;&nbsp;如：hinrc.com
			 </div>
		 </div>
		 <div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" > 接口地址：</label>

			 <div class="col-sm-9">

				 <input type="text" name="interface" class="col-xs-10 col-sm-4" value="<?php echo WEBSITE_ROOT.'weixin.php'?>"  readonly="readonly"/>
			 </div>
		 </div>

		 <div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" > 微信Token：</label>

			 <div class="col-sm-9">
				 <input type="text" name="weixintoken" id="weixintoken" class="col-xs-10 col-sm-4" value="<?php  echo $settings['weixintoken'];?>" readonly="readonly"/><a href="javascript:;" onclick="tokenGen();">生成新的</a>
			 </div>
		 </div>

		 <div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" >EncodingAESKey：</label>

			 <div class="col-sm-9">
				 <input type="text" name="accesskey" id="EncodingAESKey" class="col-xs-10 col-sm-4" value="<?php  echo $settings['accesskey'];?>" /><a href="javascript:;" onclick="EncodingAESKey();">生成新的</a>
			 </div>
		 </div>

		 <div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" >公众号AppId：</label>

			 <div class="col-sm-9">

				 <input type="text" name="appid" class="col-xs-10 col-sm-4" value="<?php  echo $settings['appid'];?>" />
			 </div>
		 </div>


		 <div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" >公众号AppSecret：</label>

			 <div class="col-sm-9">

				 <input type="text" name="appsecret" class="col-xs-10 col-sm-4" value="<?php  echo $settings['appsecret'];?>" />
			 </div>
		 </div>




		 <div class="form-group">
			 <label class="col-sm-2 control-label no-padding-left" ></label>

			 <div class="col-sm-9">
				 <input type="hidden" name="id" value="<?php echo $_GP['id']; ?>">
				 <input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary span3" />
			 </div>
		 </div>
              
			<?php if($isadd){ ?>
			<div class="alert alert-info" style="margin-left:10px">

					首次添加，请先登录mp.weixin.qq.com进行配置，对比并填写以上信息后提交
			</div>
			<?php } ?>
		 

    </form>
 
<script type="text/javascript">
	function EncodingAESKey() {
		var letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		var token = '';
		for(var i = 0; i < 43; i++) {
			var j = parseInt(Math.random() * 61 + 1);
			token += letters[j];
		}
		$(':text[name="accesskey"]').val(token);
	}
	
	function tokenGen() {
		var letters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		var token = '';
		for(var i = 0; i < 32; i++) {
			var j = parseInt(Math.random() * (31 + 1));
			token += letters[j];
		}
		$(':text[name="weixintoken"]').val(token);
	}
	if($('#weixintoken').val()=='')
	{
		tokenGen();
	}
	if($('#EncodingAESKey').val()=='')
	{
		EncodingAESKey();
	}
</script>
<?php  include page('footer');?>