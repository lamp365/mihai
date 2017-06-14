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
        <div class="layui-form-item" style="margin-top: 15px;">
            <label class="layui-form-label">请选择产品</label>
            <div class="layui-input-inline">
                <select name="p1" lay-filter="p1" id="p1">
                    <option value="0">请选择栏目</option>
                    <?php if (!empty($category)){foreach ($category as $key=>$val){?>
                    <option value="<?php echo $val['id'];?>"><?php echo $val['name'];?></option>
                    <?php }}?>
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="p2" lay-filter="p2" id="p2">
                    <option value="0">请选择栏目</option>
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="dishid" id="dishid">
                    <option value="0">请选择商品</option>
                </select>
            </div>
            <button class="layui-btn" type="button" name="submit" id="tijiao" lay-submit="" lay-filter="">搜索</button>
        </div>
    </from>
    <div id="main"></div>
</body>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/echarts.min.js"></script>
<script>
    layui.use('form',function(){
        var form = layui.form();
        form.on('select(p1)', function(data){
        	$.ajax({
                type: "post",
                url: "<?php echo mobile_url('pingfen',array('name'=>'seller','op'=>'getSecondMenu')); ?>",
                data: {id:data.value},
                dataType: "json",
                success: function(data){
                	$("#p2").html('<option value="0">请选择栏目</option>');//清空二级栏目表单
                    $("#dishid").html('<option value="0">请选择商品</option>');//清空商品表单
                    if(data.errno != 0){
                        if(data.errno == 1){
                        	$.each(data['data'], function(key, val){
                        		$("#p2").append("<option value="+val.id+">"+val.name+"</option>");
                          	});
                        }
                    }
                    form.render();
                 }
            });
            
      	});
        form.on('select(p2)', function(data){
        	$.ajax({
                type: "post",
                url: "<?php echo mobile_url('pingfen',array('name'=>'seller','op'=>'getdishList')); ?>",
                data: {p1:$("#p1").val(),p2:data.value},
                dataType: "json",
                success: function(data){
                	$("#dishid").html('<option value="0">请选择商品</option>');//清空商品表单
                    if(data.errno != 0){
                        if(data.errno == 1){
                            $.each(data['data'], function(key, val){
                        		$("#dishid").append("<option value="+val.id+">"+val.title+"</option>");
                          	});
                        }
                    }
                    form.render();
                 }
            });
            
      	});
      	
    })
    $(function(){
        option = {
            /* title : {
                text: '综合评分10000',
                subtext: '纯属虚构',
                x:'center'
            }, */
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['物流评分','服务评分','产品评分']
            },
            series : [
                {
                    name: '评分占比',
                    type: 'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    data:[
                        {value:<?php echo $total['all_wl_rate'];?>, name:'物流评分'},
                        {value:<?php echo $total['all_fw_rate'];?>, name:'服务评分'},
                        {value:<?php echo $total['all_cp_rate'];?>, name:'产品评分'}
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
    $("#tijiao").click(function(){
        var p1 = $("#p1").val();
        var p2 = $("#p2").val();
        var dishid = $("#dishid").val();
        if(p1 == 0){
        	layer.open({
                title: '提示',
                content: '请选择条件'
            });
        	return false;
        }
    	$.ajax({
            type: "post",
            url: "<?php echo mobile_url('pingfen',array('name'=>'seller','op'=>'search')); ?>",
            data: {p1:p1,p2:p2,dishid:dishid},
            dataType: "json",
            success: function(data){
                if(data.errno == 0){
                	layer.open({
                        title: '提示',
                        content: '请选择条件'
                    });
                	return false;
                }
                var rateInfo = data.data;
                if(rateInfo.all_wl_rate == null || rateInfo.all_fw_rate == null || rateInfo.all_cp_rate == null){
                	layer.open({
                        title: '提示',
                        content: '暂无数据'
                    });
                	$("#main").html('');
                	return false;								
                 }else{
                	 option = {
                             /* title : {
                                 text: '综合评分10000',
                                 subtext: '纯属虚构',
                                 x:'center'
                             }, */
                             tooltip : {
                                 trigger: 'item',
                                 formatter: "{a} <br/>{b} : {c} ({d}%)"
                             },
                             legend: {
                                 orient: 'vertical',
                                 left: 'left',
                                 data: ['物流评分','服务评分','产品评分']
                             },
                             series : [
                                 {
                                     name: '评分占比',
                                     type: 'pie',
                                     radius : '55%',
                                     center: ['50%', '60%'],
                                     data:[
                                         {value:rateInfo.all_wl_rate, name:'物流评分'},
                                         {value:rateInfo.all_fw_rate, name:'服务评分'},
                                         {value:rateInfo.all_cp_rate, name:'产品评分'}
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
                 }
             }
        });
            
    })
</script>
</html>

