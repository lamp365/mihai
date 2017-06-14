<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('seller_header');?>
  <link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>addons/seller/css/global.css" media="all">

<body style="padding:10px;">
<!--商品搜索区域-->
<form class="layui-form layui-form-pane" action="<?php echo mobile_url('shopbonus',array('op'=>'couponmember','id'=>$v['scid'])); ?>" method="post">
  <div class="layui-form-item">
      
  </div>
  <div class="layui-form-item">
    <div class="layui-inline" style="margin-right: 6px;" >
    <label class="layui-form-label">选择状态</label>
      <div class="layui-input-inline" style="width: 100px;">
          <select name="search_status" id="search_status">
              <option value="0" >未使用</option>
              <option value="1" <?php echo $_GP['search_status']==1?'selected':'';?>>已使用</option>
          </select>
      </div>
    </div>

    <div class="layui-inline">
        <button class="layui-btn layui-btn-small" lay-submit="" lay-filter="demo1">搜索</button>
        <a class="layui-btn layui-btn-small" href="<?php echo mobile_url('shopbonus',array('op'=>'index')); ?>"  style="text-decoration:none;">返回优惠券列表</a>
    </div>
  </div>


<input type="hidden" name="searchForm" id="searchForm" value="1">   
</form>
<!-- tab切换开始 -->
  <div class="layui-tab layui-tab-card">
    <div class="layui-tab-content">
      <div class="layui-tab-item layui-show">
          <div class="product-table">
            <table class="layui-table">
              <thead>
                <tr>
                  <th>用户昵称</th>
                  <th>手机号码</th>
                  <th>领取时间</th>
                  <th>使用时间</th>
                  <th>订单号</th>
                  <th>订单金额</th>
                  <th>订单时间</th>
                  <?php
                      if($oneCoupon['usage_mode'] == 3)
                      {
                    ?>
                  <th>宝贝名称</th>
                  <?php
                      }
                    ?>
                  <th>优惠券状态</th>
                </tr> 
              </thead>
              <tbody>
                <?php
                  foreach($couponMemberListData['data'] as $v){
                ?>  
                <tr>
                  <td><?php echo $v['nickname'];?></td>
                  <td><?php echo $v['mobile'];?></td>
                  <td><?php echo $v['receive_time'];?></td>
                  <td><?php echo $v['use_time'];?></td>
                  <td><?php echo $v['order_number'];?></td>
                  <td><?php echo $v['order_money'];?></td>
                  <td><?php echo $v['order_time'];?></td>
                    <?php
                      if($oneCoupon['usage_mode'] == 3)
                      {
                    ?>
                        <td><?php echo $v['dish_name'];?></td>
                    <?php
                      }
                    ?>
                  <td><?php echo $v['status'];?></td>
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