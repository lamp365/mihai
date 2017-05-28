
        <div class="alertModal-dialog-sm">
            <form action="<?php  echo web_url('goodstype',array('op'=>'do_add_spec'));?>" method="post" class="form-inline" enctype="multipart/form-data" >
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php if(empty($edit_spec)){ echo "添加";}else{ echo '修改'; } ?>规格 </h4>
                </div>
                <div class="modal-body">

                    <div class="">
                        <div class="form-group" style="width: 95%;">
                            <label for="">设置规格名称</label>
                            <input type="text" name="spec_name" class="form-control" value="<?php echo $edit_spec['spec_name']; ?>" placeholder="输入属性名称 如：标准配置" >
                        </div><br/><br/>
                        <div class="form-group this_attr_values" style="width: 95%;">
                            <label for="">设置规格项</label>
                            <input type="text" name="" class="form-control" id="shuru_item_name"  placeholder="如:4G+8核或8g+8核等" >
                            <span class="btn btn-md btn-success add_item_name">添 加</span> &nbsp; <span>未打钩的会被下架掉</span><br/>
                            <p style="padding-left:65px;" class="show_item_name">
                                <?php if(!empty($edit_spec_item)) {
                                    foreach($edit_spec_item as $item){
                                        if($item['status'] == 1){
                                            $check = "checked";
                                        }else{
                                            $check = '';
                                        }
                                        echo "<input type='checkbox' class='box_item_name' name='item_name[]' style='margin-left:10px;' value='{$item['id']}@{$item['item_name']}' {$check}><span class='remove_item_name'>{$item['item_name']}</span> ";
                                    }
                                }?>
                            </p>

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="gtype_id" value="<?php echo $gtype['id']; ?>">
                    <input type="hidden" name="id" value="<?php echo $edit_spec['spec_id']; ?>">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" name='sure_add' value="1" class="btn btn-primary">确认<?php if(empty($edit_spec)){ echo "添加";}else{ echo '修改'; } ?></button>
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

            $(".add_item_name").click(function(){
                var item_name = $("#shuru_item_name").val();
                if($.trim(item_name) == ''){
                    return '';
                }

                var isok = true;
                $(".remove_item_name").each(function(){
                    if($(this).html() == item_name){
                        alert("已经存在");
                        isok = false;
                    }
                });
                if(!isok){
                    return '';
                }
                var html = '<input type="checkbox" class="box_item_name" name="item_name[]" style="margin-left:10px;" value="'+item_name+'" checked/><span class="remove_item_name">'+item_name+'</span>';

                $(html).appendTo($('.show_item_name'));
                $("#shuru_item_name").val('');
                $("#shuru_item_name").focus();
            });
        })
    </script>
