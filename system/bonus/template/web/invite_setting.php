<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
     <form method="post" class="form-horizontal" >
     	<input type="hidden" name="op" value="post">
		<h3 class="header smaller lighter blue">邀请一位好友注册的收益配置</h3>
		
			<div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" >邀请现金奖励：</label>
				<div class="col-sm-9">
                     <input type="text" name="direct_share_price" class="col-xs-10 col-sm-4" value="<?php echo $arrInviteSetting['direct_share_price'];?>" required/>(元)
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" >订单助力现金奖励：</label>
				<div class="col-sm-9">
                    <input type="text" name="order_share_price" class="col-xs-10 col-sm-4" value="<?php echo $arrInviteSetting['order_share_price'];?>" required/>(元)
				</div>
			</div>

		 <div class="form-group">
				<label class="col-sm-2 control-label no-padding-left" >邀请积分奖励：</label>
				<div class="col-sm-9">
                    <input type="text" name="direct_share_jifen" class="col-xs-10 col-sm-4" value="<?php echo $arrInviteSetting['direct_share_jifen'];?>" required/>(积分)
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