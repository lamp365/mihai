<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('seller_header');?>
  <link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>addons/seller/css/global.css" media="all">

<body style="padding:10px;">
<!--商品搜索区域-->
<form class="layui-form layui-form-pane" action="" method="post">
  <div class="layui-form-item">
    <label class="layui-form-label">名称</label>
    <div class="layui-input-inline">
        <input type="text" name="ssa_title"  value="<?php echo $_GP['ssa_title']?>" lay-verify="title" autocomplete="off" placeholder="请输入名称" class="layui-input" value="<?php echo $_GP['title'];?>">
    </div>
    
    <label class="layui-form-label">是否置顶</label>
    <div class="layui-input-inline">
        <select name="ssa_is_require_top" lay-filter="band" >
        <option value="-1" <?php if( $_GP['ssa_is_require_top'] ==-1 || $_GP['status']==null){ echo "selected";} ?>>--查看所有--</option>
          <option value="1" <?php if($_GP['ssa_is_require_top'] ==1){ echo "selected";} ?>>--置顶--</option>
          <option value="0" <?php if($_GP['ssa_is_require_top'] ==0 && $_GP['status']!=null){ echo "selected";} ?>>--未置顶--</option>
        </select>
    </div>
    <label class="layui-form-label">开始时间</label>
    <div class="layui-input-inline">
        <input class="layui-input" name="ssa_start_time_s" value="<?php echo $_GP['ssa_start_time_s']?>" placeholder="大于" id="LAY_demorange_s">
    </div>
    <div class="layui-input-inline">
        <input class="layui-input" name="ssa_start_time_e" value="<?php echo $_GP['ssa_start_time_e']?>" placeholder="小于" id="LAY_demorange_e">
    </div>
  </div>
    <div class="layui-form-item">
         <label class="layui-form-label">结束时间</label>
        <div class="layui-input-inline">
            <input class="layui-input" name="ssa_end_time_s" value="<?php echo $_GP['ssa_end_time_s']?>"  placeholder="大于" id="ssa_end_time_s">
        </div>
        <div class="layui-input-inline">
            <input class="layui-input" name="ssa_end_time_e" value="<?php echo $_GP['ssa_end_time_e']?>"  placeholder="小于" id="ssa_end_time_e">
        </div>
        <div class="layui-input-inline">
          <button class="layui-btn" lay-submit="" lay-filter="demo1">搜索</button>
        </div>
        <div class="layui-input-inline">
            <a class="layui-btn" href="<?php echo mobile_url('store_shop_adv',array('op'=>'add'))?>">发布新活动</a>
        </div>
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
                  <th width="80">图片</th>
                  <th>名称</th>
                  <th>点击数</th>
                  <th>是否置顶</th>
                  <th>开始时间</th>
                  <th>结束时间</th>
                  <th>操作</th>
                </tr> 
              </thead>
              <tbody>
                <?php         if (is_array($result)) {  foreach($result as $v){ ?>  
                <tr>
                    <td class="product-img"><img src="<?php echo download_pic($v['ssa_thumb'],150);?>"></td>
                  <td><?php echo $v['ssa_title'];?></td>
                   <td><?php echo $v['ssa_click_count'];?></td>
                  <td>
                      <?php
                        if($v['ssa_is_require_top'] == 1 ){
                            echo "<span class='layui-btn layui-btn-small layui-btn-warm'>是</span>";
                        }else{
                            echo "<span class='layui-btn layui-btn-small layui-btn-danger'>否</span>";
                        }
                      ?>
                  </td>
                  <td>
                      <?php echo  $v['ssa_start_time']>0?  date("Y-m-d H:i:s", $v['ssa_start_time']):"" ;  ?>
                  </td>
                   <td>
                      <?php echo  $v['ssa_end_time']>0?  date("Y-m-d H:i:s", $v['ssa_end_time']):"" ;  ?>
                  </td>
                  
                  <td>
                      <!--<span class="layui-btn layui-btn-small">查看</span>-->
                      <a class="layui-btn layui-btn-small"  href="<?php echo mobile_url('store_shop_adv',array('op'=>'edit','id'=>$v['ssa_adv_id'])); ?>">编辑</a>
                      <a class="layui-btn layui-btn-small" onclick="return confirm('此操作不可恢复，确认删除？');return false;" href="<?php echo mobile_url('store_shop_adv',array('op'=>'delete','id'=>$v['ssa_adv_id'])); ?>">删除</a>
                  </td>
                </tr>
              <?php
                }}
              ?>
                
              </tbody>
            </table>
          </div> 
          <div id="demo1"><!-- 分页的div -->
              <?php echo $pager;?>
          </div>
      </div>
    </div>
  </div>
<!-- tab切换结束 -->

<input type="hidden" name="total" id="total" value="<?php echo $total;?>">       
<script src="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/layui.js" charset="utf-8"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
 
layui.use(['laypage', 'layer','form','element','laydate'], function(){
    var $ = layui.jquery, form = layui.form(), element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块
    var laypage = layui.laypage,layer = layui.layer;
    var laydate = layui.laydate;
    var start = {
//      min: laydate.now() ,
      max: '2099-06-16 23:59:59'
       ,format: 'YYYY-MM-DD hh:mm:ss' 
      ,istoday: false
      ,choose: function(datas){
        end.min = datas; //开始日选好后，重置结束日的最小日期
        end.start = datas; //将结束日的初始值设定为开始日
      }
    };
    
    var end = {
//      min: laydate.now(),
      max: '2099-06-16 23:59:59'
           ,format: 'YYYY-MM-DD hh:mm:ss' 
      ,istoday: false
      ,choose: function(datas){
        start.max = datas; //结束日选好后，重置开始日的最大日期
      }
    };
    
  document.getElementById('LAY_demorange_s').onclick = function(){
    start.elem = this;
    laydate(start);
  }
   document.getElementById('ssa_end_time_s').onclick = function(){
    start.elem = this;
    laydate(start);
  }
  document.getElementById('LAY_demorange_e').onclick = function(){
    end.elem = this;
    laydate(end);
  }
  document.getElementById('ssa_end_time_e').onclick = function(){
    end.elem = this;
    laydate(end);
  }
  //全选
  form.on('checkbox(allChoose)', function(data){
    var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
    child.each(function(index, item){
      item.checked = data.elem.checked;
    });
    form.render('checkbox');
  });

});

</script>

</body>
</html>