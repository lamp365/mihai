<?php defined('SYSTEM_IN') or exit('Access Denied');?>
<?php  include page('header');?>

<h3 class="header smaller lighter blue">拼团订单管理 &nbsp;&nbsp;<span style="font-size: 12px;">开团后有效时间：<?php echo $time;?>分</span></h3>
<h3 class="blue">	<span style="font-size:18px;"><strong>订单总数：<?php echo $total ?></strong></span></h3>
<ul class="nav nav-tabs" >
    <li style="width:7%" <?php if($group_status == 2){ echo 'class="active"';}?>><a href="<?php echo web_url('groupbuy',array('op'=>'list','group_status'=>2));?>">拼团中</a></li>
    <li style="width:7%"  <?php if($group_status == 1){ echo 'class="active"';}?>><a href="<?php echo web_url('groupbuy',array('op'=>'list','group_status'=>1));?>">拼团成功</a></li>
    <li style="width:7%"  <?php if($group_status == 0){ echo 'class="active"';}?>><a href="<?php echo web_url('groupbuy',array('op'=>'list','group_status'=>0));?>">拼团失败</a></li>
</ul>


<table class="table  table-bordered table-hover">
    <thead >
    <tr>
        <th style="width:400px;text-align:center;">宝贝</th>
        <th style="width:80px;text-align:center;">单价</th>
        <th style="width:80px;text-align:center;">成团人数</th>

        <th style="width:100px;text-align:center;">目前人数</th>
        <th style="width:50px;text-align:center;">建团人</th>
        <th style="width:150px;text-align:center;">建团时间</th>
        <th style="width:120px;text-align:center;" >订单状态</th>
        <th style="width:150px;text-align:center;">实收款</th>
        <th style="width:150px;text-align:center;">总收款</th>
        <th style="width:50px;text-align:center;">标记</th>
    </tr>
    </thead>
    <tbody>
    <?php  if(is_array($list)) { foreach($list as $items) {  $cur_num=getGoupBuyNum($items['group_id']); ?>
        <tr><td align="left" colspan="10" style="background:#E9F8FF;margin-top:10px;"><?php  echo $items['ordersn'];?>&nbsp;&nbsp;</td></tr>
        <tr>
            <td  colspan="4">
                <?php
                if ( is_array($items['goods']) ){
                    foreach ( $items['goods'] as $goods ){
                        ?>
                        <div class="items">
                            <ul>
                                <li class="img"><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $goods['aid']))?>"><img src="<?php echo getGoodsThumb($goods['gid']); ?>" height="40" /></a></li>
                                <li class="title">
                                    <div><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $goods['aid']))?>"><?php echo $goods['title']; ?></a></div>
                                    <div>
                                        <div class="name"><?php echo getGoodsProductPlace($goods['pcate']); ?></div>
                                        <?php if($item['isdraw'] == 1) { ?>
                                        &nbsp;&nbsp; <span class="btn btn-xs btn-info">抽奖团</span>
                                        <?php }else{ ?>
                                        &nbsp;&nbsp; <span class="btn btn-xs btn-info">团购商品</span>
                                        <?php } ?>
                                    </div>
                                    <div class="sn">商家编码: <?php echo $goods['goodssn']; ?></div>
                                </li>
                                <li class="price"><?php echo $goods['orderprice']; ?></li>
                                <li class="tot"><?php echo $goods['team_buy_count']; ?></li>
                                <li class="tot">
									  <span class="shouhou_status" style="color: red">
                                          <?php echo $cur_num;?>
                                      </span>
                                    <?php $coudan_num = $goods['team_buy_count']-$cur_num; if($coudan_num > 0 && $group_status != 2){ ?>
                                      <p>凑单：<?php echo $coudan_num;?></p>
                                    <?php } ?>
                                </li>
                            </ul>
                        </div>
                        <?php
                    }
                }?>
            </td>

            <td align="center" valign="middle" style="vertical-align: middle;">
                <div>收货人：<?php  echo $items['address_realname'];?></div>
                <div>电话：<?php  echo $items['address_mobile'];?></div>
                <?php if ( !empty($items['remark'])){ ?>
                    <div><a type="button" href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="<?php echo $items['remark']; ?>"><img src="images/tag.png" /></a></div>
                <?php } ?>
            </td>
            <td align="center" valign="middle" style="vertical-align: middle;"><?php  echo $items['group_createtime']?></td>
            <td align="center" valign="middle" style="vertical-align: middle;">
                <div>
                    <?php  if($items['status'] == 0) { ?><span class="label label-warning" >待付款</span><?php  } ?>
                    <!--已经付钱的，团购中 或者团购未开奖 这叫做已支付，因为不在待发货中展示，其他的叫待发货-->
                    <?php  if($items['status'] == 1) {
                        if(checkGroupBuyCanSend($items)){
                            echo '<span class="label label-danger" >待发货</span>';
                        }else{
                            echo '<span class="label label-danger" >已支付</span>';
                        }

                    }
                    ?>
                    <?php  if($items['status'] == 2) { ?><span class="label label-warning">待收货</span><?php  } ?>
                    <?php  if($items['status'] == 3) { ?><span class="label label-success" >已完成</span><?php  } ?>
                    <?php  if($items['status'] == -1) { ?><span class="label label-success">已关闭</span><?php  } ?>
                    <?php  if($items['status'] == -2) { ?><span class="label label-danger">退款中</span><?php  } ?>
                    <?php  if($items['status'] == -3) { ?><span class="label label-danger">换货中</span><?php  } ?>
                    <?php  if($items['status'] == -4) { ?><span class="label label-danger">退货中</span><?php  } ?>
                    <?php  if($items['status'] == -5) { ?><span class="label label-success">已退货</span><?php  } ?>
                    <?php  if($items['status'] == -6) { ?><span class="label  label-success">已退款</span><?php  } ?>
                </div>
                <div>
                    <a  href="<?php  echo web_url('groupbuy', array('op' => 'detail', 'group_id' => $items['group_id']))?>"><i class="icon-edit"></i>查看成员</a>
                </div>
            </td>
            <td align="center" valign="middle" style="vertical-align: middle;"><div><?php  echo $items['price'];?> 元 </div><?php  if($items['hasbonus']>0) { ?><div class="label label-success">惠<?php echo $items['bonusprice'];?></div><?php  }?><div style="font-size:10px;color:#999;">(含运费:<?php  echo $items['dispatchprice'];?> 元)</div><div style="font-size:10px;color:#999;">(含进口税:<?php  echo $items['taxprice'];?> 元)</div></td>
            <td align="center" valign="middle" style="vertical-align: middle;color: red;font-weight: bolder">
                <?php echo  round(($items['price']+$items['dispatchprice']+$items['taxprice'])*$cur_num,2)?>元
            </td>
            <td align="center" valign="middle" style="vertical-align: middle;"><a type="button" href="<?php  echo web_url('order', array('op' => 'detail', 'id' => $items['id']))?>" data-toggle="tooltip" data-placement="bottom" title="<?php if(!empty($items['retag'])){ $retag_json = json_decode($items['retag'],true); echo $retag_json['beizhu'];}else{ echo '没有标注信息'; } ?>"><img src="images/btag<?php echo $items['tag']; ?>.png" /></a></td>
        </tr>
    <?php  } } ?>
    </tbody>
</table>

<?php  echo $pager;?>

<?php  include page('footer');?>
