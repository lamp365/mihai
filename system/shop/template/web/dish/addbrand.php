<div class="alertModal-dialog-bg" style="width:32%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">添加品牌 <span class="show_tip" style="color: red;margin-left: 20px;font-size: 12px;"></span></h4>
    </div>
    <form action="<?php echo web_url('dish',array('op'=>'addbrand')) ?>" method="post" enctype="multipart/form-data" onsubmit="return checkParame();">
        <div class="modal-body form-inline">
            <div class="form-group">
                <label for="brandname">品牌名称</label>
                <input type="text" class="form-control" id="brandname" name="brandname" placeholder="请输入品牌名称">
            </div>
            <br/><br/>
            <div class="form-group">
                <label for="brandname">所属国家</label>
                <select class="form-control" id="country_id" name="country_id">
                    <?php
                        foreach($country as $one){
                            echo "<option value='{$one['id']}'>{$one['name']}</option>";
                        }
                    ?>
                </select>
            </div>
            <br/><br/>
            <div class="form-group">
                <label for="brandname">品牌图标</label>
                <input type="file" name="icon" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <button type="submit" class="btn btn-primary">确认添加</button>
        </div>
    </form>

</div>
<script>
    function checkParame()
    {
        if($("#brandname").val() == ''){
            $(".show_tip").html('品牌名字不能为空！');
            return false;
        }
        if($("#country_id").val() == 0){
            $(".show_tip").html('请选择一个国家');
            return false;
        }
        return true;
    }
</script>