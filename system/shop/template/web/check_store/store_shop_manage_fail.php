<div class="alertModal-dialog-sm">
    <form id="shenhe_bu_tongguo_form" action="" onsubmit="ajaxSubmit();return false;" method="post" class="form-inline" enctype="multipart/form-data" >
        <input type="hidden" name="id" value="<?php echo $_GP['id']; ?>">
        <input type="hidden" name="sts_info_status" value="3">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">店铺审核</h4>
        </div>
        <div class="modal-body">
            <div class="">
                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <td style="text-align:center;" class="dish-id">
                            原因
                        </td>
                        <td style="text-align:center;" class="dish-id">
                            <textarea name="ssi_shenhe_beizhu" style="width: 100%">
                                <?php echo trim($info['ssi_shenhe_beizhu'])?>
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
        var myid = '<?php echo $_GP['id']; ?>';
        var url = '<?php echo web_url('store_shop_manage',array('op'=>'shenhe')) ?>';
        $.post(url,data,function(ret){
            if(ret.errno==1){
                $("#span_"+myid).removeClass('label-info').addClass('label-warning').text('审核不通过');
                $('#alterModal').modal('hide');
            }else{
                alert(ret.message);
            }
        });
    }
</script>