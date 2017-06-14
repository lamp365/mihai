<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
    <?php include page('seller_header');?>
</head>
<style type="text/css">
#main{
    width: 80%;
    height: 600px;
}
</style>

<body style="padding-left:30px;padding-top:30px;">
    <ul class="layui-tab-title">
        <!-- layui-this代表当前选中的tab项 -->
        <li class="layui-this">综合评分</a></li>
        <li><a href="<?php echo mobile_url('comment',array('name'=>'seller','op'=>'index'));?>">差评</a></li>
        <li><a href="<?php echo mobile_url('comment',array('name'=>'seller','op'=>'goodComment'));?>">好评</a></li>
    </ul>
    <from class="layui-form layui-form-pane">
        <div class="layui-form-item">
        </div>
    </from>
    <div id="main"></div>
</body>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/echarts.min.js"></script>
<script>
    layui.use('form',function(){
        var form = layui.form();
    })
    $(function(){
        option = {
            title : {
                text: '综合评分',
//                subtext: '纯属虚构',
                x:'center'
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['好评','差评']
            },
            series : [
                {
                    name: '评分占比',
                    type: 'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    data:[
                        {value:<?php echo $num['good_num'];?>, name:'好评'},
                        {value:<?php echo $num['bad_num'];?>, name:'差评'},
                    ],
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };
        var myChart = echarts.init(document.getElementById('main'));
        myChart.setOption(option);
    })
</script>
</html>

