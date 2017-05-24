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
<h3 class="header smaller lighter blue">红包发放管理</h3>
<form action="<?php  echo web_url('red',array('op'=>'detail', 'id'=>$redid));?>" method="post" class="form-horizontal" enctype="multipart/form-data" >
	<table class="table vip-table-list" style="width:100%;" align="left">
		<tbody>
			<tr>
				<td>
					<li>手机号：</li>
					<li>
						<input name="mobile"  type="text" value="<?php  echo $mobile;?>" />
					</li>

					<li>姓名：</li>
					<li>
						<input name="realname" type="text"   value="<?php  echo $realname;?>" />
					</li>
					<li>昵称：</li>
					<li>
						<input name="nickname" type="text"   value="<?php  echo $nickname;?>" />
					</li>
					<li>
						<button type="submit" class="btn btn-md btn-info">搜 索</button>
					</li>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<table class="table table-striped table-bordered table-hover" id="tb">
			<thead>
				<tr>
					
		 <th class="text-center" >ID</th>
    <th class="text-center">手机号</th>
    <th class="text-center" >姓名</th>
    <th class="text-center" >昵称</th>
    <th class="text-center" ><a href="<?php echo web_url('red',array('op'=>'detail', 'id'=>$redid, 'order'=>'sendgold')); ?>">领取金额↓</a></th>
    <th class="text-center">领取时间</th>
    <th class="text-center">操作</th>
				</tr>
			</thead>
		<?php  if(is_array($red_detail)) { foreach($red_detail as $item) { ?>
				<tr>
					
					<td class="text-center"><?php echo $item['id']; ?></td>
          <td class="text-center"><?php echo $item['mobile']; ?></td>
           <td class="text-center"><?php echo $item['realname']; ?></td>
           <td class="text-center"><?php echo $item['nickname']; ?></td>
           <td class="text-center"><?php echo $item['sendgold']; ?></td>
          <td class="text-center"><?php echo date('Y-m-d H:i:s', $item['createtime']); ?></td>
         <td class="text-center">
						<a class="btn btn-xs btn-info" onclick="return confirm('此操作不可恢复，确认删除？');return false;"  href="<?php  echo create_url('site', array('name' => 'bonus','do' => 'red','op'=>'del_detail','id'=>$item['id']))?>"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a> </td>
                                </td>
				</tr>
				<?php  } } ?>
		</table>
		<?php  echo $pager;?>
<!-- <script>
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
</script> -->
<?php  include page('footer');?>
								