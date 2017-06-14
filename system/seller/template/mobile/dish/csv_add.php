<!DOCTYPE html>
<html>
<head>
  <?php include page('seller_header');?>

</head>
<body style="padding:10px;">
<!-- <div class="layui-form">
    <blockquote class="layui-elem-quote">批量导入 <span></span></blockquote>
    <ul class="nav nav-tabs" >
        <li style="width:7%" <?php  if( empty($_GP['op']) || $_GP['op'] == 'index' ) { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('product_bat')?>">csv导入</a></li>
        <li style="width:7%" <?php  if($_GP['op'] == 'bat_add') { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('product_bat',  array('op' => 'bat_add'))?>">产品库导入</a></li>
    </ul>

    <div>
        <form class="layui-form" action="" method="" name="">
            <blockquote style="margin-top:25px;" class="layui-elem-quote">请上传csv格式的文件</blockquote>
            <div class="layui-form-item">
                <label class="layui-form-label">下载样板</label>
                <div class="layui-input-block">
                    <div class="layui-btn layui-btn-normal layui-btn-small">点击下载</div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">上传文件</label>
                <div class="layui-input-block">
                    <input type="file">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">店铺分类</label>
                <div class="layui-input-inline">
                    <select lay-filter="oneCategory">
                        <option value="">--请选择分类--</option>
                        <option>2</option>
                        <option>3</option>
                    </select>
                </div>
                <div id="twoClass" class="layui-input-inline">
                    <select>
                        <option value="">--请选择分类--</option>
                        <option>2</option>
                        <option>3</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">&nbsp;</label>
                <div class="layui-input-block">
                    <div class="layui-btn layui-btn-normal layui-btn-small">提 交</div>
                </div>
            </div>
        </form>
    </div>
</div> -->

<div class="layui-form">
    <blockquote class="layui-elem-quote">批量操作</blockquote>
    <ul class="nav nav-tabs" >
        <li style="width:7%" <?php  if( empty($_GP['op']) || $_GP['op'] == 'index' ) { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('product_bat')?>">分类导入</a></li>
        <li style="width:7%" <?php  if($_GP['op'] == 'bat_add') { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('product_bat',  array('op' => 'bat_add'))?>">快速导入</a></li>
    </ul>

    <div>
        <form class="layui-form" action="" method="" name="">
            <div class="layui-form">
              <table class="layui-table">
                <!-- col根据需要设置tr对应的宽度 -->
                <colgroup>
                  <col width="50">
                  <col>
                  <col>
                </colgroup>
                <thead>
                  <tr>
                    <th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
                    <th>分类名称</th>
                    <th>商品数量</th>
                  </tr> 
                </thead>
                <tbody>
                    <?php
                      foreach($reData['systemGroup']['oneCategory'] as $v){
                    ?>
                  <tr>
                      <td><input type="checkbox" name="" class="category" lay-skin="primary" value="<?php echo $v['id'];?>"></td>
                    <td><?php echo $v['cat_name'];?></td>
                    <td><?php echo $v['dishtotal'];?></td>
                  </tr>
                  <?php
                      }
                  ?>
                </tbody>
              </table>
            </div>
            <button class="layui-btn layui-btn-normal" id="sub">确认导入</button>
        </form>
    </div>
</div>
<input type="hidden" value="<?php echo $brand['brand'];?>" id="brand_name" name="brand_name" class="layui-input">
<?php include page('seller_footer');?>
<script>
    layui.use("form",function(){
        //全选
        var $ = layui.jquery, form = layui.form();
        form.on('checkbox(allChoose)', function(data){
            var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
            child.each(function(index, item){
                item.checked = data.elem.checked;
            });
            form.render('checkbox');
        });
        
        $('#sub').on('click',function(){
            var ids = '';
            $(".category:checked").each(function(){
               ids +=  $(this).val()+',';
            });
            var url = "<?php echo mobile_url('product_bat',array('op'=>'category_bat')); ?>";
            var url_now = "<?php echo mobile_url('product_bat',array('op'=>'index')); ?>";
            layer.load(3);
            $.post(url,{ids:ids},function(data){
                location.href = url_now;
                layer.closeAll('loading');
            },"json");
            return false;
        });
        
        /*分类联动*/
      /*  form.on('select(oneCategory)', function(data){
            var val = parseInt(data.value);
            var weburl = '';
            var twoCategoryHtml = '';
            $.post(weburl,{val:val},function(data){

                $('#twoClass').html(twoCategoryHtml);
                //render重新渲染layui
                form.render();
            },"json");
        });*/
    })
</script>
</body>
</html>