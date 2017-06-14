<div class="alertModal-dialog" style="width:30%">
    <form action="<?php echo mobile_url('main',array('op'=>'renewal')); ?>" method="post" class="form-horizontal gtype_form">
    <input type="hidden" name="id" value="<?php  echo $_GP['id']; ?>">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">店铺续期</h4>
    </div>
    <div class="modal-body">

        <div class="form-group">
            <label class="col-sm-2 control-label">账户余额:</label>
            <div class="col-sm-9" style=" margin-top: 8px;">
                <?php echo FormatMoney($dataMember['recharge_money'],2);?>
            </div>
        </div>
        <div class="form-group" id="tablediv" >
            <label class="col-sm-2 control-label">备注:</label>
            <div class="col-sm-9">
                <table class="layui-table">
                        <tbody>
                            <tr>
                                <td class="stop-table-td-1">代理级别</td>
                                <td id="td_type"><?php echo $dataStore['rank_name'];?></td>
                            </tr>
                            <tr>
                                <td class="stop-table-td-1">有效期</td>
                                <td id="td_time"><?php echo $dataStore['time_range'];?>年</td>
                            </tr>
                            <tr id="tr_num">
                                <td class="stop-table-td-1">商品上架数量</td>
                                <td id="td_num"><?php echo $dataStore['dish_num']==0?'无限制':$dataStore['dish_num'];?></td>
                            </tr>
                            <tr>
                                <td class="stop-table-td-1">金额</td>
                                <td id="td_money"><?php echo FormatMoney($dataStore['money'],2);?></td>
                            </tr>
                        </tbody>
                    </table>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">立即续期</button>
    </div>
    </form>
</div>
<script>
    //表单验证
    $('.gtype_form').validator();
    
  function disTable() {
        var sel_val = $("#rank_level option:selected").val();
        if( sel_val <0 ){
            $("#tablediv").hide();
            return false;
        }
        var money = $("#rank_level option:selected").attr('data-money');
        var num = $("#rank_level option:selected").attr('data-num');
        var time = $("#rank_level option:selected").attr('data-time');
        var type = $("#rank_level option:selected").attr('data-type');
        
        $("#tablediv").show();
        $("#td_type").text(type);
        $("#td_time").text(time);
        if(type=='区代理'){
            $("#td_num").text(num);
        }else{
            $("#td_num").text("不限制");
        }
        $("#td_money").text(money);
        
        return false;
    }
    
</script>