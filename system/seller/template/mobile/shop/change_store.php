<div class="alertModal-dialog" style="width:16%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">店铺切换</h4>
    </div>
    <form action="<?php echo mobile_url('shop',array('op'=>'change_store')); ?>">
    <div class="modal-body">
        <select name="sts_id" id="" class="form-control">
            <option value="0">选择店铺</option>
            <?php foreach($mem_store as $one_store){ ?>
                <option value="<?php echo $one_store['sts_id'] ?>"><?php echo $one_store['sts_name'] ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">确认切换</button>
    </div>
    </form>
</div>