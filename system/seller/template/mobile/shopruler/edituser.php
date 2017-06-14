<div class="alertModal-dialog" style="width:30%">
    <form action="<?php echo mobile_url('shopruler',array('op'=>'edituser')); ?>" method="post" class="form-horizontal gtype_form">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">修改用户</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-3 control-label">用户手机</label>
            <div class="col-sm-9">
                <?php echo $the_user['mobile']; ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">修改属组</label>
            <div class="col-sm-9">
                <select name="group_id" id="" class="form-control">
                    <?php foreach($sellergroup as $s_group) {
                        $sel = '';
                        if ($s_group['group_id'] == $the_user['group_id'])
                            $sel = 'selected';

                        echo "<option value='{$s_group['group_id']}' {$sel}>{$s_group['group_name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="is_edit" value="1">
        <input type="hidden" name="id" value="<?php echo $_GP['id'];?>">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">确认编辑</button>
    </div>
    </form>
</div>
<script>
    //表单验证
    $('.gtype_form').validator()
</script>