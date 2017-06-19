<div class="alertModal-dialog" style="width:30%">
    <form action="<?php echo mobile_url('store_shop',array('op'=>'shop_edit_sub')); ?>" method="post" class="form-horizontal gtype_form " name="myform" id="myform">
    <input type="hidden" name="id" value="<?php  echo $_GP['id']; ?>">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">店铺信息(<font color="red"><b>*</b></font>号为必填项)</h4>
    </div>
    <div class="modal-body">
          <div class="layui-form-item">
            <label class="layui-form-label">店铺名<font color="red"><b>*</b></font></label>
            <div class="layui-input-block">
                <input type="text" name="sts_name" required autocomplete="off" class="layui-input" value="<?php echo $storeInfo['sts_name'] ?>">
            </div>
          </div>
        
          <div class="layui-form-item">
            <label class="layui-form-label">实体店<font color="red"><b>*</b></font></label>
            <div class="layui-input-block">
              <input type="text" name="sts_physical_shop_name" required autocomplete="off" class="layui-input" value="<?php echo $storeInfo['sts_physical_shop_name'] ?>">
            </div>
          </div>
        
        
          <div class="layui-form-item">
            <label class="layui-form-label">联系人<font color="red"><b>*</b></font></label>
            <div class="layui-input-block">
              <input type="text" name="sts_contact_name"  required  autocomplete="off" class="layui-input" value="<?php echo $storeInfo['sts_contact_name'] ?>">
            </div>
          </div>
        
        
          <div class="layui-form-item">
            <label class="layui-form-label">手机号<font color="red"><b>*</b></font></label>
            <div class="layui-input-block">
              <input type="text" name="sts_mobile" required   autocomplete="off" class="layui-input" value="<?php echo $storeInfo['sts_mobile'] ?>">
            </div>
          </div>
        
          <div class="layui-form-item">
            <label class="layui-form-label">微信号</font></label>
            <div class="layui-input-block">
              <input type="text" name="sts_weixin"  autocomplete="off" class="layui-input" value="<?php echo $storeInfo['sts_weixin'] ?>">
            </div>
          </div>
        
          <div class="layui-form-item">
            <label class="layui-form-label">QQ号</font></label>
            <div class="layui-input-block">
              <input type="text" name="sts_qq"  autocomplete="off" class="layui-input" value="<?php echo $storeInfo['sts_qq'] ?>">
            </div>
          </div>
          <div class="layui-form-item">
            <label class="layui-form-label">店铺简介<font color="red"><b>*</b></font></label>
            <div class="layui-input-block">
              <input type="text" name="sts_summary" required autocomplete="off" class="layui-input" value="<?php echo $storeInfo['sts_summary'] ?>">
            </div>
          </div>
          <div class="layui-form-item">
            <label class="layui-form-label">所在地区</label>
            <div class="layui-input-block">
                <div class="layui-input-inline" style="width: 30%;">
                   
                <select name="sts_locate_add_1" lay-verify="required" lay-filter="sts_locate_add_1" id="sts_locate_add_1">
                 <?php
                   foreach($resultProvince as $v){
                 ?>
                    <option value="<?php echo $v['region_id'];?>" <?php echo $v['region_id']==$storeInfo['sts_locate_add_1']?'selected':'';?>><?php echo $v['region_name'];?></option>
                 <?php
                   }
                 ?>
               </select>
                   
              </div>
                
               <div class="layui-input-inline" style="width: 30%;">
                   <select name="sts_locate_add_2" lay-verify="required" lay-filter="sts_locate_add_2" id="sts_locate_add_2">
                 <?php
                   foreach($resultCity as $v){
                 ?>
                    <option value="<?php echo $v['region_id'];?>" <?php echo $v['region_id']==$storeInfo['sts_locate_add_2']?'selected':'';?>><?php echo $v['region_name'];?></option>
                 <?php
                   }
                 ?>
               </select>
                   
              </div>
                
                
               <div class="layui-input-inline" style="width: 30%;" id="sts_locate_add_3_div">
                   <select name="sts_locate_add_3" lay-verify="required" lay-filter="sts_locate_add_3" id="sts_locate_add_3">
                 <?php
                   foreach($resultCounty as $v){
                 ?>
                    <option value="<?php echo $v['region_id'];?>" <?php echo $v['region_id']==$storeInfo['sts_locate_add_3']?'selected':'';?>><?php echo $v['region_name'];?></option>
                 <?php
                   }
                 ?>
               </select>
                   
              </div>
                
            </div>
          </div>
          <div class="layui-form-item">
            <label class="layui-form-label">详细地址<font color="red"><b>*</b></font></label>
            <div class="layui-input-block">
              <input type="text" name="sts_address" required  autocomplete="off" class="layui-input" value="<?php echo $storeInfo['sts_address'] ?>">
            </div>
          </div>
        
          <div class="layui-form-item">
            <label class="layui-form-label">佣金<font color="red"><b>*</b></font></label>
            <div class="layui-input-block">
              <input type="text" name="commision" required  autocomplete="off" class="layui-input" value="<?php echo $storeInfo['commision'] ?>">
            </div>
          </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">确认修改</button>
    </div>
    </form>
</div>
<script>
 $(function(){
    $('#sts_locate_add_1').on('change',function(){
        var val = parseInt($(this).val());
        var weburl = "<?php echo mobile_url('store_shop',array('op'=>'childen_region')); ?>";
        var twoCategoryHtml = '';
        $.post(weburl,{'id':val},function(data){
            for(var i in data){
                if (data.hasOwnProperty(i)) { 
                    twoCategoryHtml = twoCategoryHtml + '<option value="'+ data[i]['region_id'] +'">'+ data[i]['region_name'] +'</option>';
                };
            }
            $("#sts_locate_add_2").html(twoCategoryHtml);
            $("#sts_locate_add_3_div").html('');
        },"json");
    });
    
    $('#sts_locate_add_2').on('change',function(){
        var val = parseInt($(this).val());
        var weburl = "<?php echo mobile_url('store_shop',array('op'=>'childen_region')); ?>";
        var twoCategoryHtml = '<select name="sts_locate_add_3" lay-verify="required" lay-filter="sts_locate_add_3" id="sts_locate_add_3">';

        $.post(weburl,{'id':val},function(data){
            for(var i in data){
                if (data.hasOwnProperty(i)) { 
                    twoCategoryHtml = twoCategoryHtml + '<option value="'+ data[i]['region_id'] +'">'+ data[i]['region_name'] +'</option>';
                };
            }
            twoCategoryHtml = twoCategoryHtml + '</select>';
            $("#sts_locate_add_3_div").html(twoCategoryHtml);
            
        },"json");

    });    
});
    /*
    $('#sts_locate_add_2').on('change', function($(this)){
        var val = parseInt(data.value);
        var weburl = "<?php echo mobile_url('store_shop',array('op'=>'childen_region')); ?>";
        var twoCategoryHtml = '';
        $.post(weburl,{'id':val},function(data){
            for(var i in data){
                if (data.hasOwnProperty(i)) { 
                    twoCategoryHtml = twoCategoryHtml + '<option value="'+ data[i]['region_id'] +'">'+ data[i]['region_name'] +'</option>';
                };
            }
            $("#sts_locate_add_2").html(twoCategoryHtml);
            $("#sts_locate_add_3_div").html('');
            form.render();
        },"json");
    });
    
    $('#sts_locate_add_3').on('change', function($(this)){
        var val = parseInt(data.value);
        var weburl = "<?php echo mobile_url('store_shop',array('op'=>'childen_region')); ?>";
        var threeCategoryHtml = '<select name="sts_locate_add_3" lay-verify="required" lay-filter="sts_locate_add_3" id="sts_locate_add_3">';
        $.post(weburl,{'id':val},function(data){
            for(var i in data){
                if (data.hasOwnProperty(i)) { 
                    threeCategoryHtml = threeCategoryHtml + '<option value="'+ data[i]['region_code'] +'">'+ data[i]['region_name'] +'</option>';
                    //$('#sts_locate_add_2').append('<option value="'+ data[i]['region_id'] +'">'+ data[i]['region_name'] +'</option>');
                };
            }
            threeCategoryHtml = threeCategoryHtml + '</select>';
            $("#sts_locate_add_3_div").html(threeCategoryHtml);

            form.render();
    },"json");
    
    form.render();
    */

//表单验证
</script>