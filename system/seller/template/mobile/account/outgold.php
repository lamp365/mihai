<!DOCTYPE html>
<html>
	<head>
        <?php include page('seller_header');?>
        <style type="text/css">
            .zhifubao,.weixin,.bank,.banks-area{
                display: none;
            }
            .zhifubao li i{
                color: #a6a6a6;
                margin-left: 10px;
            }
            .zhifubao li i.checked{
                color: #43ae36;
            }
            .zhifubao li.checked,.zhifubao li.checked i{
                color: #43ae36;
            }
            .zhifubao li{
                padding-right: 10px;
                cursor: pointer;
                margin-right: 10px;
                float: left;
                border-right: 1px dotted #8b8b8b;
            }
            .zhifubao li:last-child{
                border-right:none;
            }
            .weixin li i{
                color: #a6a6a6;
                margin-left: 10px;
            }
            .weixin li i.checked{
                color: #43ae36;
            }
            .weixin li.checked,.weixin li.checked i{
                color: #43ae36;
            }
            .weixin li{
                padding-right: 10px;
                cursor: pointer;
                margin-right: 10px;
                float: left;
                border-right: 1px dotted #8b8b8b;
            }
            .weixin li:last-child{
                border-right:none;
            }
            .bank li i{
                color: #a6a6a6;
                margin-left: 10px;
            }
            .bank li i.checked{
                color: #43ae36;
            }
            .bank li.checked,.bank li.checked i{
                color: #43ae36;
            }
            .bank li{
                padding-right: 10px;
                cursor: pointer;
                margin-right: 10px;
                float: left;
                border-right: 1px dotted #8b8b8b;
            }
            .bank li:last-child{
                border-right:none;
            }
            .nav-tabs li a{
                padding-left: 18px;
                padding-right: 18px;
                text-align: center;
            }
        </style>
	</head>
	<body style="padding:10px;">
    	<blockquote class="layui-elem-quote">提现<span class="child-stop-info">觅海联手支付宝共同打击无真实交易背景的虚假交易，银行卡转账套现或洗钱等禁止的交易行为，否则充值款项将不能提现。</span></blockquote>
        <ul class="nav nav-tabs" >
            <li style="" <?php  if($_GP['op'] == 'outgold' ) { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('account',array('op'=>'outgold'))?>">店铺提现</a></li>
            <li style="" <?php  if($_GP['op'] == 'record') { ?> class="active"<?php  } ?>><a href="<?php  echo mobile_url('account',  array('op' => 'record'))?>">提现记录</a></li>
        </ul>
        <div class="layui-form-mid layui-word-aux" style="margin-left: 27px;">每次提款手续费用<?php echo $out_info['draw_money']; ?>元</div><br/>
        <form class="layui-form" action="<?php echo mobile_url('account',array('op'=>'do_outgold')) ?>" name="" method="post">
            <div class="layui-form-item">
                <label class="layui-form-label">可提现金额</label>
                <div class="layui-input-block">
                    <div class="available-money"><?php echo $out_info['recharge_money'];?></div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">提现到</label>
                <div class="layui-input-inline">
                    <select lay-filter="choose_bank">
                        <option value="">选择类型</option>
                        <option value="bank">银行卡</option>
                        <option value="zhifubao">支付宝</option>
                    </select>
                    <input type="hidden" name="bank_id" value="" id="bank_id">
                </div>
            </div>
            <div class="layui-form-item banks-area">
                <label class="layui-form-label">&nbsp;</label>
                <div class="layui-input-block">
                    <ul class="zhifubao clearfix">
                        <?php foreach($bank_list['ali'] as $one_zf){  ?>
                        <li bankId="<?php echo $one_zf['id'];?>" class=""><?php echo $one_zf['bank_number'];?><i class="fa fa-check-circle" aria-hidden="true"></i></li>
                        <?php } ?>
                    </ul>
                    <ul class="bank clearfix">
                        <?php foreach($bank_list['bank'] as $one_bank){  ?>
                        <li bankId="<?php echo $one_bank['id'];?>" class=" "><?php echo $one_bank['bank_name'].' 尾号'.$one_bank['bank_bumber_wei'].' '.$one_bank['card_kind'];?><i class="fa fa-check-circle" aria-hidden="true"></i></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">提现金额</label>
                <div class="layui-input-inline">
                    <input type="text" name="money"  lay-verify="required" autocomplete="off" placeholder="请输入提现金额" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">短信验证码</label>
                <div class="layui-input-inline">
                    <input type="text" name="mobilecode" lay-verify="required" placeholder="请输入短信验证码" autocomplete="off" class="layui-input">
                </div>
                <span class="btn btn-md btn-info"  onclick="send_phonecode(this)">获取验证码</span>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit="" lay-filter="sub_btn">立即提现</button>
                </div>
            </div>
        </form>
	</body>

<script type="text/javascript">
layui.use(['element','form'], function(){
  var $ = layui.jquery
    ,element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块
    var form = layui.form();
    form.on("select(choose_bank)",function(data){
        var val = data.value;
        $(".banks-area").show();
        $(".banks-area ul").hide();
        $("."+val).show();
        $("#bank_id").val('');
    })
});

$(".zhifubao li").on("click",function(){
    if(!$(this).hasClass("checked")){
        $(this).addClass("checked");
        $(this).siblings("li").removeClass("checked");
        $("#bank_id").val($(this).attr("bankId"));
    }
});

$(".bank li").on("click",function(){
    if(!$(this).hasClass("checked")){
        $(this).addClass("checked");
        $(this).siblings("li").removeClass("checked");
        $("#bank_id").val($(this).attr("bankId"));
    }
});

function send_phonecode(obj){
    var number = 120;
    var url = "<?php echo mobile_url('mobilecode',array('op'=>'index')); ?>";
    var parame = {action:'cash'};
    $.post(url,parame,function(data){
        if(data.errno == 1){
            //倒计时120秒
            $(obj).prop('disabled',true);
            var daojishi = setInterval(function(){
                if( number == 0 ) {
                    clearInterval(daojishi);
                    $(obj).text('获取验证码');
                    $(obj).prop('disabled',false);
                }else{
                    --number;
                    $(obj).text('发送（'+number+'s）');
                    $(obj).prop('disabled',true);
                }
            },1000);
        }else{
            layer.open({
                title: '提示'
                ,content: data.message
            });
        }
    },"json");

}
</script>
</html>