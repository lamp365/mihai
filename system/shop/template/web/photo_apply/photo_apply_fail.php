<div class="alertModal-dialog-sm">
    <form id="shenhe_bu_tongguo_form" action="" onsubmit="ajaxSubmit();return false;" method="post" class="form-inline" enctype="multipart/form-data" >
        <input type="hidden" name="id" value="<?php echo $_GP['id']; ?>">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">店铺物流申请审核</h4>
        </div>
        <div class="modal-body">
            <div class="">
                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <td style="text-align:center;" class="dish-id">
                            原因
                        </td>
                        <td style="text-align:center;" class="dish-id">
                            <textarea name="audit_detial" style="width: 100%">
                                <?php echo trim($audit['audit_fail_detial'])?>
                            </textarea>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            <button type="submit"  name='sure_add'  value="submit" class="btn btn-primary">确认</button>
        </div>
    </form>
</div>
<script language="javascript">
    function ajaxSubmit(){
        var data = $("#shenhe_bu_tongguo_form").serialize();
        var url = '<?php echo web_url('photo_apply',array('op'=>'AuditFailureSub')) ?>';
        $.post(url,data,function(ret){
            if(ret.errno==1){
               location.reload();
            }else{
                alert(ret.message);
            }
        });
    }
</script>