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
    <?php  if(is_array($list)) { foreach($list as $item) {  $cur_num=getGoupBuyNum($item['group_id']); ?>
        <tr><td align="left" colspan="10" style="background:#E9F8FF;margin-top:10px;"><?php  echo $item['ordersn'];?>&nbsp;&nbsp;</td></tr>
        <tr>
            <td  colspan="4">
                <?php
                if ( is_array($item['goods']) ){
                    foreach ( $item['goods'] as $goods ){
                        ?>
                        <div class="items">
                            <ul>
                                <li class="img"><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $goods['aid']))?>"><img src="<?php echo getGoodsThumb($goods['gid']); ?>" height="40" /></a></li>
                                <li class="title">
                                    <div><a target="_blank" href="<?php  echo mobile_url('detail', array('name'=>'shopwap','id' => $goods['aid']))?>"><?php echo $goods['title']; ?></a></div>
                                    <div>
                                        <div class="name"><?php echo getGoodsProductPlace($goods['pcate']); ?></div>
                                        &nbsp;&nbsp; <span class="btn btn-xs btn-info">团购商品</span>
                                    </div>
                                    <div class="sn">商家编码: <?php echo $goods['goodssn']; ?></div>
                                </li>
                                <li class="price"><?php echo $goods['orderprice']; ?></li>
                                <li class="tot"><?php echo $goods['team_buy_count']; ?></li>
                                <li class="tot">
									  <span class="shouhou_status" style="color: red">
                                          <?php echo $cur_num;?>
                                      </span>
                                </li>
                            </ul>
                        </div>
                        <?php
                    }
                }?>
            </td>

            <td align="center" valign="middle" style="vertical-align: middle;">
                <div>收货人：<?php  echo $item['address_realname'];?></div>
                <div>电话：<?php  echo $item['address_mobile'];?></div>
                <?php if ( !empty($item['remark'])){ ?>
                    <div><a type="button" href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="<?php echo $item['remark']; ?>"><img src="images/tag.png" /></a></div>
                <?php } ?>
            </td>
            <td align="center" valign="middle" style="vertical-align: middle;"><?php  echo $item['group_createtime']?></td>
            <td align="center" valign="middle" style="vertical-align: middle;">

                <div><a  href="<?php  echo web_url('groupbuy', array('op' => 'detail', 'group_id' => $item['group_id']))?>"><i class="icon-edit"></i>查看详情</a></div>
                &nbsp;&nbsp;
            </td>
            <td align="center" valign="middle" style="vertical-align: middle;"><div><?php  echo $item['price'];?> 元 </div><?php  if($item['hasbonus']>0) { ?><div class="label label-success">惠<?php echo $item['bonusprice'];?></div><?php  }?><div style="font-size:10px;color:#999;">(含运费:<?php  echo $item['dispatchprice'];?> 元)</div><div style="font-size:10px;color:#999;">(含进口税:<?php  echo $item['taxprice'];?> 元)</div></td>
            <td align="center" valign="middle" style="vertical-align: middle;color: red;font-weight: bolder">
                <?php echo  round(($item['price']+$item['dispatchprice']+$item['taxprice'])*$cur_num,2)?>元
            </td>
            <td align="center" valign="middle" style="vertical-align: middle;"><a type="button" href="<?php  echo web_url('order', array('op' => 'detail', 'id' => $item['id']))?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo !empty($item['retag'])?$item['retag']:'没有标注信息'; ?>"><img src="images/btag<?php echo $item['tag']; ?>.png" /></a></td>
        </tr>
    <?php  } } ?>
    </tbody>
</table>
<script>
    $(".finish").click(function(){
        if(confirm('确定进行结束操作')){
            var url = "<?php echo web_url('groupbuy',array('op'=>'finish'));?>";
            window.location.href=url;
        }
    })
</script>
<?php  echo $pager;?>

<?php  include page('footer');?>
