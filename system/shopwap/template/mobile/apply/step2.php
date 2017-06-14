<?php  include page('h'); ?>
<link rel="stylesheet" type="text/css" href="http://cdn.bootcss.com/font-awesome/4.6.0/css/font-awesome.min.css">
<link type="text/css" href="<?php echo RESOURCE_ROOT;?>addons/common/bootstrap3/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/css/cropper.min.css" media="all" />
<link rel="stylesheet" href="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/css/main.css" media="all" />
<link rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/seller/css/apply.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/js/clipboard.min.js"></script>
<script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/script/jquery.form.js"></script>

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/html5shiv.min.js"></script>
<script src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/respond.min.js"></script>
<![endif]-->
<style>

</style>

<form class="layui-form" id="formtag" onsubmit="return false;" action="<?php echo mobile_url('store_shop', array('op' => 'shopRegisterStep3')) ?>">
   <input type="hidden" name="id"  value="<?php echo $_GP['id']; ?>" />
<!-- 商家入住 -->
<div class="layui-form form-area">
    <!-- 法人信息 -->
    <div class="layui-form-item">
        <label class="layui-form-label">法人姓名</label>
        <div class="layui-input-inline">
            <input type="text" name="ssi_owner_name" id="ssi_owner_name" value="<?php echo $info['ssi_owner_name']; ?>" lay-verify="title" autocomplete="off" placeholder="请输入法人姓名" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">身份证号</label>
        <div class="layui-input-inline">
            <input type="text" name="ssi_owner_shenfenhao" value="<?php echo $info['ssi_owner_shenfenhao']; ?>"  lay-verify="title" autocomplete="off" placeholder="请输入法人身份证号" class="layui-input">
        </div>
    </div>
    <!-- 法人手持身份证照 -->
    <div class="layui-form-item">
        <label class="layui-form-label">身份证照</label>
        <div class="clearfix">
            <div class="store-left">
                <?php if(!$info['ssi_shenfenzheng']){?>
                    <img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/step2_01.png">
               <?php  } else{ ?>
                    <img src=" <?php echo download_pic($info['ssi_shenfenzheng'],290,215,2)?>">
                <?php  } ?>
                
            </div>
            <div class="store-right">
                <div>1.需清晰展示五官和文字信息</div>
                <div>2.不可自拍，不可只拍身份证</div>
                <input type="file" name="ssi_shenfenzheng"  value=" <?php echo ($info['ssi_shenfenzheng'])?>">
            </div>
        </div>
    </div>
    <!-- 营业执照 -->
    <div class="layui-form-item">
        <label class="layui-form-label">营业执照</label>
        <div class="clearfix">
            <div class="store-left">
                 <?php if(!$info['ssi_yingyezhizhao']){?>
                     <img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/step2_02.png">
               <?php  } else{ ?>
                    <img src=" <?php echo download_pic($info['ssi_yingyezhizhao'],290,215,2)?>">
                <?php  } ?>
               
            </div>
            <div class="store-right">
                <div>1.需文字清晰、边框完整、露出国徽、真实有效；</div>
                <div>2.拍复印件需加盖印章，可用有效特许证代替。</div>
                <p style="color: #EF3F80;"><?php if($info['ssi_shenhe_yingye']==2) echo "审核不通过"?></p>
                <input type="file" name="ssi_yingyezhizhao" value=" <?php echo ($info['ssi_shenfenzheng'])?>" >
            </div>
        </div>
    </div>
    <!-- 许可证 -->
    <div class="layui-form-item">
        <label class="layui-form-label">许可证</label>
        <div class="clearfix">
            <div class="store-left">
                   <?php if(!$info['ssi_xukezheng']){?>
                <img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/step2_03.png">
               <?php  } else{ ?>
                    <img src=" <?php echo download_pic($info['ssi_xukezheng'],290,215,2)?>">
                <?php  } ?>
               
            </div>
            <div class="store-right">
                <div>1.需文字清晰、边框完整、真实有效；</div>
                <p style="color: #EF3F80;"><?php if($info['ssi_shenhe_xukezheng']==2) echo "审核不通过"?></p>
                <input type="file"  name="ssi_xukezheng" value=" <?php echo ($info['ssi_xukezheng'])?>" >
            </div>
        </div>
    </div>
    <!-- 店铺门脸图 -->
    <div class="layui-form-item">
        <label class="layui-form-label">店铺门脸图</label>
        <div class="clearfix">
            <div class="store-left">
                  <?php if(!$info['ssi_dianmian']){?>
              <img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/step2_04.png">
               <?php  } else{ ?>
                    <img src=" <?php echo download_pic($info['ssi_dianmian'],290,215,2)?>">
                <?php  } ?>
                
            </div>
            <div class="store-right">
                <div>需拍出完整门匾、门框（建议正对门店2米处拍摄）</div>
                <p style="color: #EF3F80;"><?php if($info['ssi_shenhe_dianmian']==2) echo "审核不通过"?></p>
                <input type="file"  name="ssi_dianmian" value=" <?php echo ($info['ssi_dianmian'])?>">
            </div>
        </div>
    </div>
    <!-- 店内环境图 -->
    <div class="layui-form-item">
        <label class="layui-form-label">店内环境图</label>
        <div class="clearfix">
            <div class="store-left">
                  <?php if(!$info['ssi_diannei']){?>
              <img src="<?php echo RESOURCE_ROOT;?>addons/seller/images/step2_05.png">
               <?php  } else{ ?>
                    <img src=" <?php echo download_pic($info['ssi_diannei'],290,215,2)?>">
                <?php  } ?>
                
            </div>
            <div class="store-right">
                <div>需真实反映店内环境（收银台、货架桌椅等）</div>
                <input type="file"  name="ssi_diannei"  value=" <?php echo ($info['ssi_diannei'])?>">
            </div>
        </div>
    </div>
    
    <div class="layui-form-item ">
            <div class="layui-input-block">
                <input type="submit" class="layui-btn next-step" lay-submit="" lay-filter="demo" ></input>
            </div>
    </div>
</div>
    </form>>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/bootstrap3/js/bootstrap.min.js"></script>
<script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/cropper.min.js"></script>
<script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/recouse/cropper/js/main.js"></script>
<script>
layui.use('form',function(){
    var form = layui.form();
      form.on('submit(demo)', function (data) {
            layer.load(3);
            $("#formtag").ajaxSubmit({
                type: "post",
                url: "<?php echo mobile_url('store_shop', array('op' => 'shopRegisterStep3','name'=>'seller')) ?>",
                dataType: "json",
                success: function(ret){
                    //返回提示信息       
                    if(ret.errno==1){
                        layer.open({
                            content: '我们已经收到您提交的申请，服务人员将在24小时内处理，请您耐心等待。',
                            yes: function(index, layero){
                              layer.close(index); //如果设定了yes回调，需进行手工关闭
                              location.href = '<?php echo mobile_url('index', array('name' => 'shopwap')) ?>';
                            }
                        });        
                    }else{
                        layer.open({title: '提示',content: ret.message});
                    }
                    layer.closeAll('loading');
                }
            });
            return false;
        });
})
$(function(){
    var type_index = 0;
    //一级分类
    $("body").on("click",'.parent-type .parent-type-val',function(){
        //把子类的 默认被选中清除掉
        $(".choosetype-list .child-type").each(function(){
            $(this).find("li").removeClass("li-check");
        });
        type_index = $(this).parent("li").index();
        $(this).parent("li").addClass("li-check").siblings("li").removeClass("li-check");
        if( $(".child-type").eq(type_index).length == 0 ){
            $(".child-type").removeClass("type-show");
        }else{
            $(".child-type").eq(type_index).addClass("type-show").siblings().removeClass("type-show");
        }
    });
    //二级分类
    $("body").on("click",".child-type .parent-type-val",function(){
        $(".child-type li").removeClass("li-check");
        $(this).parent("li").addClass("li-check");
    });

});
</script>
<?php  include page('f'); ?>