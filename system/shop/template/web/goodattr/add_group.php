<div class="alertModal-dialog-sm" style="width: 35%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php if(empty($edit_group)){ echo '添加';}else{ echo '修改';} ?>分组</h4>
    </div>
    <form method="post" action="<?php echo web_url('goodstype',array('op'=>'add_group')); ?>">
    <div class="modal-body">
        <div class="form-inline">
            <div class="form-group" style="width: 80%;">
                <label for="">分组名字</label>
                <input type="text" name="group_name" class="form-control" id="" value="<?php echo $edit_group['group_name']; ?>" placeholder="输入商品模型名" >
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="group_id" value="<?php echo $edit_group['group_id']; ?>">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" value="1"  name="sure_add" class="btn btn-primary">确认<?php if(empty($edit_group)){ echo '添加';}else{ echo '修改';} ?></button>
    </div>
    </form>
</div>