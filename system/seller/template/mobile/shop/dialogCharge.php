<div class="alertModal-dialog" style="width:30%">
    <form action="<?php echo mobile_url('store_shop',array('op'=>'postNewLevel')); ?>" method="post" class="form-horizontal gtype_form">
    <input type="hidden" name="id" value="<?php  echo $_GP['id']; ?>">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">购买店铺等级</h4>
    </div>
    <div class="modal-body">
        <?php if(!empty($result)){   ?>
        <div class="form-group">
            <label class="col-sm-2 control-label">级别:</label>
            <div class="col-sm-9">
                <select name="rank_level" id="rank_level" onchange="disTable()" class="form-control">
                    <option value='-1'>请选择</option>"
                    <?php foreach($result as $single) { ?>
                        <option 
                            value='<?php echo   $single['rank_level']?>'  
                            data-money='<?php echo   $single['money']?>'
                            data-num='<?php echo   $single['dish_num']?>'
                            data-time='<?php echo   $single['time_range']?>'
                            data-type='<?php if($single['level_type'] == 1) echo  "区代理";?><?php if($single['level_type'] == 2) echo  "市代理";?><?php if($single['level_type'] == 3) echo  "省代理";?>'
                                >
                                    <?php echo   $single['rank_name']?> (费用：<?php echo   $single['money']?>)</option>"
                     <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group" id="tablediv" >
            <label class="col-sm-2 control-label">备注:</label>
            <div class="col-sm-9">
                <table class="layui-table">
                        <tbody>
                            <tr>
                                <td class="stop-table-td-1">代理级别</td>
                                <td id="td_type"></td>
                            </tr>
                            <tr>
                                <td class="stop-table-td-1">有效期</td>
                                <td id="td_time"></td>
                            </tr>
                            <tr id="tr_num">
                                <td class="stop-table-td-1">商品上架数量</td>
                                <td id="td_num"></td>
                            </tr>
                            <tr>
                                <td class="stop-table-td-1">金额</td>
                                <td id="td_money"></td>
                            </tr>
                        </tbody>
                    </table>
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">确认</button>
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