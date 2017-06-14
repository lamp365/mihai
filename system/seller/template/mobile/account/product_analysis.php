<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php include page('seller_header');?>
    <link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>addons/seller/css/product_analysis.css" media="all">
</head>
<body>
<div class="layui-form">
	<div class="ui-block-head">
        <h3>商品概况</h3>
        <div class="layui-input-inline">
	        <input type="text" name="date" lay-verify="date" placeholder="时间筛选" autocomplete="off" class="layui-input" id="daysearch">
	    </div>
    </div>
    <ul class="lists">
    	<li class="step clearfix">
    		<div class="step__name">商品分布</div>
    		<div class="step__block"><p class="name">在架商品数</p><p class="count">2</p></div>
    	</li>
    	<li class="step clearfix">
    		<div class="step__name">商品访问</div>
    		<div class="step__block"><p class="name">被访问商品数</p><p class="count">0</p></div>
    		<div class="step__block"><p class="name">商品访客数（商品UV）</p><p class="count">0</p></div>
    		<div class="step__block"><p class="name">商品浏览量（商品PV）</p><p class="count">0</p></div>
    	</li>
    	<li class="step clearfix">
    		<div class="step__name">商品转化</div>
    		<div class="step__block"><p class="name">付款商品数</p><p class="count">0</p></div>
    		<div class="step__block"><p class="name">商品详情页转化率</p><p class="count">0.00%</p></div>
    	</li>
    </ul>
    <div class="ui-block-head">
        <h3>商品效果</h3>
        <div class="layui-input-inline">
	        <input type="text" name="date" lay-verify="date" placeholder="时间筛选" autocomplete="off" class="layui-input" id="daysearch">
	    </div>
    </div>
    <table class="layui-table">
    	<thead>
    		<tr>
    			<th>商品信息</th>
    			<th>曝光次数</th>
    			<th>曝光人数</th>
    			<th>访客数</th>
    			<th>浏览量</th>
    			<th>付款人数</th>
    			<th>单品转化率</th>
    			<th>付款商品件数</th>
    		</tr>
    	</thead>
    	<tbody>
    		<tr>
    			<td colspan="8" style="text-align:center">没有筛选到符合条件的商品，或者符合条件的商品在查询时间内的数据均为0</td>
    		</tr>
    		<tr>
    			<td>XXXXXX</td>
    			<td>10</td>
    			<td>100</td>
    			<td>8</td>
    			<td>8</td>
    			<td>8</td>
    			<td>8%</td>
    			<td>1</td>
    		</tr>
    	</tbody>
    </table>
</div>
</body>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/echarts.min.js"></script>
	
<script>
	layui.use(["form","laydate"],function(){
		var form = layui.form();
		var laydate = layui.laydate;
		var start = {
	      	max: '2099-06-16',
	        format: 'YYYY-MM-DD' ,
	      	istoday: false,
	      	choose: function(datas){
	      		//时间选择后的ajax请求
	    		$.post("",{val:datas},function(data){

	    		},"json")
	      	}
	    };
	    document.getElementById("daysearch").onclick = function(){
	    	start.elem = this;
    		laydate(start);
	    }
	});

</script>
</html>