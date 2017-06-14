<!DOCTYPE html>
<html>
	<head>
        <?php include page('seller_header');?>
	</head>
	<body style="padding:10px;">
    	<blockquote class="layui-elem-quote">我的账务</blockquote>
        <div class="stop-info clearfix">
        <!-- 账户资金开始 -->
            <div class="stop-info-l">
                <div class="stop-info-title">账户资金</div>
                <div class="account-left-1 clearfix">
                    <div class="balance">账户余额<i class="question fa fa-question-circle-o"></i>：￥<?php echo $storeInfo['recharge_money']; ?></div>
                    <a href="<?php echo mobile_url('account',array('op'=>'outgold')); ?>" class="layui-btn tixian tixian-btn">提现</a>
                </div>
                <ul class="account-left-1-list clearfix" style="line-height: 30px;">
                    <li class="account-left-li-1">
                        <div class="">冻结余额<i class="question fa fa-question-circle-o"></i>：￥<?php echo $storeInfo['freeze_money']; ?></div>
                    </li>
                    <li class="account-left-li-2">
                        <div class="">提现处理中<i class="question fa fa-question-circle-o"></i>：￥<?php echo $tixian_money; ?></div>
                    </li>
                    <li class="account-left-li-3">
                        <div class="">开店保证金<i class="question fa fa-question-circle-o"></i>：￥<?php if($baozhengjin == 0 ){ echo '免费';}else{ echo $baozhengjin;} ?></div>
                    </li>
                </ul>
            </div>
            <!-- 账户资金结束 -->
            <!-- 应收款合计开始 -->
            <div class="stop-info-r">
                <div class="stop-info-title">待收款合计<!--<a href="#" class="account-right-a">结算增值服务</a>--></div>
                <div class="account-right-1 clearfix">
                    <div class="account-right-1-1">未确认收货金额：￥<?php echo $order_info['price']; ?></div>
                    <div class="account-right-1-2">未确认收货单数：<?php echo intval($order_info['order_num']); ?>单</div>
                </div>
            </div>
            <!-- 应收款合计结束 -->
        </div>

        <blockquote class="layui-elem-quote" style="margin-top:10px;">财务报表</blockquote>
        <div class="stop-info clearfix account-table-area">
            <!-- 收支表开始 -->
            <div class="stop-info-l" style="height:300px;">
                <div class="stop-info-title">销售额统计表<!--<a href="#" class="account-right-a">账单管理</a>--></div>
                <div id="stop-chart"></div>
            </div>
            <!-- 收支表结束 -->
            <!-- 月度财务简报开始 -->
            <div class="stop-info-r account-stop-info-r" style="height:300px;">
                <div class="stop-info-title">本月财务简报<!--<a href="#" class="account-right-a">查看统计图</a>--></div>
                <div class="account-right-1 clearfix">
                    <div class="account-right-1-div">本月简报</div>
                    <div>
                        <div class="account-right-1-left account-right-1-left-1">
                            <div>本月总收入</div>
                            <div class="in">￥<?php  echo $month_paylog['inmoney'];?></div>
                        </div>
                        <div class="account-right-1-left">
                            <div >本月总支出</div>
                            <div class="out">￥<?php  echo $month_paylog['outmoney'];?></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 月度财务简报结束 -->
        </div>

        <!-- 最近收支流水table 开始-->
        <blockquote class="layui-elem-quote" style="margin-top:10px;">最近收支流水</blockquote>
        <table class="layui-table">
            <thead>
                <tr>
                    <th>详情</th>
                    <th>发生时间</th>
                    <th>收支金额</th>
                </tr>
            </thead>
            <tbody>
            <?php  foreach($paylog_data as $item){ ?>
                <tr>
                    <td><img src="<?php echo $item['icon']; ?>" width="32"><?php echo $item['remark']; ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$item['createtime']);?></td>
                    <td>
                        <?php echo $item['fee']; ?>
                        <?php if($item['check_step'] == 1){
                            echo "<span class='layui-btn layui-btn-small'>等待审核</span>";
                        }else if($item['check_step'] == 2){
                            echo "<span class='layui-btn layui-btn-danger layui-btn-small'>审核失败</span>";
                        }else if($item['check_step'] == 3){
                            echo "<span class='layui-btn layui-btn-warm layui-btn-small'>提现成功</span>";
                        }

                        ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <!-- 最近收支流水table 结束-->

	</body>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/echarts.min.js"></script>   
<script type="text/javascript">
layui.use('element', function(){
    var $ = layui.jquery;
    var element = parent.layui.element(); //父级Tab（iframe的）的切换功能，切换事件监听等，需要依赖element模块
    $(".tixian-btn").on("click",function(){
        //$(window.parent.document).find(".tixian").trigger("click");
    });
});
$(function(){
    var line_data = '<?php echo json_encode($money_lie); ?>';
    line_data  = JSON.parse(line_data);
    var title_array = line_data.title;
    var series_array = [];
    for( var i = 0 ; i < title_array.length; i++ ){
        series_array.push({
            name:title_array[i],
            type:'line',
            stack: '总量',
            data:line_data.y_data[title_array[i]]
        });
    }

    $(".account-li-tab li").on("click",function(){
        $(this).addClass("li-active").siblings("li").removeClass("li-active");
    });
    option = {
        title: {
            text: ''
        },
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data:title_array
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: line_data.x_data
        },
        yAxis: {
            type: 'value'
        },
        series:series_array
    };
    var myChart = echarts.init(document.getElementById('stop-chart'));
        myChart.setOption(option);

})

</script>
</html>