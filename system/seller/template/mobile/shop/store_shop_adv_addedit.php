<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('seller_header');?>
        <link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/webuploader.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/style.css" />
        <link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/seller/css/main.css" />
        <link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/css/layui.css" media="all" />
        <link rel="stylesheet" href="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/css/cropper.min.css" media="all" />
        <link rel="stylesheet" href="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/css/main.css" media="all" />
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/js/jquery.dragsort-0.5.2.js"></script>
        <script src="<?php echo RESOURCE_ROOT;?>addons/common/webuploader/webuploader.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/clipboard.min.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.config.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.all.js"></script>
        <script type="text/javascript" charset="utf-8" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/lang/zh-cn/zh-cn.js"></script>

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
    <script src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/html5shiv.min.js"></script>
    <script src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/respond.min.js"></script>
  <![endif]-->
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
#layui-layer1.layui-layer1{
    margin-top:-300px!important;
}
.avatar-view{
    width: 300px;
}
.avatar-view img{
    width: 300px;
    height: auto;
}
#uploader .info {
    opacity: 0;
    filter: alpha(opacity=0);
}
    </style>
    <body style="padding:10px;" class="step2">
    <blockquote class="layui-elem-quote">发布商品<span class="child-stop-info">发布内容必须遵循国家相关法律法规，凡是涉及色情暴力，违反伦理道德的，将会被立即删除，并封停使用者账号。</span></blockquote>
            <div class="layui-form-item">
                <label class="layui-form-label">是否微信文章</label>
                <div class="layui-input-block">
                    <input name="ssa_type" style="margin-top: 12px;" lay-filter="type" type="checkbox" value="2"  <?php if($info['ssa_type']==2)  echo 'checked="true"'?> lay-skin="switch" onchange="displayUrl();">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><font color="red">*</font> 活动标题</label>
                <div class="layui-input-block">
                    <input type="text" name="ssa_title" value="<?php echo $info['ssa_title']?>" lay-verify="ssa_title" autocomplete="off" placeholder="名称中请包括品牌、品名等" class="layui-input">
                </div>
            </div>
            
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">活动简介</label>
                <div class="layui-input-block">
                    <textarea name="ssa_sub_title" value="<?php echo $info['ssa_sub_title']?>"  placeholder=" <?php echo $info['ssa_sub_title']?$info['ssa_sub_title']:'活动简介，主要用于在文章缩略图下方显示，200字以内'?>" class="layui-textarea"></textarea>
                </div>
            </div>
       
        
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
                <input type="hidden" value='<?php echo $xqImgJson;?>' id="xcimgjson" name="xcimgjson" class="layui-input">
                <input type="hidden" value="<?php echo $isEdit;?>" id="isEdit" name="isEdit" class="layui-input">
                        
            <div class="layui-form-item">
                <label class="layui-form-label"><font color="red">*</font>活动内容</label>
                <div class="layui-input-block">
                <!-- 微信  -->
                <input type="text" style="display:none" name="ssa_weixin_url" value="<?php echo $info['ssa_weixin_url']; ?>" lay-verify="ssa_weixin_url" autocomplete="off" placeholder="微信文章链接URL" class="layui-input">
                <!-- 在线编辑器  -->
                    <script id="container" name="content"  type="text/plain" style="height:500px;border:none">
                           <?php echo htmlspecialchars_decode($info['ssa_content']);?>
                    </script>
                </div>
            </div>
            <div class="" style="margin-top: 15px;">
                  <label class="layui-form-label">活动日期</label>
                  <div class="layui-input-inline">
                    <input class="layui-input" name="ssa_start_time" placeholder="开始日" value="<?php if($info['ssa_start_time']) echo date('Y-m-d H:i:s',$info['ssa_start_time']); ?>" id="LAY_demorange_s">
                  </div>
                  <div class="layui-input-inline">
                    <input class="layui-input" name="ssa_end_time" placeholder="截止日"  value="<?php if($info['ssa_end_time']) echo date('Y-m-d H:i:s',$info['ssa_end_time']); ?>"  id="LAY_demorange_e">
                  </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">是否置顶</label>
                <div class="layui-input-block">
                    <input type="checkbox" style="margin-top: 12px;" name="ssa_is_require_top" value="1" <?php if($info['ssa_is_require_top']==1)  echo 'checked="true"'?> lay-skin="switch">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="id" value="<?php echo ($info['ssa_adv_id'])?>" lay-skin="switch">
                    <input  type="submit" class="layui-btn next-step" onclick="ajaxformsubmit()" lay-filter="demo"/>
                </div>
            </div>
            <script src="<?php echo RESOURCE_ROOT;?>/addons/common/webuploader/upload.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/cropper.min.js"></script>
        <script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/main.js"></script>
        <?php include page('seller_footer'); ?>
    </body>

<script type="text/javascript">
layui.use(['form','element','layer','laydate'], function() {
    var form = layui.form();
    var element = layui.element();
    var $ = layui.jquery,
        layer = layui.layer;
    //监听提交
//    form.on('submit(demo)', function(data) {
////        return false;
//    });
    var laydate = layui.laydate;
    var start = {
      min: laydate.now()
      ,max: '2099-06-16 23:59:59'
           ,format: 'YYYY-MM-DD hh:mm:ss' 
      ,istoday: false
      ,choose: function(datas){
        end.min = datas; //开始日选好后，重置结束日的最小日期
        end.start = datas; //将结束日的初始值设定为开始日
      }
    };
  
  var end = {
    min: laydate.now()
    ,max: '2099-06-16 23:59:59'
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
  document.getElementById('LAY_demorange_e').onclick = function(){
    end.elem = this
    laydate(end);
  }
});

//解析定位结果
    /* function ajaxImg() {
        $("#avatar-form").ajaxSubmit({
            type: "post",
            dataType: "json",
            success: function(ret){
                //返回提示信息       
                if(ret.errno==1){
                    $("#postadd_uoload_img").val( ret.data.pic_url );
                    $("#avatar_locate").attr("src", ret.data.pic_url );
                    $('#avatar-modal').modal('hide');
                }else{
                    layer.open({title: '提示',content: data.message});
                }
            }
        });
        return false;
    } */

function displayUrl(){
    $("input[name=ssa_weixin_url]").toggle();
    $("#container").toggle();
}

function ajaxformsubmit(){
	var image_list = $("input[name='xcimg[]']");
	var img = '';
	 $(image_list).each(function(){
		 img += this.value+",";                
	    });
    var fdata=  {
                ssa_title: $("input[name=ssa_title]").val(),
                id: $("input[name=id]").val(),
                ssa_sub_title: $("textarea[name=ssa_sub_title]").val(),
                ssa_thumb: img,
                ssa_type: $('input:checkbox[name="ssa_type"]:checked').val(), 
                ssa_weixin_url: $("input[name=ssa_weixin_url]").val(),  
                ssa_start_time: $("input[name=ssa_start_time]").val(),  
                ssa_end_time: $("input[name=ssa_end_time]").val(),  
                submit: "submit",  
                ssa_is_require_top:  $('input:checkbox[name="ssa_is_require_top"]:checked').val(), 
                ssa_content: UE.getEditor('container').getContent()
            };
    var url= '<?php echo $_GP['op']=='add'? mobile_url('store_shop_adv', array('op' => 'add')) :mobile_url('store_shop_adv', array('op' => 'edit'))  ?>';
    //console.log(fdata);return false;      
            $.post(url, fdata, function (ret) {
                if (ret.errno == 1) {
                    location.href = '<?php echo mobile_url('store_shop_adv', array('op' => 'index')) ?>';
                } else {
                    layer.alert(ret.message);
                }
                return false;
            })
}

$(function(){
    
    var ue = UE.getEditor('container',{
        autoHeight: false
    });
    var type=<?php echo $info['ssa_type']==2?2:1 ?>;
    if(type == 2){
        $("#container").hide();
        $("input[name=ssa_weixin_url]").show();
    }else{
//        $("#container").show();//默认显示这个
        //$("input[name=ssa_weixin_url]").hide();//默认显示这个
    }
//    ue.ready(function() {
//    //设置编辑器的内容
//        ue.setContent('<?php echo $info['ssa_content'] ?>');
//    });
})

//$(".next-step").click(function(){
//    var data =$('form').serialize();
//    
//})

</script>
</html>