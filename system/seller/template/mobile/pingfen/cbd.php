<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php include page('seller_header');?>
    <style type="text/css">
    	body{
    		background-color:#f3f3f4;
    	}
    	.block-left{
		    float: left;
		    width: 48.5%;
		    height: 400px;
		    background-color: #fff;
		    margin: 1%;
		    padding: 15px;
		    border-color: #e7eaec;
		    -webkit-border-image: none;
		    -o-border-image: none;
		    border-image: none;
		    border-style: solid solid none;
		    border-width: 4px 0 0;
    	}
    	.block-left-right{
    		margin-left:0;
    	}
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
		.payment-right {
		    text-align: left;
		    color: #000;
		    box-sizing: border-box;
		    font-size: 14px;
		    line-height: 1.5;
		}
		.title-color{
			color: #676a6c;
			font-size: 12px;
		}
		.price-color{
			color: #676a6c;
			font-size: 30px;
		}
		.balance{
			/*font-size: 24px;*/
		}
		.balance span{
			font-size: 14px;
		}
		#main{
			width: 90%;
			margin:0 auto;
			height: 100%;
		}
		.daybtn{
			float: left;
			margin-right: 10px;
			height: 30px;
			border-radius: 3px;
			width: 80px;
			text-align: center;
			line-height: 30px;
			color: #797979;
			cursor: pointer;
			border:1px solid #cdcdcd;
		}
		.daycheck{
			border-color: #f9ae9a;
			color: #f36e58;
		}
		.detail-num li{
			float: left;
			width: 200px;
			text-align: center;
			border: 1px solid #cdcdcd;
		    border-radius: 3px;
		    margin-right: 10px;
		    margin-bottom: 10px;
		    padding: 10px 0;
		}
		.detail-value{
			font-size: 25px;
		}
		.detail-title{
			color: #acacac;
		}
		.ibox-title{
			font-size: 14px;
			font-weight: 600;
			color: #676a6c;
			border-bottom: 1px solid #e7eaec;
			padding: 0 15px 7px 0;
		}
		.ibox-content{
			background-color: #fff;
		    color: inherit;
		    padding: 15px 20px 20px 0;
		}
		.balance-right{
			float: right;
			font-size: 14px;
			color: #1ab394;
		}
		.level-up{
			color: #ed5565;
		}
    </style>
</head>
<body>
    <form class="layui-form " action="">
        <div class="clearfix">
            <div class="block-left">
                <ul class="block-left-list">
                    <li class="clearfix">
                        <div class="ibox-title">CBD总分计算规则：
                            充值金额*<?php echo $settings['bid_exchange'] ?>
                            +订单成交金额*<?php echo $settings['order_num_exchange'] ?>
                            + 邀请注册会员数*<?php echo $settings['enter_exchange'] ?>
                            +商品评价得分*<?php echo $settings['comment_exchange'] ?></div>
                        <div class="ibox-content">
                            <div class="price-color">
                                <span><?php echo FormatMoney($store_shop['recharge_money'],0) ?></span>
                            </div>
                            <div class="title-color">充值金额(元)
                                <span class="balance-right level-up">
    <!--	                    		<span>再接再厉</span>
                                    <i class="fa fa-level-up" aria-hidden="true"></i>-->
                                </span>
                            </div>
                        </div>
                    </li>
                    <li class="clearfix">
                        <div class="ibox-title"></div>
                        <div class="ibox-content">
                            <div class="price-color">
                                <span><?php echo $store_shop['order_money'] ?></span>
                            </div>
                            <div class="title-color">订单成交金额/元
                                <span class="balance-right level-up">
    <!--	                    		<span>再接再厉</span>
                                    <i class="fa fa-level-up" aria-hidden="true"></i>-->
                                </span>
                            </div>
                        </div>
                    </li>
                    <li class="clearfix">
                        <div class="ibox-title"></div>
                        <div class="ibox-content">
                            <div class="price-color">
                                <span><?php echo $store_shop['friend_count']?></span>
                            </div>
                            <div class="title-color">邀请注册会员数
                                <span class="balance-right">
    <!--	                    		<span>下降咯</span>
                                    <i class="fa fa-level-down" aria-hidden="true"></i>-->
                                </span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="block-left block-left-right">
                <div  class="layui-input-inline" style=" float: right;">
                    <select name="region_code"  id="region_select" lay-filter="region_select" >
                        <?php foreach ($same_region_list as $value) { ?>
                        <option value="<?php echo  $value['region_code'] ?>"  <?php if( $_GP['region_code'] ==$value['region_code'] ){  ?> selected="true" <?php  }?>   >  <?php echo  $value['region_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div id="main"></div>
            </div>
        </div>
    </form>
</body>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/echarts.min.js"></script>	
<script>
	layui.use(["form","laydate"],function(){
		var form = layui.form();
		var laydate = layui.laydate;
        
        //    省市区JS   START ↓
        form.on('select(region_select)', function(data){
            var region_code = $('#region_select').val();
            var url = '<?php echo mobile_url('pingfen',array('op'=>'cbd')) ?>';
            location.href =     url+"?region_code="+region_code;
        });
	});
	$(function(){
          option = {
            title : {
                text:  '排名第<?php echo $store_shop['rownum'];?>名',
                subtext:'综合评分<?php echo $enter_rate+$comment_rate+FormatMoney($recharge_rate,0)+$order_rate;?>',
                x:'center'
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['入驻人数','评论分','竞价','订单额']
            },
            series : [
                {
                    name: '评分占比',
                    type: 'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    data:[
                        {value:<?php echo $store_shop['f_rate'];?>, name:'入驻人数'},
                        {value:<?php echo $store_shop['p_rate'];?>, name:'评论分'},
                        {value:<?php echo FormatMoney($store_shop['r_rate'],0);?>, name:'竞价'},
                        {value:<?php echo $store_shop['o_rate'];?>, name:'订单额'}
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
        window.onresize = myChart.resize;

        $(".daybtn").on("click",function(){
        	if( $(this).hasClass("daycheck") ){
        		$(this).removeClass("daycheck");
        	}else{
        		$(".daybtn").removeClass("daycheck");
        		$(this).addClass("daycheck");
        	}
        })
    });
    
    
</script>
</html>