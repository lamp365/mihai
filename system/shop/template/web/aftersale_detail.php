<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<link href="<?php echo RESOURCE_ROOT;?>addons/common/fanda/css/lanrenzhijia.css" type="text/css" rel="stylesheet" />
<script src="<?php echo RESOURCE_ROOT;?>addons/common/fanda/js/jquery.min.js"></script>
<script src="<?php echo RESOURCE_ROOT;?>addons/common/fanda/js/jquery.imgbox.pack.js"></script>
<style>
    .imgbox-wrap img{cursor: pointer;}
</style>
<div style="padding: 0 15px 15px 15px;overflow: hidden">
    <h3 class="header smaller lighter blue"><?php echo $title;?>详情</h3>

    <input type="hidden" name="order_id" value="<?php  echo $_GP['order_good_id'];?>">
    <div style="background: #E8E8E8;margin-top: 10px;border: 1px solid #c6c6c6;line-height: 24px;padding: 10px 0px;border-radius: 8px;">
        <div class="row">
            <div class="col-sm-1" style="text-align: right">价格：</div>
            <div class="col-sm-9"><?php echo $order['price'].' x '.$order['total'];?></div>
        </div>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">税率：</div>
            <div class="col-sm-9"><?php echo $order['taxprice'];?></div>
        </div>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">原因：</div>
            <div class="col-sm-9"><?php echo $afterSale['reason'];?></div>
        </div>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">说明：</div>
            <div class="col-sm-9"><?php echo $afterSale['description'];?></div>
        </div>
        <?php if(!empty($picArr)){ ?>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">图片：</div>
            <div class="col-sm-9 piclist">
                <?php foreach($picArr as $picurl){ ?>
                    <a href="<?php echo download_pic($picurl,600,600);?>" class="onepic"><img src="<?php echo $picurl;?>" style="width: 70px;height: 70px;border: 1px solid #C6C6C6;background: #ffffff;padding: 1px;"/></a>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">时间：</div>
            <div class="col-sm-9"><?php echo $afterSale['createtime'];?></div>
        </div>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">平台处理：</div>
            <div class="col-sm-9">
                <?php if($order['status'] == -2){?>
                    <a class="btn btn-default btn-xs" href="javascript:;" disabled="disabled" >协商处理</a> <a class="btn btn-default btn-xs" href="javascript:;" disabled="disabled" >立即处理</a> &nbsp;<span>(买家已经取消申请)</span>
                <?php }else if($order['status']== 1){ ?>
                    <?php if(isHasPowerToShow('shop','order','aftersale_dialog')) { ?>
                    <a class="btn btn-danger btn-xs dialog" href="javascript:;" >协商处理</a>
                    <?php }else{ echo ' <a class="btn btn-default btn-xs" href="javascript:;" disabled="disabled">协商处理</a>';} ?>
                    <?php  if(isHasPowerToShow('shop','order','aftersale_chuli')) {  ?>
                        <a class="btn btn-danger btn-xs" href="javascript:;" data-toggle="modal" data-target="#myModal" >立即处理</a>
                    <?php }else{ echo '<a class="btn btn-default btn-xs" href="javascript:;" disabled="disabled">立即处理</a>';} ?>
                <?php } else{ echo "【已处理】".$afterSale['admin_explanation']; } ?>
            </div>
        </div>
        <?php if(!empty($afterSale['refund_price'])){ ?>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">退款金额：</div>
            <div class="col-sm-9"><span style="color: red"><?php echo $afterSale['refund_price'];?></span></div>
        </div>
        <?php } ?>

        <?php if(!empty($delivery_corp)){ ?>
        <div class="row">
            <div class="col-sm-1" style="text-align: right">买家发货：</div>
            <div class="col-sm-9">[<?php echo $delivery_name;?>] <a target="_blank" href="http://m.kuaidi100.com/index_all.html?type=<?php echo $delivery_corp;?>&postid=<?php echo $delivery_no;?>#input"><?php echo $delivery_no;?></a></div>
        </div>
        <?php } ?>

        <?php if($order['status']==2){ ?>
            <?php if($_GP['type']=='money'){  ?>
                <div class="row">
                    <div class="col-sm-1" style="text-align: right"><b>财务退款：</b></div>
                    <div class="col-sm-9">
                        <?php  if(isHasPowerToShow('shop','order','sureBackMoney')) {  ?>
                        <span class="btn btn-danger btn-xs sure_money">确定退款</span>
                        <?php }else echo '<span class="btn btn-default btn-xs" disabled="disabled">确定退款</span>'; ?>
                    </div>
                </div>
            <?php }else{ ?>
                <div class="row">
                    <div class="col-sm-1" style="text-align: right"><b>财务退款：</b></div>
                    <div class="col-sm-9">买家还没退货</div>
                </div>
            <?php } ?>
        <?php }?>

        <?php if($order['status']==3){ ?>
                <div class="row">
                    <div class="col-sm-1" style="text-align: right"><b>财务退款：</b></div>
                    <div class="col-sm-9">
                        <?php  if(isHasPowerToShow('shop','order','sureBackMoney')) {  ?>
                            <span class="btn btn-danger btn-xs sure_money">确定退款</span>
                        <?php }else echo '<span class="btn btn-default btn-xs" disabled="disabled">确定退款</span>'; ?>
                    </div>
                </div>
        <?php }?>

        <?php if($order['status']==4){ ?>
            <div class="row">
                <div class="col-sm-1" style="text-align: right"><b>财务退款：</b></div>
                <div class="col-sm-9" style="color: red;font-weight: bolder;">已经退款</div>
            </div>
        <?php }?>

    </div>

    <h3>协商记录</h3>
    <div style="background: #E8E8E8;margin-top: 10px;border: 1px solid #c6c6c6;line-height: 24px;padding: 10px 20px;border-radius: 8px;">
        <div class="dialog_list">
              <?php  if(!empty($afterSaleDialog)) {
                  foreach ($afterSaleDialog as $row) {
                      if ($row['role'] == 1)
                          $master = '平台';
                      else
                          $master = '买家';

                      echo "<p>时间：{$row['createtime']} [<span style='color:red;'>{$master}</span>]</p>";
                      echo "<p>内容：{$row['content']}</p>";

                  }
              }else {
                    echo "<p class='nodata'>暂无</p>";
              }
            ?>
        </div>
        <?php if($order['status'] == 1 && isHasPowerToShow('shop','order','aftersale_dialog')){ ?>
        <div class="row" style="margin-top: 15px;">
            <form id="from_xieshan" action="<?php echo web_url('order',array('op'=>'aftersale_dialog','aftersales_id'=>$afterSale['aftersales_id']));?>" method="post">
                <div class="pull-left" style="width: 58px;text-align: right">协商：</div>
                <div class="col-sm-4 pull-left"><input type="text" name="content" class="form-control" id="xieshang" placeholder="请输入协商内容" value=""></div>
                <div class="col-sm-2 pull-left"><button type="button" class="btn btn-sm btn-primary" id="dialog_btn">确定</button></div>
            </form>
           </div>
        <?php } ?>
    </div>


    <h3><?php echo $title;?>记录</h3>
    <div style="background: #E8E8E8;margin-top: 10px;border: 1px solid #c6c6c6;line-height: 24px;padding: 10px 20px;border-radius: 8px;">
        <?php if(!empty($afterSaleLog)){ foreach($afterSaleLog as $row) {
                echo "<p>时间：{$row['createtime']} [<span style='color:red;'>{$statusArr[$row['status']]}</span>]</p>";
                echo "<p>{$row['title']}</p>";

        }}?>
    </div>

    <br/>
    <br/>
    <a class="btn btn-primary btn-sm" href="<?php echo web_url('order',array('op'=>'detail','id'=>$orderid));?>">返回订单详情</a>
</div>

<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form role="form" action="<?php echo web_url('order',array('op'=>'aftersale_chuli','order_good_id'=>$_GP['order_good_id'],'type'=>$_GP['type'],'orderid'=>$_GP['orderid'])); ?>" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">商家处理</h4>
            </div>
            <div class="modal-body">
                <div class="checkbox">
                    <label><input type="radio" checked="checked" name="status" value="2" class="tongyi">同意</label>
                    <label> <input type="radio" name="status" value="-1" class="tongyi">不同意</label>
                </div>
                <div class="form-group">
                    <label for="jine">退款金额</label>
                    <input type="text" name="refund_price" class="form-control" id="jine" placeholder="请输入金额" value="<?php echo $order['price']*$order['total']+$order['taxprice'];?>">
                </div>
                <div class="form-group">
                    <label for="name">处理说明(如告知地址)</label>
                    <input type="text" name="admin_explanation" class="form-control" id="name" placeholder="请输入处理说明">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="submit" name="admin_chuli" value="sub" class="btn btn-primary">确认处理</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>


<script language='javascript'>
$(".dialog").click(function(){
    $("#xieshang").focus();
})
$("#dialog_btn").click(function(){
    if($("#xieshang").val() == ''){
        alert('请输入要协商的内容');
    }else{
        $("#from_xieshan").submit();
    }
})

    $(".sure_money").click(function(){
        if(confirm("确认退款么？")){
            var url = "<?php echo web_url('order',array('op'=>'sureBackMoney','order_good_id'=>$_GP['order_good_id'],'order_id'=>$_GP['orderid'],'type'=>$_GP['type']));?>";
            window.location.href = url;
        }
    })

    $(function(){
        $(".piclist .onepic").imgbox();
    })
</script>