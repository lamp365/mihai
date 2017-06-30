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
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/fontawesome3/css/font-awesome.min.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/third-party/highcharts/highcharts.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<style type="text/css">
    body{
        background:none!important;
    }
    .payment-amount{
        width: 100%;
        height: 300px;
    }
    .payment-amount-area{
        float: left;
        width: 49%;
       
        overflow: hidden;
    }
    .number-area{
        float: left;
        width: 49%;
        margin-top: 45px;
    }
    .number-area ul{
        float: left;
        width: 50%;
    }
    .number-area img{
        width: 50px;
        height: 50px;
    }
    .payment-amount-area-left{
        float: left;
        overflow: hidden;
        width: 25%;
    }
    .payment-amount-area ul{
        width: 75%;
        float: left;
        overflow: hidden;
        font-size: 13px;
        line-height: 1.5;
        text-align: left;
    }
    .payment-amount-area li{
        float: left;
        overflow: hidden;
        width: 25%;
        padding-left: 2%;
        box-sizing: border-box;
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
        width: 70%;
        text-align: left;
        padding-left: 5%;
        color: #000;
        box-sizing: border-box;
        font-size: 14px;
        line-height: 1.5;
    }
    .payment-right div{
        height: 25px;
        line-height: 25px;
    }
    .access_amount{
        color: #000;
        line-height: 1.5;
    }
    .number-area li{
        font-size: 14px;
        margin: 15px 0 0 15px;
        overflow: auto;
    }
    .access-amount-head li,.shop-car-head li{
        float: left;
        margin-right: 7px;
    }
    .product-name-left{
        width: 60px;
        float: left;
    }
    .product-name-right{
        width: auto;
        float: left;
    }
    .workbench .today-presentation,.workbench .pending-order{
        box-sizing: initial;
    }
    .access-amount-table{
        margin-top: 20px;
    }
    .product-name-left img{
        width: 60px;
        height: 60px;
    }
    .product-name-time{
        margin-top: 15px;
    }
    .access-amount-table #begintime,.access-amount-table #endtime,.access-amount-table .search-input{
        height: 30px;
        border-radius: 4px;
        border: 1px solid #adadad;
        padding-left: 5px;
    }
    .shop-car-head #shopbegintime,.shop-car-head #shopendtime,.shop-car-head .search-input{
        height: 30px;
        border-radius: 4px;
        border: 1px solid #adadad;
        padding-left: 5px; 
    }
    .access-amount-head ul,.shop-car-head ul{
        overflow: auto;
        margin-bottom: 0;
    }
    .number-area i{
        width: 20px;
        text-align: center;
        display: inline-block;
    }
    .main-t{
        padding-top: 0;
    }
    .payment-amount-area h3{
        text-align: left;
        padding: 0;
        margin-left: 25px;
        font-size: 22px;
        font-weight: bold;
    }
    .payment-li-float{
        float: left;
        width: 50%;
    }

    .main-payment{
        width: 100%;
        overflow: hidden;
        background-color: #fff;
        border:1px solid #cad2e2;
    }
    .main-payment .small-title{
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 10px;
        font-size: 14px;
        line-height: 36px;
        height: 36px;
        font-weight: 700;
        color: #1b96a9;
        padding: 0 15px;
        background-color: #f7f7f7;
    }
    .access-amount-head,.shop-car-head{
        height: 36px;
        padding: 0;
        line-height: 36px;
        padding-left: 15px;
        border-bottom: none;
    }
    .access-amount-head .title,.shop-car-head .title{
        font-size: 16px;
        color: #1596ad;
        font-weight: 700;
    }
    .main-wrap .panel-default{
        border-color: #cad2e2;
        box-shadow: none;
    }
</style>
<script type="text/javascript">
	function hiddenall()
{
	 document.getElementById('container').style.display='none';
	   /* document.getElementById('container2').style.display='none';
	   document.getElementById('container3').style.display='none';*/
	
}

		</script>
</head>
<body >
<div class="main-wrap">

 </div>
 <?php  include page('footer');?>
     <script type="text/javascript">

    function myheight(){
        var myheight1 = $(".main-wrap").height()+120;
        $("#main",window.parent.document).height(myheight1);
    }
    myheight();
    
    //搜索查询ajax请求
    function searchBtn(){
        $(".access-amount-head .search-btn").on("click",function(){
            var begintime = $("#begintime").val();
            var endtime = $("#endtime").val();
            var search_input = $(".access-amount-head .search-input").val();
            var access_amount_html = "";
            $.post("",{beginTime:begintime,endTime:endtime,searchVal:search_input},function(data){
                if(data.errno==200){
                    $(".access-amount-html").html("");
                    access_amount_html += "<td><div class='product-name-left'><img src='http://hinrc.com/attachment/jpg/2016/08/534379437927161.jpg'></div>"+
            "<div class='product-name-right'><div>ON欧普特蒙一水肌酸纯肌酸粉600g健身增健肌粉肌肉爆发补充能量抗疲劳</div><div class='product-name-time'>"+
            "发布时间2016-01-03 18:16:06</div></div></td><td>12</td><td>12</td><td>12</td><td>12</td><td>12%</td><td><a href='javascript:;'>查看详情</a></td>"
                }else{
                    alert(data.errno);
                }
                $(".access-amount-html").html(access_amount_html);
            },"json")
        });
        $(".shop-car-head .search-btn").on("click",function(){
            var begintime = $("#shopbegintime").val();
            var endtime = $("#shopendtime").val();
            var search_input = $(".shop-car-head .search-input").val();
            var shop_car_html = "";
            $.post("",{beginTime:begintime,endTime:endtime,searchVal:search_input},function(data){
                if(data.errno==200){
                    $(".shop-car-html").html("");
                    shop_car_html += "<td>ON欧普特蒙一水肌酸纯肌酸粉600g健身增健肌粉肌肉爆发补充能量抗疲劳</td><td>12</td><td>12</td></tr>"
                }else{
                    alert(data.errno);
                }
                $(".shop-car-html").html(shop_car_html);
            },"json")
        });
    }
    searchBtn();
     </script>
     </body>
</html>