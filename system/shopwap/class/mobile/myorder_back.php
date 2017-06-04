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

}
if ($op == 'returngood') {

    exit;
} if ($op == 'resendgood') {
    $orderid = intval($_GP['orderid']);

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

} else if ($op == 'detail') {


    //提交清关材料
} else if ($op == 'identity') {

    exit;

    //提交清关材料
}elseif($op == 'identity_submit')
{


}else if($op == 'show_wuliu'){ //显示物流信息


} else {

}


