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
<h3 class="header smaller lighter blue" style="display: inline-block">分销详情</h3><a class="btn btn-xs btn-info" href="<?php  echo web_url('distributor',array('op'=>'search', 'start_time'=>$s_time, 'end_time'=>$e_time));?>"></i>返回列表</a>
<h3 class="blue"><span style="font-size:18px;"><strong>商品总数：<?php echo $total ?></strong></span></h3>
			<table class="table table-striped table-bordered table-hover" id="tb">
			<thead>
				<tr>
					<th style="text-align:center;">商品ID</th>
					<th style="text-align:center;">商品名称</th>
					<th style="text-align:center;">收入佣金↓</th>
					<th style="text-align:center;">卖出数量↓</th>
					<th style="text-align:center;">代销时间</th>
					<th style="text-align:center;">状态↑↓</th>
				</tr>
			</thead>
			<tbody>
 <?php  if(is_array($list)) { 
	 foreach($list as $v) { ?>
								<tr>
									<td class="text-center">
										<?php  echo $v['goodid'];?>
									</td>
									<td class="text-center">
										<?php echo $v['good_name']; ?><br/>
									</td>
									<td class="text-center">
										<?php echo $v['all_commision']; ?><br/>
									</td>
									<td class="text-center">
										<?php echo $v['sale_num']; ?><br/>
									</td>
									<td class="text-center">
										<?php  echo date('Y-m-d',$v['operatetime']);?>
									</td>
									<td class="text-center">
										<?php  echo $status_ary[$v['status']];?>
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
<br>
<?php  include page('footer');?>