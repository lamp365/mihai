<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('seller_header');?>
        <link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/webuploader.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/style.css" />
        <link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/seller/css/main.css" />
        <link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/css/layui.css" media="all" />
        <link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/css/select2.min.css" />
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/jquery.dragsort-0.5.2.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/jquery-ui.js"></script>
        <script src="<?php echo RESOURCE_ROOT;?>addons/common/webuploader/webuploader.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/clipboard.min.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.config.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.all.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/select2.min.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/zh-CN.js"></script>
        <script type="text/javascript" charset="utf-8" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/lang/zh-cn/zh-cn.js"></script>
	</head>
    <style>
.ncap-form-default {
    padding: 10px 0;
    overflow: hidden;
}
.ncap-form-default dl.row, .ncap-form-all dd.opt {
    color: #777;
    background-color: #FFF;
    padding: 12px 0;
    margin-top: -1px;
    border-style: solid;
    border-width: 1px 0;
    border-color: #F0F0F0;
    position: relative;
    z-index: 1;
}
.table-bordered {
    width: 100%;
}
table {
    border-collapse: collapse;
}
.row .table-bordered td {
    padding: 8px;
    line-height: 1.42857143;
}
.table-bordered tr td {
    border: 1px solid #f4f4f4;
}
.row{
    margin:0;
    padding:0;
}
.layui-form-label{
    width: 110px;
}
.layui-layer-iframe.layui-layer1{
    top:50%!important;
}
.type-area{
    line-height: 38px;
}
.edit-imgs img{
    width: 110px;
    height: 110px;
}
.img-close{
    position: relative;
    display: inline-block;
    border: 1px solid #E4E4E4;
    margin-right: 15px;
}
.close-btn{
    position: absolute;
    top: -3px;
    right: -19px;
    color: #9C9C9C;
    width: 20px;
    text-align: center;
    height: 20px;
    line-height: 20px;
    cursor: pointer;
}
#uploader .info{
    opacity: 0;
    filter: alpha(opacity=0);
}
#brand{
    width: 500px;
}
    </style>
	<body style="padding:10px;" class="step2">
    <form action="" method="post" onsubmit="return myfun();">
        <?php
          if($_GP['dish_id'] <= 0)
          {
        ?>
    <blockquote class="layui-elem-quote">从产品库添加<span class="child-stop-info"></span></blockquote>
        <div class="layui-form-item">
            <label class="layui-form-label">产品库</label>
            <div class="layui-input-block">
                <span class="layui-btn" lay-submit="" onclick="chooseGood(this)" data-url="<?php echo mobile_url('product_bat',array('op'=>'bat_goods_add','choose_one'=>1)); ?>">选择产品</span>
                <input type="hidden" value="<?php echo $_GP['goodid'] ?>" name="gid" id="gid">
            </div>
        </div>
    	<?php
          }
        ?>
        <blockquote class="layui-elem-quote">发布商品<span class="child-stop-info">发布内容必须遵循国家相关法律法规，凡是涉及色情暴力，违反伦理道德的，将会被立即删除，并封停使用者账号。</span></blockquote>

        <!-- 商品名称 -->
            <div class="layui-form " >
                <div class="layui-form-item">
                    <label class="layui-form-label"><font color="red">*</font> 商铺分类</label>
                    <div class="layui-input-inline">
                        <div class="type-area" style="display:<?php if(empty($product['store_p1'])){ echo 'none';} ?> ">
                            <span class="type-area-1"><?php if(isset($shop_cate1)) echo $shop_cate1['name'] ?></span> > <span class="type-area-2"><?php if(isset($shop_cate2)) echo $shop_cate2['name'] ?></span>
                        </div>
                    </div>
                    <div class="layui-input-inline">
                        <span class="layui-btn chooseType" lay-submit="" lay-filter="demo" onclick="chooseType(this)" data-url="<?php echo mobile_url('product',array('op'=>'choose_type')); ?>"><?php if(empty($product['store_p1'])){ echo '选择';}else{ echo '修改';}; ?>分类</span>
                    </div>
                    <input type="hidden" id="store_p1" name="store_p1" value="<?php echo $product['store_p1'];?>">
                    <input type="hidden" id="store_p2" name="store_p2" value="<?php echo $product['store_p2'];?>">
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><font color="red">*</font> 商品名称</label>
                    <div class="layui-input-inline" style="width: 500px;">
                        <input type="text" name="title" lay-verify="productname" value="<?php echo $product['title'];?>" autocomplete="off" placeholder="名称中请包括品牌、品名等" class="layui-input productname">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"> 商品品牌</label>
                <div class="layui-input-inline">
                    <select class="js-data-example-ajax" name="brand" id="brand">
                        <option value="<?php echo $brand['id'];?>" selected="selected"><?php echo $brand['brand']!=''?$brand['brand']:'select2/select2';?></option>
                      </select>
                </div>
            </div>
            <div class="layui-form " >
                <div class="layui-form-item remark-area">
                    <label class="layui-form-label"></label>
                    <div  class="layui-input-block">注：商品名称不符合规范将会被扣分或违规处罚<i class="question fa fa-question-circle-o"></i></div>
                </div>
                <!-- 商品介绍 -->
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">商品简介</label>
                    <div class="layui-input-inline" style="width: 500px;">
                        <textarea name="description" placeholder="商品的额外信息，主要用于补充和关键词优化" class="layui-textarea"><?php echo $product['description'];?></textarea>
                    </div>
                </div>

                <div class="layui-form-item remark-area">
                    <label class="layui-form-label"></label>
                    <div  class="layui-input-block">注：商品名称不符合规范将会被扣分或违规处罚<i class="question fa fa-question-circle-o"></i></div>
                </div>
                <!-- 编辑的商品主图 -->
                <?php
                  if($_GP['dish_id'] > 0 && count($picArr) > 0)
                  {
                ?>
                <!--<div class="layui-form-item layui-form-text">
                    <label class="layui-form-label"><font color="red">*</font> 商品主图</label>
                    <div class="layui-input-block">
                        <div class="edit-imgs">
                            <?php
                                foreach($picArr as $v)
                                {
                            ?>
                            <span class="img-close">
                                <img src="<?php echo $v;?>">
                                <i class="close-btn fa fa-trash-o" aria-hidden="true" name="delete_pic" data-pic="<?php echo $v;?>"></i>
                            </span>
                            <?php
                                }
                            ?>
                            
                        </div>
                    </div>
                </div>-->
                <?php
                  }
                ?>
                <!-- 商品主图 -->
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label"><font color="red">*</font> 商品主图</label>
                    <div class="layui-input-block">
                        <div id="uploader">
                            <div class="queueList">
                                <div id="dndArea" class="placeholder">
                                    <div id="filePicker"></div>
                                    <p>或将照片拖到这里，单次最多可选300张</p>
                                </div>
                            </div>
                            <div class="statusBar" style="display:none;">
                                <div class="progress">
                                    <span class="text">0%</span>
                                    <span class="percentage"></span>
                                </div><div class="info"></div>
                                <div class="btns">
                                    <div id="filePicker2"></div><div class="uploadBtn">开始上传</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <!-- <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">商品描述</label>
                    <div class="layui-input-block">
                    在线编辑器
                        <script id="container" name="content" type="text/plain" style="height:500px;border:none">
                           <?php echo htmlspecialchars_decode($product['content']);?>
                        </script>
                    </div>
                </div> -->
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">商品描述</label>
                    <div class="layui-input-block">
                        <textarea placeholder="商品描述" class="layui-textarea" name="content" id="content"><?php echo $product['content']?></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">商品详细图</label>
                    <div class="layui-input-block">
                        <div id="uploader2">
                            <div class="queueList">
                                <div id="dndArea2" class="placeholder">
                                    <div id="filePicker3"></div>
                                    <p>或将照片拖到这里，单次最多可选300张</p>
                                </div>
                            </div>
                            <div class="statusBar" style="display:none;">
                                <div class="progress">
                                    <span class="text">0%</span>
                                    <span class="percentage"></span>
                                </div><div class="info"></div>
                                <div class="btns">
                                    <div id="filePicker4"></div><div class="uploadBtn">开始上传</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">选择规格</label>
                    <div class="layui-input-inline">
                        <span id="gtype_name"><?php if(isset($edit_gtype) && !empty($edit_gtype)){ echo $edit_gtype['name'];} ?></span>
                        <span class="layui-btn add-spec" lay-submit="">选择规格</span>
                        <input type="hidden" value='<?php if(isset($spec_info_jsonstring)){ echo $spec_info_jsonstring;}?>' id="itemPriceJson" name="itemPriceJson" class="layui-input">
                        <input type="hidden" value="<?php echo $product['gtype_id']; ?>" id="gtype_id" name="gtype_id" class="layui-input">
                        <input type="hidden" value='<?php if(isset($item_info_jsonstring)){ echo $item_info_jsonstring;} ?>' id="item_value_str"  class="layui-input"><!-- 用于编辑的时候 知道是哪些个被选中 -->

                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><font color="red">*</font> 市场价</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="number" name="productprice"  placeholder="市场价" autocomplete="off" class="layui-input" value="<?php echo FormatMoney($product['productprice'],0); ?>">
                    </div>
                    <div class="layui-input-inline" style="width: 300px;line-height:38px;"></div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><font color="red">*</font> 促销价</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="number" name="marketprice"  placeholder="促销价" autocomplete="off" class="layui-input" value="<?php echo FormatMoney($product['marketprice'],0); ?>">
                    </div>
                    <div class="layui-input-inline" style="width: 300px;line-height:38px;"></div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">商品佣金</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="number" name="commision"  placeholder="商品佣金" autocomplete="off" class="layui-input" value="<?php echo $product['commision']; ?>">
                    </div>
                    <div class="layui-input-inline" style="width: 300px;line-height:38px;">%<span> (为价格比例,例如5则为单价的百分之5)</span></div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">条形码</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="number" name="goodssn"  placeholder="条形码" autocomplete="off" class="layui-input" value="<?php echo $product['goodssn']; ?>">
                    </div>
                    <div class="layui-input-inline" style="width: 300px;line-height:38px;"></div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><font color="red">*</font> 库存</label>
                    <div class="layui-input-inline" style="width: 300px;">
                        <input type="number" name="store_count"  placeholder="库存" autocomplete="off" class="layui-input" value="<?php echo $product['store_count']; ?>">
                    </div>
                    <div class="layui-input-inline" style="width: 300px;line-height:38px;"></div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">立即上架</label>
                    <div class="layui-input-block">
                        <input type="radio" name="status" value="1" title="是"  <?php if($product['status'] == 1){ echo "checked";} ?>> &nbsp;
                        <input type="radio" name="status" value="0" title="否"  <?php if($product['status'] == 0 ){ echo "checked";} ?>>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">设为新品</label>
                    <div class="layui-input-block">
                        <input type="radio" name="isnew" value="1" title="是"  <?php if($product['isnew'] == 1){ echo "checked";} ?>> &nbsp;
                        <input type="radio" name="isnew" value="0" title="否"  <?php if($product['isnew'] == 0 ){ echo "checked";} ?>>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">是否支持7天包换</label>
                    <div class="layui-input-block">
                        <input type="radio" name="isreason" value="1" title="支持"  <?php if($product['isreason'] == 1){ echo "checked";} ?>> &nbsp;
                        <input type="radio" name="isreason" value="0" title="不支持"  <?php if($product['isreason'] == 0 ){ echo "checked";} ?>>
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <input type="hidden" name="do_add" value="1">
                        <input type="hidden" value="<?php echo $product['id'];?>" id="dish_id" name="dish_id" class="layui-input">
                        <input type="hidden" value="<?php echo $product['goodid'];?>" id="goodid" name="goodid" class="layui-input">
                        <input type="hidden" value='<?php echo $picJson;?>' id="xcimgjson" name="xcimgjson" class="layui-input">
                        <input type="hidden" value='<?php echo $xqImgJson;?>' id="xqimgjson" name="xqimgjson" class="layui-input">
                        <input type="hidden" value="<?php echo $isEdit;?>" id="isEdit" name="isEdit" class="layui-input">
                        
                        <input type="hidden" value="<?php echo $brand['brand'];?>" id="brand_name" name="brand_name" class="layui-input">
                        
                        <button class="layui-btn" lay-submit="" lay-filter="sureadd">确认<?php if(empty($product)){ echo '发布';}else{ echo '编辑';} ?></button>
                    </div>
                </div>
            </div>
             <!--<input name="xcimg" type="hidden" class="list1SortOrder" /> -->
        </form>
        <?php include page('seller_footer'); ?>
	</body>

<script src="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/upload.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/upload1.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
layui.use(['form','element','layer'], function() {
    var form = layui.form();
    var element = layui.element();
    var $ = layui.jquery,
        layer = layui.layer;
    //监听提交
    form.on('submit(sureadd)', function(data) {
        if($(".list1SortOrder").length <= 0)
        {
            alert('相册不能为空');
            return false;
        }
        
        //检验参数是否 星号的没有填写
        return true;
    });

    $(".add-spec").on("click",function(){
        var gtype_id = $("#gtype_id").val();
        var dish_id  = $("#dish_id").val();
        var url = "<?php echo mobile_url('product',array('op'=>'choose_spec')); ?>";
        if(gtype_id){
            url = url + "?gtype_id="+gtype_id;
        }
        if(dish_id && gtype_id){
            url = url + "&dish_id="+dish_id;
        }else if(dish_id && !gtype_id){
            url = url + "?dish_id="+dish_id;
        }
        layer.open({
          title:'填写规格',
          type: 2, 
          fixed: false, //不固定
          maxmin: false,
          area : ['900px' , '600px'],
          content: url ,//这里content是一个URL，如果你不想让iframe出现滚动条，你还可以content: ['http://sentsin.com', 'no']
          success:function(layero,index){
            var body = layer.getChildFrame('body', index);
            var iframeWin = window[layero.find('iframe')[0]['name']];
            $(iframeWin).addClass("layui-layer1")
          }
        });
    });
});

function myfun(){
    if($(".type-area .type-area-1").text()==""){
        alert("请选择分类");
        return false;
    }
    if($(".productname").val()==""){
        alert("请输入商品名称");
        return false;
    }
}
function chooseType(obj){

    var url = $(obj).data('url');
    $.ajaxLoad(url,{},function(){
        $('#alterModal').modal('show');
        //获取已选择分类的隐藏域值
        var parent_hidden_val = $("#store_p1").val();
        var child_hidden_val = $("#store_p2").val();
        //如果已经选择分类，保留已选的分类
        if( parent_hidden_val&&child_hidden_val){
            $(".parent-type .parent-type-val[type-id="+parent_hidden_val+"]").trigger("click");
            $(".parent-type .parent-type-val[type-id="+parent_hidden_val+"]").parent("li").addClass("li-check");
            $(".child-type .parent-type-val[type-id="+child_hidden_val+"]").parent("li").addClass("li-check");
        }

    });
}

function chooseGood(obj){
    var url = $(obj).data('url');
    window.location.href=url;
   /* $.ajaxLoad(url,{},function(){
        $('#alterModal').modal('show');
    });*/
}

$(function(){
   
    
    //var ue = UE.getEditor('container');

    //select2下拉框初始化
    $(".js-data-example-ajax").select2({
        placeholder: '请选择客户',
        language: 'zh-CN',
        allowClear: true,
        ajax: {
          url: "/seller/product/searcgbrand.html",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              key: params.term, // search term
              page: params.page
            };
          },
          processResults: function (data, params) {
            params.page = params.page || 1;
            return {
              results: data.brand,
              pagination: {
                more: (params.page * 30) < data.total_count
              }
            };
          },
          cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepoDefault, // omitted for brevity, see the source of this page
        templateSelection: formatRepoProvince // omitted for brevity, see the source of this page
    });
    
    function formatRepoDefault(repo){
        if($('#brand_name').val() != '')
            {
                var markup = "<div>"+$('#brand_name').val()+"</div>";
            }
            else
            {
                var markup = "<div>品牌搜索</div>";
            }
            return repo.brand;
    }

    function formatRepoProvince(repo) {
        if(repo.brand != undefined)
        {
            var markup = "<div>"+repo.brand+"</div>";
        }
        else{
            if($('#brand_name').val() != '')
            {
                var markup = "<div>"+$('#brand_name').val()+"</div>";
            }
            else
            {
                var markup = "<div>品牌搜索</div>";
            }
        }
        return markup;
    }
    // 商品主图编辑操作
    $(".close-btn").on("click",function(){
        
    })
    
    $('.fa-trash-o').on("click",function(){
        //$(this).attr('data-pic')
        
        //var weburl = '/seller/product/deleteImg.html';
        //$.post(weburl,{'dish_id':$('#dish_id').val(),'data_pic':$(this).attr('data-pic'),'xcimg':$('#xcimgstr').val()},function(redata){
            
        //},"json");
        //location.reload();
    })
/*    var el = $("#uploader .filelist")[0];
    new Sortable(el);*/
    
    $( "#uploader .filelist" ).sortable();
    $( "#uploader .filelist" ).disableSelection();
    $( "#uploader2 .filelist" ).sortable();
    $( "#uploader2 .filelist" ).disableSelection();

     //图片上传的拖放功能
/*   $("#uploader .filelist").dragsort({ 
        dragSelector: "li", 
        dragBetween: true
        //dragEnd: saveOrder
    }); */
   //将图片URL存入list1SortOrder隐藏域
   /* function saveOrder() {
        var data = $("#uploader .filelist li").map(function() { return $(this).attr('postadd-data-url') }).get();
        $(".list1SortOrder").val(data);
    };*/
});


</script>
</html>