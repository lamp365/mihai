 <div class="alertModal-dialog-sm">
        <div class="modal-header">
<h3 class="header smaller lighter blue" style="margin-bottom: 30px; padding-left: 50px;">型号选择</h3>
</div>
     
    <div class="modal-body">
        <?php
          foreach($list as $k=>$v){
              $picList = array();
              $picList = explode(',', $v['ms_type_url']);
        ?>
	<div class="form-group">
		<div class="col-sm-2">
                    <input type="text" name="ms[<?php echo $v['ms_type_id'];?>][ms_nums]" class="form-control" placeholder="请输入物料数量" value="<?php echo $materialManagementInfo['detialArray'][$v['ms_type_id']]['ms_nums'];?>" readonly>
		</div>
	</div>
	
	<div class="template form-group template3">
		<label class="col-sm-1 control-label no-padding-left"> 模板照片</label>
		<div class="col-sm-10">
                    <input type="hidden" name="ms[<?php echo $v['ms_type_id'];?>][ms_pic]" class="template-val" value="<?php echo $materialManagementInfo['detialArray'][$v['ms_type_id']]['ms_pic'];?>">
			<div class="template-list">
				<ul>
                                    <li>
                                        <img src="<?php echo $materialManagementInfo['detialArray'][$v['ms_type_id']]['ms_pic'];?>" style="width:120px;height: 120px;">
                                    </li>
				</ul>
			</div>
		</div>
	</div>
        <?php
          }
        ?>
	<div class="form-group">
		<label class="col-sm-1 control-label no-padding-left">备注</label>
		<div class="col-sm-10">
                    <textarea class="form-control" rows="3" placeholder="备注信息" name="audit_detial" ><?php echo $materialManagementInfo['audit_detial'];?></textarea>
		</div>
	</div>
     </div>
     
</div>