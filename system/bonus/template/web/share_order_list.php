<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/third-party/highcharts/highcharts.js"></script>
<script type="text/javascript">
$(function () {
    // Create the chart
    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: '助力统计'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category',
            crosshair:true
        },
        yAxis: {
            title: {
                text: '数量'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#ddd'
            }]
        },
        legend: {
            enabled: true
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y:.1f}'
                }
            }
        },
        tooltip: {
            pointFormat: '{point.y}位</b>'
        },
        series: [{
            name: '日',
            data: [100,2000,30000]
        }, {
            name: '周',
            data: [100,200,500]
        },{
            name: '月',
            data: [1000,2000,3000]
        }]
    });
});

	 
</script>
<div class="main-payment" style="margin-bottom:20px;">
    <div class="payment-amount-area" style="width:100%;">
        <div class="payment-amount" id="container" style="width:100%;"></div>
    </div>
</div>
<h3 class="header smaller lighter blue">
	助力订单列表&nbsp;&nbsp;&nbsp;
</h3>

<div class="alert alert-info" style="margin:10px 0; width:auto;">
	<i class="icon-lightbulb"></i> 
</div>

<ul class="nav nav-tabs">
	<li style="width:7%" <?php if($_GP['from_platform']!='tmall' && $_GP['from_platform']!='shua'){?>class="active"<?php }?>><a href="<?php echo web_url('share_order')?>">觅海订单</a></li>
	<li style="width:7%" <?php if($_GP['from_platform']=='tmall'){?>class="active"<?php }?>><a href="<?php echo web_url('share_order',array('from_platform'=>'tmall'))?>">天猫订单</a></li>
	<li style="width:9%" <?php if($_GP['from_platform']=='shua'){?>class="active"<?php }?>><a href="<?php echo web_url('share_order',array('from_platform'=>'shua'))?>">虚拟用户刷单</a></li>
</ul>

<?php if($_GP['from_platform'] == 'shua'){ ?>
    <br/>
    <div class="alert alert-info" style="margin:10px 0; width:auto;">
        <i class="icon-lightbulb"></i> 对于刷的助力，只能是虚拟用户，其他用户无效果！
    </div><br/>

    <form method="post" action="<?php echo web_url('share_order',array('name'=>'bonus','op'=>'shua_member')); ?>">
    <label class="col-sm-1 control-label no-padding-left" for="username">虚拟用户名称：</label>
    <input name="username" type="text" id="username" placeholder="请输入手机号或者昵称"> &nbsp;&nbsp;<span class="btn btn-sm btn-danger" onclick="getrandUser()">随机获取</span>
    <br/><br/>
    <label class="col-sm-1 control-label no-padding-left" for="usernum">刷单人数：</label>
    <input name="usernum" type="text" id="usernum" placeholder="请输入人数个数">
    <br/><br/>

    <label class="col-sm-1 control-label no-padding-left" for="input-search">&nbsp;</label>
    <input type="hidden" name="openid" value="" id="openid">
    <button type="submit" name="sure" class="btn btn-primary btn-md">确定</button>
    </form>
    <script>
        function getrandUser(){
            var url = "<?php echo web_url('share_order',array('name'=>'bonus','op'=>'getrandUser')); ?>";
            $.getJSON(url,function(data){
                var nameArr = data.message;
                var name   = nameArr.name;
                var openid = nameArr.openid;
                $("#openid").val(openid);
                $("#username").val(name);
            },'json')
        }
    </script>
<?php }else{  ?>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th style="text-align: center;">订单编号</th>
			<th style="text-align: center;">下单时间</th>
			<th style="text-align: center;">买家</th>
			<th style="text-align: center;">分享的状态</th>
			<th style="text-align: center;">助力人数</th>
			<th style="text-align: center;">减免金额</th>
			<th style="text-align: center;">订单实付金额</th>
			<th style="text-align: center;">更新时间</th>
			<th style="text-align: center;">操作</th>
		</tr>
	</thead>
	<tbody>
        <?php if(is_array($list)) { foreach($list as $value) { ?>
        <tr style="text-align: center;">
			<td><?php echo $value['ordersn'];?></td>
			<td><?php echo date('Y-m-d H:i:s',$value['createtime']);?></td>
			<td><?php echo '收货人：'.$value['address_realname'].'<br>电话:'.$value['address_mobile']; ?></td>
			<td>
			<?php if($value['share_status']=='-1')
					echo '驳回';	
				elseif($value['share_status']=='1')
					echo '已添加分享';
				elseif($value['share_status']=='2')
					echo '提交申请';
				elseif($value['share_status']=='3')
					echo '审核通过';
			?>
			</td>
			<td><?php echo $value['friend_cnt'];?></td>
			<td><?php echo $value['order_fee'];?></td>
			<td><?php echo $value['price'];?></td>
			<td><?php if(!empty($value['updatetime'])) echo date('Y-m-d H:i:s',$value['updatetime']);?></td>
			<td>
				<?php if($value['share_status']==2){?>
					<a class="btn btn-danger btn-xs" href="javascript:viod();" data-toggle="modal" data-target="#myModal" onclick="shareOrderProcess('<?php echo $value['ordersn'];?>','<?php echo $value['from_platform'];?>')">处理</a>
				<?php  } ?>
			</td>
		</tr>
        <?php  } } ?>
     </tbody>
</table>
<?php  echo $pager;?>


<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" action="<?php echo web_url('share_order',array('op'=>'apply_process')); ?>" method="post">
            <input type="hidden" name="ordersn" id="J_ordersn" value="">
            <input type="hidden" name="from_platform" id="J_from_platform" value="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">商家处理</h4>
            </div>
            <div class="modal-body">
                <div class="checkbox">
                    <label><input type="radio" checked="checked" name="share_status" value="3">同意</label>
                    <label><input type="radio" name="share_status" value="-1">不同意</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" name="admin_process" value="sub" class="btn btn-primary">确认处理</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>


<script language='javascript'>
function shareOrderProcess(ordersn,from_platform)
{
	$('#J_ordersn').val(ordersn);
	$('#J_from_platform').val(from_platform);
}
</script>
<?php } ?>

<?php  include page('footer');?>