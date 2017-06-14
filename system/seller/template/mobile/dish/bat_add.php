<!DOCTYPE html>
<html>
<head>
  <?php include page('seller_header');?>

</head>
<style>
    .select-area{
        margin: 15px 0;
    }
    .select-area select{
        padding: 3px;
    }
    .spec-p1,.spec-p2{
        padding: 3px;
    }
    .choose-before{
        color: #b0b0b0;
        font-size: 20px;
        cursor: pointer;
    }
    .choose-checked{
        color: #60a75a;
        font-size: 20px;
        cursor: pointer;
    }
    .good-table th,.good-table td{
        text-align: center;
    }
    .spec-name,.spec-detail{
        margin-bottom: 5px;
    }
    .choose-all i{
        vertical-align: middle;
        margin-left: 3px;
    }
    .choose-all{
        display: none;
        cursor: pointer;
    }
    .save-btn{
        float: right;
        margin-bottom: 30px;
    }
</style>
<body style="padding:10px;">
<div >
    <blockquote class="layui-elem-quote"><?php if(empty($_GP['choose_one'])){ echo '批量导入'; }else{ echo '从产品库添加';} ?> <span></span></blockquote>
    <?php if(empty($_GP['choose_one'])){  ?>
    <ul class="nav nav-tabs" >
        <li style="width:7%" <?php  if( empty($_GP['op']) || $_GP['op'] == 'index' ) { ?> class="active"<?php  } ?>><a href="<?php echo mobile_url('product_bat');?>">csv导入</a></li>
        <li style="width:7%" <?php  if($_GP['op'] == 'bat_add') { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('product_bat',  array('op' => 'bat_add'));?>">产品库导入</a></li>
    </ul>
    <?php } ?>
    <form name="myform" method="post" action="" id="myform">
    <div style="padding:10px 20px;">
        <div class="select-area">
             <select class="p1" name="pcate" id="p1">
                <option value="">请选择一级行业分类</option>
                <?php
                  foreach($oneShopCate as $v)
                  {
                ?> 
                 <option value="<?php echo $v['id'];?>" <?php if($v['id']==$_GP['pcate']){echo 'selected';}?>><?php echo $v['name'];?></option>
                 <?php
                  }
                 ?>
             </select>
            
             <select class="p2" name="ccate" id="p2">
                 <?php
                    if(count($twoShopCate) > 0){
                        foreach($twoShopCate as $v){
                 ?>
                 <option value="<?php echo $v['id'];?>" <?php if($v['id']==$_GP['ccate']){echo 'selected';}?>><?php echo $v['name'];?></option>
                 <?php
                        }
                    }
                 ?>
             </select>
            
            <button class="layui-btn layui-btn-normal" onclick="$('#myform').submit()">提交</button>
        </div>
        
    </div>
        <table class="good-table table table-bordered">
            <thead>
                <tr>
                    <th>产品名称</th>
                    <th>缩略图</th>
                    <th>促销价</th>
                    <th>市场价</th>
                    <th>操作&nbsp;<span class="choose-all">全选<i class="choose-before fa fa-circle-thin" goodurl="1" aria-hidden="true"></i></span></th>
                </tr>
            </thead>
            <tbody>
             <?php
               foreach($goodsPage['goodslist'] as $v)
               {
             ?>   
                <tr>
                    <td><?php echo $v['title'];?></td>
                    <td>
                        <div class="spec-name"><img src="<?php echo $v['thumb'];?>" width="60" height="60"></div>
                    </td>
                    <td>
                        <div class="spec-name"><?php echo $v['marketprice'];?></div>
                    </td>
                    <td>
                        <div class="spec-name"><?php echo $v['productprice'];?></div>
                    </td>
                    <td>
                        <i class="choose-i choose-before fa fa-circle-thin" goodid="<?php echo $v['id'];?>" goodurl="<?php echo mobile_url('product',array('op'=>'postadd','goodid'=>$v['id'])) ?>" aria-hidden="true"></i>
                    </td>
                </tr>
              <?php
               }
              ?>  
                
            </tbody>
        </table>
    
    </form>
        <div id="demo1"><!-- 分页的div -->
            <?php echo $pager;?>
            <button class="layui-btn layui-btn-normal" onclick="save()">确定导入</button>
        </div>
    </div>
    
</div>
<!-- url-hidden隐藏域用来存储跳转的URL -->
<input type="hidden" class="url-hidden" value="">

<div class="p1p2-modal modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">选择店铺分类</h4>
      </div>
      <div class="modal-body">
          <select class="spec-p1" name="store_p1" id="store_p1">
              <option value="">请选择一级行业分类</option>
            <?php
              foreach($shopStoreCateData as $v)
              {
            ?>
            <option value="<?php echo $v['id'];?>"><?php echo $v['cat_name'];?></option>
            <?php
              }
            ?>
        </select>
        <select class="spec-p2" name="store_p2" id="store_p2">
            
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" id="close_button">关闭</button>
        <button type="button" class="save-btn-2 btn btn-primary" onclick="save2()">确定导入</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php include page('seller_footer');?>
<script>
<?php if(empty($_GP['choose_one'])){  ?>
var flag = 2;//1选择产品跳转过来的,2代表批量导入。
<?php }else{ ?>
var flag = 1;//1选择产品跳转过来的,2代表批量导入。
<?php } ?>
var id_array = [];//储存选中的id数组
$(function(){
    if( flag == 1 ){
        $("body").on("click",".choose-i",function(){
            if( $(this).hasClass("fa-check-circle") ){
                //已经选择 直接return
                return false;
            }else{
                $(".choose-i").removeClass("fa-check-circle choose-checked").addClass("fa-circle-thin choose-before");
                $(this).removeClass("fa-circle-thin choose-before").addClass("fa-check-circle choose-checked");
                var url = $(this).attr("goodurl");
                $(".url-hidden").val(url);
            }
        });
    }else if( flag == 2 ){
        $(".choose-all").show();
        $("body").on("click",".choose-i",function(){
            if( $(this).hasClass("fa-check-circle") ){
                $(this).removeClass("fa-check-circle choose-checked").addClass("fa-circle-thin choose-before");
            }else{
                $(this).removeClass("fa-circle-thin choose-before").addClass("fa-check-circle choose-checked");
            }
            var length = $(".choose-i.fa-check-circle").length;
            if( length==0 ){
                $(".choose-all").find("i").removeClass("fa-check-circle choose-checked").addClass("fa-circle-thin choose-before");
            }else{
                $(".choose-all").find("i").removeClass("fa-circle-thin choose-before").addClass("fa-check-circle choose-checked");
            }
        });
        //全选操作
        $("body").on("click",".choose-all",function(){
            //已选中
            if( $(this).find("i").hasClass("fa-check-circle") ){
                $(this).find("i").removeClass("fa-check-circle choose-checked").addClass("fa-circle-thin choose-before");
                $(".choose-i").each(function(){
                    $(this).removeClass("fa-check-circle choose-checked").addClass("fa-circle-thin choose-before");
                });
            }else{
                //未选中
                $(this).find("i").removeClass("fa-circle-thin choose-before").addClass("fa-check-circle choose-checked");
                $(".choose-i").each(function(){
                    $(this).removeClass("fa-circle-thin choose-before").addClass("fa-check-circle choose-checked");
                });
            }
            
        })
    }
    
})
// 分类一change事件带出分类二的数据
function changeP1(obj){
    var that = $(obj);
    var select_val = that.val();
    var url = "";
    //下拉框联动，请补充URL
    $.post(url,{store_p1:select_val},function(data){
        if( data.errno == 1 ){
            //暂时写个例子，请遍历服务端返回的数据拼接到html中。
            var html = '<option value="0">--分请选择分类二--</option>';
            $(".p2").html(html);
        }else{
            alert(data.message);
        }
    },"json");
}
// 分类二change事件带出分类表格的数据
function changeP2(obj){
    var that = $(obj);
    var store_p1 = $(".p1").val();
    var store_p2 = that.val();
    var url = "";
    $.post(url,{store_p1:store_p1,store_p2:store_p2},function(data){
        if( data.errno == 1 ){
            //暂时写个例子，请遍历服务端返回的数据拼接到html中。
            var html = '<option value="0">--分请选择分类二--</option>';
            $(".good-table").html(html);
        }else{
            alert(data.message);
        }
    },"json")
}
//保存
function save(){
    id_array = [];//先清空
    if( flag == 1 ){
        var url = $(".url-hidden").val(); 
        window.location.href = url;
    }else if( flag == 2 ){
        $(".choose-checked").each(function(){
            id_array.push($(this).attr("goodid"));
        });
        $(".p1p2-modal").modal();
    }
}
function save2(){
    var store_p1 = $("#store_p1").val();
    var store_p2 = $("#store_p2").val();

    var url = "<?php echo mobile_url('product_bat',array('op'=>'bat_dish_add')); ?>";
    $.post(url,{goods_id:id_array,store_p1:store_p1,store_p2:store_p2},function(data){
        $('#close_button').trigger("click");
        alert(data['message']);
    },"json");
}

$(function(){
    $('#p1').change(function(){
        var url = "<?php echo mobile_url('product_bat',array('op'=>'cate_tow')); ?>";
        $.ajaxLoad(url,{'pid':$(this).val()},function(data){
            $('#p2').empty();
            $('#p2').append("<option value='0'>请选择分类</option>");
            var da = JSON.parse(data);
            for(var i in da){
                $('#p2').append("<option value='" + da[i]['id'] + "'>" + da[i]['name'] + "</option>");
            }
        });
    })
    
    
    $('#store_p1').change(function(){
        var url = "<?php echo mobile_url('product_bat',array('op'=>'store_cate_two')); ?>";
        $.ajaxLoad(url,{'pid':$(this).val()},function(data){
            $('#store_p2').empty();
            $('#store_p2').append("<option value='0'>请选择分类</option>");
            var da = JSON.parse(data);
            for(var i in da){
                $('#store_p2').append("<option value='" + da[i]['id'] + "'>" + da[i]['cat_name'] + "</option>");
            }
        });
    })
    
    
    
})
</script>
</body>
</html>