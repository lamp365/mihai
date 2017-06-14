
<div class="alertModal-dialog-sm">
    <form action="<?php echo web_url('region_category', array('op' => 'post')); ?>" method="post" class="form-inline" enctype="multipart/form-data" >
        <input type="hidden" name="id" value="<?php echo $info['rc_id']; ?>">
        <input type="hidden" name="rc_shop_cate_id" value="<?php echo $_GP['rc_shop_cate_id']; ?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php if (empty($info)) {echo "添加";} else {echo '修改';} ?>区域限制</h4>
        </div>
        <div class="modal-body">
            <div class="">
                <div class="form-group" style="width: 95%;">
                    <label for="">店铺限制数</label>
                    <input type="number" name="limit" class="form-control" value="<?php echo $info['rc_limit']; ?>" placeholder="输入限制数量" >
                </div>
                <br/><br/>
            </div>

            <div class="">
                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-left" >所在城市</label>
                    <div class="col-sm-9">
                        <select  id="cate_1" style="margin-right:15px;"  name="province_id" class="pcates" onchange="fetchChildCategory(this, this.options[this.selectedIndex].value)"  autocomplete="off">
                            <option value="0">请选择一级城市</option>
                            <?php
                            if (is_array($result)) {
                                foreach ($result as $row) {
                                    ?>
                                    <?php if ($row['parent_id'] == 0) { ?>
                                        <option value="<?php echo $row['region_id']; ?>" <?php if ($row['region_id'] == $extend_arr['p1']) { ?> selected="selected"<?php } ?>><?php echo $row['region_name']; ?></option>
        <?php } ?>
    <?php
    }
}
?>
                        </select>

                        <select  id="cate_2" name="city_id" class="cates_2" onchange="fetchChildCategory2(this, this.options[this.selectedIndex].value)" autocomplete="off">
                            <option value="-1">请选择二级城市</option>
                            <?php if (!empty($extend_arr['p2']) && !empty($childrens[$extend_arr['p1']])) { ?>
                                <?php
                                if (is_array($childrens[$extend_arr['p1']])) {
                                    foreach ($childrens[$extend_arr['p1']] as $row) {
                                        ?>
                                        <option  value="<?php echo $row['0']; ?>" <?php if ($row['0'] == $extend_arr['p2']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
                                    <?php }
                                }
                                ?>
                            <?php } ?>
                        </select>
                        <select  id="cate_3" name="cate_3" autocomplete="off" >
                            <option value="0">请选择三级城市</option>
<?php
if (!empty($extend_arr['p3']) && !empty($childrens[$extend_arr['p2']])) {
    if (is_array($childrens[$extend_arr['p2']])) {
        foreach ($childrens[$extend_arr['p2']] as $row) {
            ?>
                                        <option value="<?php echo $row['0']; ?>" <?php if ($row['0'] == $extend_arr['p3']) { ?> selected="selected"<?php } ?>><?php echo $row['1']; ?></option>
        <?php }
    }
} ?>
                        </select>
                    </div>
                </div>
            </div>




        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            <button type="submit" name='sure_add' value="1" class="btn btn-primary">
                确认<?php if (empty($info)) {echo "添加";} else {echo '修改';} ?>
            </button>
        </div>
    </form>
</div>

<script language="javascript">
    var category = <?php echo json_encode($childrens) ?>;
    
    function fetchChildCategory(o_obj, cid) {
        var html = '<option value="0">请选择二级分类</option>';

        var obj = $(o_obj).parent().find('.cates_2').get(0);
        if (!category || !category[cid]) {
            $(o_obj).parent().find('.cates_2').html(html);

            fetchChildCategory2(o_obj, obj.options[obj.selectedIndex].value);
            return false;
        }
        for (i in category[cid]) {
            html += '<option value="' + category[cid][i][0] + '">' + category[cid][i][1] + '</option>';
        }
        $(o_obj).parent().find('.cates_2').html(html);
        fetchChildCategory2(o_obj, obj.options[obj.selectedIndex].value);

    }
    function fetchChildCategory2(o_obj, cid) {
        var html = '<option value="0">请选择三级分类</option>';
        if (!category || !category[cid]) {
            $(o_obj).parent().find('.cate_3').html(html);
            return false;
        }
        for (i in category[cid]) {
            html += '<option value="' + category[cid][i][2] + '">' + category[cid][i][1] + '</option>';
        }
        $('#cate_3').html(html);
    }
    
    
</script>
