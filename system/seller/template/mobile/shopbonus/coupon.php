<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('seller_header');?>
  <link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>addons/seller/css/global.css" media="all">

<body style="padding:10px;">
<!--商品搜索区域-->
<form class="layui-form layui-form-pane" action="" method="post">
  <div class="layui-form-item">

    <label class="layui-form-label">使用方式</label>
    <div class="layui-input-inline">
      <select name="usage_mode" lay-filter="usage_mode">
        <option value="">--请选择使用方式--</option>
        <option value="1" <?php echo $_GP['usage_mode']==1?'selected':'';?>>全场</option>
        <option value="2" <?php echo $_GP['usage_mode']==2?'selected':'';?>>分类</option>
        <option value="3" <?php echo $_GP['usage_mode']==3?'selected':'';?>>单品</option>
      </select>
    </div>
    
    <label class="layui-form-label">领取方式</label>
    <div class="layui-input-inline">
      <select name="payment" lay-filter="payment">
        <option value="">--请选择领取方式--</option>
        <option value="1" <?php echo $_GP['payment']==1?'selected':'';?>>用户</option>
        <option value="2" <?php echo $_GP['payment']==2?'selected':'';?>>通用</option>
        <option value="3" <?php echo $_GP['payment']==3?'selected':'';?>>活动</option>
      </select>
    </div>
    <div class="layui-inline">
        <button class="layui-btn " lay-submit="" lay-filter="demo1">搜索</button>
        <a class="layui-btn " href="<?php echo mobile_url('shopbonus',array('op'=>'addcoupon')); ?>">添加优惠券</a>
    </div>
  </div>
  <div class="layui-form-item">
    <!--<div class="layui-inline" style="margin-right: 6px;" >
    <label class="layui-form-label">销售价格</label>
      <div class="layui-input-inline" style="width: 100px;">
        <input type="text" name="price_min" placeholder="￥" autocomplete="off" class="layui-input">
      </div>
      <div class="layui-form-mid">-</div>
      <div class="layui-input-inline" style="width: 100px;margin-right:0">
        <input type="text" name="price_max" placeholder="￥" autocomplete="off" class="layui-input">
      </div>
    </div>-->


    
  </div>

</form>
<!-- tab切换开始 -->
  <div class="layui-tab layui-tab-card">
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">
          <div class="product-table">
            <table class="layui-table">
              <thead>
                <tr>
                  <th>优惠券名称</th>
                  <th>领取方式</th>
                  <th>使用方式</th>
                  <th>优惠券金额</th>
                  <th>最小金额</th>
                  <th>发放数量</th>
                  <th>使用数量</th>
                  <th>发放时间</th>
                  <th>使用时间</th>
                  <th>操作</th>
                </tr> 
              </thead>
              <tbody>
                <?php
                  foreach($storeCouponListData['data'] as $v){
                ?>  
                <tr>
                  <td><?php echo $v['coupon_name'];?></td>
                  <td><?php echo $v['payment'];?></td>
                  <td><?php echo $v['usage_mode'];?></td>
                  <td><?php echo $v['coupon_amount'];?></td>
                  <td><?php echo $v['amount_of_condition'];?></td>
                  <td><?php echo $v['release_quantity'];?></td>
                  <td><?php echo $v['inventory'];?></td>
                  <td><?php echo $v['receive_start_time'];?>~<?php echo $v['receive_end_time'];?></td>
                  <td><?php echo $v['use_start_time'];?>~<?php echo $v['use_end_time'];?></td>
                  <td>
                      <a href="<?php echo mobile_url('shopbonus',array('op'=>'upcoupon','id'=>$v['scid'])); ?>" class="layui-btn layui-btn-small" style="text-decoration:none;">编辑</a>
                      <a href="<?php echo mobile_url('shopbonus',array('op'=>'couponmember','id'=>$v['scid'])); ?>" class="layui-btn layui-btn-small" style="text-decoration:none;">查看发放记录</a>
                      <?php
                        if($v['usage_mode'] == '单品')
                        {
                      ?>
                      <a href="<?php echo mobile_url('shopbonus',array('op'=>'grantCoupon','id'=>$v['scid'])); ?>"  class="layui-btn layui-btn-small" style="text-decoration:none;">发放</a>
                      <?php
                        }
                      ?>
                  </td>
                </tr>
              <?php
                  }
              ?>
                
              </tbody>
            </table>
          </div> 
          <div id="demo1"><!-- 分页的div -->
              <?php echo $pager;?>
          </div>
      </div>
        <!--
        <div class="layui-tab-item">
            
        </div>
        <div class="layui-tab-item">
            
        </div>
        <div class="layui-tab-item">
            
        </div>-->
    </div>
  </div>
<!-- tab切换结束 -->

<input type="hidden" name="total" id="total" value="<?php echo $total;?>">       
<script src="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/layui.js" charset="utf-8"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
layui.use(['laypage', 'layer','form','element'], function(){
  var $ = layui.jquery, form = layui.form();
  element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块
  //全选
  form.on('checkbox(allChoose)', function(data){
    var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
    child.each(function(index, item){
      item.checked = data.elem.checked;
    });
    form.render('checkbox');
  });

  var laypage = layui.laypage,layer = layui.layer;
  
  /*
  layui.use('form', function(){
    var form = layui.form();
    form.on('select(oneCategory)', function(data){
        var val = parseInt(data.value);
        var weburl = '/seller/product/parCategory.html';
        var twoCategoryHtml = '';
        $.post(weburl,{'pid':val},function(data){
            twoCategoryHtml = '<select name="twoCategory" lay-filter="twoCategory"><option value="">--请选择分类--</option>';
            for(var i in data){
                if (data.hasOwnProperty(i)) { //filter,只输出man的私有属性
                    //console.log(i,":",data[i]);
                    twoCategoryHtml = twoCategoryHtml + '<option value="'+ data[i]['id'] +'">'+ data[i]['name'] +'</option>';
                };
            }
            twoCategoryHtml = twoCategoryHtml + '</select>';
            $('#twoClass').html(twoCategoryHtml);
            form.render();
        },"json");
    });
  });
  */
});

</script>

</body>
</html>