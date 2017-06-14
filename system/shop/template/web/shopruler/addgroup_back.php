<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/css/layui.css" media="all" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/seller/plugins/layui/layui.js"></script>
<style>
    .choose-all{
        margin-top: 15px;
        color: #333;
    }
    body{
        font-family: '微软雅黑';
    }
    .choose-box label{
        font-weight: 400;
        cursor: pointer;
    }
    .choose-all-1{
        float: left;
        width: 110px;
        text-align: right;
        padding:5px 15px 5px 0; 
        box-sizing:border-box;
        cursor: auto;
    }
    .choose-box input[type="checkbox"]{
        margin-right: 5px;
    }
    .choose-all-2{
        padding: 5px 0;
        margin-left: 110px;
        border-bottom: 1px #ddd dotted;
    }
    .left-list{
        float: left;
        width: 120px;
    }
    .right-list{
        width: 80%;
        float: left;
    }
    .right-list li{
        float: left;
        width: 120px;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }
</style>

    <blockquote style="margin: 15px 35px 20px;" class="layui-elem-quote"><?php if(empty($_GP['group_id'])){ echo '添加'; }else{ echo '编辑';} ?>角色  <span style="margin-left: 20px;font-size: 14px;color: red">权限是逆向思维，默认都有权限，打钩了反而是需要限制操作的</span></blockquote>
    <?php if(empty($_GP['group_id'])){   ?>
    <form action="<?php echo web_url('shopruler',array('op'=>'addgroup'));?>" method="post">
    <?php }else{  ?>
    <form action="<?php echo web_url('shopruler',array('op'=>'editgroup'));?>" method="post">
    <?php } ?>
        <div class="layui clearfix">
            <div class="layui-form">
                <label class="layui-form-label">角色名称 </label>
                <div class="layui-input-inline" style="width: 300px;">
                    <input type="text" name="group_name" placeholder="角色名称" autocomplete="off" class="layui-input" lay-verify="required" value="<?php  echo $sellergroup['group_name'];?>">
                </div>
            </div><br/>
            <div class="layui-form">
                <label class="layui-form-label">角色简介</label>
                <div class="layui-input-inline" style="width: 300px;">
                    <input type="text" name="description" placeholder="对角色的简单简介" autocomplete="off" value="<?php  echo $sellergroup['description'];?>" class="layui-input">
                </div>
            </div>
        </div>
        <div class="layui-tab layui-tab-card" style="margin-left: 50px;margin-right: 30px;">
            <ul class="layui-tab-title">
                <li class="layui-this">基础权限</li>
                <li>其他权限</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">

                    <?php foreach($menulist as $first_menu){  $p1_check = '';if(in_array($first_menu['main']['id'],$sellergroup['rule'])){ $p1_check="checked";} ?>
                        <!-- 全选 -->
                        <div class="each_block">
                            <div class="choose-box choose-all clearfix">
                                <div class="choose-all-1"><b><?php echo $first_menu['main']['rule_name']; ?></b></div>
                                <div class="choose-all-2">
                                    <label><input type="checkbox" name="rule_id[]" class="checkall p1" value="<?php echo $first_menu['main']['id']; ?>" <?php echo $p1_check; ?>>全选</label>
                                </div>
                            </div>
                            <div class="choose-box">
                                <?php foreach($first_menu['child'] as $second_menu){ $p2_check = '';if(in_array($second_menu['main']['id'],$sellergroup['rule'])){ $p2_check="checked";} ?>
                                    <div class="choose-all-1">&nbsp;</div>
                                    <div class="choose-all-2 clearfix box_p2">
                                        <label class="left-list"><input type="checkbox" value="<?php echo $second_menu['main']['id']; ?>" class="p2" name="rule_id[]" <?php echo $p2_check;?>><b><?php echo $second_menu['main']['rule_name']; ?></b></label>
                                        <ul class="right-list clearfix">
                                            <?php foreach($second_menu['child'] as $third_menu){  $p3_check = '';if(in_array($third_menu['main']['id'],$sellergroup['rule'])){ $p3_check="checked";} ?>
                                                <li><label><input type="checkbox" name="" value="<?php echo $third_menu['main']['id']; ?>" class="p3" <?php echo $p3_check;?>><?php echo $third_menu['main']['rule_name']; ?></label></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                    <?php } ?>
                </div>
                <div class="layui-tab-item layui-form">
                    <?php foreach($sellerActRule as $key => $vale){  ?>
                        <div class="layui clearfix">
                            <div class="layui-form-item">
                                <label class="layui-form-label"><?php echo $vale; ?></label>
                                <div class="layui-input-block">
                                    <input type="radio" name="other_rule[<?php echo $key ?>]" value="1" title="允许"  <?php if($sellergroup['other_rule'][$key] == 1){ echo "checked";} ?>> &nbsp;
                                    <input type="radio" name="other_rule[<?php echo $key ?>]" value="-1" title="不允许"  <?php if($sellergroup['other_rule'][$key] == -1 || empty($sellergroup['other_rule'][$key])){ echo "checked";} ?>>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <br/>
        <div class="layui clearfix">
            <div class="layui-form">
                <label class="layui-form-label"></label>
                <div class="layui-input-inline">
                    <input type="hidden" name="do_add" value="1">
                    <input type="hidden" name="group_id" value="<?php echo $_GP['group_id']; ?>">
                    <button type="submit" class="layui-btn"  lay-filter="demo1">确认<?php if(empty($_GP['group_id'])){ echo '添加'; }else{ echo '编辑';} ?></button>
                </div>
            </div><br/>
        </div>
    </form>
<?php  include page('footer');?>


<script type="text/javascript">
//注意：选项卡 依赖 element 模块，否则无法进行功能性操作
layui.use(['element','form'], function(){
    var element = layui.element();
    var form = layui.form();
    //…
});
$(function(){
    $(".checkall").on("click",function(){
        if($(this).prop("checked")){
            $(this).closest('.each_block').find(".choose-all-2").find("input").prop("checked",true);
        }else{
            $(this).closest('.each_block').find(".choose-all-2").find("input").prop("checked",false);
        }
    });
    $(".left-list").on("click",function(){
        var flag = $(this).find("input").prop("checked");

        if( flag == true ){
            $(this).siblings(".right-list").find("input").prop("checked",true);
            //父类 p1打钩
            $(this).closest('.each_block').find(".p1").prop("checked",true);
        }else{

            $(this).siblings(".right-list").find("input").prop("checked",false);
            //如果p2全部没有打钩的  父类 p1就去掉购
            var closeparent = true;
            $(this).parents(".choose-box").find(".p2").each(function(){
                if(this.checked){
                    closeparent = false;
                }
            })

            if(closeparent){
                $(this).closest('.each_block').find(".p1").prop("checked",false);
            }
        }
    });
    $(".p3").on("click",function(){
        if(this.checked){
            $(this).closest('.box_p2').find('.p2').prop("checked",true);
            $(this).closest('.each_block').find('.p1').prop("checked",true);
        }else{
            //如果p3全部  是没有打钩的  父类 p2就去掉沟
            var closeparent = true;
            $(this).parents(".right-list").find(".p3").each(function(){
                if(this.checked){
                    closeparent = false;
                }
            });
            if(closeparent){
                $(this).closest('.box_p2').find(".p2").prop("checked",false);
            }
            //如果p2全部没有打钩的  父类 p1就去掉购
            closeparent = true;
            $(this).parents(".each_block").find(".p2").each(function(){
                if(this.checked){
                    closeparent = false;
                }
            });
            if(closeparent){
                $(this).closest('.each_block').find(".p1").prop("checked",false);
            }
        }

    });
})
</script>
</html>