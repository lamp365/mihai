<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
	<h3 class="header smaller lighter blue">物料设置</h3>
	<style>
		.nav-tabs li a{
			padding: 6px 22px;
		}
	</style>
	<ul class="nav nav-tabs" >
		<li style="" <?php  if($data['audit_status'] == '1') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('photo_apply',  array('op' => 'index','audit_status'=>1))?>">申请中</a></li>
		<li style="" <?php  if($data['audit_status'] == '2') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('photo_apply',  array('op'=>'index','audit_status'=>2))?>">申请成功</a></li>
		<li style="" <?php  if($data['audit_status'] == '3') { ?> class="active"<?php  } ?>><a href="<?php  echo web_url('photo_apply',  array('op'=>'index','audit_status'=>3))?>">申请失败</a></li>
	</ul><br/>
        

    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th style="text-align: center; width: 120px">店铺名称</th>
            <th style="text-align: center;">实体店名</th>
            <th style="text-align: center;">联系人</th>
            <th style="text-align: center;">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($materialManagement['data'])) { foreach($materialManagement['data'] as $value) { ?>
            <tr style="text-align: center;">
                <td><?php echo $value['sts_name'];?></td>
                <td><?php echo $value['sts_physical_shop_name'];?></td>
                <td><?php echo $value['sts_contact_name'];?></td>

                <td style="text-align: center;">
                    <?php
                      if($data['audit_status'] == '1')
                      {
                    ?>
                    <a class="btn btn-xs btn-info" href="<?php echo web_url('photo_apply', array('op' => 'AuditSuccessSub','id'=>$value['id']))?>">
                        <i class="icon-edit"></i>申请通过
                    </a>
                    <a class="btn btn-xs btn-info shop_fail" href="javascript:void(0);"  data-id="<?php echo $value['id'];?>">
                        <i class="icon-edit"></i>审核失败
                    </a>
                    <?php
                      }
                    ?>
                    <a class="btn btn-xs btn-info show_mm" href="javascript:void(0);" data-sts_id="<?php echo $value['sts_id'];?>">
                        <i class="icon-edit"></i>查看
                    </a>
                </td>
            </tr>
        <?php  } } ?>
        </tbody>
    </table>
<!--用于加载远程地址 作为弹框页面  注意更新 -->
<div id="alterModal" class="alertModalBox"></div>
<script type="text/javascript">
    $(function(){
        $('.show_mm').on('click',function(){
            var sts_id = $(this).data('sts_id');
            var url = "<?php echo web_url('photo_apply', array('op' => 'spec_show'))?>";
            $.ajaxLoad(url,{sts_id:sts_id},function(){
                $('#alterModal').modal('show');
            });
        });
        
        $('.shop_fail').on('click',function(){
            var id = $(this).data('id');
            var url = "<?php echo web_url('photo_apply', array('op' => 'AuditFailure'))?>";
            $.ajaxLoad(url,{id:id},function(){
                $('#alterModal').modal('show');
            });
        });
        
        
        
    });
    
</script>

<?php  include page('footer');?>