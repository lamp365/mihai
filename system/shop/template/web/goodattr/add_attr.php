
        <div class="alertModal-dialog-sm">
            <form action="<?php  echo web_url('goodstype',array('op'=>'do_add_atr'));?>" method="post" class="form-inline" enctype="multipart/form-data" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php if(empty($edit_attr)){ echo "添加";}else{ echo '修改'; } ?>属性</h4>
                </div>
                <div class="modal-body">

                    <div class="">
                        <div class="form-group" style="width: 95%;">
                            <label for="">设置属性名称</label>
                            <input type="text" name="attr_name" class="form-control" value="<?php echo $edit_attr['attr_name']; ?>" placeholder="输入属性名称 如：手机款式" >
                        </div><br/><br/>
                        <!-- 全部都以 手动输入的形式，不要打开 该选项 -->
                        <div class="form-group" style="width: 95%; display: none">
                            <label for="">属性值录入方式</label>
                            <input type="radio" name="attr_input_type" class="form-control" id="" value="0" <?php if($edit_attr['attr_input_type'] == 0){ echo "checked";} ?> onclick="check_attr_value(this)" checked>手工录入&nbsp;
                            <input type="radio" name="attr_input_type" class="form-control" id="" value="1"  <?php if($edit_attr['attr_input_type'] == 1){ echo "checked";} ?> onclick="check_attr_value(this)">从下面中选择&nbsp;
                        </div><br/><br/>
                        <div class="form-group this_attr_values" style="width: 95%;display: none">
                            <label for="">可选值列表</label>
                            <input type="text" name="" class="form-control" id="shuru_attr_value"  placeholder="可选值 如：翻盖或者直板等" >
                            <span class="btn btn-md btn-success add_attr_values">添 加</span> &nbsp; <span>未打钩的会被移除掉</span><br/>
                            <p style="padding-left:65px;" class="show_attr_value">
                                <?php if(!empty($edit_attr['attr_values'])) {
                                    $attr_values_arr = explode(',', $edit_attr['attr_values']);
                                    foreach($attr_values_arr as $value){
                                        echo "<input type='checkbox' class='box_attr_value' name='attr_values[]' style='margin-left:10px;' value='{$value}' checked><span class='remove_attr_value'>{$value}</span> ";
                                    }
                                }?>
                            </p>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="gtype_id" value="<?php echo $gtype['id']; ?>">
                    <input type="hidden" name="id" value="<?php echo $edit_attr['attr_id']; ?>">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" name='sure_add' value="1" class="btn btn-primary">确认<?php if(empty($edit_attr)){ echo "添加";}else{ echo '修改'; } ?></button>
                </div>
            </form>
        </div>


    <script>
        function check_attr_value(obj){
            if($(obj).val() != 0){
                $(".this_attr_values").show();
            }else{
                $(".this_attr_values").hide();
            }
        }
        $(function(){
            $("input[name='attr_input_type']").each(function(){
               if(this.checked){
                   if($(this).val() == 1){
                       $(".this_attr_values").show();
                   }
               }
            });

            $(".add_attr_values").click(function(){
                var attr_value = $("#shuru_attr_value").val();
                if($.trim(attr_value) == ''){
                    return '';
                }

                var isok = true;
                $(".remove_attr_value").each(function(){
                   if($(this).html() == attr_value){
                       alert("已经存在");
                       isok = false;
                   }
                });
                if(!isok){
                    return '';
                }
                var html = '<input type="checkbox" class="box_attr_value" name="attr_values[]" style="margin-left:10px;" value="'+attr_value+'" checked/><span class="remove_attr_value">'+attr_value+'</span>';

                $(html).appendTo($('.show_attr_value'));
                $("#shuru_attr_value").val('');
                $("#shuru_attr_value").focus();
            });
        })
    </script>
