<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
	<style>
		.modal-body i{
			color: red;
		}
	.vip-table-list{
		border: 1px solid #ddd;padding: 7px 0;
	}
	.left-span{
		float: left;
	    line-height: 28px;
	    background-color: #ededed;
	    padding: 0 5px;
	    border: 1px solid #cdcdcd;
	    border-right: 0;
	    font-size: 12px;
	}
	.vip-table-list li{
		float: left;    
		margin-right: 10px;
		list-style-type: none;
	}
	.vip-table-list .li-height{
	    height: 30px;
	    padding-left: 5px;
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
					<li>
						<span class="left-span">用户名</span>
						<input name="u_name" class="li-height" type="text" placeholder="用户名" value="<?php  echo $u_name;?>" />
					</li>
					<li>
						<span class="left-span">手机号码</span>
						<input name="mobile" class="li-height" type="text" placeholder="手机号码" value="<?php  echo $mobile;?>" />
					</li>
					<li>
						<span class="left-span">起始日期</span>
						<input name="start_time" class="li-height" placeholder="起始日期" id="start_time" type="text" value="<?php  echo empty($b_time)?null:date('Y-m-d',$b_time);?>" readonly="readonly"  /> 
					</li>
					<li>
						<span class="left-span">终止日期</span>
						<input name="end_time" class="li-height" placeholder="终止日期" id="end_time" type="text" value="<?php  echo empty($e_time)?null:date('Y-m-d',$e_time);?>" readonly="readonly"  /> 
					</li>
					<li>
						<button type="submit" class="btn btn-md btn-primary btn-sm">查 询</button>
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