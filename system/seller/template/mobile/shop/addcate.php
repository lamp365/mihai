
<div class="alertModal-dialog" style="width:30%">
  
    <form  action="<?php  echo mobile_url('category',array('name'=>'seller','op'=>'post')) ?>" method="post" class="gtype_form form-horizontal">
        <input type="hidden" name="pid" id="isHavePid"  value="<?php echo intval($_GP['pid']) ?>" >
        <input type="hidden" name="id" id="self_id"  value="<?php echo intval($_GP['id']) ?>" >
        <input type="hidden" name="store_shop_id" id="shop_id"  value="<?php echo intval($_GP['store_shop_id']) ?>" >
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">
                  <?php if(intval($_GP['id'])>0){
                      echo "编辑分类";
                  }else{
                      echo "添加".(empty($_GP['pid']) ?'分类':'子类');
                  } ?>
            </h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label  class="col-sm-3 control-label">分类名称</label>
                <div class="col-sm-9">
                    <input type="text" name="cat_name" value="<?php echo $info['name'] ?>" id="new_name"  class="form-control"  placeholder="请输入分类名称" required>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            <button type="submit"  class="btn btn-primary" >确认<?php echo intval($_GP['id'])>0?"编辑":"添加" ?></button>
        </div>
    </form>
</div>

<script>
    //监听表单验证  input框 需要的规则 可以查看bootstrap
    $(".gtype_form").validator();

    function postSend() {
        var name =$("#new_name");
        var pid =$("#isHavePid");
        var id= $("#self_id"); 
        var url = "<?php  echo mobile_url('category',array('name'=>'seller','op'=>'post')) ?>";
     
        $.post(url, {id: id,cat_name:name,pid:pid}, function (data) {
            if (data.errno == 1) {
                location.href="<?php  echo mobile_url('category',array('name'=>'seller')) ?>";
            } else {
                alert(data.message);
            }
        }, "json");
        return false;
    }
</script>