<div class="alertModal-dialog-bg" style="width:40%">
    <form action="<?php echo web_url('shopruler',array('op'=>'do_add_sonmenu'))?>" method="post" class="form-horizontal"  >
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel"><?php if(empty($_GP['rule_id'])){ echo '添加';}else{ echo '编辑'; } ?>菜单</h4>
        </div>
        <div class="modal-body">
            <p style="margin:0px 10px 10px 10px;background: #fcfcfc;border: 1px solid #e5e5e5;padding: 10px;color:red;font-size: 12px;">注：对于添加和编辑的操作，最好选择上操作类型。顶级菜单其他项可放空</p>
            <?php if(empty($_GP['id'])){ ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 上级菜单：</label>

                    <div class="col-sm-4">
                        <input type="hidden" name="pid" value="<?php echo $menu['rule_id'];?>">
                        <input type="text" value="<?php echo $menu['rule_name'];?>" class="col-xs-10 col-sm-4 form-control" disabled>
                    </div>
                </div>
            <?php } ?>


            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 模块名：</label>

                <div class="col-sm-4">
                    <input type="text"  name="modname" class="col-xs-10 col-sm-4 form-control" value="<?php if(!empty($editMenu)){ echo $editMenu['modname'];}else{ echo 'seller';} ?>"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 控制器：</label>

                <div class="col-sm-4">
                    <input type="text"  name="moddo" class="col-xs-10 col-sm-4 form-control"  value="<?php if(!empty($editMenu)){echo $editMenu['moddo'];}else { echo $menu['moddo'];} ?>"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 操作方法：</label>

                <div class="col-sm-4">
                    <input type="text"  name="modop"  placeholder="不给会默认为index" class="col-xs-10 col-sm-4 form-control"  value="<?php echo $editMenu['modop']; ?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-left" > 菜单名：</label>

                <div class="col-sm-4">
                    <input type="text" name="rule_name"   placeholder="菜单名称必须填写" class="col-xs-10 col-sm-4 form-control" value="<?php echo $editMenu['rule_name']; ?>"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 操作类型：</label>

                <div class="col-sm-4">
                    <select name="act_type" id="" class="form-control">
                        <option value="0">选择操作</option>
                        <option value="1" <?php if($editMenu['act_type']=='1') echo "selected";?>>add</option>
                        <option value="2" <?php if($editMenu['act_type']=='2') echo "selected";?>>edit</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label no-padding-left" for="form-field-1"> 排序：</label>

                <div class="col-sm-4">
                    <input type="number"  name="sort" class="col-xs-10 col-sm-4 form-control"  value="<?php echo $editMenu['sort']; ?>"/>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="rule_id" value="<?php  echo $_GP['rule_id'];?>" />
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <button type="submit" class="btn btn-primary">确认<?php if(empty($_GP['rule_id'])){ echo '添加';}else{ echo '编辑'; } ?></button>
        </div>
    </form>
</div>


<script>

</script>
