<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">商铺常规设置</h3>
<style>
	.good_line_table{
		
		width:100%;
		}
	.choose_kefu span{margin-right: 10px;cursor: pointer}
	</style>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" >
	   <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 每评分可兑换积分：</label>
			<div class="col-sm-9">
				  <input type="number" name="comment_exchange" class="col-xs-10 col-sm-2" value="<?php  echo $settings['comment_exchange'];?>" />
			</div>
		</div>
									
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 一块钱可兑换积分：</label>
			<div class="col-sm-9">
				  <input type="number" name="bid_exchange" class="col-xs-10 col-sm-2" value="<?php  echo $settings['bid_exchange'];?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 一个入驻兑换积分：</label>
			<div class="col-sm-9">
				  <input type="number" name="enter_exchange" class="col-xs-10 col-sm-2" value="<?php  echo $settings['enter_exchange'];?>" />
			</div>
		</div>
        <div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 一个订单兑换积分：</label>
			<div class="col-sm-9">
				  <input type="number"  name="order_num_exchange" class="col-xs-10 col-sm-2" value="<?php if ($settings['order_num_exchange']) echo $settings['order_num_exchange'];?>" />
			</div>
		</div>
	   <!--
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 最低提款限制：</label>
			<div class="col-sm-9">
				  <input type="number" step="0.01" name="lowst_draw_limit" class="col-xs-10 col-sm-2" value="<?php if ($settings['lowst_draw_limit']) echo FormatMoney($settings['lowst_draw_limit'],0);?>" />
			</div>
		</div>
		-->
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 提款手续费：</label>
			<div class="col-sm-9">
				  <input type="text"  name="draw_money" class="col-xs-10 col-sm-2" value="<?php if ($settings['draw_money']) echo FormatMoney($settings['draw_money'],0);?>" />
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" > 支付费率(%)：</label>
			<div class="col-sm-9">
				  <input type="number" step="1" name="pay_rate" class="col-xs-10 col-sm-2" value="<?php if ($settings['pay_rate']) echo $settings['pay_rate'];?>" />
			</div>
		</div>
    
		<div class="form-group">
			<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>
			<div class="col-sm-9">
				<br/><input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
    				
            </div>
         </div>
</form>
<script type="text/javascript">
	
</script>

<?php  include page('footer');?>