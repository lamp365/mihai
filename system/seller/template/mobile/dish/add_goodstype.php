<div class="alertModal-dialog" style="width:30%">
    <form action="<?php echo mobile_url('product',array('op'=>'do_addgtype')); ?>" method="post" class="form-horizontal gtype_form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"><?php  if(empty($gtype['id'])){ echo '添加';}else{ echo '编辑';} ?>模型</h4>
    </div>
    <div class="modal-body">
        <?php if(!empty($selfgroup)){   ?>
        <div class="form-group">
            <label class="col-sm-3 control-label"><?php  if(empty($gtype['id'])){ echo '选择';}else{ echo '移至';} ?>分组</label>
            <div class="col-sm-9">
                <select name="group_id" id="" class="form-control">
                    <?php foreach($selfgroup as $s_group) {
                        $sel = '';
                        if ($s_group['group_id'] == $gtype['group_id'])
                            $sel = 'selected';

                        echo "<option value='{$s_group['group_id']}' {$sel}>{$s_group['group_name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <?php } ?>
        <div class="form-group">
            <label class="col-sm-3 control-label">模型名称</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" value="<?php echo $gtype['name'];?>" name="gtype_name" placeholder="请输入模型名称" required>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="hide_id" value="<?php echo $gtype['id'];?>">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">确认<?php  if(empty($gtype['id'])){ echo '添加';}else{ echo '编辑';} ?></button>
    </div>
    </form>
</div>
<script>
    //表单验证
    $('.gtype_form').validator()
</script>