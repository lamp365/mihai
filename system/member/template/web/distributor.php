<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
	<style>
		.modal-body i{
			color: red;
		}
		.vip-table-list tr{
			background-color: #f9f9f9;
			border-top: 1px solid #ddd;
		}
		.vip-table-list td{
			border: 1px solid #ddd;
		}
		.vip-table-list li{
			margin-top:3px;
			float: left;
			margin-right: 10px;
			list-style: none;
		}
		.vip-table-list li select{
			height:26px;
		}
		.vip-table-list li span{
			display: inline-block;
			height:24px;
			line-height: 24px;
		}
	</style>
	<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>/addons/common/css/datetimepicker.css" />
	<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/laydate/laydate.js"></script>
<h3 class="header smaller lighter blue" style="display: inline-block">分销商列表</h3>
<form action="<?php  echo web_url('distributor',array('op'=>'search'));?>" method="post" class="form-horizontal" enctype="multipart/form-data" >

	<table class="table vip-table-list" style="width:100%;" align="left">
		<tbody>
			<tr>
				<td>
					<li>用户名：</li>
					<li>
						<input name="u_name"  type="text" value="<?php  echo $u_name;?>" />
					</li>

					<li>手机号码：</li>
					<li>
						<input name="mobile" type="text"   value="<?php  echo $mobile;?>" />
					</li>
					<li>起始日期：</li>
					<li>
						<input name="start_time" id="start_time" type="text" value="<?php  echo empty($b_time)?null:date('Y-m-d',$b_time);?>" readonly="readonly"  /> 
					</li>
					<li>终止日期：</li>
					<li>
						<input name="end_time" id="end_time" type="text" value="<?php  echo empty($e_time)?null:date('Y-m-d',$e_time);?>" readonly="readonly"  /> 
					</li>
					<li>
						<button type="submit" class="btn btn-md btn-info">搜 索</button>
					</li>
				</td>
			</tr>
		</tbody>
	</table>
	<div style="clear: both;height: 15px;"></div>
</form>
<h3 class="blue">	<span style="font-size:18px;"><strong>分销商总数：<?php echo $total ?></strong></span></h3>
			<table class="table table-striped table-bordered table-hover" id="tb">
			<thead>
				<tr>
					<th style="text-align:center;">店铺ID</th>
					<th style="text-align:center;">店铺名称</th>
					<th style="text-align:center;">用户名</th>
					<th style="text-align:center;">手机号</th>
					<th style="text-align:center;">代销商品总数↑↓</th>
					<th style="text-align:center;">佣金收入↑↓</th>
					<th style="text-align:center;">操作</th>
				</tr>
			</thead>
			<tbody>
 <?php  if(is_array($list)) { 
	 foreach($list as $v) { ?>
								<tr>
									<td class="text-center">
										<?php  echo $v['id'];?>
									</td>
									<td class="text-center">
										<?php echo $v['shopname']; ?><br/>
									</td>
									<td class="text-center">
										<?php echo $v['username']; ?><br/>
									</td>
									<td class="text-center">
										<?php  echo $v['mobile'];?>
									</td>
									<td class="text-center">
										<?php  echo $v['goods_num'];?>
									</td>
									<td class="text-center">
										<?php  echo $v['all_commision'];?>
									</td>
									<td class="text-center">
										&nbsp;<a class="btn btn-xs btn-info" href="<?php  echo web_url('distributor',array('op'=>'detail','shopid' => $v['id'],'stime' => $b_time,'etime' => $e_time));?>"><i class="icon-edit"></i>详情</a>&nbsp;
									</td>
								</tr>
								<?php  } } ?>
  </tbody>
    </table>

	<script>
		var table=document.getElementById("tb");
		var table_th=document.getElementsByTagName("th");
		var table_tbody=table.getElementsByTagName("tbody")[0];
		var table_tr=table_tbody.getElementsByTagName("tr");
		function bind_click(_i){
		        table_th[_i].onclick=function(){
		            var temp_arr=[];
		            var temp_tr_arr=[];
		            for(j=0;j<table_tr.length;j++){
		                temp_arr.push(table_tr[j].getElementsByTagName("td")[_i].innerHTML);
		                temp_tr_arr.push(table_tr[j].cloneNode(true));
		            };
		            var tr_length=table_tr.length
		            for(x=0;x<tr_length;x++){
		                table_tbody.removeChild(table_tbody.getElementsByTagName("tr")[0]);
		            }
		            var temp=parseInt(temp_arr[0])||temp_arr[0];
		            if(typeof(temp)=='number'){
		                temp_arr.sort(function(a,b){return a-b;});
		            }else{
		                temp_arr.sort(function(a,b){return b-a;});
		            }
		            for(k=0;k<temp_arr.length;k++){
		                    for(vv=0;vv<temp_tr_arr.length;vv++){
		                        if(temp_arr[k]==temp_tr_arr[vv].getElementsByTagName("td")[_i].innerHTML){
		                            table_tbody.appendChild(temp_tr_arr[vv]);
		                        }
		                    }
		            }
		        }
		    }
		for(i=0;i<table_th.length;i++){
		    bind_click(i);
		}
	</script>
	<script type="text/javascript">
		laydate({
	        elem: '#start_time',
	        istime: true, 
	        event: 'click',
	        format: 'YYYY-MM-DD hh:mm:ss',
	        istoday: true, //是否显示今天
	        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	    });
	    laydate.skin("molv"); 
	</script> 
	<script type="text/javascript">
		laydate({
	        elem: '#end_time',
	        istime: true, 
	        event: 'click',
	        format: 'YYYY-MM-DD hh:mm:ss',
	        istoday: true, //是否显示今天
	        start: laydate.now(0, 'YYYY-MM-DD hh:mm:ss')
	    });
	    laydate.skin("molv"); 
	</script>
<?php  include page('footer');?>