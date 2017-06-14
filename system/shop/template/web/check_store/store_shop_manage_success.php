<div class="alertModal-dialog-sm">
    <form id="formtagid" action="" onsubmit="ajaxSubmit();return false;" method="post" class="form-inline" enctype="multipart/form-data" >
        <input type="hidden" name="id" value="<?php echo $_GP['id']; ?>">
        <input type="hidden" name="sts_info_status" value="0">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">店铺审核(默认1级区代理)</h4>
        </div>
        <div class="modal-body">
            <div class="">
                <table class="table table-striped table-bordered table-hover">
                    <tr>
                        <td style="text-align:center;" class="dish-id">
                            店铺等级
                        </td>
                        <td style="text-align:center;" class="dish-id">
                            <select type="text" name="level_type" class="col-xs-10 col-sm-7" >
                                <?php foreach ($result as $c) { ?>
                                    <option value="<?php echo $c['rank_level'] ?>"><?php echo $c['rank_name'] ?><?php echo "(".$typeText[$c['level_type']].")" ?></option>
                                <?php } ?>
                            </select>
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
        var data = $("#formtagid").serialize();
        var myid = '<?php echo $_GP['id']; ?>';
        var url = '<?php echo web_url('store_shop_manage',array('op'=>'shenhe')) ?>';
        $.post(url,data,function(ret){
            if(ret.errno==1){
                $("#span_"+myid).removeClass('label-warning').addClass('label-success').text('已认证');
                $("#span_"+myid).parents('tr').remove();
                $('#alterModal').modal('hide');
            }else{
                alert(ret.message);
            }
        });
    }
</script>