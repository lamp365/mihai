<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<style type="text/css">
	.shop-list-tr{
		background-color: #fff!important;
	}
	.shop-list-tr li{
		float:left;list-style-type:none;
	}
	.shop-list-tr select{
		margin-right:10px;height:30px; line-height:28px; padding:2px 0;
	}
	
</style>

<div style="padding: 0 15px 15px 15px;overflow: hidden">
    <h3 class="header smaller lighter blue">待配置免单详情</h3>
   
    <div style="background: #E8E8E8;margin-top: 10px;border: 1px solid #c6c6c6;line-height: 24px;padding: 10px 0px;border-radius: 8px;">
        <div class="row">
            <div class="col-sm-1" style="text-align: right">分类：</div>
            <div class="col-sm-9"><?php echo $categoryInfo['name'];?></div>
        </div>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">免单期间：</div>
            <div class="col-sm-9"><?php echo date('Y-m-d',$period['monday_time']).'  ~  '.date('Y-m-d',$period['sunday_time']); ?></div>
        </div>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">免单金额：</div>
            <div class="col-sm-9"><?php echo getFreeAmount($_GP['category_id'],$period['monday_time'],$period['sunday_time']);?></div>
        </div>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">免单人数：</div>
            <div class="col-sm-9"><?php echo getFreeMemberCount($_GP['category_id'],$period['monday_time'],$period['sunday_time']);?></div>
        </div>
    </div>

    <h3>订单商品信息</h3>
    
    <div style="margin-top: 10px;border: 1px solid #c6c6c6;line-height: 24px;padding: 10px 20px;border-radius: 8px;">
	    <table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th style="text-align: center;">订单编号</th>
					<th style="text-align: center;">宝贝</th>
					<th style="text-align: center;">买家</th>
					<th style="text-align: center;">免单金额</th>
				</tr>
			</thead>
			<tbody>
		        <?php if(is_array($list)) { foreach($list as $value) { ?>
		        <tr style="text-align: center;">
					<td><a target="_blank" href="<?php echo web_url('order',array('op'=>'detail','name'=>'shop','id'=>$value['orderid']));?>"><?php echo $value['ordersn'];?></a></td>
					<td><?php echo $value['title'];?></td>
					<td><?php echo '收货人：'.$value['address_realname'].'<br>电话：'.$value['address_mobile']; ?></td>
					<td><?php echo $value['price']*$value['total']; ?></td>
				</tr>
		        <?php  } } ?>
		     </tbody>
		</table>
		
		<?php  echo $pager;?>
    </div>

    <br/>
    <br/>
    <a class="btn btn-primary btn-sm" href="<?php echo web_url('free_order',array('op'=>'new_list'));?>">返回</a>
</div>
