<div class="alertModal-dialog" style="width:30%">
    <form action="<?php echo mobile_url('product',array('op'=>'addLtcSub')); ?>" method="post" class="form-horizontal gtype_form layui-form" name="myform" id="myform" lay-filter="myform">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">参与限时购(<font color="red"><b>*</b></font>号为必填项)</h4>
    </div>
    <div class="modal-body">
          <div class="layui-form-item">
            <label class="layui-form-label">系统分类<font color="red"><b>*</b></font></label>
             <div class="layui-input-inline" >
                <select name="ac_p1_id" lay-verify="required" lay-filter="ac_p1_id" id="ac_p1_id">
                    <option value="0">请选择</option>
                    <?php
                      foreach($oneCate as $v){
                    ?>
                    <option value="<?php echo $v['id'];?>" <?php if($ltcInfo['ac_p1_id'] == $v['id']){echo 'selected';}?>><?php echo $v['name'];?></option>
                    <?php
                      }
                    ?>
                </select>
            </div>
            
            <div class="layui-input-inline" id="twoCateDiv">
               <?php
                    if($ac_dish_id > 0){
                      if(count($twoCategory) > 0)
                      {
                        echo '<select name="ac_p2_id" lay-filter="ac_p2_id">';
                        foreach($twoCategory as $v){
                ?>
                    <option value="<?php echo $v['id'];?>" <?php if($ltcInfo['ac_p2_id'] == $v['id']){echo 'selected';}?>><?php echo $v['name'];?></option>
                <?php
                        }
                        echo '</select>';
                      }
                    }
                ?>
            </div>
            
            
            
          </div>
        
          <div class="layui-form-item">
            <label class="layui-form-label">活动ID<font color="red"><b>*</b></font></label>
            <div class="layui-input-block">
                <select name="ac_action_id" lay-verify="required" lay-filter="ac_action_id" id="ac_action_id">
                    <option value="0">请选择</option>
                    <?php
                      foreach($areaList as $v){
                    ?>
                    <option value="<?php echo $v['ac_id'];?>" <?php if($ltcInfo['ac_action_id'] == $v['ac_id']){echo 'selected';}?>><?php echo $v['ac_title'];?></option>
                    <?php
                      }
                    ?>
                </select>
            </div>
          </div>
        
          <div class="layui-form-item">
            <label class="layui-form-label">活动时段</label>
            <div class="layui-input-block" id="ac_area_id_div">
                <select name="ac_area_id" lay-filter="ac_area_id" id="ac_area_id">
                    <option value="0">全天可见</option>
                    <?php
                      foreach($activGroup as $v){
                    ?>
                         <option value="<?php echo $v['ac_area_id'];?>" <?php if($ltcInfo['ac_area_id'] == $v['ac_area_id']){echo 'selected';}?>><?php echo $v['ac_area_title'];?></option>
                    <?php
                      }
                    ?>
                </select><font color="red">(若不选则为全天可见)</font>
            </div>
          </div>
        
           <!--
          <div class="layui-form-item">
            <label class="layui-form-label">活动时段详情</label>
            <div class="layui-input-block">
                <textarea name="list_desc" class="layui-textarea" id="list_desc" readonly="">
                  <?php echo $ltcInfo['time_html'];?>
                </textarea>
            </div>
          </div-->
        
          <div class="layui-form-item">
            <label class="layui-form-label">价格<font color="red"><b>*</b></font></label>
            <div class="layui-input-block">
                <input type="text" name="ac_dish_price"  required  autocomplete="off" class="layui-input" id="ac_dish_price" value="<?php echo $ltcInfo['ac_dish_price'];?>">
            </div>
          </div>
        
          <div class="layui-form-item">
            <label class="layui-form-label">库存<font color="red"><b>*</b></font></label>
            <div class="layui-input-block">
                <input type="text" name="ac_dish_total" required   autocomplete="off" class="layui-input" id="ac_dish_total"  value="<?php echo $ltcInfo['ac_dish_total'];?>">
            </div>
          </div>
          <?php if ($ac_dish_id > 0){?>
          <div class="layui-form-item">
            <label class="layui-form-label">审核状态</label>
            <div class="layui-input-block">
                <font color="red"><?php if ($ltcInfo['ac_dish_status'] == 1){echo "审核成功";}elseif ($ltcInfo['ac_dish_status'] == 2) {echo "审核失败";}else{echo "正在审核";}?></font>
            </div>
          </div>
          <span>注意：已参加限时购的商品编辑后需要重新审核，请谨慎修改</span>
          <?php }?>
        
    </div>
    <div class="modal-footer">
        <input type="hidden" name="ac_shop_dish" id="ac_shop_dish" value="<?php echo $dish_id?>">
        <input type="hidden" name="ac_dish_id" id="ac_dish_id" value="<?php echo $ac_dish_id?>">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">确认修改</button>
    </div>
    </form>
</div>


<script>
layui.use('form', function(){
    var $ = layui.jquery, form = layui.form();
    form.render();
    
    form.on('select(ac_area_id)', function(obj){
        var val = parseInt(obj.value);
        var weburl = "<?php echo mobile_url('product',array('op'=>'ltcAreaDeti')); ?>";
        $.post(weburl,{'aid':val},function(data){

        },"json");
    });
    
    form.on('select(ac_action_id)', function(data){
        var val = parseInt(data.value);
        var weburl = "<?php echo mobile_url('product',array('op'=>'getActivityAreaList')); ?>";
        var twoCategoryHtml = '';
        $.post(weburl,{'ac_id':val},function(data){
            twoCategoryHtml = '<select name="ac_area_id" lay-filter="ac_area_id" id="ac_area_id"><option value="0">全天可见</option>';
            for(var i in data){
                if (data.hasOwnProperty(i)) { //filter,只输出man的私有属性
                    twoCategoryHtml = twoCategoryHtml + '<option value="'+ data[i]['ac_area_id'] +'">'+ data[i]['ac_area_title'] +'</option>';
                };
            }
            twoCategoryHtml = twoCategoryHtml + '</select>';
            $('#ac_area_id_div').html(twoCategoryHtml);
            form.render();
        },"json");
    });
    
    form.on('select(ac_p1_id)', function(data){
        var val = parseInt(data.value);
        var weburl = "<?php echo mobile_url('product',array('op'=>'getSystemCate')); ?>";
        var twoCategoryHtml = '';
        if(val == '')
        {
            $('#twoCateDiv').html('');
            return false;
        }
        $.post(weburl,{'pid':val},function(data){
            twoCategoryHtml = '<select name="ac_p2_id" lay-filter="ac_p2_id">';
            for(var i in data){
                if (data.hasOwnProperty(i)) { //filter,只输出man的私有属性
                    twoCategoryHtml = twoCategoryHtml + '<option value="'+ data[i]['id'] +'">'+ data[i]['name'] +'</option>';
                };
            }
            twoCategoryHtml = twoCategoryHtml + '</select>';
            $('#twoCateDiv').html(twoCategoryHtml);
            form.render();
        },"json");
    });
    
    form.on('submit(myform)', function(data){
        if($('#ac_p1_id').val() == '0')
        {
            layer.alert('请选择系统分类');
            return false;
        }
        if($('#ac_action_id').val() == '0')
        {
            layer.alert('请选择活动ID');
            return false;
        }
        var ac_dish_price = $("#ac_dish_price").val();
		if(pricecheck(ac_dish_price)){
			layer.alert('请输入有效金额');
            return false;
		}
		var ac_dish_total = $("#ac_dish_total").val();
    	if((/^(\+|-)?\d+$/.test(ac_dish_total)) && ac_dish_total > 0){  
	        return true;  
	    }else{  
	    	layer.alert('请输入有效库存');  
	        return false;  
	    }
    });  
    function pricecheck(price){
        var fix_amountTest=/^(([1-9]\d*)|\d)(\.\d{1,2})?$/;
        if(fix_amountTest.test(price)==false){
            return true;
           }
        }
});
</script>