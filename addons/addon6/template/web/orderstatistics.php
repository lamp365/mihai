<?php 
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 百家威信 <QQ:2752555327> <http://www.squdian.com>
// +----------------------------------------------------------------------
defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<style>
.c_hidden {
	clear: both;
	display: none;
	background-color: #f6faf1;
	width: 100%;
}
.sub-search-list{
	border: 1px solid #ddd;
}
.sub-search-list li{
	float: left;
	margin-right: 10px;
	list-style: none;
}
.sub-search-list li select{
	height:26px;
}

.left-span{
	float: left;
    line-height: 28px;
    background-color: #ededed;
    padding: 0 5px;
    border: 1px solid #cdcdcd;
    border-right: 0;
    font-size: 12px;
}
.sub-search-list .li-height{
    height: 30px;
    padding-left: 5px;
}
</style>
	<script>
		$(function(){
	$("#TabOrders tr").not(".table_title,.c_hidden").click(function(){

	            //$(this).next("tr").removeClass("c_hidden");
	            if($(this).next("tr").is(":hidden")){
	                $(this).next("tr").removeClass("c_hidden");
	                  
	            }else{
	                $(this).next("tr").addClass("c_hidden");
	            }     
	
	});
});
</script>
<h3 class="header smaller lighter blue">订单统计</h3>
	<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/css/datetimepicker.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>

				<div class="alert alert-info" style="margin:10px 0; width:auto;">
			<i class="icon-lightbulb"></i> 查询有购买记录客户的(完成)订单统计，您可以按时间查询客户的总订单数和总订单金额。
		</div>
		
		<form action="">
			<input type="hidden" name="mod" value="site" />
			<input type="hidden" name="name" value="addon6" />
			<input type="hidden" name="do" value="orderstatistics" />
			<table class="table sub-search sub-search-list">
				<tbody>
					<tr>
						<td>
							<li >
								<span class="left-span">会员名</span>
								<input name="realname" type="text" placeholder="会员名" class="span3 li-height" value="<?php  echo $realname;?>">
							</li>	
							<li>
								<span class="left-span">收货人</span>
								<input name="addressname" type="text" placeholder="收货人" class="span3 li-height" value="<?php  echo $addressname;?>">
							</li>
							<li>
								<span class="left-span">订单号</span>
								<input name="ordersn" type="text" placeholder="订单号" class="span3 li-height" value="<?php  echo $ordersn;?>">
							</li>
							<li >
								<span class="left-span">起始日期</span>
								<input name="start_time" id="start_time" class="li-height" type="text" value="<?php  echo empty($start_time)?date('Y-m-d',time()):date('Y-m-d',$start_time);?>" readonly="readonly"  /> 
							</li>	
							<li>
								<span class="left-span">终止日期</span>
								<input name="end_time" id="end_time" class="li-height" type="text" value="<?php  echo empty($end_time)?date('Y-m-d',time()):date('Y-m-d',$end_time);?>" readonly="readonly"  /> 
							</li>
							<li style="display: none;">
								<span class="left-span">食堂</span>
								<select style="margin-right:15px;" id="mess" name="mess" >
								 	<option value="" <?php  echo empty($_GP['dispatch'])?'selected':'';?>>--未选择--</option>
									<?php  if(is_array($_mess)) { foreach($_mess as $item) { ?>
	                 				<option value="<?php  echo $item["id"];?>" <?php  echo $item['id']==$_GP['mess']?'selected':'';?>><?php  echo $item['title']?></option>
	                  				<?php  } } ?>
	                   			</select>
							</li>
							<li>
								<span class="left-span">状态</span>
								<select name="isstatus" style="height: 30px">
									<option value="" <?php  echo $isstatus==''?'selected':'';?>>全部</option>
									<option value="1" <?php  echo $isstatus==1?'selected':'';?>>已付款</option>
									<option value="3" <?php  echo $isstatus==3?'selected':'';?>>已完成</option>
								</select>
							</li>
							<li><input type="submit" name="" value=" 查 询 " class="btn btn-primary btn-sm" ></li>
							<li><button type="submit" name="orderstatisticsEXP01" value="orderstatisticsEXP01" class="btn btn-sm btn-warning btn-primary">导出excel</button></li>	
						<td>
					</tr>
				</tbody>
			</table>
		</form>
	<script type="text/javascript">
		laydate({
	        elem: '#start_time',
	        istime: true, 
	        event: 'click',
	        format: 'YYYY-MM-DD hh:mm:ss',
	        istoday: true, //是否显示今天
	        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	    });
	    laydate({
	        elem: '#end_time',
	        istime: true, 
	        event: 'click',
	        format: 'YYYY-MM-DD hh:mm:ss',
	        istoday: true, //是否显示今天
	        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	    });
	    laydate.skin("molv");
	</script>
		

	<table  id="TabOrders" class="table table-striped table-bordered table-hover">
			<thead >
				<tr>
					<th width="85"  >订单号</th>
					<th width="41" >下单时间</th>
				<th width="41" >总订单金额</th>
						<th width="42" >付款方式</th>
					<th width="42" >收货人</th>
					<th width="42" >收货电话</th>
				</tr>
			</thead>
			<tbody>
		<?php  $index=0;$countmoney=0?>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
						<?php  $index++;?>
				<tr>
					<td><?php  echo $item['ordersn'];?></td>
					<td><?php  echo date('Y-m-d  H:i:s',$item['createtime'])?></td>
					<td><?php  $countmoney=$countmoney+$item['price']?> <?php  echo $item['price'];?><?php  if(!empty($item['dispatchprice'])&&$item['dispatchprice']>0 ) { ?>&nbsp;(运费：<?php  echo $item['dispatchprice'];?>)<?php  } ?></td>
					<td><?php  if($item['paytype'] == 1) { ?><span class="label label-important">余额支付</span><?php  } ?><?php  if($item['paytype'] == 2) { ?><span class="label label-important">在线支付</span><?php  } ?><?php  if($item['paytype'] == 3) { ?><span class="label label-warning">货到付款</span><?php  } ?></td>
						<td><?php  echo $item['tdrealname'];?></td>
						<td><?php  echo $item['tdmobile'];?></td>
				</tr>	
				
				<tr  style="background: #e0dcce;" class="c_hidden">
		
			<td colspan="6">
        <table width="100%">
        
         <tbody>
         				<?php  if(is_array($item['ordergoods'])) { foreach($item['ordergoods'] as $itemgoods) { ?>
         	<tr style="background: #e0dcce;">
         <td><img src="<?php  echo WEBSITE_ROOT;?>attachment/<?php  echo $itemgoods['thumb'];?>" style="border-width:0px;width: 200px; height: 150px;"></td>
         <td><span class="Name"><?php  echo $itemgoods['title'];?></span><br/> <span class="colorC">规格：<?php  echo $itemgoods['optionname'];?></span>
	      </td>
         <td>商品单价：<?php  echo $itemgoods['price'];?></td>
         <td>购买数量：<?php  echo $itemgoods['total'];?></td>
         <td>总价(元)：<strong class="colorG"><?php  echo round(($itemgoods['total']*$itemgoods['price']),2)?></strong></td>
        </tr>
        <?php  } } ?>
       
       
        </tbody></table>	   
	   </td></tr>	

	   				<?php  } } ?>
			
			</tr>
			<h4 class="sub-title">
				<span>当前页共计<span style="color:red; "><?php  echo $index?></span>个,订单金额共计<span style="color:red; "><?php  echo $countmoney?></span>元</span>
		</h4>
		</table>
			<?php  echo $pager;?>


<?php  include page('footer');?>