<?php
namespace shop\controller;

class order extends \common\controller\basecontroller
{
	public function display()
	{
		$_GP = $this->request;
		$pindex    = max(1, intval($_GP['page']));
		$psize     = 15;
		$status    = $_GP['status'];
		$condition = '1=1';

		if($status!= null && $status == 0){
			$condition .= " AND A.status=0";
		}else{
			if(!empty($status)){
				$condition .= " AND A.status={$status}";
			}
		}

		if (!empty($_GP['ordersn'])) {
			$condition .= " AND A.ordersn = '{$_GP['ordersn']}'";
		}
		if (!empty($_GP['tag']) && $_GP['tag'] != -1) {
			$condition .= " AND A.tag ='".$_GP['tag']."'";
		}
		if (!empty($_GP['paytype'])) {
			$condition .= " AND A.paytypecode ='".$_GP['paytype']."'";
		}
		if (!empty($_GP['dispatch'])) {
			$condition .= " AND A.dispatch =".intval($_GP['dispatch']);
		}
		if (!empty($_GP['endtime'])) {
			$condition .= " AND A.createtime  <= ". strtotime($_GP['endtime']);
		}
		if (!empty($_GP['begintime'])) {
			$condition .= " AND A.createtime  >= ". strtotime($_GP['begintime']);
		}

		if (!empty($_GP['address_realname'])) {
			$condition .= " AND A.address_realname  LIKE '%{$_GP['address_realname']}%'";
		}
		if (!empty($_GP['address_mobile'])) {
			$condition .= " AND A.address_mobile  = '{$_GP['address_mobile']}'";
		}
		if (!empty($_GP['mobile'])) {
			//按照买主 查询
			$buyer = mysqld_select("select openid from ".table('member')." where mobile={$_GP['mobile']}");
			if(!empty($buyer))
				$condition .= " AND A.openid ='{$buyer['openid']}'";
		}
		$sts_id = $_GP['sts_id'];
		if (!empty($sts_id))
		{
		    $condition .= " and A.sts_id = {$sts_id} ";
		    $storeInfo = mysqld_select("select sts_name,sts_id as id from ".table('store_shop')." where sts_id=:sts_id",array('sts_id'=>$sts_id));
		}

		$selectCondition="LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		if (!empty($_GP['report'])) {
			$selectCondition="";
		}

		$sql    = "SELECT A.* FROM " . table('shop_order') . " A  WHERE  {$condition} ORDER BY  A.id  DESC ".$selectCondition;
		$sqlNum = 'SELECT COUNT(A.id) FROM ' . table('shop_order') . " A WHERE  {$condition}";
		
		$list   = mysqld_selectall($sql);
		$total  = mysqld_selectcolumn($sqlNum);
		$pager  = pagination($total, $pindex, $psize);

		foreach ( $list as $id => $item) {
			$stsinfo = member_store_getById($item['sts_id'],'sts_name');
			$sql  = "select o.total,o.dishid, o.id as order_id,o.price as orderprice, o.status as order_status, o.type as order_type,o.shop_type ";
			$sql .= " ,h.marketprice as dishprice,h.title,h.thumb,h.draw,h.goodssn from ".table('shop_order_goods')." as o ";
			$sql .= " left join ".table('shop_dish')." as h ";
			$sql .= " on o.dishid=h.id ";
			$sql .= " where o.orderid={$item['id']}";
			$goods = mysqld_selectall($sql);
			$list[$id]['goods']    = $goods;
			$list[$id]['sts_name'] = $stsinfo['sts_name'];
			$list[$id]['price']    = FormatMoney($list[$id]['price'],0);
			$list[$id]['balance_sprice'] = FormatMoney($list[$id]['balance_sprice'],0);
			$list[$id]['bonusprice']     = FormatMoney($list[$id]['bonusprice'],0);
		}


		if (!empty($_GP['report'])) {
			foreach ( $list as $id => $item) {
				$identity = mysqld_select("SELECT * FROM " . table('member_identity') . " WHERE identity_id=:identity_id", array(':identity_id'=>$item['identity_id']));
				$list[$id]['identity']	= $identity['identity_number'];
			}
			$report='orderreport';
			require_once 'report.php';
			exit;
		}

		$payments = mysqld_selectall("SELECT * FROM " . table('payment') . " WHERE enabled = 1");

		include page('order/order_list');
	}

	public function returnDish()
	{
		$_GP = $this->request;
		$pindex    = max(1, intval($_GP['page']));
		$psize     = 15;
		$limit     = "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		$status    = $_GP['status'] ?: 1;
		if($status == 23){
			$where  = "o.status=2 or o.status=3";
		}else{
			$where  = "o.status={$status}";
		}

		$filed = "o.total,o.dishid, o.id as order_id,o.orderid as oo_id,o.price as orderprice, o.status as order_status, o.type as order_type,o.shop_type";
		$filed .= " ,h.marketprice as dishprice,h.title,h.thumb,h.draw,h.goodssn";

		$sql = "select  {$filed} from ".table('shop_order_goods')." as o ";
		$sql .= " left join ".table('shop_dish')." as h ";
		$sql .= " on o.dishid=h.id";
		$sql .= " where {$where} order by o.id desc {$limit}";

		$sqlnum = "select count(o.id) from ".table('shop_order_goods')." as o ";
		$sqlnum .= " left join ".table('shop_dish')." as h ";
		$sqlnum .= " on o.dishid=h.id";
		$sqlnum .= " where {$where}";

		$list = mysqld_selectall($sql);
		$total = mysqld_selectcolumn($sqlnum);
		$pager  = pagination($total, $pindex, $psize);

		$list_data = array();
		foreach($list as $key=> $item){
			$order   = mysqld_select("select * from ".table('shop_order')." where id={$item['oo_id']}");
			$stsinfo = member_store_getById($order['sts_id'],'sts_name');
			$order['sts_name'] = $stsinfo['sts_name'];
			$order['price']    = FormatMoney($order['price'],0);
			$order['balance_sprice'] = FormatMoney($order['balance_sprice'],0);
			$order['bonusprice']     = FormatMoney($order['bonusprice'],0);

			$list_data[$key] = $order;
			$list_data[$key]['goods'] = $item;
		}

		include page('order/return_list');
	}

	/**
	 * 等待返还金额
	 */
	public function waitReturn()
	{
		$_GP = $this->request;
		$pindex    = max(1, intval($_GP['page']));
		$psize     = 15;
		$limit     = "LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		$status    = 4;
		$where  = "o.status={$status} and finish_return_money=0";

		$filed = "o.total,o.dishid, o.id as order_id,o.orderid as oo_id,o.price as orderprice, o.status as order_status, o.type as order_type,o.shop_type";
		$filed .= " ,h.marketprice as dishprice,h.title,h.thumb,h.draw,h.goodssn";

		$sql = "select  {$filed} from ".table('shop_order_goods')." as o ";
		$sql .= " left join ".table('shop_dish')." as h ";
		$sql .= " on o.dishid=h.id";
		$sql .= " where {$where} order by o.id desc {$limit}";

		$sqlnum = "select count(o.id) from ".table('shop_order_goods')." as o ";
		$sqlnum .= " left join ".table('shop_dish')." as h ";
		$sqlnum .= " on o.dishid=h.id";
		$sqlnum .= " where {$where}";

		$list = mysqld_selectall($sql);
		$total = mysqld_selectcolumn($sqlnum);
		$pager  = pagination($total, $pindex, $psize);

		$list_data = array();
		foreach($list as $key=> $item){
			$order   = mysqld_select("select * from ".table('shop_order')." where id={$item['oo_id']}");
			$stsinfo = member_store_getById($order['sts_id'],'sts_name');
			//找出退款金额
			$afterSale = mysqld_select("select refund_price,refund_gold from ".table('aftersales')." where order_goods_id={$item['id']}");
			$return_money = FormatMoney(intval($afterSale['refund_price'])+intval($afterSale['refund_gold']),0);

			$order['sts_name'] = $stsinfo['sts_name'];
			$order['price']    = FormatMoney($order['price'],0);
			$order['balance_sprice'] = FormatMoney($order['balance_sprice'],0);
			$order['bonusprice']     = FormatMoney($order['bonusprice'],0);

			$list_data[$key] = $order;
			$list_data[$key]['goods']        = $item;
			$list_data[$key]['return_money'] = $return_money;
		}

		include page('order/wait_list');
	}

	public function detail()
	{
		$_GP = $this->request;
		$orderid=intval($_GP['id']);
		$order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id=:id",array(":id"=>$orderid));
		if($order['hasbonus']) {
			$bonuslist = mysqld_selectall("SELECT * FROM " . table('store_coupon_member') . " where ordersn=:order_id ",array(":order_id"=>$orderid));
		}
        if ($order['paytype']){
            switch ($order['paytype']){
                case 1:
                    $order['paytypename'] = '余额付款';//'1为余额，2为在线，3为到付',
                    break;
                case 2:
                    $order['paytypename'] = '在线付款';
                    break;
                case 3:
                    $order['paytypename'] = '货到到付';
                    break;
            }
        }
		/* $dispatchlist = mysqld_selectall("SELECT * FROM " . table('dispatch')." where sendtype=0 order by sort desc" );

		$dispatchs    = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') );
		$dispatchdata = array();
		if(is_array($dispatchs)) {
			foreach($dispatchs as $disitem) {
				$dispatchdata[$disitem['id']]=$disitem;
			}
		} */

		$goods = mysqld_selectall("SELECT d.*,o.total,o.id as order_id,o.total,o.price as orderprice,o.status as order_status, o.type as order_type FROM " . table('shop_order_goods') . " o left join " . table('shop_dish') . " d on o.dishid=d.id "
			. " WHERE o.orderid='{$orderid}'");
		$order['goods'] = $goods;
		//确认是否可以展示发货按钮  部分团购商品已经支付，不能显示发货按钮
		$ishowSendBtn = checkGroupBuyCanSend($order);
		if (checksubmit('reset')) { //确认标记
			$retag = '';
			if(!empty($order['retag'])){
				$retag = json_decode($order['retag'],true);
			}
			$retag['beizhu'] = $_GP['retag'];
			$json_retag = json_encode($retag);

			mysqld_update('shop_order', array('tag' => $_GP['tag'], 'retag' => $json_retag), array('id' => $orderid));
			message('订单操作成功！', refresh(), 'success');
		}

		include page('order/order_detail');
	}

	/**
	 * 确认支付
	 */
	public function confrimpay()
	{
		$_GP = $this->request;
		if(empty($_GP['id'])){
			message('参数有误！', refresh(), 'error');
		}
		paySuccessProcess($_GP['id']);
		message('操作成功！', refresh(), 'success');
	}

	/**
	 * 确认发货
	 */
	public function confirmsend()
	{
		$_GP = $this->request;
		$orderGoodInfo = mysqld_selectall("select * from ". table('shop_order_goods') ." where orderid={$_GP['id']}");
		$order         = mysqld_select("select * from ". table('shop_order') ." where id={$_GP['id']}");
		if ($_GP['express']=="-1" || empty($_GP['expresssn'])) {
			message('请选择快递并输入快递单号！');
		}
		$express=$_GP['express'];

		if(!isSureSendGoods($orderGoodInfo)){
			message('不能发货，该订单有部分商品还没处理完!',refresh(),'error');
		}
		if(!checkGroupBuyCanSend($order)){
			//如果不能发货
			message('不能发货，该团购订单有商品可能还在开奖中！',refresh(),'error');
		}
		$json_retag = setOrderRetagInfo($order['retag'], '发货：已经确认发货');
		$res = mysqld_update('shop_order', array(
			'status'     => 2,
			'retag'      => $json_retag,
			'express'    => $_GP['express'],
			'expresscom' => $_GP['expresscom'],
			'expresssn'  => $_GP['expresssn'],
			'sendtime'   =>time()  //发货时间
		), array('id' => $_GP['id']));
		message('发货操作成功！', refresh(), 'success');
	}

	/**
	 * 开启订单
	 */
	public function open()
	{
		$_GP = $this->request;
		$orderGoodInfo = mysqld_selectall("select * from ". table('shop_order_goods') ." where orderid={$_GP['id']}");
		if(!isSureOpenGoods($orderGoodInfo))
			message("该订单的所有商品都退款退货了，不允许开启订单",refresh(),'error');

		$order      = mysqld_select("select retag from ". table('shop_order') ." where id={$_GP['id']}");
		$json_retag = setOrderRetagInfo($order['retag'], '开启订单：开启了订单');
		mysqld_update('shop_order', array('status' => 0,'retag'=>$json_retag,'closetime'=>0), array('id' => $_GP['id']));
		message('开启订单操作成功！', refresh(), 'success');
	}

	/**
	 * 关闭订单
	 */
	public function close()
	{
		$_GP = $this->request;
		//退还余额和优惠卷 并关闭订单
		update_order_status($_GP['id'],-1);
		//记录管理员操作日志
		$order      = mysqld_select("select retag from ". table('shop_order') ." where id={$_GP['id']}");
		$json_retag = setOrderRetagInfo($order['retag'], '关闭订单：关闭了订单');
		mysqld_update('shop_order', array('retag'=>$json_retag), array('id' => $_GP['id']));
		message('订单关闭操作成功！', refresh(), 'success');

	}

	//完成相当于确认收货
	public function finish()
	{
		$_GP  = $this->request;
		$data = hasFinishGetOrder($_GP['id']);
		if($data['errno'] == 200){
			message($data['message'],refresh(),'success');
		}else{
			message($data['message'],refresh(),'error');
		}
	}

	/**
	 * 修改用户信息
	 */
	public function modifyaddress()
	{
		$_GP  = $this->request;
		$id   = $_GP['id'];
		if(empty($id))
			message('参数有误！',refresh(),'error');

		$order      = mysqld_select("select retag from ". table('shop_order') ." where id={$_GP['id']}");
		$json_retag = setOrderRetagInfo($order['retag'], '修改订单：修改了订单的收货人信息');
		mysqld_update('shop_order',array(
			'retag'			   => $json_retag,
			'address_realname' => $_GP['address_realname'],
			'address_mobile'   => $_GP['address_mobile'],
			'address_province' => $_GP['address_province'],
			'address_city'     => $_GP['address_city'],
			'address_area'     => $_GP['address_area'],
			'address_address'  => $_GP['address_address']
		),array('id'=>$id));
		message('修改成功！',refresh(),'success');
	}

	//查看清关材料
	public function identity()
	{
		$_GP  = $this->request;
		$orderid = intval($_GP['id']);

		//订单信息
		$order 		= mysqld_select("SELECT identity_id,relation_uid,addressid FROM " . table('shop_order') . " WHERE id=:id",array(":id"=>$orderid));

		$identity 	= mysqld_select("SELECT * FROM " . table('member_identity') . " WHERE identity_id=:identity_id", array(':identity_id'=>$order['identity_id']));
		include page('order/order_identity');
	}

	//退款详情
	public function aftersale_detail()
	{
		$_GP  = $this->request;
		if(empty($_GP['order_good_id'])){
			message('对不起参数有误！',refresh(),'error');
		}
		$orderid = $_GP['orderid'];

		$afterSale       = mysqld_select("select * from ". table('aftersales') ." where order_goods_id={$_GP['order_good_id']}");
		$afterSaleLog    = mysqld_selectall("select * from ". table('aftersales_log') ." where aftersales_id={$afterSale['aftersales_id']} order by log_id asc");
		$afterSaleDialog = mysqld_selectall("select * from ".table('aftersales_dialog')." where aftersales_id={$afterSale['aftersales_id']} order by id asc");
		$order        = mysqld_select("select id,type,status,price,taxprice,total from ". table('shop_order_goods') ." where id={$_GP['order_good_id']}");		//订单商品信息
		$orderInfo		= mysqld_select("select price,goodsprice,balance_sprice,freeorder_price from ". table('shop_order') ." where id={$orderid}");			//订单信息

		//物流货运
		$dispatchlist = mysqld_selectall("SELECT code,name FROM " . table('dispatch')." where sendtype=0" );
		$delivery_corp = $delivery_no = '';   //快递公司和单号
		if(!empty($afterSale['sendback_data'])){
			$sendback_data = unserialize($afterSale['sendback_data']);
			$delivery_name = $delivery_corp = $sendback_data['delivery_corp'] ;
			$delivery_no   = $sendback_data['delivery_no'];
			foreach($dispatchlist as $val){
				if($val['code'] == $delivery_corp){
					$delivery_name = $val['name'];
				}
			}
		}

		$picArr = '';
		if(!empty($afterSale['evidence_pic'])){
			$picArr = explode(";",$afterSale['evidence_pic']);
		}
		// `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '申请状态，-2为撤销申请，-1为审核驳回，0为未申请，1为正在申请，2为审核通过，3为退款成功',
		if($_GP['type'] == 'money'){
			$title = '退款';
			$statusArr = array('-2'=>'撤销申请','-1'=>'审核驳回','1'=>'申请退款','2'=>'审核通过','4'=>'退款成功');
		}else{
			$title = '退货';
			$statusArr = array('-2'=>'撤销申请','-1'=>'审核驳回','1'=>'申请退货','2'=>'审核通过','3'=>'买家退货','4'=>'退货成功');
		}

		include page('order/aftersale_detail');
	}

	//协商内容记录
	public function aftersale_dialog()
	{
		$_GP  = $this->request;
		if(empty($_GP['aftersales_id'])){
			message('参数有误!',refresh(),'error');
		}
		if(empty($_GP['content'])){
			message('内容不能为空!',refresh(),'error');
		}
		$data = array(
			'aftersales_id' => $_GP['aftersales_id'],
			'role'		    => 1,
			'content'	    => $_GP['content'],
			'createtime'	=> date('Y-m-d H:i:s')
		);
		mysqld_insert('aftersales_dialog',$data);
		if(mysqld_insertid()){
			message('操作成功!',refresh(),'success');
		}else{
			message('操作失败!',refresh(),'error');
		}
	}

	//平台处理是否退换货
	public function aftersale_chuli()
	{
		$_GP  = $this->request;
		if(empty($_GP['refund_price']) && empty($_GP['refund_gold']) && empty($_GP['refund_freeorder_price']) && $_GP['status'] == '2')
			message('退款现金或余额不能为空!',refresh(),'error');

		$afterSale = mysqld_select("select * from ". table('aftersales') ." where order_goods_id={$_GP['order_good_id']}");
		$orderInfo = mysqld_select("select id,price,goodsprice,balance_sprice,freeorder_price from ". table('shop_order') ." where id={$_GP['orderid']}");			//订单信息

		if(!empty($afterSale)){
			$data = array(
				'admin_explanation' => $_GP['admin_explanation'],
				'modifiedtime'      => date("Y-m-d H:i:s")
			);
			if($_GP['status'] == '2'){

				$arrRefund = $this->filterRefundInfo($orderInfo,array('refund_price'=>$_GP['refund_price'],'refund_gold'=>$_GP['refund_gold'],'refund_freeorder_price'=>$_GP['refund_freeorder_price']));

				$data['refund_price']  			= $arrRefund['refund_price'];
				$data['refund_gold']  			= $arrRefund['refund_gold'];				//返还余额
				$data['refund_freeorder_price'] = $arrRefund['refund_freeorder_price'];		//返还免单余额
			}
			mysqld_update('aftersales',$data,array('aftersales_id'=>$afterSale['aftersales_id']));

			//插入一条log记录
			$arrLogContent                 = array();
			$arrLogContent['description']  = $_GP['admin_explanation'];

			$data = array(
				'aftersales_id'  => $afterSale['aftersales_id'],
				'order_goods_id' => $_GP['order_good_id'],
				'status' 		 => $_GP['status'],
				'content'        => serialize($arrLogContent),
				'createtime' 	 => date("Y-m-d H:i:s")
			);

			if($_GP['type'] == 'money') {  //表示退款
				if($_GP['status'] == 2){
					$data['title']  = "掌门，您好！卖家同意本次退款申请";
				}else{
					$data['title']  = "掌门，很遗憾...您的退款申请被拒绝";
				}
			}else{
				if($_GP['status'] == 2){
					$data['title']  = "掌门，您好！卖家同意本次退款退货申请";
				}else{
					$data['title']  = "掌门，很遗憾...您的退货退款申请被拒绝";
				}
			}

			mysqld_insert('aftersales_log',$data);

			if(mysqld_insertid()){
				mysqld_update('shop_order_goods',array('status'=>$_GP['status']),array('id'=>$_GP['order_good_id']));
				//加入订单操作日志
				$order_retag = mysqld_select("select o.retag,o.id from ".table('shop_order')." as o left join ".table('shop_order_goods')." as g on o.id=g.orderid where g.id={$_GP['order_good_id']}");
				$json_retag  = setOrderRetagInfo($order_retag['retag'], "售后处理：{$data['title']}");
				mysqld_update('shop_order',array('retag'=>$json_retag),array('id'=>$order_retag['id']));

				$url = web_url('order',array('op'=>'detail','id'=>$_GP['orderid']));
				message('操作成功！',$url,'success');
			}
		}else{
			message('对不起，售后记录不存在!',refresh(),'error');
		}
	}

	//财务确认退钱
	public function sureBackMoney()
	{
		$_GP = $this->request;
		$order_good_id = $_GP['order_good_id'];
		$order_id      = $_GP['order_id'];
		$aftersales   = mysqld_select("select aftersales_id,refund_price,refund_gold,refund_freeorder_price from ". table('aftersales') ." where order_goods_id={$order_good_id}");
		$orderInfo    = mysqld_select("select * from ". table('shop_order') ." where id={$order_id}");

		if(empty($aftersales)||empty($orderInfo))
			message('对不起，记录不存在!',refresh(),'error');

		//修改订单状态
		$res = mysqld_update('shop_order_goods', array('status' => 4), array('id' => $order_good_id));
		//加入订单操作日志
		$json_retag  = setOrderRetagInfo($orderInfo['retag'], "售后处理：财务确认打款");
		mysqld_update('shop_order', array('retag' => $json_retag), array('id' => $orderInfo['id']));
		if($res){

			//有现金退款时
			if($aftersales['refund_price']>0)
			{
				//paylog记录
				$mark = PayLogEnum::getLogTip('LOG_BACK_THIRD_TIP');
				member_gold($orderInfo['openid'],$aftersales['refund_price'],'addgold',$mark,false,$orderInfo['id']);
			}

			$orderAllGood = mysqld_selectall("select id,status,type from ". table('shop_order_goods') ." where orderid={$order_id}");
			$num = 0;
			foreach($orderAllGood as $row){
				if($row['type'] != 0 && $row['status'] == 4)
					$num ++;
			}
			if($num == count($orderAllGood))  //如果商品全部都发生退款退货则，关闭该总订单状态
				mysqld_update('shop_order', array('status' => -1,'closetime'=>time()), array('id' => $order_id));

			//记录售后日志
			$xinxi = "卖家已经给您退款现金¥{$aftersales['refund_price']}元; 返还余额：{$aftersales['refund_gold']}; 返还免单余额：{$aftersales['refund_freeorder_price']}";
			$descript = array('description'=>$xinxi);
			$data = array(
				'aftersales_id'  => $aftersales['aftersales_id'],
				'order_goods_id' => $order_good_id,
				'status' 		 => 4,
				'title'          => '财务已经退款',
				'content'        => serialize($descript),
				'createtime' 	 => date("Y-m-d H:i:s")
			);
			mysqld_insert('aftersales_log',$data);

			//返还免单金额
			$this->returnPriceToMember($orderInfo,$aftersales);

			message('退款操作成功！', refresh(), 'success');
		}else{
			if($_GP['type'] == 'money')
				mysqld_update('shop_order_goods', array('status' => 2), array('id' => $order_good_id));
			else
				mysqld_update('shop_order_goods', array('status' => 3), array('id' => $order_good_id));
			message('操作失败',refresh(),'error');
		}
	}

	public function getAdminName()
	{
		$_GP = $this->request;
		$uid   = $_GP['uid'];
		$admin = 'xxx';
		if(!empty($uid)){
			$admin = getAdminName($uid);
		}
		die(showAjaxMess(200,$admin));
	}

	public function refundbat()
	{
		message('已经关闭该功能!',refresh(),'error');
	}

	/**
	 * 对现金、余额等进行过滤
	 *
	 * @param  $orderInfo : array 订单信息
	 * @param  $arrRefund : array 返还的金额数组
	 *
	 * @return $arrRefund : array 过滤后的金额数组
	 */
	public function filterRefundInfo($orderInfo,$arrRefund)
	{
		//同一笔订单中已有的退款记录
		$arrAftersales 	= mysqld_selectall("SELECT og.id,a.refund_price,a.refund_gold,a.refund_freeorder_price FROM ".table('shop_order_goods')." og,".table('aftersales')." a WHERE og.id=a.order_goods_id and og.orderid=".$orderInfo['id']);


		#################### 现金  start####################
		//使用了现金时
		if (!empty($orderInfo['price'])) {

			//扣除已有的现金退款记录
			if(!empty($arrAftersales))
			{
				foreach($arrAftersales as $value)
				{
					$orderInfo['price'] = $orderInfo['price']-(float)$value['refund_price'];
				}
			}

			//退回现金大于下单时使用的现金金额时
			if($arrRefund['refund_price']>$orderInfo['price'])
			{
				$arrRefund['refund_price'] = $orderInfo['price'];
			}
		}
		else{
			$arrRefund['refund_price'] = 0.00;
		}

		//返还现金比0.00元少时
		if($arrRefund['refund_price']<0.00)
		{
			$arrRefund['refund_price'] = 0.00;
		}
		#################### 现金  end####################



		#################### 余额  start####################
		//使用了余额时
		if ($orderInfo['balance_sprice']>0) {

			//扣除已有的余额退款记录
			if(!empty($arrAftersales))
			{
				foreach($arrAftersales as $value)
				{
					$orderInfo['balance_sprice'] = $orderInfo['balance_sprice']-(float)$value['refund_gold'];
				}
			}

			//退回免单余额大于下单时使用的免单金额时
			if($arrRefund['refund_gold']>$orderInfo['balance_sprice'])
			{
				$arrRefund['refund_gold'] = $orderInfo['balance_sprice'];
			}

		}
		else{
			$arrRefund['refund_gold'] = 0.00;
		}

		//返还余额比0.00元少时
		if($arrRefund['refund_gold']<0.00)
		{
			$arrRefund['refund_gold'] = 0.00;
		}
		#################### 余额  end####################



		#################### 免单余额  start####################
		//使用了免单余额时
		if ($orderInfo['freeorder_price']>0) {

			//扣除已有的余额退款记录
			if(!empty($arrAftersales))
			{
				foreach($arrAftersales as $value)
				{
					$orderInfo['freeorder_price'] = $orderInfo['freeorder_price']-(float)$value['refund_freeorder_price'];
				}
			}

			//退回免单余额大于下单时使用的免单金额时
			if($arrRefund['refund_freeorder_price']>$orderInfo['freeorder_price'])
			{
				$arrRefund['refund_freeorder_price'] = $orderInfo['freeorder_price'];
			}
		}
		else{
			$arrRefund['refund_freeorder_price'] = 0.00;
		}

		//返还免单余额比0.00元少时
		if($arrRefund['refund_freeorder_price']<0.00)
		{
			$arrRefund['refund_freeorder_price'] = 0.00;
		}
		#################### 免单余额  end####################


		return $arrRefund;
	}

	/**
	 * 返还余额及免单金额
	 *
	 * @param $orderInfo:array 订单信息数组
	 * @param $aftersales: 退款信息数组
	 *
	 */
	public function returnPriceToMember($orderInfo,$aftersales)
	{
		//有返还余额或者免单金额时
		if ($aftersales['refund_gold']>0 || $aftersales['refund_freeorder_price']>0 ) {

			//用户信息
			$mem = mysqld_select("SELECT gold,freeorder_gold_endtime,freeorder_gold FROM ".table('member')." WHERE openid='".$orderInfo['openid']."'");

			if($aftersales['refund_gold']>0)
			{
				$memberData['gold'] = $mem['gold']+$aftersales['refund_gold'];

				//记录用户账单的余额收支情况
				insertMemberPaylog($orderInfo['openid'], $aftersales['refund_gold'],$memberData['gold'], 'addgold',PayLogEnum::getLogTip('LOG_BACK_CASH_TIP'),$orderInfo['ordersn']);
			}

			if($aftersales['refund_freeorder_price']>0)
			{
				$freeorder_gold_endtime = strtotime('Sunday')+24*3600-1;						//周天的23:59:59

				//已有本期免单金额时
				if($mem['freeorder_gold_endtime']==$freeorder_gold_endtime)
				{
					$memberData['freeorder_gold'] 			= $aftersales['refund_freeorder_price']+$mem['freeorder_gold'];
					$memberData['freeorder_gold_endtime'] 	= $freeorder_gold_endtime;
				}
				else{
					$memberData['freeorder_gold'] 			= $aftersales['refund_freeorder_price'];
					$memberData['freeorder_gold_endtime'] 	= $freeorder_gold_endtime;
				}

				//记录用户账单的免单金额收支情况
				insertMemberPaylog($orderInfo['openid'], $aftersales['refund_freeorder_price'],$memberData['freeorder_gold'], 'addgold',PayLogEnum::getLogTip('LOG_BACK_FREE_TIP'),$orderInfo['ordersn']);
			}

			mysqld_update ('member',$memberData,array('openid' =>$orderInfo['openid']));
		}
	}
	public function store_search(){
	    $_GP = $this->request;
        $reData = array();
        $sts_name = $_GP['sts_name'];
        $sql = "select sts_id as id,sts_name from ".table('store_shop')." where sts_name like '%{$sts_name}%' ";
        $reData['store'] = mysqld_selectall($sql);
        echo json_encode($reData);
        exit;
	}
}

