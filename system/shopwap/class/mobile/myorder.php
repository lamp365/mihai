<?php
$member=get_member_account(true,true);
$openid =$member['openid'] ;
$id = $profile['id'];
$op = $_GP['op'];
//-6已退款 -5已退货 -4退货中， -3换货中， -2退款中，-1取消状态，0普通状态，1为已付款，2为已发货，3为成功
$status_ln = array(
    "-6"=>"已退款",
    "-5"=>"已退货",
    "-4"=>"退货中",
    "-3"=>"换货中",
    "-2"=>"退款中",
    "-1"=>"关闭",
    "0"=>"待付款",
    "1"=>"已付款",
    "2"=>"已发货",
    "3"=>"交易成功"
);
// -1等待开奖 0未中奖 1中奖
$draw_ln = array(
    "3"=>"等待开奖",
    "2"=>"很遗憾，未中奖",
    "1"=>"恭喜！中奖",
    "0"=>"您未支付！"
);
// 进行订单的自动关闭操作
order_auto_close();
//更新该用户团购信息
update_user_group_status($openid);
if ($op == 'cancelsend') {
    $orderid = intval($_GP['orderid']);
    $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    if (empty($item)) {
        message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
    }
    if($item['ordertype'] == 1){
        message('对不起，团购商品，不允许取消！',refresh(),'error');
    }
    if(($item['paytype']==3&&$item['status']==1)||$item['status']==0)
    {
        //退还余额和优惠卷 并关闭订单
        update_order_status($orderid,-1);
        message('订单已关闭！', mobile_url('myorder',array('status'=>$_GP['fromstatus'])), 'success');
    }
    if($item['status']==2)
    {
        message('商家已发货无法修改订单',refresh(),'error');

    }
    message('该订单不可取消');
}
if ($op == 'returngood') {
    $orderid = intval($_GP['orderid']);
    $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    $dispatch = mysqld_select("select id,dispatchname,sendtype from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));

    if (empty($item)) {
        message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
    }
    $opname="退货";
    if (checksubmit("submit")) {
        mysqld_update('shop_order', array('status' => -4,'isrest'=>1,'rsreson' => $_GP['rsreson']), array('id' => $orderid, 'openid' => $openid ));

        message('申请退货成功，请等待审核！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
    }
    include themePage('order_detail_return');
    exit;
} if ($op == 'resendgood') {
    $orderid = intval($_GP['orderid']);
    $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    $dispatch = mysqld_select("select id,dispatchname,sendtype from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));

    if (empty($item)) {
        message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
    }
    $opname="换货";
    if (checksubmit("submit")) {
        mysqld_update('shop_order', array('status' =>  -3,'isrest'=>1,'rsreson' => $_GP['rsreson']), array('id' => $orderid, 'openid' => $openid ));

        message('申请换货成功，请等待审核！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
    }
    include themePage('order_detail_return');
    exit;
}


if ($op == 'returncomment') {
    $orderid       = intval($_GP['orderid']);
    $order_good_id = intval($_GP['order_good_id']);

    $list = mysqld_selectall("SELECT comment.*,member.realname,member.mobile FROM " . table('shop_goods_comment') . " comment  left join " . table('member') . " member on comment.openid=member.openid WHERE comment.orderid=:orderid and comment.openid=:openid ", array(':orderid' => $orderid, 'openid' => $openid ));

    $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    if(empty($item))
        message('该订单不存在', refresh(), 'error');

    $shop_order = mysqld_select("SELECT * FROM " . table('shop_order_goods') . " WHERE id = {$order_good_id} and orderid={$orderid}");
    if(empty($shop_order))
        message('该订单不存在', refresh(), 'error');

    if (checksubmit("submit")) {
        $optionid = intval($_GP['optionid']);

        $option = mysqld_select("select * from " . table("shop_goods_option") . " where id=:id limit 1", array(":id" => $optionid));

        if($item['status']!=3)
        {
            message('订单未完成不能评论', refresh(), 'error');
        }
        if(empty($_GP['rsreson']))
        {

            message('请输入评论内容', refresh(), 'error');
        }
        $system = getSystemType();
        $username = $member['realname'];
        if(empty($username)){
            $username = $member['mobile'];
        }
        $ispic     = 0;
        if(!empty($_GP['picurl'])){
            $ispic = 1;
        }
        mysqld_insert('shop_goods_comment', array(
            'createtime'=>time(),
            'rate'      => $_GP['rate'],
            'ordersn'   => $item['ordersn'],
            'optionname'=>$option['title'],
            'goodsid'   => $shop_order['shopgoodsid'],
            'comment'   => $_GP['rsreson'],
            'orderid'   => $orderid,
            'openid'    => $openid,
            'system'    => $system,
            'username'  => $username,
            'ispic'     => $ispic
        ));
        $lastid = mysqld_insertid();
        mysqld_update('shop_order_goods', array('iscomment'=>1 ),array('id'=>$order_good_id));
        if(!empty($_GP['picurl'])){
            foreach($_GP['picurl'] as $picurl){
                mysqld_insert('shop_comment_img',array('img'=>$picurl,'comment_id'=>$lastid));
            }
        }
        message('评论成功！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']), 'op'=>'detail','orderid'=>$orderid)), 'success');
    }
    include themePage('order_detail_comment');
    exit;
}

if($op == 'uploadpic'){
    $upload = file_upload($_FILES['Filedata']);
    if (is_error($upload)) {
        echo showAjaxMess('1002',$upload['message']);
        die();
    }
    $data = array("picurl"=>$upload['path'],'small_picurl'=>download_pic($upload['path'],650));
    echo showAjaxMess(200,$data);
    die();
}

if ($op == 'returnpay') {
    $orderid = intval($_GP['orderid']);
    $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    $dispatch = mysqld_select("select id,dispatchname,sendtype from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));

    if (empty($item)) {
        message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
    }
    $opname="退款";
    if (checksubmit("submit")) {
        if($order['paytype']==3)
        {
            message('货到付款订单不能进行退款操作!', refresh(), 'error');
        }
        mysqld_update('shop_order', array('status' => -2,'rsreson' => $_GP['rsreson']), array('id' => $orderid, 'openid' => $openid ));

        message('申请退款成功，请等待审核！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
    }
    include themePage('order_detail_return');
    exit;
} elseif ($op == 'confirm') {
    $orderid = intval($_GP['orderid']);
    $order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
    $orderGoodInfo = mysqld_selectall("select * from ". table('shop_order_goods') ." where orderid={$orderid}");
    if (empty($order) || empty($orderGoodInfo)) {
        message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
    }
    if(!isSureGetGoods($orderGoodInfo)){
        message("对不起，你有商品还等待处理中，暂不允许确认收货!",refresh(),'error');
    }

    $res = mysqld_update('shop_order', array('status' => 3,'completetime'=>time()), array('id' => $orderid, 'openid' => $openid ));
    if($res){
        //确认收获后 卖家得到佣金金额 买家收到积分  同时记录账单和APP消息推送
        sureUserCommisionToMoney($orderGoodInfo,$order);
    }else{
        message('操作失敗！',refresh(),'error');
    }
    message('确认收货完成！', mobile_url('myorder',array('status' => 3)), 'success');
} else if ($op == 'detail') {

    $orderid = intval($_GP['orderid']);
    $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE openid = '".$openid."' and id='{$orderid}' limit 1");
    if (empty($item)) {
        message('抱歉，您的订单不存或是已经被取消！', mobile_url('myorder'), 'error');
    }
    if($item['hasbonus'])
    {
        $bonuslist = mysqld_selectall("SELECT bonus_user.*,bonus_type.type_name FROM " . table('bonus_user') . " bonus_user left join  " . table('bonus_type') . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id WHERE bonus_user.order_id=:order_id",array(":order_id"=>$orderid));
    }

    if($item['paytype']!=$this->getPaytypebycode($item['paytypecode']))
    {
        mysqld_update('shop_order', array('paytype' => $this->getPaytypebycode($item['paytypecode'])), array('id' => $orderid, 'openid' => $openid ));
        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE openid = '".$openid."' and id='{$orderid}' limit 1");

    }

    $goods = mysqld_selectall("SELECT g.id, o.goodsid, o.total as order_total, o.status as order_status, o.type as order_type, g.title, g.thumb,g.pcate,o.id as order_good_id, o.aid, o.price as marketprice,o.optionid,o.iscomment,o.shop_type FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.shopgoodsid=g.id "
        . " WHERE o.orderid='{$orderid}'");

    if ($item['ordertype'] == '1') {
        $dish = mysqld_select("SELECT * FROM ".table('shop_dish')." WHERE id=".$goods[0]['goodsid']);
    }

    if($item['ordertype'] == 1){
        //先更新后再找出该组团的信息
        $sql   = "select t.group_id,t.dish_id,t.status,t.createtime from ".table('team_buy_member')." as m left join ". table('team_buy_group') ." as t on t.group_id = m.group_id where m.order_id={$item['id']}";
        $group = mysqld_select($sql);
        $group_member = mysqld_selectcolumn("select count(group_id) from ".table('team_buy_member')." where group_id={$group['group_id']}");
        $dish_info    = mysqld_select("select id,team_buy_count from ".table('shop_dish')." where id={$group['dish_id']}");
        $need_group_member = $dish_info['team_buy_count'] - $group_member;
    }

    //是否显示确认收货按钮
    $isShowGetGoogBtn = isSureGetGoods($goods,'order_type','order_status');

    foreach ($goods as &$g) {
        //属性
        $option = mysqld_select("select * from " . table("shop_goods_option") . " where id=:id limit 1", array(":id" => $g['optionid']));
        if ($option) {
            $g['title'] = "[" . $option['title'] . "]" . $g['title'];
            $g['marketprice'] = $option['marketprice'];
        }
    }
    unset($g);

    $dispatch = mysqld_select("select id,dispatchname,sendtype,dispatch_web from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));

    $paymentconfig="";
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
        $paymentconfig=" and code!='alipay'";
    }
    $payments = mysqld_selectall("select * from " . table("payment")." where enabled=1 {$paymentconfig} order by `order` desc");

    include themePage('order_detail');
    exit;

    //提交清关材料
} else if ($op == 'identity') {
    $orderid = intval($_GP['orderid']);
    //订单信息
    $order 		= mysqld_select("SELECT identity_id FROM " . table('shop_order') . " WHERE id=:id",array(":id"=>$orderid));
    //当前订单的身份证信息
    $identity 	= mysqld_select("SELECT * FROM " . table('member_identity') . " WHERE identity_id=:identity_id", array(':identity_id'=>$order['identity_id']));
    //用户的所有订单信息
    $arrIdentity = mysqld_selectall("SELECT * FROM " . table('member_identity') . " WHERE status = 0 and openid = :openid", array(':openid' => $openid));
    include themePage('order_identity');
    exit;

    //提交清关材料
}elseif($op == 'identity_submit')
{
    $orderid = intval($_GP['orderid']);

    $identity_id = intval($_GP['identity_id']);

    $data = array('identity_id'=>$identity_id);

    if(mysqld_update('shop_order', $data,array('openid' =>$openid,'id'=>$orderid)))
    {
        message('清关材料提交成功！', '', 'success');
    }
    else{
        message('清关材料提交失败！', '', 'error');
    }

}else if($op == 'show_wuliu'){ //显示物流信息
    $name = $_GP['wuliu_name'];
    $num  = $_GP['wuliu_num'];
    if($name == 'beihai'){
        $data = fetch_beihai($num);
        if(empty($data['data'])){
            die(showAjaxMess('1002','物流更新可能会慢一点，暂无物流信息'));
        }
        $data = $data['data'];
    }else{
        $data = fetch_expressage($name,$num);
        if($data['status'] != 200){
            die(showAjaxMess('1002','物流更新可能会慢一点，暂无物流信息'));
        }
        $data = $data['data'];
        if(is_array($data))
            $data = array_reverse($data);
    }
    die(showAjaxMess(200,$data));

} else {
    $status = intval($_GP['status']);
    $where = "openid = '".$openid."'";
    $pindex = max(1, intval($_GP['page']));
    $psize = 4;
    $status_arr = array(14,34);//退货完成  退款完成
    if ($status == 99 || in_array($status,$status_arr)) {
        $where.="";
    }  else {
        $where.=" and status=$status";
    }

    if($status == 14){
        $sql    = "SELECT A.* from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where {$where} AND  B.type=1 and B.status=4 group by A.id ORDER BY  A.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $sqlNum = "SELECT COUNT(A.id) from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$where} AND  B.type=1 and B.status=4 group by A.id";
    }else if($status == 34){
        $sql    = "SELECT A.* from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where {$where} AND  B.type=3 and B.status=4 group by A.id ORDER BY  A.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $sqlNum = "SELECT COUNT(A.id) from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$where} AND  B.type=3 and B.status=4 group by A.id";
    }else{
        $sql    = "SELECT * FROM ". table('shop_order') . " WHERE {$where} ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $sqlNum = "SELECT COUNT(*) FROM " . table('shop_order') . " WHERE {$where}";
    }
    $listorder = mysqld_selectall($sql);
    $total = mysqld_selectcolumn($sqlNum);
    $pager = pagination($total, $pindex, $psize,'.myoderlist');
    if (!empty($listorder)) {
        foreach ($listorder as &$orderow) {
            $goods = mysqld_selectall("SELECT g.id, g.title, g.thumb,g.pcate, o.price as marketprice,o.total,o.optionid,o.aid,o.status as order_status, o.type as order_type,o.shop_type FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.shopgoodsid=g.id "
                . " WHERE o.orderid='{$orderow['id']}'");
            /*
            foreach ($goods as &$item) {
                //属性
                $option = mysqld_select("select title,marketprice,weight,stock from " . table("shop_goods_option") . " where id=:id limit 1", array(":id" => $item['optionid']));
                if ($option) {
                    $item['title'] = "[" . $option['title'] . "]" . $item['title'];
                    $item['marketprice'] = $option['marketprice'];
                }
            }

            unset($item);
                */
            $orderow['goods'] = $goods;
            $orderow['total'] = $total;
            $orderow['dispatch'] = mysqld_select("select id,dispatchname from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $orderow['dispatch']));
        }
    }

    getBackMonryOrGoodData($status,$listorder);

    if ( !empty($_POST['page']) ){
        if (is_array($listorder)) { foreach($listorder as $item) {
            $html .= '<div class="myoder">
						<div class="myoder-hd">
							<span class="pull-left">订单编号：'.$item['ordersn'].'</span>
							<span class="pull-right">'.date('Y-m-d H:i', $item['createtime']).'</span>
						</div>';
            if(count($item['goods'])==1) {
                if(is_array($item['goods'])) { foreach($item['goods'] as $goods) {
                    $html .='
							<div class="myoder-detail">
								<a href="'.mobile_url('detail', array('id' => $goods['aid'])).'"><img src="'.$goods['thumb'].'" width="160"></a>
								<div class="pull-left">
									<div class="name"><a href="'.mobile_url('detail', array('id' => $goods['aid'])).'">'.$goods['title'].'</a></div>
									<div class="price">
										<span>¥'.$goods['marketprice'].' * '.$goods['total'].'</span>
										<span class="btn btn-xs btn-success">'.getGoodsType($goods['ordertype']).'</span>
                                    </div>
                                </div>
                                <div class="status">'.$status_ln[$item['status']].'</div>
                            </div>';
                } }  } else {
                $html .='<div class="myoder-detail">';
                if(is_array($item['goods'])) { foreach($item['goods'] as $goods) {
                    $html .= '<a href="'.mobile_url('detail', array('id' => $goods['aid'])).'"><img src="'.$goods['thumb'].'" width="160"></a>';
                } }
                $html .= '<div class="status">'.$status_ln[$item['status']].'</div></div>';
            }

            $html .= '<div class="myoder-total">
		<span>共计：<span class="false">';
            if($item['dispatchprice']<=0) {
                $html .= $item['price'].' 元';
            } else {
                $html .= $item['price'].' 元';
                $html .= '<span style="font-size: 12px;">(含运/税费: '.$item['dispatchprice']+$item['taxprice'].' 元) </span>';
            }
            $html .='</span>';
            if($item['hasbonus']>0) {
                $html .='<span style="color:green"> ( 已优惠：'.$item['bonusprice'].' 元)</span>';
            }
            $html .='</span><a href="'.mobile_url('myorder', array('orderid' => $item['id'], 'op' => 'detail','fromstatus'=>$status)).'" class="btn btn-default pull-right btn-sm" >订单详情</a>
	</div>
</div>';
        }}
        echo $html;
        exit;
    }
    include themePage('order');
}


function update_user_group_status($openid){
    //获取该用户团购订单
    $order = mysqld_selectall('select g.goodsid from '.table('shop_order')." as o left join ".table('shop_order_goods')." as g on g.orderid=o.id where o.openid={$openid} and o.ordertype=1");
    if(!empty($order)){
        foreach($order as $row){
            update_group_status($row['goodsid']);
        }
    }

}