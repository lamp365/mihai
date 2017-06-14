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
.nav-tabs li a{
    padding-left: 18px;
    padding-right: 18px;
    text-align: center;
}
</style>

<body style="padding-left:30px;padding-top:30px;">
    <ul class="nav nav-tabs" >
        <li style="" <?php  if( !isset($_GET['status']) ) { ?> class="active"<?php  } ?>><a href="<?php echo mobile_url('pingfen',array('name'=>'seller','op'=>'index'));?>">商品评分</a></li>
        <li style="" <?php  if(isset($_GET['status']) && $_GET['status'] == 1) { ?> class="active"<?php  } ?>><a href="<?php echo mobile_url('pingfen',array('name'=>'seller','op'=>'synthetic','status'=>1));?>">综合占比</a></li>
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
                text: '综合评分<?php echo $enter_rate+$comment_rate;?>',
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
                data: ['入驻人数','评论分']
            },
            series : [
                {
                    name: '评分占比',
                    type: 'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    data:[
                        {value:<?php echo $enter_rate;?>, name:'入驻人数'},
                        {value:<?php echo $comment_rate;?>, name:'评论分'},
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

