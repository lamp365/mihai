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
                    <a href="<?php echo mobile_url(''); ?>" class="layui-btn tixian tixian-btn">提现</a>
                </div>
                <ul class="account-left-1-list clearfix">
                    <li class="account-left-li-1">
                        <div class="">冻结余额<i class="question fa fa-question-circle-o"></i>：￥<?php echo $storeInfo['freeze_money']; ?></div>
                    </li>
                    <li class="account-left-li-2">
                        <div class="">提现处理中<i class="question fa fa-question-circle-o"></i>：￥960,200.00</div>
                    </li>
                    <li class="account-left-li-3">
                        <div class="">开店保证金<i class="question fa fa-question-circle-o"></i>：￥10000.00</div>
                    </li>
                </ul>
            </div>
            <!-- 账户资金结束 -->
            <!-- 应收款合计开始 -->
            <div class="stop-info-r">
                <div class="stop-info-title">应收款合计<a href="#" class="account-right-a">结算增值服务</a></div>
                <div class="account-right-1 clearfix">
                    <div class="account-right-1-1">已发货未确认收货金额：￥223,973.00</div>
                    <div class="account-right-1-2">待结算金额：￥223,973.00</div>
                </div>
            </div>
            <!-- 应收款合计结束 -->
        </div>  
        <!-- 最近收支流水table 开始-->
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>最近收支流水</legend>
        </fieldset>
        <table class="layui-table">
            <thead>
                <tr>
                    <th>发生时间</th>
                    <th>类型</th>
                    <th>单据编号</th>
                    <th>收支金额</th>
                    <th>备注</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2017-04-14 01:05:05</td>
                    <td>订单</td>
                    <td>112233444</td>
                    <td>￥10.29</td>
                    <td>备注备注备注备注备注备注备注</td>
                </tr>
            </tbody>
        </table>
        <!-- 最近收支流水table 结束-->
        <div class="stop-info clearfix account-table-area">
        <!-- 收支表开始 -->
            <div class="stop-info-l">
                <div class="stop-info-title">收支表<a href="#" class="account-right-a">账单管理</a></div>
                <div class="account-left-1 clearfix">
                    <div class="balance time">2017-04-01 ~ 2017-04-30</div>
                    <div class="tixian account-li-tab">
                        <ul>
                            <li>本周</li>
                            <li class="li-active">本月</li>
                            <li class="li-last">本季度</li>
                        </ul>
                    </div>
                </div>
                <div class="account-left-1-list">
                    <table class="layui-table">
                        <thead>
                            <tr>
                                <th>收入（元）</th>
                                <th>支出（元）</th>
                                <th>总计（元）</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="in">￥183,106.13</td>
                                <td class="out">-￥183,106.13</td>
                                <td class="in">￥183,106.13</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- 收支表结束 -->
            <!-- 月度财务简报开始 -->
            <div class="stop-info-r account-stop-info-r">
                <div class="stop-info-title">月度财务简报<a href="#" class="account-right-a">查看统计图</a></div>
                <div class="account-right-1 clearfix">
                    <div class="account-right-1-div">3月简报</div>
                    <div>
                        <div class="account-right-1-left account-right-1-left-1">
                            <div>月总收入</div>
                            <div class="in">￥168486468</div>
                        </div>
                        <div class="account-right-1-left">
                            <div >月总支出</div>
                            <div class="out">￥168486468</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 月度财务简报结束 -->
        </div>  
	</body>

<script type="text/javascript">
layui.use('element', function(){
    var $ = layui.jquery;
    var element = parent.layui.element(); //父级Tab（iframe的）的切换功能，切换事件监听等，需要依赖element模块
    $(".tixian-btn").on("click",function(){
        $(window.parent.document).find(".tixian").trigger("click");
    });
});
$(".account-li-tab li").on("click",function(){
    $(this).addClass("li-active").siblings("li").removeClass("li-active");
});

</script>
</html>