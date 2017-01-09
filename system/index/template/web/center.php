<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit">
<meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1"/>
<title>首页</title>

<link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/bootstrap3/css/bootstrap.min.css" />   
<link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/index/css/c.css" />   
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/third-party/highcharts/highcharts.js"></script>
<style type="text/css">
    .payment-amount{
        width: 100%;
        height: 300px;
    }
    .payment-amount-area{
        float: left;
        width: 49%;
        margin-top: 50px;
    }
    .number-area{
        float: left;
        width: 49%;
        margin-top: 50px;
    }
    .number-area ul{
        float: left;
        width: 33.3%;
    }
    .number-area img{
        width: 50px;
        height: 50px;
    }
    .payment-amount-area ul{
        width: 100%;
        overflow: hidden;
        font-size: 14px;
        line-height: 1.5;
        text-align: left;
    }
    .payment-amount-area li{
        float: left;
        overflow: hidden;
        width: 25%;
        padding-left: 2%;
        box-sizing: border-box;
        line-height: 2;
        padding-top: 15px;
        padding-bottom: 15px;
    }
    .payment-left{
        float: left;
        width: 8%;
        min-width: 50px;
        text-align: right;
    }
    .payment-left img{
        max-width: 50px;
        height: 50px;
    }
    .payment-right{
        float: left;
        width: 80%;
        text-align: left;
        padding-left: 20px;
        color: #000;
        box-sizing: border-box;
        font-size: 16px;
        line-height: 1.5;
    }
    .access_amount{
        color: #000;
        font-size: 16px;
        line-height: 1.5;
    }
    .number-area li{
        font-size: 14px;
        line-height: 2;
        margin-bottom: 20px;
    }
</style>
<script type="text/javascript">
	function hiddenall()
{
	 document.getElementById('container').style.display='none';
	   /* document.getElementById('container2').style.display='none';
	   document.getElementById('container3').style.display='none';*/
	
}
$(function () {
    
    $('#container').highcharts({
    	 credits: {
          enabled:false
				},
        chart: {
            type: 'column'
        },
        title: {
            text: '本周订单统计'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '{point.y}￥</b>'
        },
        series: [{
            name: 'Population',  
             color: 'rgba(126,86,134,.9)',
            data: [
        		<?php  $index=0?>
            	<?php  if(is_array($chartdata1)) { foreach($chartdata1 as $item) { ?>
                ['<?php  echo $item['dates'];?>', <?php  echo $item['counts'];?>],	
          <?php  $index++?>
                	<?php  } } ?>
            ],
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
    /*
    
      $('#container2').highcharts({
    	 credits: {
          enabled:false
				},
        chart: {
            type: 'column'
        },
        title: {
            text: '本月订单统计'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '{point.y}￥</b>'
        },
        series: [{
            name: 'Population',  
             color: 'rgba(126,86,134,.9)',
            data: [
        		<?php  $index=0?>
            	<?php  if(is_array($chartdata2)) { foreach($chartdata2 as $item) { ?>
                ['<?php  echo $item['dates'];?>', <?php  echo $item['counts'];?>],	
          <?php  $index++?>
                	<?php  } } ?>
            ],
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
    
    
    
      $('#container3').highcharts({
    	 credits: {
          enabled:false
				},
        chart: {
            type: 'column'
        },
        title: {
            text: '本年订单统计'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '{point.y}￥</b>'
        },
        series: [{
            name: 'Population',  
             color: 'rgba(126,86,134,.9)',
            data: [
        		<?php  $index=0?>
            	<?php  if(is_array($chartdata3)) { foreach($chartdata3 as $item) { ?>
                ['<?php  echo $item['dates'];?>', <?php  echo $item['counts'];?>],	
          <?php  $index++?>
                	<?php  } } ?>
            ],
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });*/
	  hiddenall();
	  document.getElementById('container').style.display='block';
      //支付金额初始化
    Highcharts.setOptions({
        colors: ['#ff426d', '#27b9e5', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
    });
    $('#paymentAmount').highcharts({
        credits: {
            enabled:false
        },
        title: {
            text: '24小时累计图',
            x: -20 //center
        },
        subtitle: {
            text: '24小时累计图',
            x: -20
        },
        chart: {
            borderColor: '#f0f0f0',
            borderWidth: 1,
            type: 'line'
        },
        xAxis: {
            categories: ['0h', '6h', '12h', '18h', '24h']
        },
        yAxis: {
            title: {
                text: '支付金额 (K)'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#ddd'
            }]
        },
        tooltip: {
            valueSuffix: 'k'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: '今日',
            data: [7.0, 6.9, 9.5, 14.5, 18.2]
        }, {
            name: '昨日',
            data: [1, 0.8, 5.7, 11.3, 17.0]
        }]
    });


});

		</script>
</head>
 <body onload="myheight()">
<div class="main-wrap">
			

	<div class="workbench">
		<!--begin map-->
	
		<!--end map-->
		<!--begin main-->
        <div style="width: 100%;overflow: hidden;">
            <div class="payment-amount-area" style="margin-right: 1%">
                <div style="overflow: hidden;"> 
                    <div class="payment-left">
                        <img src="<?php echo RESOURCE_ROOT;?>addons/common/image/payment_amount.png">
                    </div>
                    <div class="payment-right">
                        <div>支付金额</div>
                        <div>12,695</div>
                    </div>
                </div>
                <ul>
                    <li class="ios-android">
                        <div>IOS支付</div>
                        <div>110,000</div>
                    </li>
                    <li class="ios-android">
                        <div>android支付</div>
                        <div>110,000</div>
                    </li>
                    <li class="pc-wap">
                        <div>PC支付</div>
                        <div>110,000</div>
                    </li>
                    <li class="pc-wap">
                        <div>WAP支付</div>
                        <div>110,000</div>
                    </li>
                </ul>
                <div class="payment-amount" id="paymentAmount"></div>
            </div>
            <div class="number-area" style="margin-left: 1%">
                <ul>
                    <li>
                        <div>
                            <img src="<?php echo RESOURCE_ROOT;?>addons/common/image/access_amount.png">
                        </div>
                        <div class="access_amount">
                            <div>访客数量</div>
                            <div>12695</div>
                        </div>
                    </li>
                    <li>
                        <span>IOS访客</span>
                        <span>110000</span>
                    </li>
                    <li>
                        <span>android访客</span>
                        <span>110000</span>
                    </li>
                    <li>
                        <span>PC访客</span>
                        <span>110000</span>
                    </li>
                    <li>
                        <span>wap访客</span>
                        <span>110000</span>
                    </li>
                </ul>
                <ul>
                    <li>
                        <div>
                            <img src="<?php echo RESOURCE_ROOT;?>addons/common/image/page_views.png">
                        </div>
                        <div class="access_amount">
                            <div>浏览量</div>
                            <div>12695</div>
                        </div>
                    </li>
                    <li>
                        <span>IOS浏览量</span>
                        <span>110000</span>
                    </li>
                    <li>
                        <span>android浏览量</span>
                        <span>110000</span>
                    </li>
                    <li>
                        <span>PC浏览量</span>
                        <span>110000</span>
                    </li>
                    <li>
                        <span>wap浏览量</span>
                        <span>110000</span>
                    </li>
                </ul>
                <ul>
                    <li>
                        <div>
                            <img src="<?php echo RESOURCE_ROOT;?>addons/common/image/buyers_num.png">
                        </div>
                        <div class="access_amount">
                            <div>支付买家数</div>
                            <div>12695</div>
                        </div>
                    </li>
                    <li>
                        <span>IOS买家数</span>
                        <span>110000</span>
                    </li>
                    <li>
                        <span>android买家数</span>
                        <span>110000</span>
                    </li>
                    <li>
                        <span>PC买家数</span>
                        <span>110000</span>
                    </li>
                    <li>
                        <span>wap买家数</span>
                        <span>110000</span>
                    </li>
                </ul>
              
            </div>
        </div>
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">访问数TOP50</div>
            <!-- Table -->
            <table class="table">
                <tr>
                    <td>1</td>
                    <td>12</td>
                </tr>
            </table>
        </div>
		<div class="main-t clearfix" style="min-height:200px;">
			<div class="work-bench-r" >
		        <div class="pending-order">
		            <dl>
		                <dt><span class="title">待处理订单</span></dt>
		                <dd><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => 1))?>">待发货：<?php echo $needsend_count ?>笔</a>￥<?php echo $needsend__price ?></dd>
		                <dd><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => -4))?>">退换货：<?php echo $returnofgoods_count ?>笔</a>￥<?php echo $returnofgoods_price ?></dd>
		               <dd><a href="<?php  echo create_url('site',  array('name' => 'shop','do'=>'order','op' => 'display', 'status' => -2))?>">退款单：<?php echo $returnofmoney_count ?>笔</a>￥<?php echo $returnofmoney_price ?></dd>
		              
		            </dl>
		        </div>
		    </div>
		    <div class="work-bench-l" >
		        <!--begin 今日简报-->
		        <div class="today-presentation">
		            <dl>
		                <dt>
		                    <span class="totay-1">今日简报</span>
		                    <span class="totay-2">订单</span>
		                    <span class="totay-3">订单金额</span>
		                    <span class="totay-4">已退货单</span>
		                    <span class="totay-5">已退货金额</span>
		                </dt>
		                <dd>
		                    <span class="totay-1">今日</span>
		                    <span class="totay-2"><?php echo $todayordercount ?>笔</span>
		                    <span class="totay-3">￥<?php echo $todayorderprice ?></span>
		                    <span class="totay-4"><?php echo $todayordercount_re ?>笔</span>
		                    <span class="totay-5">￥<?php echo $todayorderprice_re ?></span>
		                </dd>
		                <dd>
		                    <span class="totay-1">本月</span>
		                    <span class="totay-2"><?php echo $monthordercount ?>笔</span>
		                    <span class="totay-3">￥<?php echo $monthorderprice ?></span>
		                    <span class="totay-4"><?php echo $monthordercount_re ?>笔</span>
		                    <span class="totay-5">￥<?php echo $monthorderprice_re ?></span>
		                </dd>
		                <dd>
		                    <span class="totay-1">本年</span>
		                    <span class="totay-2"><?php echo $yearordercount ?>笔</span>
		                    <span class="totay-3">￥<?php echo $yearorderprice ?></span>
		                    <span class="totay-4"><?php echo $yearordercount_re ?>笔</span>
		                    <span class="totay-5">￥<?php echo $yearorderprice_re ?></span>
		                </dd>
		            </dl>
		        </div>
		        <!--end 今日简报-->		        
		    </div>
			<!--begin 业务简报-->
		        <div class="business-presentation" >
		        	<dl>
		                <dt class="briefreporttab"><span class="title">业务简报</span>
		               <!-- 	<span class="briefreporttab-radios">
		                		
			                	<input type="radio" name="dateSegment" value="4" onclick="if(this.checked){hiddenall();document.getElementById('container').style.display='block';}" checked/>周
			                	<input type="radio" name="dateSegment" value="4"  onclick="if(this.checked){hiddenall();document.getElementById('container2').style.display='block';}" />月
			                	<input type="radio" name="dateSegment" value="6"  onclick="if(this.checked){hiddenall();document.getElementById('container3').style.display='block';}" />年
		                	</span>-->
			           
						</dt>
	                </dl>
	                <div class="order-unit">订货金额（元）</div>
	                <div id="container" style="width:98%;height:230px; margin: 0 auto"></div>
	               <!--   <div id="container2" style="width:98%;height:230px; margin: 0 auto"></div>
	               <div id="container3" style="width:98%;height:230px; margin: 0 auto"></div>-->
		        </div>
		        <!--end 业务简报-->
		</div>
    </div>
</div>
     <?php  include page('footer');?>
     <script type="text/javascript">
    function myheight(){
        var myheight1 = $(".main-wrap").height()+80;
        $("#main",window.parent.document).height(myheight1);
    }
     </script>
     </body>
</html>