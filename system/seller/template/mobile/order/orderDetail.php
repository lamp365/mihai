<!DOCTYPE html>
<html>
<head>
<?php include page('seller_header');?>
<style>
  .order-detail-list li{
    float: left;
    width: 220px;
    line-height: 30px;
    margin-bottom: 10px;
}
.detail-title{

}
.detail-value{
    font-size: 12px;
    color: #999;
}
.order-detail-list2 li{
    width: auto;
    margin-right: 25px;
}
.modify{
    margin-left: 15px;
}
.form-group{
  overflow: hidden;
}
.no-padding-left{
  text-align: right;
}
.layui-textarea{
  padding: 6px 10px;
}
</style>
</head>
<body style="padding:10px;">
<div class="layui-form">
    <blockquote class="layui-elem-quote">订单基本信息</blockquote>
    <div>
        <ul class="order-detail-list clearfix">
            <li>
                <span class="detail-title">下单时间:</span>
                <span class="detail-value"><?php echo date('Y-m-d H:i:s',$info['createtime']); ?></span>
            </li>
            <li>
                <span class="detail-title">支付时间:</span>
                <span class="detail-value"><?php if (!empty($info['paytime'])) echo date("Y-m-d H:i:s",$info['paytime']);?></span>
            </li>
            <li>
                <span class="detail-title">订单编号:</span>
                <span class="detail-value"><?php echo $info['ordersn']; ?></span>
            </li>
            <li>
                <span class="detail-title">订单状态:</span>
                <span class="detail-value"><?php echo $info['status_name'];?></span>
            </li>
            <li>
                <span class="detail-title">总金额:</span>
                <span class="detail-value"><?php  echo ($info['price']+$info['balance_sprice']);?></span>
            </li>
            <li>
                <span class="detail-title">现金支付:</span>
                <span class="detail-value"><?php echo $info['price'];?></span>
            </li>
            <li>
                <span class="detail-title">支付方式:</span>
                <span class="detail-value">微信支付</span>
            </li>
            <li>
                <span class="detail-title">配送方式:</span>
                <span class="detail-value"><?php echo $info['expresscom'];?></span>
            </li>
        </ul>
    </div>
    <blockquote class="layui-elem-quote">收货人信息<button class="layui-btn layui-btn-small modify" lay-submit="" lay-filter="">修改信息</button></blockquote>
    <div>
        <ul class="order-detail-list clearfix">
            <li>
                <span class="detail-title">收货人姓名:</span>
                <span class="detail-value"><?php  echo $info['address_realname']?></span>
            </li>
            <li>
                <span class="detail-title">联系电话:</span>
                <span class="detail-value"><?php echo $info['address_mobile']?></span>
            </li>
        </ul>
        <ul class="order-detail-list order-detail-list2 clearfix">
            <li>
                <span class="detail-title">收货地址:</span>
                <span class="detail-value"><?php  echo $info['address_province'];?><?php  echo $info['address_city'];?><?php  echo $info['address_area'];?><?php  echo $info['address_address'];?></span>
            </li>
            <li>
                <span class="detail-title">订单备注:</span>
                <span class="detail-value"><?php echo $info['remark'];?></span>
            </li>
        </ul>
    </div>
    <!-- 修改收货人信息弹出框 -->
<form action="<?php echo mobile_url('order',array('name'=>'seller','op'=>'modifyaddress','id'=>$info['id'])); ?>" method="post" enctype="multipart/form-data" id="update_use_form" class="form-horizontal" >
    <div class="address-modal modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">修改收货人信息</h4>
          </div>
          <div class="modal-body">
              <div class="form-group">
                <label class="col-sm-3 control-label no-padding-left layui-form-label"> 姓名：</label>
                <div class="col-sm-8">
                  <input type="text" name="address_realname" class="span5 layui-input" value="<?php  echo $info['address_realname']?>">
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label no-padding-left layui-form-label"> 联系电话：</label>
                <div class="col-sm-8">
                  <input type="text" name="address_mobile" class="span5 layui-input" id="address_mobile" value="<?php echo $info['address_mobile']?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label no-padding-left layui-form-label" > 省份：</label>
                <div class="col-sm-8">
                  <input type="text" name="address_province" class="span5 layui-input" value="<?php  echo $info['address_province'];?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label no-padding-left layui-form-label"> 城市：</label>
                <div class="col-sm-8">
                  <input type="text" name="address_city" class="span5 layui-input" value="<?php  echo $info['address_city'];?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label no-padding-left layui-form-label"> 地区：</label>
                <div class="col-sm-8">
                  <input type="text" name="address_area" class="span5 layui-input" value="<?php  echo $info['address_area'];?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label no-padding-left layui-form-label"> 地址：</label>
                <div class="col-sm-8">
                  <input type="text" name="address_address" class="span5 layui-input" value="<?php  echo $info['address_address'];?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label no-padding-left layui-form-label"> 订单备注：</label>
                <div class="col-sm-8">
                  <textarea placeholder="买家订单备注" class="layui-textarea"><?php echo $info['remark'];?></textarea>
                </div>
              </div>
            </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <button type="button" id="update_use_info" class="btn btn-primary">保存</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</form>
    <table class="layui-table">
      <thead>
        <tr>
          <th>管理员</th>
          <th>订单操作日志</th>
          <th>操作时间</th>
        </tr> 
      </thead>
      <tbody>
      <?php if (is_array($info['recoder'])){ foreach ($info['recoder'] as $key=>$v){?>
        <tr>
          <td><?php echo $v[0];?></td>
          <td><?php echo $v[1];?></td>
          <td><?php echo date("Y-m-d H:i:s",$v[2]);?></td>
        </tr>
        <?php }}?>
      </tbody>
    </table>
    <table class="layui-table">
      <thead>
        <tr>
          <th>序号</th>
          <th>商品标题</th>
          <th>商品规格</th>
          <th>货号</th>
          <th>成交价</th>
          <th>数量</th>
          <th>状态</th>
          <th>操作</th>
        </tr> 
      </thead>
      <tbody>
		<?php  if(is_array($info['goods'])) { foreach($info['goods'] as $key=>$goods) { ?>
        <tr>
          <td><?php echo $key+1;?></td>
          <td><?php echo $goods['title'];?></td>
          <td>
            <?php 
                if ($goods['spec_key_name']){
                    $spec_key_name = json_decode($goods['spec_key_name'],1);
                    $spec_key_name = array_values($spec_key_name);
                    echo implode(",", $spec_key_name);
                }
            ?>
          </td>
          <td><?php echo $goods['goodssn'];?></td>
          <td><?php echo FormatMoney($goods['orderprice'],0);?></td>
          <td><?php echo $goods['total'];?></td>
          <td><?php echo $goods['status_name'];?></td>
          <td>
            <?php if($goods['order_type'] != 4){?>
    		      <a>详情</a>		
   		    <?php }else{?>
   		                           无
   		    <?php }?>
          </td>
        </tr>
        <?php  } } ?>
      </tbody>
    </table>
    <div class="layui-form-item layui-form-text">
      <label class="layui-form-label">订单备注</label>
      <div class="layui-input-block">
        <textarea placeholder="卖家订单备注" class="layui-textarea"><?php echo $info['beizhu'];?></textarea>
      </div>
    </div>
    
    <!-- 修改快递信息弹出框 -->
<form action="<?php echo mobile_url('order',array('name'=>'seller','op'=>'confirmsend','id'=>$info['id'])); ?>" method="post" id="update_dispatch_form" enctype="multipart/form-data" class="form-horizontal" >
    <div class="comfirm-modal modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">快递信息</h4>
          </div>
          <div class="modal-body">
              <div class="layui-form-item">
                <label class="layui-form-label">快递公司</label>
                <div class="layui-input-inline">
                  <select lay-filter="aihao" name="express" id="express">
                  <?php if (is_array($dispatchList) && !empty($dispatchList)){ foreach ($dispatchList as $val){?>
                    <option value="<?php echo $val['code'];?>" <?php if ($info['express'] == $val['code']){?>selected<?php }?>><?php echo $val['name'];?></option>
                  <?php }}?>
                  </select>
                  <input type='hidden' class='input span3' name='expresscom' id='expresscom' value="<?php echo $info['expresscom'];?>" />
                </div>
              </div>
              <div class="layui-form-item">
                <label class="layui-form-label">快递单号</label>
                <div class="layui-input-inline">
                  <input type="text" name="expresssn" id="expresssn" value="<?php echo $info['expresssn'];?>" lay-verify="title" autocomplete="off" placeholder="请输入快递单号" class="layui-input">
                </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            <button type="button" id="update_dispatch" class="btn btn-primary">保存</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</form>
    <div class="layui-form-item">
        <label class="layui-form-label"> </label>
        <a href="<?php echo mobile_url('order',array('name'=>'seller','op'=>'lists')); ?>" class="layui-btn" lay-submit="" lay-filter="">确认</a>
		<?php if($info['status'] == 1) { ?>
			<button class="layui-btn comfirm-btn" lay-submit="" lay-filter="" >确认发货</button>
		<?php  } ?>

		<!--  
		<?php  if($info['status'] ==2) { ?>
		<a href="<?php echo mobile_url('order',array('name'=>'seller','op'=>'check_status','type'=>'finish','id'=>$info['id']));?>" class="layui-btn" onclick="return confirm('确认完成此订单吗？'); return false;" name="finish">完成订单</a>
	   <?php  } ?>


		<?php  if(($info['status']==0||($info['status']<-1&&$info['status']>-5))) { ?>
		<a href="<?php echo mobile_url('order',array('name'=>'seller','op'=>'check_status','type'=>'close','id'=>$info['id']));?>" class="layui-btn" name="close" onclick="return confirm('永久关闭此订单吗？'); return false;" >关闭订单</a>
		<?php  } ?>

		<?php  if($info['status'] ==-1) { ?>
			<a href="<?php echo mobile_url('order',array('name'=>'seller','op'=>'check_status','type'=>'open','id'=>$info['id']));?>" class="layui-btn" onclick="return confirm('确认开启此订单吗？'); return false;" name="open">开启订单</a>
		<?php  } ?>
		-->
    </div>
    
</div>         

<script>
layui.use(['form', 'layedit', 'laydate'], function(){
  var form = layui.form();
  
});
$(function(){
  // 修改收货人信息
    $(".modify").on("click",function(){
        $(".address-modal").modal();
    });
    $(".comfirm-btn").on("click",function(){
        $(".comfirm-modal").modal();
    })
})
$("#update_use_info").click(function(){
	if (!$("#address_mobile").val().match(/^((13|14|15|17|18)+\d{9})$/)) {
		layer.open({
            title: '提示'
            ,content: '手机格式不对！'
        });
		return false;
	}
	$("#update_use_form").submit();
});
$("#update_dispatch").click(function(){
	if($("#expresssn").val() == ''){
		layer.open({
            title: '提示'
            ,content: '请填写快递单号！'
        });
		return false;
	}
	var sel =$("#express").find("option:selected").text();
	$("#expresscom").val(sel);
	$("#update_dispatch_form").submit();
	
});
</script>

</body>
</html>