<!doctype html>
<html lang="en">
<head>
    <?php include page('seller_header');?>
    
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/layui-mz-min.js" charset="utf-8"></script>
<style type="text/css">
    #store_shop_dishid_enter{
        width: 400px;height:400px;border-left-width: 1px;
    }
    #store_shop_dishid{
        width: 400px;height:400px;margin-bottom: 20px;
    }
    #store_shop_dishid_enter option,#store_shop_dishid option{
        overflow: hidden;
        white-space: nowrap;
        text-overflow:ellipsis;
    }
    .layui-upload-button{
        position: absolute;    top: 81px;    left: 42.5px;    opacity: 0.8;
    }
</style>
</head>
<body  style="padding:10px;">
<blockquote class="layui-elem-quote"><?php echo $Title;?></blockquote>
<!--<form class="layui-form" action="<?php echo mobile_url('shopbonus',array('op'=>'addcouponsub'));?>" method="post">-->
<div class="layui-form">
    <div class="layui clearfix">
        
        <div class="layui-form-item">
            <label class="layui-form-label">领取方式</label>
            <div class="layui-input-inline" >
                <select name="payment" lay-verify="required" id='payment'>
                    <option value="1" <?php echo $coupon['payment']==1?'selected':'';?>>用户</option>
                    <option value="2" <?php echo $coupon['payment']==2?'selected':'';?>>通用</option> 
                    <option value="3" <?php echo $coupon['payment']==3?'selected':'';?>>活动</option>
                </select>
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">使用方式</label>
            <div class="layui-input-inline" >
                <select name="usage_mode" lay-verify="required" id='usage_mode' lay-filter="usage_mode">
                    <option value="1" <?php echo $coupon['usage_mode']==1?'selected':'';?>>全场</option>
                    <option value="2" <?php echo $coupon['usage_mode']==2?'selected':'';?>>分类</option>
                    <option value="3" <?php echo $coupon['usage_mode']==3?'selected':'';?>>单品</option>
                </select>
            </div>
        </div>
        
        <div class="layui-form-item" id="pruductcagegory"
             <?php
                if($coupon['usage_mode'] == 1 || $_GP['id'] <= 0)
                {
                    echo 'style="display: none;"';
                }
             ?>
        >
            <label class="layui-form-label">商品分类</label>
            <div class="layui-input-inline" >
                <select name="oneCategory"  lay-filter="oneCategory" id='oneCategory'>
                    <option value="">请选择商品分类</option>
                <?php
                  foreach($storyShopClass['oneClass'] as $v)
                  {
                ?>    
                    <option value="<?php echo $v['id'];?>" <?php echo $v['id']==$coupon['store_category_idone']?'selected':'';?>><?php echo $v['name'];?></option>
                <?php
                  }
                ?>
                </select>
            </div>
            <div class="layui-input-inline" id='twoCategoryDiv'>
                <?php
                    if($coupon['usage_mode'] == 2 || $coupon['usage_mode'] == 3 || $_GP['id'] > 0)
                    {
                ?>
                <select name="twoCategory" lay-filter="twoCategory" id="twoCategory">
                    <option value="0" <?php echo $coupon['store_category_idtwo']==0?'selected':'';?>>请选择分类</option>
                    <?php
                        foreach($storyShopClass['twoClass'] as $v)
                        {
                    ?>
                    <option value="<?php echo $v['id'];?>" <?php echo $v['id']==$coupon['store_category_idtwo']?'selected':'';?>><?php echo $v['name'];?></option>
                    <?php
                        }
                    ?>
                </select>
                <?php
                    }
                ?>
                
            </div>
        </div>
        </div>
        </div>
    
        <div class="layui-form-item" style="display: <?php echo $styleCss?>;" id="dishIds" >
            <label class="layui-form-label">商品ID</label>
            <div class="layui-input-inline" style="width:400px;">
                <select name="store_shop_dishid"  id="store_shop_dishid" multiple="true" size="" >
                <?php
                  foreach($dishData as $v)
                  {
                ?>    
                    <option value="<?php echo $v['id'];?>"><?php echo $v['title'];?></option>
                <?php
                  }
                ?>
                </select> 
            </div>
            
            <div class="layui-input-inline" style="margin-top: 185px;width: 91px;">
                <button class="layui-btn layui-btn-small layui-btn-primary"  id='addselect'><i class="layui-icon"></i></button>
                <button class="layui-btn layui-btn-small layui-btn-primary" id='delselect'><i class="layui-icon" ></i></button>
            </div>
            
            <div class="layui-input-inline" style="width:400px;">
                <select name="store_shop_dishid_enter"  id="store_shop_dishid_enter" multiple="true" size="" >
                    <?php
                        foreach($dishRightData as $v)
                        {
                    ?>
                        <option value="<?php echo $v['id'];?>"><?php echo $v['title'];?></option>
                    <?php
                        }
                    ?>
                </select> 
            </div>
        </div>
            
        <div class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">优惠券名称</label>
            <div class="layui-input-inline" >
                <input type="text" name="coupon_name" placeholder="请输入优惠券名称" autocomplete="off" class="layui-input" lay-verify="required" id='coupon_name' value="<?php echo $coupon['coupon_name'];?>">
            </div>
        </div>
            
        <div class="layui-form-item">
            <label class="layui-form-label">图片上传</label>
            <div class="layui-input-inline" style="position:relative;">
                
                <img id="coupon_img_show" name="coupon_img_show" src="<?php echo $coupon['coupon_img'];?>" style="width: 200px; height: 200px; border-radius: 100%;">
                <input type="file" name="file" class="layui-upload-file" id="file" >
                <input type="hidden" value="<?php echo $coupon['coupon_img'];?>" id="coupon_img" name="coupon_img" >
            </div>
        </div>
                
        <div class="layui-form-item">
            <label class="layui-form-label">优惠券金额</label>
            <div class="layui-input-inline" >
                <input type="number" name="coupon_amount" id="coupon_amount"  placeholder="请输入优惠券面值" autocomplete="off" class="layui-input" lay-verify="required" value="<?php echo $coupon['coupon_amount'];?>">
            </div>
        </div>
        
        
        <div class="layui-form-item">
            <label class="layui-form-label">消费金额</label>
            <div class="layui-input-inline" >
                <input type="number" name="amount_of_condition" id="amount_of_condition" placeholder="请输入消费金额" autocomplete="off" class="layui-input" lay-verify="required" value="<?php echo $coupon['amount_of_condition'];?>">
            </div>
        </div>
        
        <div class="layui-form-item">
            <label class="layui-form-label">优惠券总量</label>
            <div class="layui-input-inline" >
                <input type="number" name="release_quantity" id="release_quantity" placeholder="请输入发放数量" autocomplete="off" class="layui-input" lay-verify="required" value="<?php echo $coupon['release_quantity'];?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">用户领取限制</label>
            <div class="layui-input-inline" >
             <input type="number" name="get_limit" id="get_limit" placeholder="0表示无限制" autocomplete="off" class="layui-input" lay-verify="required" value="<?php echo $coupon['get_limit'];?>">
            </div>
        </div>
        
        <div class="layui-form-pane" style="margin-top: 15px;">
        <div class="layui-form-item">
          <label class="layui-form-label">可领取时间</label>
          <div class="layui-input-inline">
              <input class="layui-input" placeholder="开始日" id="receive_start_time"  name="receive_start_time"  value="<?php echo $coupon['receive_start_time'];?>">
          </div>
          <div class="layui-input-inline">
            <input class="layui-input" placeholder="截止日" id="receive_end_time"  name="receive_end_time" value="<?php echo $coupon['receive_end_time'];?>">
          </div>
        </div>
      </div>       
        
        <div class="layui-form-pane" style="margin-top: 15px;">
        <div class="layui-form-item">
          <label class="layui-form-label">可使用时间</label>
          <div class="layui-input-inline">
            <input class="layui-input" placeholder="开始日" id="use_start_time"  name="use_start_time" value="<?php echo $coupon['use_start_time'];?>">
          </div>
          <div class="layui-input-inline">
            <input class="layui-input" placeholder="截止日" id="use_end_time"  name="use_end_time" value="<?php echo $coupon['use_end_time'];?>">
          </div>
        </div>
      </div>  
        
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-inline" >
                <input type="hidden" name="do_add" value="1">
                    <button class="layui-btn" lay-submit lay-filter="formDemo" id="couponsub">提交</button>
            </div>
        </div>
    </div>
   
<input type='hidden' value='' name="oneG" id='oneG'>
<input type='hidden' value='<?php echo $types;?>' name="types" id='types'>
<input type='hidden' value='<?php echo $_GP['id'];?>' name="id" id='id'>
<!--</form>-->
</div>
<?php include page('seller_footer');?>
<script>
    layui.use(['form', 'laydate','upload'], function(){
        var form = layui.form();
        var laydate = layui.laydate;
        
        layui.upload({
            //upload_name
            url: '/api/fileupload/index.html'
            ,elem:"#file"
            ,method:'post'
            ,success: function(res){
              layui.jquery('#coupon_img').val(res.data['pic_url']);
              layui.jquery('#coupon_img_show').attr('src',res.data['pic_url']);
            }
        });      
      
        
        
        //layui.selMeltiple(layui.jquery);
        
        layui.jquery('#addselect').click(function(){
            layui.jquery('#store_shop_dishid').append(layui.jquery('#store_shop_dishid_enter option:selected'));
        });

        layui.jquery('#delselect').click(function(){
            layui.jquery('#store_shop_dishid_enter').append(layui.jquery('#store_shop_dishid option:selected'));
        });
        
        form.on('select(usage_mode)', function(data){
            var val = parseInt(data.value);
            switch(val)
            {
                case 1:
                    layui.jquery('#dishIds').css('display','none');
                    layui.jquery('#pruductcagegory').css('display','none');
                  break;
                case 2:
                    layui.jquery('#pruductcagegory').css('display','');
                    layui.jquery('#dishIds').css('display','none');
                  break;
                case 3:
                    layui.jquery('#pruductcagegory').css('display','');
                    layui.jquery('#dishIds').css('display','');
                  break;
            }
        });    
        
        form.on('select(oneCategory)', function(data){
            var val = parseInt(data.value);
            //var weburl = '/seller/product/parCategory.html'; 
            var weburl = "<?php echo mobile_url('product',array('op'=>'parCategory')); ?>"; 
            var twoCategoryHtml = '';
            layui.jquery.post(weburl,{'pid':val},function(data){
                twoCategoryHtml = '<select name="twoCategory" lay-filter="twoCategory" id="twoCategory"><option value="">--请选择分类--</option>';
                for(var i in data){
                    if (data.hasOwnProperty(i)) { //filter,只输出man的私有属性
                        //console.log(i,":",data[i]);
                        twoCategoryHtml = twoCategoryHtml + '<option value="'+ data[i]['id'] +'">'+ data[i]['name'] +'</option>';
                    };
                }
                twoCategoryHtml = twoCategoryHtml + '</select>';
                layui.jquery('#twoCategoryDiv').html(twoCategoryHtml);
                form.render();
            },"json");
        });
        
        form.on('select(twoCategory)', function(data){
            layui.jquery('#store_shop_dishid').find("option").remove();
            layui.jquery('#store_shop_dishid_enter').find("option").remove();

            //var weburl = '/seller/shopbonus/getDish.html';
            var weburl = "<?php echo mobile_url('shopbonus',array('op'=>'getDish')); ?>";
            layui.jquery.post(weburl,{oneCategory:layui.jquery('#oneCategory').val(),twoCategory:data.value},function(redata){
                
                var r = 0;
                for(var r in redata){
                    layui.jquery('#store_shop_dishid').append('<option value=' + redata[r]['id'] + '>' + redata[r]['title'] + '</option>');
                    r = r + 1;
                }
                
            },"json");
        });
        
    
        var start = {
        max: '2099-06-16 23:59:59'
        ,istoday: false
        ,format: 'YYYY-MM-DD hh:mm:ss'
        ,istime: true
        ,istoday: true
        ,choose: function(datas){
          end.min = datas; //开始日选好后，重置结束日的最小日期
          end.start = datas //将结束日的初始值设定为开始日
          
        }
      };

      var end = {
        max: '2099-06-16 23:59:59'
        ,istoday: false
        ,format: 'YYYY-MM-DD hh:mm:ss'
        ,istime: true
        ,istoday: true
        ,choose: function(datas){
          start.max = datas; //结束日选好后，重置开始日的最大日期
        }
      };
      
      //可领取时间
      var receivestart = {
        max: '2099-06-16 23:59:59'
        ,istoday: false
        ,format: 'YYYY-MM-DD hh:mm:ss'
        ,istime: true
        ,istoday: true
        ,choose: function(datas){
          receiveend.min = datas; //开始日选好后，重置结束日的最小日期
          receiveend.start = datas //将结束日的初始值设定为开始日
        }
      };

      var receiveend = {
        max: '2099-06-16 23:59:59'
        ,istoday: false
        ,format: 'YYYY-MM-DD hh:mm:ss'
        ,istime: true
        ,istoday: true
        ,choose: function(datas){
          receivestart.max = datas; //结束日选好后，重置开始日的最大日期
        }
      };
      
      layui.jquery("#use_start_time").click(function(){
        start.elem = this;
        laydate(start);
      });
      
      layui.jquery("#use_end_time").click(function(){
        end.elem = this
        laydate(end);
      });
        
      layui.jquery("#receive_start_time").click(function(){
        receivestart.elem = this;
        laydate(receivestart);
      });
      
      layui.jquery("#receive_end_time").click(function(){
        receiveend.elem = this
        laydate(receiveend);
      });
        
        
    });
    
    layui.jquery("#couponsub").click(function(){
        var coupon_amount = parseFloat(layui.jquery('#coupon_amount').val());
        var amount_of_condition = parseFloat(layui.jquery('#amount_of_condition').val());
        var form_data_json = {};
        var select_data_dish = '';
        var i = 0
        if(coupon_amount > amount_of_condition)
        {
            layer.open({
                title: '',
                content: '消费金额小于优惠券金额',
                time: 2000
            });     
            return false;
        }
        
        i = 0;
        layui.jquery("#store_shop_dishid_enter option").each(function() {
            select_data_dish= select_data_dish + layui.jquery(this).attr("value") +',';
            i = i + 1;
        });
        if(select_data_dish != ''){
            select_data_dish = select_data_dish.substring(0,select_data_dish.length-1);
        }
        form_data_json = {
            'id':layui.jquery('#id').val(),
            'coupon_name':layui.jquery('#coupon_name').val(),
            'payment':layui.jquery('#payment').val(),
            'usage_mode':layui.jquery('#usage_mode').val(),
            'coupon_amount':layui.jquery('#coupon_amount').val(),
            'amount_of_condition':layui.jquery('#amount_of_condition').val(),
            'release_quantity':layui.jquery('#release_quantity').val(),
            'receive_start_time':layui.jquery('#receive_start_time').val(),
            'receive_end_time':layui.jquery('#receive_end_time').val(),
            'use_start_time':layui.jquery('#use_start_time').val(),
            'use_end_time':layui.jquery('#use_end_time').val(),
            'oneCategory':layui.jquery('#oneCategory').val(),
            'twoCategory':layui.jquery('#twoCategory').val(),
            'get_limit':layui.jquery('#get_limit').val(),
            'coupon_img':layui.jquery('#coupon_img').val(),
            'store_shop_dishid':select_data_dish
        };
        if( form_data_json.use_end_time < form_data_json.receive_start_time || form_data_json.use_start_time < form_data_json.receive_start_time){
            layer.alert("可使用时间不能小于可领取时间");
            return false
        }
        var url = "<?php echo mobile_url('shopbonus',array('op'=>'addcoupon')); ?>";
        
        layui.jquery.post(url,form_data_json,function(data){
            if(data.errno == 1){
                layer.open({
                    title: '提示'
                    ,content: data.message
                    ,yes: function(index){
                        location.href = data.data;
                    }
                });
            }else{
                layer.open({
                    title: '提示'
                    ,content: data.message
                });
            }
            return false;
        });
    });

</script>
</body>
</html>