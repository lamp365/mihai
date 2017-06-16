<!DOCTYPE html>
<html>
	<head>
        <?php include page('seller_header');?>
		<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/third-party/highcharts/highcharts.js"></script>
	</head>
    <style type="text/css">
    .block-bottom{
            width: 98%;
            height: auto;
            background-color: #fff;
            padding: 15px;
            margin: 0 auto;
            border-color: #e7eaec;
            -webkit-border-image: none;
            -o-border-image: none;
            border-image: none;
            border-style: solid solid none;
            border-width: 4px 0 0;
        }
        .ui-block-head{
            position: relative;
            padding: 10px;
            background: #f8f8f8;
        }
        .ui-block-head h3 {
            display: inline-block;
            margin: 0 12px 0 0;
            padding: 0 0 0 10px;
            border-left: 4px solid #f70;
            font-size: 14px;
            font-weight: bold;
            line-height: 20px;
        }
        .do-best ul li{
            float: left;
            width: 120px;
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        .do-best{
            margin-top: 20px;
        }   
        .best-icon img{
            width: 80px;
            height: 80px;
            border-radius: 80px;
            border: 1px solid #cecece;
            box-sizing: border-box;
            padding: 4px;
        }
        .do-best h3{
            height: 25px;
            line-height: 25px;
            overflow: hidden;
        }
        .qrcode{
            position: absolute;
            top: 120px;
            left: -30px;
            z-index: 99;
            background: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            box-sizing: border-box;
            border-radius: 3px;
        }
    </style>
	<body style='background:#ebebeb;'>
		<div class="admin-main">
            <div class="admin-main-tip clearfix">
                <div class="admin-main-tip-left">
                    <div><?php echo $store_title;?>；逾期未订购的店铺将被打烊，影响店铺的正常运营。</div>
                    <div class="tip1">官方咨询电话： <span class="phone-number">0591-85210086</span></div>
                </div>
                <?php
                  if($dataStore['is_free'] <= 0)
                  {
                ?>
                <div class="layui-btn layui-btn-normal recharge-btn"><a href="javascript:void(0);" data-url="<?php echo mobile_url('main',array('op'=>'diamain')); ?>" id="ljxq" >立即续期</a></div>
                <?php
                  }
                ?>
            </div>
			<div class="admin-main-blocks clearfix">
				<div class="admin-main-block">
					<div>账户余额</div>
					<div>18888.71元</div>
				</div>
				<div class="admin-main-block">
					<div>点击量</div>
					<div>1000000</div>
				</div>
				<div class="admin-main-block">
					<div>点击率</div>
					<div>66.66%</div>
				</div>
				<div class="admin-main-block">
					<div>总成交笔数</div>
					<div>888</div>
				</div>
				<div class="admin-main-block">
					<div>总成交金额</div>
					<div>88888.55元</div>
				</div>
			</div>
			<div id="container" style="width:99.4%;height:230px;"></div>
		</div>
        <div class="block-bottom" style="margin-top:1%;padding-bottom: 200px;">
            <div class="ui-block-head">
                <h3>他们做得不错</h3>
            </div>
            <div class="do-best">
                <ul class="clearfix">
                    <li>
                        <div class="best-icon" dataurl="www.helloweba.com">
                            <img src="https://img.yzcdn.cn/upload_files/2015/10/22/016b17bcf390f422ed734d9d54cc3ed0.jpeg" onerror="this.src='https://b.yzcdn.cn/v2/image/no_pic.png'">
                        </div>
                        <h3 title="良品铺子美食旅行">良品铺子美食旅行</h3>
                    </li>
                    <li>
                        <div class="best-icon" dataurl="www.helloweba.com">
                            <img src="https://img.yzcdn.cn/upload_files/2016/04/06/c4c6492a140431b6e031980b1e5f4203.jpeg" onerror="this.src='https://b.yzcdn.cn/v2/image/no_pic.png'">
                        </div>
                        <h3 title="良品铺子美食旅行">良品铺子美食旅行</h3>
                    </li>

                </ul>
            </div>
        </div><div id="alterModal" class="alertModalBox"></div>
	</body>
    <script type="text/javascript" src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/js/jquery.qrcode.min.js"></script>    
<script type="text/javascript">
	$('#container').highcharts({
    title: {
        text: '不同城市的月平均气温',
        x: -20
    },
    subtitle: {
        text: '数据来源: WorldClimate.com',
        x: -20
    },
    xAxis: {
        categories: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月']
    },
    yAxis: {
        title: {
            text: '温度 (°C)'
        },
        plotLines: [{
            value: 0,
            width: 1,
            color: '#808080'
        }]
    },
    tooltip: {
        valueSuffix: '°C'
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle',
        borderWidth: 0
    },
    series: [{
        name: '柏林',
        data: [-0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0]
    }, {
        name: '伦敦',
        data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
    }]
});

$(function(){
    $(".do-best .best-icon img").mouseover(function(){
        var qrcode_hength = $(this).parents("li").find(".qrcode").length;
        //dataurl 二维码的地址
        var url = $(this).parent(".best-icon").attr("dataurl");
        if( qrcode_hength == 0 ){
            $(this).parents("li").append('<div class="qrcode"></div>');
            $(".qrcode").qrcode({
                render: "canvas",                  //table方式
                width: 150,                       //宽度
                height:150,                       //高度
                background: "#ffffff",            //背景颜色
                foreground: "#333",                //前景颜色
                text: url         //任意内容
            });
        }
    });

    $(".do-best .best-icon img").mouseout(function(){
        $(".qrcode").remove();
    });
    
    $('#ljxq').on('click',function(){
        var url = $(this).data('url');
        $.ajaxLoad(url,{},function(){
            $('#alterModal').modal('show');
        });
    })
})
    
</script>
</html>