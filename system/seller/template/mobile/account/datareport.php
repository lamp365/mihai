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
	<div class="clearfix">
		<div class="block-left">
			<ul class="block-left-list">
				<li class="clearfix">
					<div class="ibox-title">总收入</div>
	                <div class="ibox-content">
	                    <div class="price-color">
	                    	<span><?php echo $data['wait_income']['store_money']; ?></span>
	                    </div>
	                    <div class="title-color">余额(元)
	                    	<span class="balance-right level-up">
	                    		<span>再接再厉</span>
	                    		<i class="fa fa-level-up" aria-hidden="true"></i>
	                    	</span>
	                    </div>
	                </div>
				</li>
				<li class="clearfix">
					<div class="ibox-title">待收入</div>
	                <div class="ibox-content">
	                    <div class="price-color">
	                    	<span><?php echo $data['wait_income']['wait_price']; ?></span>
	                    </div>
	                    <div class="title-color">待收货金额/元
	                    	<span class="balance-right level-up">
	                    		<span>再接再厉</span>
	                    		<i class="fa fa-level-up" aria-hidden="true"></i>
	                    	</span>
	                    </div>
	                </div>
				</li>
				<li class="clearfix">
					<div class="ibox-title">待收货</div>
	                <div class="ibox-content">
	                    <div class="price-color">
	                    	<span><?php echo $data['wait_income']['wait_order_num']; ?></span>
	                    </div>
	                    <div class="title-color">待收货单量/单
	                    	<span class="balance-right">
	                    		<span>再接再厉</span>
	                    		<i class="fa fa-level-up" aria-hidden="true"></i>
	                    	</span>
	                    </div>
	                </div>
				</li>
			</ul>
		</div>
		<div class="block-left block-left-right">
			<div id="main"></div>
		</div>
	</div>

	<div class="block-bottom">
		<div class="layui-form-item">
	      	<label class="layui-form-label">选择时间</label>
	      	<div class="layui-input-inline">
	        	<input type="text" name="begindate" id="begindate" lay-verify="begindate" placeholder="开始时间" autocomplete="off" class="layui-input" onclick="layui.laydate({elem: this})">
	      	</div>
	      	<div class="layui-input-inline">
	        	<input type="text" name="enddate" id="enddate" lay-verify="enddate" placeholder="截止时间" autocomplete="off" class="layui-input" onclick="layui.laydate({elem: this})">
	      	</div>
	    </div>
	    <div class="layui-form-item">
	      	<label class="layui-form-label">快捷选择</label>
	      	<div class="layui-input-block" id="">
	        	<div class="daybtn daycheck" data-type="today">今天</div>
	        	<div class="daybtn"  data-type="yestoday">昨天</div>
	        	<div class="daybtn"  data-type="week">本周</div>
	        	<div class="daybtn"  data-type="month">本月</div>
	        	<div class="daybtn"  data-type="lastmonth">上月</div>
	      	</div>
	    </div>
	    <div class="layui-form-item">
    		<label class="layui-form-label">
	    		<i class="fa fa-bar-chart" aria-hidden="true"></i>
				<span>详细数据</span>
			</label>
			<div class="layui-input-block">
				<ul class="detail-num clearfix">
					<li>
						<div class="detail-value money"><?php echo $data['sales_data_info']['money']; ?></div>
						<div class="detail-title">销售额/元</div>
					</li>
					<li>
						<div class="detail-value wait_money"><?php echo $data['sales_data_info']['wait_money']; ?></div>
						<div class="detail-title">待支付金额/元</div>
					</li>
					<li>
						<div class="detail-value pay_num"><?php echo $data['sales_data_info']['pay_num']; ?></div>
						<div class="detail-title">已支付单量/单</div>
					</li>
					
					<li>
						<div class="detail-value wait_num"><?php echo $data['sales_data_info']['wait_num']; ?></div>
						<div class="detail-title">待支付单量/单</div>
					</li>
					<li>
						<div class="detail-value conversion"><?php echo $data['sales_data_info']['conversion']; ?></div>
						<div class="detail-title">转化率</div>
					</li>
				</ul>
			</div>
	    </div>
	</div>
	
</body>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/echarts.min.js"></script>
	
<script>
	layui.use(["form","laydate",'layer'],function(){
		var form = layui.form();
		var laydate = layui.laydate;
		var layer = layui.layer;
		var start_time = "";
		var end_time = "";
		var start = {
		   	max: '2099-06-16 23:59:59'
		    ,istoday: false
		    ,choose: function(datas){
		      end.min = datas; //开始日选好后，重置结束日的最小日期
		      start_time = end.start = datas //将结束日的初始值设定为开始日
		      var url = "<?php echo mobile_url('datareport',array('op'=>'ajaxSalesInfo')) ?>";
		      	url = url+"?type=time"+"&start_time="+start_time+"&end_time="+end_time;
		      	if( end_time != ""){
		      		reset_data_info(url);
		      	}else{
		      		//layer.alert("请选择开始时间");
		      	}
		    }
		  };
		  
		  var end = {
		    max: '2099-06-16 23:59:59'
		    ,istoday: false
		    ,choose: function(datas){
		    	end_time = datas;
		      	start.max = datas; //结束日选好后，重置开始日的最大日期
		      	var url = "<?php echo mobile_url('datareport',array('op'=>'ajaxSalesInfo')) ?>";
		      	url = url+"?type=time"+"&start_time="+start_time+"&end_time="+datas;
		      	if( start_time != ""){
		      		reset_data_info(url);
		      	}else{
		      		//layer.alert("请选择开始时间");
		      	}
		    }
		  };
		  document.getElementById('begindate').onclick = function(){
		    start.elem = this;
		    laydate(start);
		  }
		  document.getElementById('enddate').onclick = function(){
		    end.elem = this
		    laydate(end);

		  }
	});
	$(function(){
		var title     = '<?php echo json_encode($data['visted_rate']['title']); ?>';
		var rate_data = '<?php echo json_encode($data['visted_rate']['rate']); ?>';
		title     = JSON.parse(title);
		rate_data = JSON.parse(rate_data);
        option = {
		    title : {
		        text: '今日访客',
		        subtext: '数据比例',
		        x:'center'
		    },
		    tooltip : {
		        trigger: 'item',
		        formatter: "{a} <br/>{b} : {c} ({d}%)"
		    },
		    legend: {
		        orient: 'vertical',
		        left: 'left',
		        data: title
		    },
		    series : [
		        {
		            name: '访问来源',
		            type: 'pie',
		            radius : '65%',
		            center: ['50%', '60%'],
		            data:rate_data,
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
        // 快捷选择
        $(".daybtn").on("click",function(){
        	if( $(this).hasClass("daycheck") ){
        		$(this).removeClass("daycheck");
        	}else{
        		$(".daybtn").removeClass("daycheck");
        		$(this).addClass("daycheck");
        	}
			var type = $(this).data('type');
			var url = "<?php echo mobile_url('datareport',array('op'=>'ajaxSalesInfo')) ?>";
			url = url+"?type="+type;
			reset_data_info(url);
        });
        /*选择时间*/

    });

	function reset_data_info(url){
		$.post(url,"",function(data){
			if( data.errno == 1 ){
				$(".money").text(data.data.money);
				$(".pay_num").text(data.data.pay_num);
				$(".wait_money").text(data.data.wait_money);
				$(".wait_num").text(data.data.wait_num);
				$(".conversion").text(data.data.conversion);
			}else{
				layer.alert(data.message);
			}
		},"json");
	}
	//时间类型的额 ?type=time&start_time=2017-12-05&end_time=2017-04-05
</script>
</html>