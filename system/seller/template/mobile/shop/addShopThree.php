<?php defined('SYSTEM_IN') or exit('Access Denied'); ?>
<?php include page('seller_header'); ?>
<!--不做页面提交，用ajaxsubmit提交和控制回调-->
<script type="text/javascript"  src="<?php echo WEBSITE_ROOT;?>themes/default/__RESOURCE__/script/jquery.form.js"></script>

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
    .left-list,.right-list{
        float: left;
        width: 50%;
        width: 300px;
        height: 200px;
        overflow: auto;
        border: 1px solid #ddd;
        padding: 10px;
        box-sizing:border-box;
    }
    .right-list{
        margin-left: 10px;
    }
    .right-list li{
        cursor: pointer;
        display: none;
        line-height: 25px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        padding: 3px 5px;
        box-sizing:border-box;
        border: 1px solid #fff;
    }
    .left-list li{
        cursor: pointer;
        line-height: 25px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        padding: 3px 5px;
        box-sizing:border-box;
        border: 1px solid #fff;
    }
    .left-list li.left-list-li-check,.right-list li.left-list-li-check{
        background-color: #d9edf7;
        color: #4e90b5;
        border: 1px solid #bee9f1;
    }
</style>
<body style="padding:10px;" class="step2">
    <blockquote class="layui-elem-quote">店铺资质信息<span class="child-stop-info"></span></blockquote>
    <form class="layui-form" id="formtag" onsubmit="ajaxImg();return false;" action="<?php echo mobile_url('store_shop', array('op' => 'shopRegisterStep3')) ?>">
        <input type="hidden" name="id"  value="<?php echo $_GP['id']; ?>" />
        <!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label"><font color="red">*</font> 法人姓名</label>
            <div class="layui-input-inline">
                <input type="text" name="ssi_owner_name" id="ssi_owner_name" lay-verify="title" autocomplete="off" placeholder="店铺名" class="layui-input">
            </div>
        </div>
        <!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label">身份证号</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input"  name="ssi_owner_shenfenhao" id="ssi_owner_shenfenhao" placeholder="身份证号" >
            </div>
        </div>
        <!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label">法人手持身份证照</label>
            <div class="layui-input-inline">
                <input type="file" id="ssi_shenfenzheng"  name="ssi_shenfenzheng" placeholder="法人手持身份证照" >
            </div>
        </div><!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label">营业执照</label>
            <div class="layui-input-inline">
                <input type="file"   id="ssi_yingyezhizhao"  name="ssi_yingyezhizhao" placeholder="营业执照" >
            </div>
        </div><!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label">许可证</label>
            <div class="layui-input-inline">
                <input type="file"   name="ssi_xukezheng" id="ssi_xukezheng" placeholder="许可证" >
            </div>
        </div>
        <!-- 分隔符 -->
        <div class="layui-form-item">
            <label class="layui-form-label">店铺门脸图</label>
            <div class="layui-input-inline">
                <input type="file"  id="ssi_dianmian" name="ssi_dianmian" placeholder="店铺门脸图" >
            </div>
        </div>
      
        <div class="layui-form-item">
            <label class="layui-form-label">店内环境图</label>
             <div class="layui-input-inline">
                <input type="file" name="ssi_diannei" id="ssi_diannei" placeholder="实体店" >
            </div>
        </div>
     
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="submit" class="layui-btn next-step" lay-submit="" lay-filter="demo" ></input>
            </div>
        </div>
    </form>
<?php include page('seller_footer'); ?>
</body>

<script type="text/javascript">
    layui.use(['form', 'element', 'layer',], function () {
        var form = layui.form();
        var element = layui.element();
        var $ = layui.jquery,
                layer = layui.layer;
        //监听提交
        form.on('submit(demo)', function (data) {
            $("#formtag").ajaxSubmit({
                type: "post",
                url: "<?php echo mobile_url('store_shop', array('op' => 'shopRegisterStep3')) ?>",
                dataType: "json",
                success: function(ret){
                    //返回提示信息       
                    if(ret.errno==1){
                        layer.open({
                            content: '我们已经收到您提交的申请，服务人员将在24小时内处理，请您耐心等待。',
                            yes: function(index, layero){
                              layer.close(index); //如果设定了yes回调，需进行手工关闭
                              location.href = '<?php echo mobile_url('main', array('name' => 'seller')) ?>';
                            }
                        });        
                    }else{
                        layer.open({title: '提示',content: data.message});
                    }
                }
            });
            return false;
        });
    });


</script>

</html>