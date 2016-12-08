<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">优惠券管理&nbsp;&nbsp;&nbsp;<a href="<?php  echo web_url('bonus', array('do'=>'bonus','op'=>'post'));?>" class="btn btn-primary">添加优惠券</a></h3>
<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
		 <th class="text-center" >优惠券名称</th>
    <th class="text-center"  >发放类型</th>
    <th class="text-center" width="100px">优惠券金额</th>
    <th class="text-center" >最小订单金额</th>
    <th class="text-center" >发放数量</th>
     <th class="text-center">使用数量</th>
	 <th class="text-center">发放时间</th>
	 <th class="text-center">使用时间</th>
    <th class="text-center">操作</th>
				</tr>
			</thead>
		<?php  if(is_array($bonus_list)) { foreach($bonus_list as $item) { ?>
				<tr>
					<td class="text-center"><?php echo $item['type_name']; ?></td>
          <td class="text-center">
          	<?php echo $item['send_type']==0?'按用户发放':''; ?>
          	<?php echo $item['send_type']==1?'按商品发放':''; ?>
          	<?php echo $item['send_type']==2?'按订单金额发放':''; ?>
          	<?php echo $item['send_type']==3?'线下发放的优惠券':''; ?></td>
           <td class="text-center"><?php echo $item['type_money']; ?></td>
          <td class="text-center"><?php echo $item['min_goods_amount']; ?> </td>
          <td class="text-center"><?php echo $item['sendcount']; ?></td>
          <td class="text-center"><?php echo $item['usercount']; ?></td>
		  <td class="text-center"><?php echo date('Y-m-d H:i',$item['send_start_date']).'~'.date('Y-m-d H:i',$item['send_end_date']); ?></td>
		  <td class="text-center"><?php echo date('Y-m-d H:i',$item['use_start_date']).'~'.date('Y-m-d H:i',$item['use_end_date']); ?></td>
         <td class="text-center">
    			<a class="btn btn-xs btn-info"  href="<?php  echo create_url('site', array('name' => 'bonus','do' => 'bonusview','op'=>'post','id'=>$item['type_id']))?>"><i class="icon-zoom-out"></i>查看发放记录</a> 
                    	&nbsp;&nbsp;	       
                    	  	<?php if($item['send_type']!=2){?>
                                             	<a class="btn btn-xs btn-info"  href="<?php  echo create_url('site', array('name' => 'bonus','do' => 'sendbonus','op'=>'post','id'=>$item['type_id']))?>"><i class="icon-tasks"></i>&nbsp;发&nbsp;放&nbsp;</a> 
                    	&nbsp;&nbsp;	  	<?php } ?>                       	<a class="btn btn-xs btn-info"  href="<?php  echo create_url('site', array('name' => 'bonus','do' => 'bonus','op'=>'post','id'=>$item['type_id']))?>"><i class="icon-edit"></i>&nbsp;修&nbsp;改&nbsp;</a> 
                    	&nbsp;&nbsp;	<a class="btn btn-xs btn-info" onclick="return confirm('此操作不可恢复，确认删除？');return false;"  href="<?php  echo create_url('site', array('name' => 'bonus','do' => 'bonus','op'=>'delete','id'=>$item['type_id']))?>"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a> <a class="btn btn-xs btn-info" data-toggle="modal" data-target=".bonus-<?php echo $item['type_id']; ?>" href="javascript:void(0);"><i class="icon-copy"></i>&nbsp;复&nbsp;制&nbsp;</a> <div class="modal fade bonus-<?php echo $item['type_id']; ?>"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel-<?php echo $item['type_id']; ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel-<?php echo $item['type_id']; ?>"><?php echo $item['type_name']; ?></h4>
      </div>
      <div class="modal-body">
            <input style="width:80%" id="foo-<?php echo $item['type_id']; ?>" value="<?php echo WEBSITE_ROOT.mobile_url('bonus',array('name'=>'shopwap','op'=>'get','id'=>$item['type_id'])); ?>" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-primary btn-copy"  data-clipboard-target="#foo-<?php echo $item['type_id']; ?>" aria-label="复制成功！">复制</button>
      </div>
    </div>
  </div>
</div> </td>
            </td>
				</tr>
				<?php  } } ?>
		</table>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/clipboard.min.js"></script>
<script>
var clipboard = new Clipboard('.btn-copy');
clipboard.on('success', function(e) {
	var msg = e.trigger.getAttribute('aria-label');
	alert(msg);
    console.info('Action:', e.action);
    console.info('Text:', e.text);
    console.info('Trigger:', e.trigger);
    e.clearSelection();
});
</script>
<?php  include page('footer');?>
								