<?php
namespace shopwap\controller;
use  shopwap\controller;

class myorder extends \common\controller\basecontroller
{
    public $status_ln = array(
            "-1"=>"已关闭",
            "0"=>"待付款",
            "1"=>"已付款",
            "2"=>"已发货",
            "3"=>"交易成功"
    );
    // -1等待开奖 0未中奖 1中奖
    public $draw_ln = array(
            "3"=>"等待开奖",
            "2"=>"很遗憾，未中奖",
            "1"=>"恭喜！中奖",
            "0"=>"您未支付！"
    );

    public function __construct()
    {
        if(!checkIsLogin()){
            tosaveloginfrom();
            header("location:".mobile_url('login'));
        }
        $mem = get_member_account();
        // 进行订单的自动关闭操作
        order_auto_close();
        //更新该用户团购信息
        $this->update_user_group_status($mem['openid']);
    }

    public function index()
    {
        $_GP = $this->request;
        $status_ln = $this->status_ln;
        $draw_ln   = $this->draw_ln;
        $member    = get_member_account();
        $openid    = $member['openid'];
        $pindex    = max(1, intval($_GP['page']));
        $psize     = 4;
        $status    = $_GP['status'];
        $condition = "openid='{$openid}'";

        if($status!= null && $status == 0){
            $condition .= " AND A.status=0";
        }else{
            if(!empty($status)){
                $condition .= " AND A.status={$status}";
            }
        }
        $limit ="LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

        $sql    = "SELECT A.* FROM " . table('shop_order') . " A  WHERE  {$condition} ORDER BY  A.id  DESC ".$limit;
        $sqlNum = 'SELECT COUNT(A.id) FROM ' . table('shop_order') . " A WHERE  {$condition}";

        $listorder = mysqld_selectall($sql);
        $total     = mysqld_selectcolumn($sqlNum);
        $pager     = pagination($total, $pindex, $psize,'.myoderlist');

        foreach ($listorder as &$orderow) {
            $goods = mysqld_selectall("SELECT h.id, h.title,h.thumb,h.transport_id,h.p1,o.price as marketprice,o.total,o.spec_key_name,o.goodsid,o.status as order_status, o.type as order_type,o.shop_type FROM " . table('shop_order_goods') . " o left join " . table('shop_dish') . " h on o.goodsid=h.id "
                . " WHERE o.orderid='{$orderow['id']}' limit 2");
            $orderow['goods'] = $goods;
            $orderow['total'] = count($goods);
        }

        $this->mobileGetOrderNextPage($listorder,$status_ln);
        include themePage('order/order');
    }

    public function returnDish()
    {
        $_GP = $this->request;
        $status_ln = array(1=>'退换申请',23=>'退换处理','4'=>'退换成功',-1=>'退换失败');
        $draw_ln   = $this->draw_ln;
        $member    = get_member_account();
        $openid    = $member['openid'];
        $pindex    = max(1, intval($_GP['page']));
        $psize     = 15;
        $limit     = "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $status    = $_GP['status'] ?: 1;

        $condition = "o.openid='{$openid}' And o.status={$status}";
        $sqlNum = $sql = "SELECT h.id, h.title,h.thumb,h.transport_id,h.p1,o.price as marketprice,o.orderid as oo_id,o.spec_key_name,o.total,o.goodsid,o.status as order_status, o.type as order_type,o.shop_type FROM " . table('shop_order_goods') . " o left join " . table('shop_dish') . " h on o.goodsid=h.id ";
        $sql .= " where {$condition} {$limit}";

        $list    = mysqld_selectall($sql);
        $total   = mysqld_selectcolumn($sqlNum);
        $pager   = pagination($total, $pindex, $psize);

        $listorder = array();
        foreach($list as $key=>$one){
            $order = mysqld_select("select * from ".table('shop_order')." where id={$one['oo_id']}");
            $listorder[$key] = $order;
            $listorder[$key]['goods'] = $one;
            $listorder[$key]['total'] = 1;
        }
        $this->mobileGetOrderNextPage2($listorder,$status_ln);
        include themePage('order/returnDish');
    }

    public function detail()
    {
        $_GP = $this->request;
        $status_ln = $this->status_ln;
        $member    = get_member_account();
        $openid    = $member['openid'];
        $orderid   = intval($_GP['orderid']);
        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE openid = '".$openid."' and id='{$orderid}' limit 1");
        if (empty($item)) {
            message('抱歉，您的订单不存或是已经被取消！', mobile_url('myorder'), 'error');
        }
        if($item['hasbonus'])
        {
            $bonuslist = mysqld_selectall("SELECT bonus_user.*,bonus_type.type_name FROM " . table('bonus_user') . " bonus_user left join  " . table('bonus_type') . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id WHERE bonus_user.order_id=:order_id",array(":order_id"=>$orderid));
        }


        $goods = mysqld_selectall("SELECT h.id,h.p1, h.title, h.thumb,h.transport_id,o.id as order_good_id, o.goodsid, o.price as marketprice,o.iscomment,o.shop_type,o.total as order_total, o.status as order_status, o.type as order_type,o.spec_key_name FROM " . table('shop_order_goods') . " o left join " . table('shop_dish') . " h on o.goodsid=h.id "
            . " WHERE o.orderid='{$orderid}'");


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

        $dispatch = mysqld_select("select id,dispatchname,sendtype,dispatch_web from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));

        $paymentconfig="";
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $paymentconfig=" and code!='alipay'";
        }
        $payments = mysqld_selectall("select * from " . table("payment")." where enabled=1 {$paymentconfig} order by `order` desc");

        include themePage('order/order_detail');
    }

    //取消订单
    public function cancelsend()
    {
        $_GP = $this->request;
        $member    = get_member_account();
        $openid    = $member['openid'];
        $orderid = intval($_GP['orderid']);
        $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
        if (empty($item)) {
            message('您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
        }
        if($item['ordertype'] == 1){
            message('团购商品，不允许取消！',refresh(),'error');
        }
        if($item['status']==1 ||$item['status']==0)
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


    //评论
    public function returncomment()
    {
        $_GP     = $this->request;
        $member    = get_member_account();
        $openid    = $member['openid'];

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
                'spec_key_name'=>$shop_order['spec_key_name'],
                'dishid'    => $shop_order['goodsid'],
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
        include themePage('order/order_detail_comment');
    }

    public function uploadpic()
    {
        $upload = file_upload($_FILES['Filedata']);
        if (is_error($upload)) {
            echo showAjaxMess('1002',$upload['message']);
            die();
        }
        $data = array("picurl"=>$upload['path'],'small_picurl'=>download_pic($upload['path'],650));
        die(showAjaxMess(200,$data));
    }


    //确认收货 已经完成
    public function confirm()
    {
        $_GP      = $this->request;
        $member    = get_member_account();
        $openid    = $member['openid'];

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
    }

    public function identity()
    {
        $_GP      = $this->request;
        $member    = get_member_account();
        $openid    = $member['openid'];

        $orderid = intval($_GP['orderid']);
        //订单信息
        $order 		= mysqld_select("SELECT identity_id FROM " . table('shop_order') . " WHERE id=:id",array(":id"=>$orderid));
        //当前订单的身份证信息
        $identity 	= mysqld_select("SELECT * FROM " . table('member_identity') . " WHERE identity_id=:identity_id", array(':identity_id'=>$order['identity_id']));
        //用户的所有订单信息
        $arrIdentity = mysqld_selectall("SELECT * FROM " . table('member_identity') . " WHERE status = 0 and openid = :openid", array(':openid' => $openid));
        include themePage('order/order_identity');
    }

    public function identity_submit()
    {
        $_GP      = $this->request;
        $member    = get_member_account();
        $openid    = $member['openid'];
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
    }

    public function show_wuliu()
    {
        $_GP      = $this->request;
        $member    = get_member_account();
        $openid    = $member['openid'];
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
    }

    function mobileGetOrderNextPage($listorder,$status_ln)
    {
        $html = '';
        if ( !empty($_POST['page']) ){
            if (is_array($listorder)) { foreach($listorder as $item) {
                $html .= '<div class="myoder">
						<div class="myoder-hd">
							<span class="pull-left">订单编号：'.$item['ordersn'].'</span>
							<span class="pull-right">'.date('Y-m-d H:i', $item['createtime']).'</span>
						</div>';
                if(count($item['goods'])==1) {
                    if(is_array($item['goods'])) { foreach($item['goods'] as $goods) {
                        $cate_name = getGoodsCategory($goods['p1']);
                        $html .='
							<div class="myoder-detail">
								<a href="'.mobile_url('detail', array('id' => $goods['goodsid'])).'"><img src="'.$goods['thumb'].'" width="160"></a>
								<div class="pull-left">
									<div class="name"><a href="'.mobile_url('detail', array('id' => $goods['goodsid'])).'">'.$goods['title'].'</a></div>
									<div class="price">
										<span>¥'.$goods['marketprice'].' * '.$goods['total'].'</span>
										<span style="padding: 0 3px; border: 1px solid #fe3d53;color: #fe3d53;font-size: 10px;display:inline-block;">'.$cate_name.'</span>
										<span class="btn btn-xs btn-success">'.getGoodsType($goods['ordertype']).'</span>
                                    </div>
                                </div>
                                <div class="status">'.$status_ln[$item['status']].'</div>
                            </div>';
                    } }  } else {
                    $html .='<div class="myoder-detail">';
                    if(is_array($item['goods'])) { foreach($item['goods'] as $goods) {
                        $cate_name = getGoodsCategory($goods['p1']);
                        $html .= '<a href="'.mobile_url('detail', array('id' => $goods['goodsid'])).'" style="width:140px;"><img src="'.$goods['thumb'].'" width="160">
                            <span style="padding: 0 3px; border: 1px solid #fe3d53;color: #fe3d53;font-size: 10px;display:inline-block;">'.$cate_name.'</span>
                        </a>';
                    } }
                    $html .= '<div class="status">'.$status_ln[$item['status']].'</div></div>';
                }

                $html .= '<div class="myoder-total">
		<span>共计：<span class="false">';
                $html .= $item['price'].' 元';
                $html .= '<span style="font-size: 12px;">(含运费: '.$item['dispatchprice'].' 元) </span>';
                $html .='</span>';
                if($item['hasbonus']>0) {
                    $html .='<span style="color:green;font-size: 12px;"> (优惠: '.$item['bonusprice'].' 元)</span>';
                }
                $html .='</span><a href="'.mobile_url('myorder', array('orderid' => $item['id'], 'op' => 'detail')).'" class="btn btn-default pull-right btn-sm" >订单详情</a>
                        </div>
                    </div>';
            }}
            echo $html;
            exit;
        }
    }

    function mobileGetOrderNextPage2($listorder,$status_ln)
    {
        $html = '';
        if ( !empty($_POST['page']) ){
            if (is_array($listorder)) { foreach($listorder as $item) {
                $html .= '<div class="myoder">
						<div class="myoder-hd">
							<span class="pull-left">订单编号：'.$item['ordersn'].'</span>
							<span class="pull-right">'.date('Y-m-d H:i', $item['createtime']).'</span>
						</div>';


                $goods = $item['goods'];
                $cate_name = getGoodsCategory($goods['p1']);
                $html .='
                    <div class="myoder-detail">
                        <a href="'.mobile_url('detail', array('id' => $goods['goodsid'])).'"><img src="'.$goods['thumb'].'" width="160"></a>
                        <div class="pull-left">
                            <div class="name"><a href="'.mobile_url('detail', array('id' => $goods['goodsid'])).'">'.$goods['title'].'</a></div>
                            <div class="price">
                                <span>¥'.$goods['marketprice'].' * '.$goods['total'].'</span>
                                <span style="padding: 0 3px; border: 1px solid #fe3d53;color: #fe3d53;font-size: 10px;display:inline-block;">'.$cate_name.'</span>
                                <span class="btn btn-xs btn-success">'.getGoodsType($goods['ordertype']).'</span>
                            </div>
                        </div>
                        <div class="status">'.$status_ln[$item['status']].'</div>
                    </div>';

                $html .= '<div class="myoder-total">
		<span>共计：<span class="false">';
                $html .= $item['price'].' 元';
                $html .= '<span style="font-size: 12px;">(含运费: '.$item['dispatchprice'].' 元) </span>';
                $html .='</span>';
                if($item['hasbonus']>0) {
                    $html .='<span style="color:green;font-size: 12px;"> (优惠: '.$item['bonusprice'].' 元)</span>';
                }
                $html .='</span><a href="'.mobile_url('myorder', array('orderid' => $item['id'], 'op' => 'detail')).'" class="btn btn-default pull-right btn-sm" >订单详情</a>
                        </div>
                    </div>';
            }}
            echo $html;
            exit;
        }
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

    function ajaxGetOrderStatus()
    {
        $_GP = $this->request;
        $orderid = intval($_GP['id']);
        $orders = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id", array(':id' => $orderid));

         die(json_encode($orders));
    }
}