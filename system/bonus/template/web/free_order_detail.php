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
    <h3 class="header smaller lighter blue">免单详情</h3>
   
    <div style="background: #E8E8E8;margin-top: 10px;border: 1px solid #c6c6c6;line-height: 24px;padding: 10px 0px;border-radius: 8px;">
        <div class="row">
            <div class="col-sm-1" style="text-align: right">分类：</div>
            <div class="col-sm-9"><?php echo $free_config['name'];?></div>
        </div>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">免单期间：</div>
            <div class="col-sm-9"><?php echo date('Y-m-d',$free_config['free_starttime']).'  ~  '.date('Y-m-d',$free_config['free_endtime']); ?></div>
        </div>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">免单金额：</div>
            <div class="col-sm-9"><?php echo $free_config['free_amount'];?></div>
        </div>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">免单人数：</div>
            <div class="col-sm-9"><?php echo $free_config['free_member_count'];?></div>
        </div>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">创建时间：</div>
            <div class="col-sm-9"><?php echo date('Y-m-d H:i:s',$free_config['createtime']);?></div>
        </div>
    </div>

    <h3>免单申请记录</h3>
    
    <form action="<?php echo web_url('free_order',array('op' =>'free_detail','free_id'=>(int)$_GP['free_id']))?>" class="form-horizontal" method="post">
	<table class="table table-striped table-bordered table-hover">
		<tbody>
				<tr class="shop-list-tr">
					<td>
						<li>
						   <select name="free_status">
							   <option value="">请选择免单状态</option>
							   <option value="0" <?php if($_GP['free_status']==='0'){?>selected="selected"<?php  } ?>>未申请</option>
                               <option value="1" <?php if($_GP['free_status']==1){?>selected="selected"<?php  } ?>>申请中</option>
                               <option value="2" <?php if($_GP['free_status']==2){?>selected="selected"<?php  } ?>>审核通过</option>
                               <option value="-1" <?php if($_GP['free_status']==-1){?>selected="selected"<?php  } ?>>驳回</option>
						   </select>
						</li>
						
						<li>
							<button class="btn btn-primary btn-sm"><i class="icon-search icon-large"></i> 搜索</button>
						</li>
					</td>
				</tr>
		</tbody>
	</table>
	</form>
    
    <div style="margin-top: 10px;border: 1px solid #c6c6c6;line-height: 24px;padding: 10px 20px;border-radius: 8px;">
	    <table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th style="text-align: center;">订单编号</th>
					<th style="text-align: center;">宝贝</th>
					<th style="text-align: center;">买家</th>
					<th style="text-align: center;">免单金额</th>
					<th style="text-align: center;">免单状态</th>
					<th style="text-align: center;">平台处理说明</th>
					<th style="text-align: center;">操作</th>
				</tr>
			</thead>
			<tbody>
		        <?php if(is_array($list)) { foreach($list as $value) { ?>
		        <tr style="text-align: center;">
					<td><?php echo $value['ordersn'];?></td>
					<td><?php echo $value['title'];?></td>
					<td><?php echo '收货人：'.$value['address_realname'].'<br>电话：'.$value['address_mobile']; ?></td>
					<td><?php echo $value['price']*$value['total']; ?></td>
					<td><?php 
						if ($value['free_status'] == -1)
                          	echo '驳回';
                      	elseif($value['free_status'] == 1)
							echo '申请中';
                        elseif($value['free_status'] == 2)
                        	echo '审核通过';
                        else 
                        	echo '未申请';
						?>
					</td>
					<td><?php echo $value['free_explanation'];?></td>
					<td>
						<?php if($value['free_status']==1 && $period['sunday_time']==$free_config['free_endtime']){?>
						<a class="btn btn-danger btn-xs" href="javascript:viod();" data-toggle="modal" data-target="#myModal" onclick="freeProcess('<?php echo $value['id'];?>','<?php echo $value['price']*$value['total'];?>')">处理</a>
						<?php  } ?>
					</td>
				</tr>
		        <?php  } } ?>
		     </tbody>
		</table>
		
		<?php  echo $pager;?>
    </div>

    <br/>
    <br/>
    <a class="btn btn-primary btn-sm" href="<?php echo web_url('free_order');?>">返回</a>
</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" action="<?php echo web_url('free_order',array('op'=>'free_process','free_id'=>$_GP['free_id'])); ?>" method="post">
            <input type="hidden" name="order_goods_id" id="J_order_goods_id" value="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">商家处理</h4>
            </div>
            <div class="modal-body">
                <div class="checkbox">
                    <label><input type="radio" checked="checked" name="free_status" value="2">同意</label>
                    <label><input type="radio" name="free_status" value="-1">不同意</label>
                </div>
                <div class="form-group">
                    <label for="jine">免单金额</label>
                    <br>
                    <span id='J_free_amount'></span>
                </div>
                <div class="form-group">
                    <label for="name">处理说明</label>
                    <input type="text" name="free_explanation" class="form-control" placeholder="请输入处理说明" maxlength="200">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" name="admin_chuli" value="sub" class="btn btn-primary">确认处理</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>


<script language='javascript'>
function freeProcess(id,free_amount)
{
	$('#J_order_goods_id').val(id);
	$('#J_free_amount').html(free_amount);
}
</script>