<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">红包管理&nbsp;&nbsp;&nbsp;<a href="<?php  echo web_url('red', array('op'=>'post'));?>" class="btn btn-primary">添加红包活动</a></h3>
<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
		 <th class="text-center" >红包ID</th>
     <th class="text-center" >红包总金额</th>
    <th class="text-center"  >红包类型</th>
    <th class="text-center" width="100px">单个红包领取最大值</th>
    <th class="text-center" >中奖率</th>
    <th class="text-center" >每日最大摇奖数</th>
	 <th class="text-center">开始时间</th>
	 <th class="text-center">结束时间</th>
    <th class="text-center">操作</th>
				</tr>
			</thead>
		<?php  if(is_array($red_setting)) { foreach($red_setting as $item) { ?>
				<tr>
					<td class="text-center"><?php echo $item['id']; ?></td>
          <td class="text-center"><?php echo $item['amount']; ?></td>
           <td class="text-center"><?php echo $type_ary[$item['type']]; ?></td>
          <td class="text-center"><?php echo $item['goldmax']; ?> </td>
          <td class="text-center"><?php echo (string)((float)$item['winrate']*100)."%"; ?></td>
          <td class="text-center"><?php echo $item['sendmax']; ?></td>
		  <td class="text-center"><?php echo date('Y-m-d H:i',$item['begintime']); ?></td>
		  <td class="text-center"><?php echo date('Y-m-d H:i',$item['endtime']); ?></td>
         <td class="text-center">
    			<a class="btn btn-xs btn-info"  href="<?php  echo create_url('site', array('name' => 'bonus','do' => 'red','op'=>'detail','id'=>$item['id']))?>"><i class="icon-zoom-out"></i>查看领取记录</a> 
                    	&nbsp;&nbsp;<a class="btn btn-xs btn-info"  href="<?php  echo create_url('site', array('name' => 'bonus','do' => 'red','op'=>'post','id'=>$item['id']))?>"><i class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> 
                    	&nbsp;&nbsp;	<a class="btn btn-xs btn-info" onclick="return confirm('此操作不可恢复，确认删除？');return false;"  href="<?php  echo create_url('site', array('name' => 'bonus','do' => 'red','op'=>'delete','id'=>$item['id']))?>"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a></td>
            </td>
				</tr>
				<?php  } } ?>
		</table>
<?php  include page('footer');?>
								