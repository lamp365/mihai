<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('seller_header');?>
<html>
<head>
<style type="text/css">
  .row{margin:0}
  .addSpecItem{
    margin-left: 10px;
  }
  .modal-span-01 .layui-btn-small{
    float: left;
  }
  .layui-form-label{
    width: auto;
    min-width: 110px;
  }
  .addspecinput{
    margin-left: 10px;border:1px solid #e2e2e2;padding:4px 0;border-radius:3px;display: inline-block;
  }
</style>
</head>
<body style="padding:10px;box-sizing: border-box;">
<div class="layui-tab layui-tab-card" lay-filter="mark">
  <ul class="layui-tab-title">
    <li class="layui-this" lay-id="one_tab">个人模型库</li>
    <li lay-id="two_tab">系统模型库</li>
  </ul>
  <div class="layui-tab-content">
    <div class="layui-tab-item layui-show">
      <form action="" method="post" class="layui-form">
          <input type='hidden' value="<?php if(empty($self_ku)){ echo 2;}else{ echo 1;}?>" name='localspectype' id='localspectype'><!--模型类型 1表示个人模型库 2表示系统模型库-->
          <input type='hidden' value="<?php if(empty($groupArr['selfgroup'])){  echo 2; }else{ echo 1;} ?>" name='spectype' id='spectype'><!--个人模型库 1选择模型 2添加模型-->
          
        <div>
            <!-- 填写规格和价格 -->
            <?php if(empty($groupArr['selfgroup'])){  ?>
            <blockquote class="layui-elem-quote">选择模型<div class="layui-btn layui-btn-small addmodal isadd" style="margin-left:10px;">选择模型</div></blockquote>
            <input type="hidden" class="has_gtype" value="0">
            <?php }else{  ?>
            <blockquote class="layui-elem-quote">选择模型<div class="layui-btn layui-btn-small addmodal" style="margin-left:10px;">添加模型</div></blockquote>
            <input type="hidden" class="has_gtype" value="1">
            <?php } ?>
            <div class="spec_list_0" style="display: <?php if(!empty($groupArr['selfgroup'])){ echo 'none';} ?>">
              <div class="spec_list_parent" id='addspecdiv'>
                <div class="layui-form-item">
                  <div class="layui-form-item layui-form">
                      <label class="layui-form-label">选择分组</label>
                      <div class="layui-input-inline">
                          <select name="interest" id='group_id_add' lay-filter="group_id_add">
                          <?php
                               if(empty($groupArr['selfgroup'])){
                                    echo "<option value='0'>默认分组</option>";
                               }else{
                                   foreach($groupArr['selfgroup'] as $v1) {
                                       $sel = '';
                                       if ($v1['group_id'] == $group_id) {
                                           $sel = 'selected';
                                       }
                                       echo "<option value='{$v1['group_id']}'  {$sel}>{$v1['group_name']}</option>";
                                   }
                               }

                          ?>

                          </select>
                      </div>
                  </div>
                    
                    
                    
                    <label class="layui-form-label">商品规格</label>
                    <div class="layui-input-inline">
                        <input type="text" class="layui-input add-spec-name" name="spec_name" placeholder="添加商品规格名称" >
                    </div>
                    <div class="layui-input-inline">
                        <div class="layui-btn" onclick="addSpecName(this,0)">添加</div>
                    </div>
                </div>
              </div>
              <div class="ajax_spec_data_area0"></div>
            </div>
            <div class="spec_list_4 the_box" style="display: <?php if(empty($groupArr['selfgroup'])){ echo 'none';} ?>">
              <div class="layui-form-item layui-form">
                  <label class="layui-form-label">选择分组</label>
                  <div class="layui-input-inline">
                      <select name="interest" id='group_id' lay-filter="group_id">
                          <?php
                          if(empty($groupArr['selfgroup'])){
                              echo "<option value='0' >选择分组</option>";
                          }else{
                            foreach($groupArr['selfgroup'] as $v2) {
                              $sel = '';
                              if ($v2['group_id'] == $group_id) {
                                  $sel = 'selected';
                              }
                              echo "<option value='{$v2['group_id']}'  {$sel}>{$v2['group_name']}</option>";
                            }
                          }
                          
                          ?>
                      </select>
                  </div>
                  <label class="layui-form-label">选择模型2</label>
                  <div class="layui-input-inline">
                      <select name="interest" id="gtype_type_id" lay-filter="gtype_type_id">
                          <?php
                              if($self_ku) {
                                  if (empty($gtype_info)) {
                                      echo "<option value='0'>选择模型2</option>";
                                  } else {
                                      foreach ($gtype_info as $item_gtype) {
                                          $sel = '';
                                          if($_GP['gtype_id'] == $item_gtype['id']){
                                              $sel = "selected";
                                          }
                                          echo "<option value='{$item_gtype['id']}' {$sel}>{$item_gtype['name']}</option>";
                                      }
                                  }
                              }else{
                                  echo "<option value='0'>选择模型2</option>";
                              }
                          ?>
                      </select>
                  </div>
              </div>

              <!-- 商品规格 -->
              <blockquote class="layui-elem-quote">
                  <span>商品规格</span>
              </blockquote>
                <div class="spec_list_parent">
                    <?php if($self_ku){  ?>
                    <?php foreach($speclist_data as $one_spec){ ?>
                    <div class="layui-form-item spec_list">
                        <label class="layui-form-label">
                            <span class="new_spec_name" data-spec_id="<?php echo $one_spec['spec_id']; ?>"><?php echo $one_spec['spec_name']; ?></span>
                        </label>
                        <div class="layui-input-block">
                                <span class="modal-span-01">
                                  <?php  foreach($one_spec['child_item'] as $one_item){  ?>
                                  <?php if($one_item['ischoose']){ $_class='btn-default';}else{ $_class='btn-default';} ?>
                                  <span class="new-item-span layui-btn layui-btn-small <?php echo $_class; ?>" onclick="itemState(this,4)" >
                                    <span class="specs-val" data-item_id="<?php echo $one_item['id']; ?>"><?php echo $one_item['item_name']; ?></span>
                                    <i class="layui-icon spec-remove" onclick="itemRemove(this,4)"></i>
                                  </span>
                                 <?php } ?>
                                </span>
                            <div class="layui-btn layui-btn-warm layui-btn-small addSpecItem" lay-submit="" addtype="1" onclick="addSpecItem(this,4)">添加规格项</div>
                        </div>
                    </div>
                    <?php } } ?>
                </div>

               <div class="ajax_spec_data_area4">
               </div>
            </div>

        </div>
        <div class="layui-form-item">
            <button class="layui-btn" style="float:right;margin-right:100px;" lay-submit="" lay-filter="spec-sub"  id="localsumbmit">提交</button>
        </div>
    </form>
    </div>
    <div class="layui-tab-item">
      <blockquote class="layui-elem-quote">选择模型</blockquote>
        <div class="spec_list_3 the_box">
          <div class="layui-form-item layui-form">
              <label class="layui-form-label">选择分组</label>
              <div class="layui-input-inline">
                  <select  lay-filter="pingtai_group_id" id="pingtai_group_id">
                      <?php
                      if(empty($groupArr['pingtaigroup'])){
                          echo "<option value='0'>选择分组</option>";
                      }else{
                        foreach($groupArr['pingtaigroup'] as $v3) {
                            $sel = '';
                            if ($v3['group_id'] == $group_id) {
                                $sel = 'selected';
                            }
                            echo "<option value='{$v3['group_id']}'  {$sel}>{$v3['group_name']}</option>";
                        }
                      }
                      
                      ?>
                  </select>
              </div>
              <label class="layui-form-label">选择模型</label>
              <div class="layui-input-inline">
                  <select name="interest" id="pingtai_gtype_id" lay-filter="pingtai_gtype_id">
                        <?php
                        if(empty($self_ku)) {
                            if (empty($gtype_info)) {
                                echo "<option value='0'>选择模型</option>";
                            } else {
                                foreach ($gtype_info as $item_gtype) {
                                    $sel = '';
                                    if($_GP['gtype_id'] == $item_gtype['id']){
                                        $sel = "selected";
                                    }
                                    echo "<option value='{$item_gtype['id']}' {$sel}>{$item_gtype['name']}</option>";
                                }
                            }
                        }else{
                            echo "<option value='0'>选择模型</option>";
                        }
                        ?>
                  </select>
              </div>
          </div>

          <!-- 商品规格 -->
          <blockquote class="layui-elem-quote">商品规格
              <span>&nbsp;&nbsp;</span>
          </blockquote>
          <div class="spec_list_parent">
              <?php if(empty($self_ku)){   ?>
              <?php foreach($speclist_data as $one_spec){ ?>
            <div class="layui-form-item spec_list">
                <label class="layui-form-label"><b data-spec_id="<?php echo $one_spec['spec_id']; ?>" class="new_spec_name"><?php echo $one_spec['spec_name']; ?></b></label>
                <div class="layui-input-block">
                    <span class="modal-span-01">
                         <?php  foreach($one_spec['child_item'] as $one_item){  ?>
                        <span title="点击禁用"  class="layui-btn layui-btn-small btn-default" onclick="itemState(this,3)">
                            <span class="specs-val" data-item_id="<?php echo $one_item['id']; ?>"><?php echo $one_item['item_name']; ?></span>
                        </span>
                         <?php } ?>
                    </span>
                </div>
            </div>
            <?php }} ?>
          </div>
          <div class="ajax_spec_data_area3"></div>
        </div>
        <div class="layui-form-item">
            <button class="layui-btn" style="float:right;margin-right:100px;" lay-submit="" lay-filter="spec-sub2" id="localsumbmits">提交</button>
        </div>
    </form>
    </div>

  </div>
</div>
    
<script>
<?php if($self_ku){ ?>
var the_layui = 'one_tab';
<?php }else{ ?>
var the_layui = 'two_tab';
<?php } ?>
layui.use(['form','element','layer'], function() {
    var form = layui.form();
    var layer = layui.layer;
    var $ = layui.jquery;
    var element = layui.element();
    element.tabChange('mark', the_layui);
    //下拉联动实例
    form.on('select(group_id)', function(data){
        var group_id = $("#group_id").val();
        getgtypeBygroupId(group_id,"#gtype_type_id");
    });
    form.on('select(pingtai_group_id)', function(data){
        var group_id = $("#pingtai_group_id").val();
        getgtypeBygroupId(group_id,"#pingtai_gtype_id");
    });
    form.on('select(gtype_type_id)', function(data){
        var gtype_id = $("#gtype_type_id").val();
        var url = "<?php echo mobile_url('product',array('op'=>'choose_spec')); ?>";
        var dish_id = "<?php echo $_GP['dish_id'];?>";
        url = url + "?gtype_id="+gtype_id;
        if(dish_id != '' && dish_id != null){
            url = url + "&dish_id="+dish_id;
        }
        window.location.href = url;
    });
    form.on('select(pingtai_gtype_id)', function(data){
        var gtype_id = $("#pingtai_gtype_id").val();
        var url = "<?php echo mobile_url('product',array('op'=>'choose_spec')); ?>";
        var dish_id = "<?php echo $_GP['dish_id'];?>";
        url = url + "?gtype_id="+gtype_id;
        if(dish_id != '' && dish_id != null){
            url = url + "&dish_id="+dish_id;
        }
        window.location.href = url;
    });
});

$(function(){
    $(window.parent.document).find(".layui-layer-iframe").addClass("layui-layer1");
    $(".addmodal").on("click",function(){
      var has_gtype = $(".has_gtype").val();
      if(!$(this).hasClass("isadd")){
            $(this).addClass("isadd");
            $(this).text("选择模型");
            $(".spec_list_4").hide();
            $(".spec_list_0").show();
            $(".ajax_spec_data_area0").show();
            $('#spectype').val(2);
      }else{
          if(has_gtype == 0){
              layer.alert("暂无可选的模型");
              return false;
          }
            $(this).removeClass("isadd");
            $(this).text("添加模型");
            $(".spec_list_4").show();
            $(".spec_list_0").hide();
            $(".ajax_spec_data_area4").show();
            $('#spectype').val(1);
      }
    })
    
    $('#localsumbmits').on('click',function(){
        var spec_name = [];
        var spec_name_arr = {};
        var spectypeval = parseInt($('#spectype').val());
        var specJson = {};
        var itemJson0= {};
        var itemJson1= {};
        var obj_block = "";
        var has_choose_item = false;
        var spec_name_item = "";
        var spec_name_id = "";
        var gtype_id = "<?php echo $_GP['gtype_id'];?>";

        obj_block = ".spec_list_3";

        $(obj_block).find('.spec_list').each(function(index, element) {
            spec_name.push($(this).find('.new_spec_name').text()); //规格名称
            // {"标准配置":3,"显示器":18}
            spec_name_arr[$(this).find('.new_spec_name').text()] = $(this).find('.new_spec_name').data('spec_id');

            if( index == 0 ){
                //找到已经点击选中的 规格项
                $(this).find('.modal-span-01').find('.btn-success').find('.specs-val').each(function(index, element) {
                    has_choose_item = true;
                    spec_name_item = $(this).text();
                    spec_name_id   = $(this).data('item_id');
                    //{1G:2,6G:14,8G:0};
                    itemJson0[spec_name_item] = spec_name_id; 
                });
                //{'内存':{1G:2,6G:14,8G:0}};
            }else if( index == 1 ){
                //找到已经点击选中的 规格项
                $(this).find('.modal-span-01').find('.btn-success').find('.specs-val').each(function(index, element) {
                    has_choose_item = true;
                    spec_name_item = $(this).text();
                    spec_name_id   = $(this).data('item_id');
                    //{1G:2,6G:14,8G:0};
                    itemJson1[spec_name_item] = spec_name_id;
                });
                //{'内存':{1G:2,6G:14,8G:0}};
            }
        });
        if( !$.isEmptyObject(itemJson0) ){
          specJson[spec_name[0]] = itemJson0;
        }
        if( !$.isEmptyObject(itemJson1) ){
          specJson[spec_name[1]] = itemJson1;
        }

        if(specJson.length <= 0){
            layer.alert('请添加规格！');
            return false;
        }
        if(!has_choose_item){
            //如果规格项一个都没有选
            layer.alert('请选择对应的规格项');
            return false;
        }
        var isok = true;
        $(".itemtbody").find(".specitem_put").each(function(){
           if($(this).val() == ''){
               isok = false;
           }
        });
        if(!isok){
            layer.alert('请完善规格项对应的值！');
            return false;
        }
        
        var url = "<?php  echo mobile_url('product',array('op'=>'addGoodsSpec'));?>";
        var parame = {
            'spec_and_item':specJson,
            'group_id':$('#group_id_add').val(),
            'gtype_id':gtype_id,
            'spec_name_arr':spec_name_arr
        };
        layer.load(3); //加载
        $.post(url,parame,function(data){
             if(data.errno == 0){
                 layer.alert(data.message);
             }else{
                 //将新创建的 规格 对应到 相应的价格项匹配
                 packageRelativePrice(data.data);
             }
            parent.layer.closeAll();
        },'json');  

        return false;
        
    })
    
    $('#localsumbmit').on('click',function(){
        var spec_name = [];
        var spec_name_arr = {};
        var spectypeval = parseInt($('#spectype').val());
        var specJson = {};
        var itemJson0= {};
        var itemJson1= {};
        var obj_block = "";
        var has_choose_item = false;
        var spec_name_item = "";
        var spec_name_id = "";
        var gtype_id = "<?php echo $_GP['gtype_id'];?>";
        if(spectypeval == 2){
            gtype_id  = 0;  //当类型是2说明是新添加的规格
            obj_block = ".spec_list_0";
        }else{
            obj_block = ".spec_list_4";
        }
        $(obj_block).find('.spec_list').each(function(index, element) {
            spec_name.push($(this).find('.new_spec_name').text()); //规格名称
            // {"标准配置":3,"显示器":18}
            spec_name_arr[$(this).find('.new_spec_name').text()] = $(this).find('.new_spec_name').data('spec_id');

            if( index == 0 ){
                //找到已经点击选中的 规格项
                $(this).find('.modal-span-01').find('.btn-success').find('.specs-val').each(function(index, element) {
                    has_choose_item = true;
                    spec_name_item = $(this).text();
                    spec_name_id   = $(this).data('item_id');
                    //{1G:2,6G:14,8G:0};
                    itemJson0[spec_name_item] = spec_name_id; 
                });
                //{'内存':{1G:2,6G:14,8G:0}};
            }else if( index == 1 ){
                //找到已经点击选中的 规格项
                $(this).find('.modal-span-01').find('.btn-success').find('.specs-val').each(function(index, element) {
                    has_choose_item = true;
                    spec_name_item = $(this).text();
                    spec_name_id   = $(this).data('item_id');
                    //{1G:2,6G:14,8G:0};
                    itemJson1[spec_name_item] = spec_name_id;
                });
                //{'内存':{1G:2,6G:14,8G:0}};
            }
        });
        if( !$.isEmptyObject(itemJson0) ){
          specJson[spec_name[0]] = itemJson0;
        }
        if( !$.isEmptyObject(itemJson1) ){
          specJson[spec_name[1]] = itemJson1;
        }

        if(specJson.length <= 0){
            layer.alert('请添加规格！');
            return false;
        }
        if(!has_choose_item){
            //如果规格项一个都没有选
            layer.alert('请选择对应的规格项');
            return false;
        }
        var isok = true;
        $(".itemtbody").find(".specitem_put").each(function(){
           if($(this).val() == ''){
               isok = false;
           }
        });
        if(!isok){
            layer.alert('请完善规格项对应的值！');
            return false;
        }

        var url = "<?php  echo mobile_url('product',array('op'=>'addGoodsSpec'));?>";
        var parame = {
            'spec_and_item':specJson,
            'group_id':$('#group_id_add').val(),
            'gtype_id':gtype_id,
            'spec_name_arr':spec_name_arr
        };
        layer.load(3); //加载
        $.post(url,parame,function(data){
             if(data.errno == 0){
                 layer.alert(data.message);
             }else{
                 //将新创建的 规格 对应到 相应的价格项匹配
                 packageRelativePrice(data.data);
             }
            parent.layer.closeAll();
        },'json');


        return false;
    });  // locksubmit


    //初始化 模型的下拉框数据
    init_gtype_select();

    //当点击父dom点击选择规格的时候  抓取页面缓存的规格信息 进行画出表格
    init_spec_price_table();

});

//将新创建的 规格 对应到 相应的价格项匹配
//规格价格部分组织
function packageRelativePrice(data){
    var gtype_id   = data.gtype_id;
    var spec_info  = data.spec_info;
    var gtype_name = data.gtype_name;

    var itemPriceJson = [];
    var itemOption    = {};
    var item_name_str = '';
    var item_name_array1 = [];
    var item_name_array2 = [];
    var item_name_obj ={};
    var k2 = [];
    var i = 0;
    $('.itemtbody >tr').each(function(index, element) {
        if(index > 0) {
            var itemStr   = {};
            var itemId    = '';
            var optionIndex = 0;
            $(this).find('td').each(function(i, element) {

                if($(this).find(".specitem_put").length >0 ){
                    itemStr[$(this).find(".specitem_put").data('field')] = $(this).find(".specitem_put").val();
                }else{

                    var the_item_name = $(this).html();

                    var curt_spec     = itemOption[optionIndex];  //当前遍历的规格名字

                    itemId  = itemId + spec_info[curt_spec][the_item_name] + '_';
                    
                    itemStr[curt_spec] = the_item_name;

                    if($.inArray(curt_spec,k2)==-1){
                        k2.push(curt_spec);
                    }
                    if( curt_spec == k2[0] ){
                        item_name_array1.push(the_item_name);
                        item_name_obj[curt_spec] = item_name_array1;
                    }else if( curt_spec == k2[1] ){
                        item_name_array2.push(the_item_name);
                        item_name_obj[curt_spec] = item_name_array2;
                    }

                    //取用特殊的双@防止 规格项名称带有逗号 或者一个@ 但是双@的比较少
                    item_name_str = item_name_str + the_item_name + '@@';
                    
                }

                optionIndex = optionIndex + 1;
            });

            itemStr['spec_key'] = itemId.substr(0,itemId.length - 1);
            
            itemPriceJson.push(itemStr);

        } else {
            var optionIndex = 0;
            $(this).find('td').each(function(index, element) {
                itemOption[optionIndex] = $(this).html();
                optionIndex = optionIndex + 1;
            });
        }
    });

    itemPrice_str = JSON.stringify(itemPriceJson);
    //保存对应的 规格价格数据
    $(window.parent.document).find("#itemPriceJson").val(itemPrice_str);
    //保存对应的 模型id
    $(window.parent.document).find("#gtype_id").val(gtype_id);
    //保存对应的 模型名
    $(window.parent.document).find("#gtype_name").html(gtype_name);
    //保存对应的 规格具体的项如 1G  2G
    item_name_obj = JSON.stringify(item_name_obj);

    $(window.parent.document).find("#item_value_str").val(item_name_obj);
    //关闭当前弹框
    //parent.layer.closeAll('iframe');
}

/**
 * 初始化 模型的下拉框数据
 */
function init_gtype_select(){
    var gtype_type_id    = $("#gtype_type_id").find("option:first").val();
    var group_id         = $("#group_id").find("option:first").val();
    var pingtai_gtype_id = $("#pingtai_gtype_id").find("option:first").val();
    var pingtai_group_id = $("#pingtai_group_id").find("option:first").val();

    if(gtype_type_id == 0 && group_id !=0 ){
        getgtypeBygroupId(group_id,"#gtype_type_id");
    }
    if(pingtai_gtype_id == 0 && pingtai_group_id !=0 ){
        getgtypeBygroupId(pingtai_group_id,"#pingtai_gtype_id");
    }

}
//根据组id获取对应的模型
function getgtypeBygroupId(group_id,obj) {
    layui.use('form', function() {
        var form = layui.form();
        var url = "<?php echo mobile_url('product',array('op'=>'ajaxGtypeBygroupid')) ?>";
        $.post(url,{group_id:group_id},function(data){
            var html = '<option value="0">选择模型</option>';
            if(data.errno == 1){
                var optionObj = data.data;
                for(var i=0;i<optionObj.length;i++){
                    var this_data = optionObj[i];
                    html = html +"<option value='"+this_data.id+"'>"+this_data.name+"</option>";
                }
                $(obj).html(html);
                form.render();//重新渲染layui框架
                $(obj).closest(".the_box").find(".spec_list_parent").find(".spec_list").remove();
            }else{
                $(obj).html(html);
                form.render();//重新渲染layui框架
                $(obj).closest(".the_box").find(".spec_list_parent").find(".spec_list").remove();
                layer.alert(data.message);
            }
        },'json');
    });
}

//当点击父dom点击选择规格的时候  抓取页面缓存的规格信息 进行画出表格
function init_spec_price_table(){
    var gtype_id       = $(window.parent.document).find("#gtype_id").val();
    var get_gtyep_id   = "<?php echo $_GP['gtype_id'] ?>";
    var table_html = "";
    var tr_html_1 = "";
    var tr_html_2 = "";
    var tr_html_3 = "";
    var tr_html_4 = "";

    if(get_gtyep_id != gtype_id){
        //如果当前编辑的规格不是 之前的那个 则不操作
        return '';
    }
    var item_value_str = $(window.parent.document).find("#item_value_str").val();
    var itemPriceJson  = $(window.parent.document).find("#itemPriceJson").val();
    if(itemPriceJson == '' || item_value_str == ''){
        return '';
    }else{
        //转成JSON对象
        var item_value_str_obj = JSON.parse(item_value_str);
        var itemPriceJsonObj = JSON.parse(itemPriceJson);
        var new_spec_name_val = "";
        var item_value_check_array = [];
        var k1 = "";
        var k2 = "";
        var ishidden = "";
        //个人模型库 对应 spec_list_4
        if( the_layui == "one_tab" ){
            //遍历规格名称
            $(".spec_list_4 .spec_list .new_spec_name").each(function(index,elem){
                new_spec_name_val = $(elem).text();

                tr_html_1 += '<td>'+new_spec_name_val+'</td>';
                /*查找规格对应的数据*/
                if( index == 0 ){
                    /*第一个规格名*/
                    k1 = new_spec_name_val;
                }else if( index == 1 ){
                    /*第二个规格名*/
                    k2 = new_spec_name_val;
                }
                item_value_check_array = item_value_str_obj[new_spec_name_val];
                /*遍历具体规格值*/
                $(elem).parents(".spec_list").find(".specs-val").each(function(index2,elem2){
                    var specs_val = $(elem2).text();
                    /*判断规格是否在数组中，在数组中就加上btn-success选中*/
                    var flag = $.inArray(specs_val,item_value_check_array);
                    if( flag != -1 ){
                        //$(elem2).parent("span").trigger("click");
                        $(elem2).parent("span").addClass("btn-success").removeClass("btn-default");
                    }
                });
            });

            
        }else if( the_layui == "two_tab" ){
          //系统模型库 对应 spec_list_3
            $(".spec_list_3 .spec_list .new_spec_name").each(function(index,elem){
                
                new_spec_name_val = $(elem).text();
                tr_html_1 += '<td>'+new_spec_name_val+'</td>';

                /*查找规格对应的数据*/
                if( index == 0 ){
                    /*第一个规格名*/
                    k1 = new_spec_name_val;
                }else if( index == 1 ){
                    /*第二个规格名*/
                    k2 = new_spec_name_val;
                }
                item_value_check_array = item_value_str_obj[new_spec_name_val];
                /*遍历具体规格值*/
                $(elem).parents(".spec_list").find(".specs-val").each(function(index2,elem2){
                    var specs_val = $(elem2).text();
                    /*判断规格是否在数组中，在数组中就加上btn-success选中*/
                    var flag = $.inArray(specs_val,item_value_check_array);
                    if( flag != -1 ){
                        //$(elem2).parent("span").trigger("click");
                        $(elem2).parent("span").addClass("btn-success").removeClass("btn-default");
                    }
                });
            });

        }
        
        for( var i = 0 ; i < itemPriceJsonObj.length; i++ ){
                if( k2 != "" ){
                    //第二项不选
                    if(itemPriceJsonObj[i][k2] == undefined){
                        var ishidden = 1;
                        tr_html_4 += '<tr><td>'+itemPriceJsonObj[i][k1]+'</td>'+
                        '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].productprice+'" data-field="productprice"></td>'+
                        '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].marketprice+'" data-field="marketprice"></td>'+
                        '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].store_count+'" data-field="store_count"></td>'+
                        '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].bar_code+'" data-field="bar_code"></td></tr>';
                    }
                    else if(itemPriceJsonObj[i][k1] == undefined){
                        var ishidden = 2;
                        //第一项不选
                        tr_html_4 += '<tr><td>'+itemPriceJsonObj[i][k2]+'</td>'+
                        '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].productprice+'" data-field="productprice"></td>'+
                        '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].marketprice+'" data-field="marketprice"></td>'+
                        '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].store_count+'" data-field="store_count"></td>'+
                        '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].bar_code+'" data-field="bar_code"></td></tr>';
                    }else{
                        var ishidden = 3;
                        //两项齐全
                        tr_html_4 += '<tr><td>'+itemPriceJsonObj[i][k1]+'</td><td>'+itemPriceJsonObj[i][k2]+'</td>'+
                        '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].productprice+'" data-field="productprice"></td>'+
                        '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].marketprice+'" data-field="marketprice"></td>'+
                        '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].store_count+'" data-field="store_count"></td>'+
                        '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].bar_code+'" data-field="bar_code"></td></tr>';
                    }
                }else{
                    tr_html_4 += '<tr><td>'+itemPriceJsonObj[i][k1]+'</td>'+
                    '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].productprice+'" data-field="productprice"></td>'+
                    '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].marketprice+'" data-field="marketprice"></td>'+
                    '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].store_count+'" data-field="store_count"></td>'+
                    '<td><input type="number" name="" class="form-control specitem_put" value="'+itemPriceJsonObj[i].bar_code+'" data-field="bar_code"></td></tr>';
                }
            }
            table_html = '<div class="col-xs-12" id="col-xs-12-two">'+
                    '<div>'+
                        '<table class="table table-bordered">'+
                            '<tbody class="itemtbody">'+
                                '<tr style="font-weight: bolder"> '+tr_html_1+
                                    '<td>市场价</td>'+
                                    '<td>促销价</td>'+
                                    '<td>库存</td>'+
                                    '<td>条形码</td>'+
                                '</tr>'+tr_html_4+
                            '</tbody>'+
                        '</table>'+
                     '</div>'+
                '</div>';
                
            if( the_layui == "two_tab" ){
              $(".ajax_spec_data_area3").html(table_html);
            }else if( the_layui == "one_tab" ){
              $(".ajax_spec_data_area4").html(table_html);
            }
            
            if(ishidden == 1)
            {
                $(".itemtbody tr").eq(0).find("td").eq(1).remove();
            }
            else if(ishidden == 2){
                $(".itemtbody tr").eq(0).find("td").eq(0).remove();
            }
            
    }

    /**
     [
        {"内存":"8G","硬盘":"8G","productprice":"4123","marketprice":"12323","store_count":"23","bar_code":"123","spec_key":"19_32"},
        {"内存":"8G","硬盘":"32G","productprice":"234","marketprice":"234","store_count":"1231","bar_code":"23","spec_key":"20_32"}
     ]
     **/
/*    itemPriceJson  = JSON.parse(itemPriceJson);
    //{"内存":["8G","8G"],"硬盘":["8G","32G"]}
    item_value_str = JSON.parse(item_value_str);
    //开始让 规格项 默认被选中  以及规格价格还原*/
}

function addSpec(obj){
    if($('.spec_list').length < 2){
        $("#addSpecModal").modal('show');
    }
}

//添加商品规格
function addSpecName(obj,type){
  var spec_name = $(".add-spec-name").val();
  spec_name = $.trim(spec_name);
  var theflag = hasExist(".new_spec_name",spec_name);
  if($('#onespec').length > 0)
  {
      var divspec = '<div id="twospec" class="spec">';
  }
  else
  {
      var divspec = '<div id="onespec" class="spec">';
  }
  var html = divspec +'<div class="layui-form-item spec_list">'+
           '<label class="layui-form-label" style="padding:0;padding-right:10px;"><div class="layui-btn layui-btn-small layui-btn-primary"><span data-spec_id="0" class="new_spec_name">'+spec_name+'</span><i class="layui-icon spec-remove" onclick="specNameRemove(this,'+type+')">&#xe640;</i></div></label>'+
            '<div class="layui-input-block">'+
                '<span class="modal-span-01">'+
                 '</span>'+
                '<div class="layui-btn layui-btn-warm layui-btn-small addSpecItem" lay-submit="" addtype="1" onclick="addSpecItem(this,'+type+')">添加规格项</div>'+
            '</div>'+
        '</div>'+
        '</div>';
  
    if( theflag == true && spec_name!=""){
        if($(".spec_list_"+type+" .spec_list_parent .spec_list").length>=2){
          layer.alert("最多添加2个规格");
          return false;
        }
        $(obj).parents(".spec_list_parent").append(html);
    }else if( theflag == false ){
        layer.alert("请勿重复添加");
    }else if( spec_name == "" ){
        layer.alert("请输入商品规格");
    }
}
//添加规格值
function addSpecItem(thisObj,type){
    var addtype = $(thisObj).attr("addtype");
    var input_html = "";
    if(addtype==1){
        input_html = '<input type="text" class="addspecinput" onblur="inputBlur(this,1,'+type+')">';
    }else{
        input_html = '<input type="text" class="addspecinput" onblur="inputBlur(this,2,'+type+')">';
    }
    if($(".addspecinput").length==0){
      $(thisObj).siblings(".modal-span-01").append(input_html);
    }
}
//是否已经存在
function hasExist(dom,domval){
    var flag = true;
    if($(dom).length==0){
      flag = true;
    }
    $(dom).each(function(i,elem){
      if($(elem).text()==domval){
        flag = false;//false代表已经存在
      }
    });
    return flag;
}
//插入规格dom节点
function inputBlur(thisObj,addtype,type){
    var thisval = $(thisObj).val();
    thisval = $.trim(thisval);
    var input_html = "";
    var flag = true;
    if($(".specs-val").length==0){
      flag = true;
    }
    $(thisObj).parents(".modal-span-01").find(".specs-val").each(function(i,elem){
      if($(elem).text()== thisval){
        flag = false;//false代表已经存在
      }
    });

    if( thisval ){
        if( addtype == 1 ){
            input_html = '<span class="new-item-span layui-btn layui-btn-small btn-success" onclick="itemState(this,'+type+')"><span class="specs-val" data-item_id="0">'+thisval+'</span><i class="layui-icon spec-remove" onclick="itemRemove(this,'+type+')">&#xe640;</i></span>'
        }else if( addtype == 2 ){
            input_html = '<span class="layui-btn layui-btn-small btn-default" onclick="itemState(this,'+type+')">'+thisval+'<i class="layui-icon spec-remove" onclick="item_remove(this)">&#xe640;</i></span>'
        }
        if( flag == true ){
            $(thisObj).parents(".modal-span-01").append(input_html);
            $(thisObj).remove();
            appendType(type);
        }else{
            layer.alert("请勿重复添加");
        }
        
    }
}
//笛卡尔积计算
var DescartesUtils = {
  /**
   * 如果传入的参数只有一个数组，求笛卡尔积结果
   * @param arr1 一维数组
   * @returns {Array}
   */
  descartes1:function(arr1){
    // 返回结果，是一个二维数组
    var result = [];
    var i = 0;
    for (i = 0; i < arr1.length; i++) {
      var item1 = arr1[i];
      result.push([item1]);
    }
    return result;
  },
 
  /**
   * 如果传入的参数只有两个数组，求笛卡尔积结果
   * @param arr1 一维数组
   * @param arr2 一维数组
   * @returns {Array}
   */
  descartes2: function(arr1, arr2) {
    // 返回结果，是一个二维数组
    var result = [];
    var i = 0, j = 0;
    for (i = 0; i < arr1.length; i++) {
      var item1 = arr1[i];
      for (j = 0; j < arr2.length; j++) {
        var item2 = arr2[j];
        result.push([item1, item2]);
      }
    }
    return result;
  },
 
  /**
   *
   * @param arr2D 二维数组
   * @param arr1D 一维数组
   * @returns {Array}
   */
  descartes2DAnd1D: function(arr2D, arr1D) {
    var i = 0, j = 0;
    // 返回结果，是一个二维数组
    var result = [];
 
    for (i = 0; i < arr2D.length; i++) {
      var arrOf2D = arr2D[i];
      for (j = 0; j < arr1D.length; j++) {
        var item1D = arr1D[j];
        result.push(arrOf2D.concat(item1D));
      }
    }
 
    return result;
  },
 
  descartes3: function(list) {
    var listLength = list.length;
    var i = 0, j = 0;
    // 返回结果，是一个二维数组
    var result = [];
    // 为了便于观察，采用这种顺序
    var arr2D = DescartesUtils.descartes2(list[0], list[1]);
    for (i = 2; i < listLength; i++) {
      var arrOfList = list[i];
      arr2D = DescartesUtils.descartes2DAnd1D(arr2D, arrOfList);
    }
    return arr2D;
  },
 
  //笛卡儿积组合
  descartes: function(list)
  {
    if (!list) {
      return [];
    }
    if (list.length <= 0) {
      return [];
    }
    if (list.length == 1) {
      return DescartesUtils.descartes1(list[0]);
    }
    if (list.length == 2) {
      return DescartesUtils.descartes2(list[0], list[1]);
    }
    if (list.length >= 3) {
      return DescartesUtils.descartes3(list);
    }
  }
 
};

function appendType(type){
    var table_html = "";
    var tr_html = "";
    var array1 = [];
    var array2 = [];
    var list2  = [];
    var new_array = [];
    var array_name = [];
    var type_number = 1;

    $(".spec_list_"+type+" .spec_list_parent .spec_list").each(function(index,elem){
         if( $(elem).find(".btn-success").length > 0 ){
            array_name.push($(elem).find(".new_spec_name").text());
         }
        if(index==0){
            $(elem).find(".btn-success .specs-val").each(function(i,e){
                array1.push($(e).text());
            });
        }
        if(index==1){
            $(elem).find(".btn-success .specs-val").each(function(i,e){
                array2.push($(e).text());
            });
        }
    });
    if ( array1.length > 0 && array2.length > 0){
        type_number=2;
    }
    if(array1.length==0 && array2.length==0){
        type_number=0;
    }

        //新增的规格只有一行的
        if( type_number ==1 ){
            
            if( array1.length > 0 ){
                new_array = array1;
            }else if( array2.length > 0 ){
                new_array = array2;
            }

            for( var j=0; j< new_array.length; j++ ){
                tr_html += '<tr> '+
                                '<td>'+new_array[j]+'</td>'+
                                '<td><input type="number" name="" class="form-control specitem_put" value="" data-field="productprice"></td>'+
                                '<td><input type="number" name="" class="form-control specitem_put" value="" data-field="marketprice"></td>'+
                                '<td><input type="number" name="" class="form-control specitem_put" value="" data-field="store_count"></td>'+
                                '<td><input type="number" name="" class="form-control specitem_put" value="" data-field="bar_code"></td>'+
                            '</tr>';
            }
            table_html = '<div class="col-xs-12" id="col-xs-12-one">'+
                '<div>'+
                    '<table class="table table-bordered">'+
                        '<tbody class="itemtbody">'+
                            '<tr style="font-weight: bolder"> '+
                                '<td>'+array_name[0]+'</td>'+
                                '<td>市场价</td>'+
                                '<td>促销价</td>'+
                                '<td>库存</td>'+
                                '<td>条形码</td>'+
                            '</tr>'+tr_html+
                        '</tbody>'+
                    '</table>'+
                 '</div>'+
            '</div>';
        }else if( type_number ==2 ){
          //新增的规格只有2行的
            var tr_html_1 = "";
            var tr_html_2 = "";
            var tr_html_3 = "";
            var tr_html_4 = "";
            list2 = [array1,array2];
            /*笛卡尔积计算的调用*/
            var result = DescartesUtils.descartes(list2);
            //遍历商品规格名
            for( var k=0; k < array_name.length; k++){
                tr_html_1 += '<td>'+array_name[k]+'</td>';
            }
            //遍历商品规格具体参数
            for( var i=0; i< result.length; i++){
                tr_html_3="";
                tr_html_2 = "";
                tr_html_2 += '<td><input type="number" name="" class="form-control specitem_put" value="" data-field="productprice"></td>'+
                            '<td><input type="number" name="" class="form-control specitem_put" value="" data-field="marketprice"></td>'+
                            '<td><input type="number" name="" class="form-control specitem_put" value="" data-field="store_count"></td>'+
                            '<td><input type="number" name="" class="form-control specitem_put" value="" data-field="bar_code"></td>';
                for( var m=0 ; m < result[i].length; m++ ){
                    tr_html_3 += '<td>'+result[i][m]+'</td>';
                }
                tr_html_4 += '<tr>'+tr_html_3+tr_html_2+'</tr>';
            }
            table_html = '<div class="col-xs-12" id="col-xs-12-two">'+
                    '<div>'+
                        '<table class="table table-bordered">'+
                            '<tbody class="itemtbody">'+
                                '<tr style="font-weight: bolder"> '+tr_html_1+
                                    '<td>市场价</td>'+
                                    '<td>促销价</td>'+
                                    '<td>库存</td>'+
                                    '<td>条形码</td>'+
                                '</tr>'+tr_html_4+
                            '</tbody>'+
                        '</table>'+
                     '</div>'+
                '</div>';
        }else if( type_number ==0 ){
            table_html = "";
        }
        $(".ajax_spec_data_area"+type+"").html(table_html);     
}

//删除整个规格
function specNameRemove(thisObj,type){
    $(thisObj).parents(".spec_list").remove();
    appendType(type);
}
//新增规格的删除
function itemRemove(thisObj,type){
    $(thisObj).parents(".new-item-span").remove();
    appendType(type);
}
//选中规格或取消规格
function itemState(obj,type){
    if( $(obj).hasClass("btn-success") ){
        $(obj).removeClass("btn-success");
        $(obj).addClass("btn-default"); 
    }else{
        $(obj).addClass("btn-success");
        $(obj).removeClass("btn-default");
    }
    appendType(type);
}
//禁用规格或者使用规格
function item_remove(thisObj,item_id){
    layer.confirm('确认操作？', {
        btn: ['确认','取消'] //按钮
    }, function(){
        //确认回调
        var status = 0;
        if($(thisObj).parent().hasClass('btn-success')){
            //禁用
            status = 0;
        }else if($(thisObj).parent().hasClass('btn-default')){
            //启用
            status = 1;
        }
        var url = "<?php  echo mobile_url('product',array('op'=>'setitem_status'));?>";
        $.post(url,{'status':status,'item_id':item_id},function(data){
            layer.open({
                title: '提示',
                content: data.message
            });
            if(data.errno = 1){
                if($(thisObj).parent().hasClass('btn-success')){
                    $(thisObj).parent().removeClass('btn-success');
                    $(thisObj).parent().addClass('btn-default');
                    $(thisObj).parent().attr('title','点击启用');
                }else if($(thisObj).parent().hasClass('btn-default')){
                    $(thisObj).parent().removeClass('btn-default');
                    $(thisObj).parent().addClass('btn-success');
                    $(thisObj).parent().attr('title','点击禁用');
                }
            }
        },'json');
        layer.closeAll('dialog');
    }, function(){
        //取消的回调
    });
}
    </script>
</body>
</html>