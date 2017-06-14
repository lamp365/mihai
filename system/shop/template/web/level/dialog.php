<div class="alertModal-dialog-sm" style="width: 35%">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">调整等级</h4>
    </div>
    <form method="post" action="<?php echo web_url('shop_level_manage',array('op'=>'UpgradeType')); ?>">
        <div class="modal-body">
            <div class="form-inline">
                <div class="form-group" style="width: 80%;">
                    <label for="">等级</label>
                    <select name="level_type" class="form-control" placeholder="请选择" >
                        <option value="1" <?php if($_GP['level_type']==1) echo "selected=true";?> >区代理</option>
                        <option value="2" <?php if($_GP['level_type']==2) echo "selected=true";?> >市代理</option>
                        <option value="3" <?php if($_GP['level_type']==3) echo "selected=true";?> >省代理</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="rank_level" value="<?php echo $_GP['rank_level']; ?>">
            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            <button type="submit" value="1"  name="sure_add" class="btn btn-primary">确认调整</button>
        </div>
    </form>
</div>