<div class="alertModal-dialog-sm" style="width: 35%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php if(empty($edit_gtype)){ echo '添加';}else{ echo '修改';} ?>模型</h4>
    </div>
    <form method="post" action="<?php echo web_url('goodstype',array('op'=>'add_gtype')); ?>">
    <div class="modal-body">
        <div class="form-inline">
            <div class="form-group" style="width: 95%;">
                <label for="exampleInputEmail2"><?php if(empty($edit_gtype)){ echo '选择';}else{ echo '移至';} ?>分组</label>

                <select name="group_id" class="form-control"   style="margin-right:15px;"  >
                    <option value="0">请选择模型分组</option>
                    <?php foreach($group_list as $gitem) {
                        if($gitem['group_id'] == $edit_gtype['system_group_id']){
                            $sel = "selected";
                        }else{
                            $sel = '';
                        }
                        echo "<option value='{$gitem['group_id']}' {$sel} data-group_id='{$_GP['group_id']}'>{$gitem['group_name']}</option>";
                    }
                    ?>
                </select>

            </div><br/><br/>
            <div class="form-group" style="width: 95%;">
                <label for="exampleInputEmail2">所属分类</label>

                <select name="p1" class="form-control get_category" onchange="getShop_sonCategroy(this,1)"  style="margin-right:15px;"  >
                    <option value="">请选择分类</option>
                    <?php foreach($parent_category as $item) {
                        if($item['id'] == $edit_gtype['p1']){
                            $sel = "selected";
                        }else{
                            $sel = '';
                        }
                        echo "<option value='{$item['id']}' {$sel}>{$item['name']}</option>";
                    }
                    ?>
                </select>
                <select  class="form-control get_category" style="margin-right:15px;" name="p2"  autocomplete="off" onchange="">
                    <option value="0">请选择分类</option>
                    <?php foreach($first_son as $item2) {
                        if($item2['id'] == $edit_gtype['p2']){
                            $sel = "selected";
                        }else{
                            $sel = '';
                        }
                        echo "<option value='{$item2['id']}' {$sel}>{$item2['name']}</option>";
                    }
                    ?>
                </select>

            </div><br/><br/>
            <div class="form-group" style="width: 80%;">
                <label for="">模型名字</label>
                <input type="text" name="gtype_name" class="form-control" id="" value="<?php echo $edit_gtype['name']; ?>" placeholder="输入商品模型名" >
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="id" value="<?php echo $edit_gtype['id']; ?>">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" value="1"  name="sure_add" class="btn btn-primary">确认<?php if(empty($edit_gtype)){ echo '添加';}else{ echo '修改';} ?></button>
    </div>
    </form>
</div>