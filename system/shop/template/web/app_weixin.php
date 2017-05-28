<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
     <form method="post" class="form-horizontal" >
     	<input type="hidden" name="op" value="post">
		<h3 class="header smaller lighter blue">移动端微信设置</h3>
		
			<div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" >移动端AppId：</label>
				<div class="col-sm-9">
                     <input type="text" name="weixin_mobile_appId" class="col-xs-10 col-sm-4" value="<?php echo $arrWeixinMobile['weixin_mobile_appId'];?>" required/>
				</div>
			</div>
									
			<div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" >移动端AppSecret：</label>
				<div class="col-sm-9">
                    <input type="text" name="weixin_mobile_appSecret" class="col-xs-10 col-sm-4" value="<?php echo $arrWeixinMobile['weixin_mobile_appSecret'];?>" required/>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" >移动端微信支付商户号(MchId)：</label>
				<div class="col-sm-9">
                    <input type="text" name="weixin_mobile_mchId" class="col-xs-10 col-sm-4" value="<?php echo $arrWeixinMobile['weixin_mobile_mchId'];?>" required/>
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" >移动端商户支付密钥(api密钥)：</label>
				<div class="col-sm-9">
                    <input type="text" name="weixin_mobile_signKey" class="col-xs-10 col-sm-4" value="<?php echo $arrWeixinMobile['weixin_mobile_signKey'];?>" required/>
				</div>
			</div>
									
			<div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" ></label>

				<div class="col-sm-9">
					<input name="submit" id="submit" type="submit" value="提交" class="btn btn-primary span3" />
				</div>
			</div>
    </form>
<?php  include page('footer');?>