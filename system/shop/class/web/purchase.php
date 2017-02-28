<?php
order_auto_close();//自动更新一些订单为关闭
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
$hasaddon16=false;
$normal_order_list = array();
$express_order_list = array();
$addon16=mysqld_select("SELECT * FROM " . table('modules') . " WHERE name = 'addon16' limit 1");
if(!empty($addon16['name'])){
	if(file_exists(ADDONS_ROOT.'addon16/key.php'))
	{
		$normal_order_list = mysqld_selectall("SELECT * FROM " . table('addon16_printer') . " WHERE  printertype=0 order by isdefault desc");
		$express_order_list = mysqld_selectall("SELECT * FROM " . table('addon16_printer') . " WHERE  printertype=1 order by isdefault desc");
		$hasaddon16=true;
	}
}
// 进行发货处理
if ( isset($_GP['shipment']) && isset($_GP['express']) && ($_GP['express'] != -1) && !empty($_GP['expressno']) && !empty($_GP['order_id']) ){
	   $ship_Data =  array('expresscom'=>$_GP['expresscom'], 'expresssn'=>$_GP['expressno'], 'express'=>$_GP['express']);
       foreach ( $_GP['shipment'] as $shipment_id ){
            mysqld_update('shop_order_goods', $ship_Data, array('orderid'=>$_GP['order_id'], 'goodsid'=> $shipment_id));
	   }
	   $check_ship = mysqld_select("SELECT * FROM ".table('shop_order_goods')." WHERE expresssn = '' and orderid = ".$_GP['order_id']);
	   if ( !$check_ship ){
            // 已经全部发货完毕，自动设置为确认发货
            mysqld_update('shop_order', array('status' => 2), array('status' => 1, 'id' => $_GP['order_id']));
	   }
	   $ship_Data['shipment'] = $_GP['shipment'];
	   die(showAjaxMess('200', $ship_Data)); 
}
$mess_list = array();
$dispatchlist = mysqld_selectall("SELECT * FROM " . table('dispatch')." where sendtype=0" );
$_mess    =  mysqld_selectall("SELECT * FROM " . table('shop_mess'));
if ($operation == 'display') {
	$pindex = max(1, intval($_GP['page']));
	$psize = 10;
	$status = !isset($_GP['status']) ? -110 : $_GP['status'];
	$sendtype = !isset($_GP['sendtype']) ? 0 : $_GP['sendtype'];
	$condition = 'A.ordertype=-2';   //只显示批发订单
	$param_ordersn=$_GP['ordersn'];

	//业务员只能查看跟自己有关联客户的订单
	if(isAgentAdmin()){
		$amdin_uid = $_SESSION['account']['id'];
		$condition .= " AND A.relation_uid={$amdin_uid}";
	}
	if (!empty($_GP['ordersn'])) {
		$condition .= " AND A.ordersn LIKE '%{$_GP['ordersn']}%'";
	}
    if (!empty($_GP['tag']) && $_GP['tag'] != -1) {
		$condition .= " AND A.tag ='".$_GP['tag']."'";
	}
	if (!empty($_GP['relation_uid']) && !isAgentAdmin()) {
		$condition .= " AND A.relation_uid ='".$_GP['relation_uid']."'";
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
		$condition .= " AND A.address_mobile  LIKE '%{$_GP['address_mobile']}%'";
	}

	$status_arr = array(-2,-4,14,34,-121,-321);//退货，退款 退货完成  退款完成 退款关闭  退货关闭 另外处理
	if(in_array($status,$status_arr)){
		//不处理
	}else if ($status == '-110' ) {
		//平台发货订单  我方承运
		$condition .= " AND A.sendtype=0 AND A.status != -1 ";
	}else if($status == '-100'){
		//为自提的订单
		$condition .= " AND A.sendtype=1 AND A.status != -1 ";
	}else if($status == '-99'){
		// 对于全部订单不显示关闭 和支付审核的订单
		$condition .= " AND A.status != -1 AND A.status != -7";
	}else{
		$condition .= " AND A.status = '" . intval($status) . "'";
	}

	$dispatchs    = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') );
	$dispatchdata = array();
	if(is_array($dispatchs)) {
		foreach($dispatchs as $disitem) {
			$dispatchdata[$disitem['id']]=$disitem;
		}
	}
	$selectCondition="LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
	if (!empty($_GP['report'])) {
		$selectCondition="";
	}

	if($status == -2){ //退款
		$sql    = "SELECT A.* FROM " . table('shop_order') . " A  left join ". table('shop_order_goods')." as C on A.id=C.orderid WHERE  {$condition} and C.type=3 and C.status in (1,2) group by A.id ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = "SELECT COUNT(distinct A.id) FROM " . table('shop_order') . " A  left join ". table('shop_order_goods')." as C on A.id=C.orderid WHERE  {$condition} and C.type=3 and C.status in (1,2) group by A.id";
	}else if($status == -4){  //退货
		$sql   = "SELECT A.* FROM " . table('shop_order') . " A  left join ". table('shop_order_goods')." as C on A.id=C.orderid WHERE  {$condition} and C.type=1 and C.status in (1,2,3) group by A.id ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = "SELECT COUNT(distinct A.id) FROM " . table('shop_order') . " A  left join ". table('shop_order_goods')." as C on A.id=C.orderid WHERE  {$condition} and C.type=1 and C.status in (1,2,3) group by A.id";
	}else if($status == 34){  //退款完成
		$sql    = "SELECT A.* from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=3 and B.status=4 group by A.id ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = "SELECT COUNT(distinct A.id) from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=3 and B.status=4 group by A.id";
	}else if($status == 14){  //退货完成
		$sql    = "SELECT A.* from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=1 and B.status=4 group by A.id ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = "SELECT COUNT(distinct A.id) from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=1 and B.status=4 group by A.id";
	}else if($status == -321){  //退款关闭
		$sql    = "SELECT A.* from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=3 and B.status in (-1,-2) group by A.id ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = "SELECT COUNT(distinct A.id) from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=3 and B.status in (-1,-2) group by A.id";
	}else if($status == -121){  //退货关闭
		$sql    = "SELECT A.* from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=1 and B.status in (-1,-2) group by A.id ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = "SELECT COUNT(distinct A.id) from ". table('shop_order') ." as A left join ". table('shop_order_goods'). " as B on A.id=B.orderid where  {$condition} AND B.type=1 and B.status in (-1,-2) group by A.id";
	}else{
		$sql    = "SELECT A.* FROM " . table('shop_order') . " A  WHERE   {$condition} ORDER BY  A.createtime DESC ".$selectCondition;
		$sqlNum = 'SELECT COUNT(A.id) FROM ' . table('shop_order') . " A WHERE   {$condition}";
	}

	$list = mysqld_selectall($sql);


	$total = mysqld_selectcolumn($sqlNum);
	$pager = pagination($total, $pindex, $psize);
	foreach ( $list as $id => $item) {
		$sql  = "select o.total,o.aid,o.optionname,o.shopgoodsid, o.id as order_id,o.optionid,o.price as orderprice, o.status as order_status, o.type as order_type,o.shop_type,o.expresssn,o.express,o.expresscom ";
		$sql .= " ,h.marketprice as dishprice,h.pcate,h.title,h.thumb,h.gid,h.p1 from ".table('shop_order_goods')." as o ";
		$sql .= " left join ".table('shop_dish')." as h ";
		$sql .= " on o.goodsid=h.id ";
		$sql .= " where o.orderid={$item['id']}";
		$goods = mysqld_selectall($sql);
		$list[$id]['goods'] = $goods;
	}

	getBackMonryOrGoodData($status,$list);

	if (!empty($_GP['report'])) {
		foreach ( $list as $id => $item) {
			$identity = mysqld_select("SELECT * FROM " . table('member_identity') . " WHERE identity_id=:identity_id", array(':identity_id'=>$item['identity_id']));
			$list[$id]['identity']	= $identity['identity_number'];
		}
		$report='orderreport';
		require_once 'report.php';
		exit;
	}
	//获取所有业务员
	$agentAdmin = getAllAgent();
	$hasaddon11=false;
	$addon11=mysqld_select("SELECT * FROM " . table('modules') . " WHERE name = 'addon11' limit 1");
	if(!empty($addon11['name']))
	{
		if(file_exists(ADDONS_ROOT.'addon11/key.php'))
		{
			$hasaddon11=true;
		}

	}
	if (empty($_GP['print'])){
		include page('purchase_list');
	}else{
		foreach($list as $key=>$item) {
			$orderid=intval($item['id']);
			$order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id=:id",array(":id"=>$orderid));
			if($order['hasbonus'])
			{

				$bonuslist = mysqld_selectall("SELECT bonus_user.*,bonus_type.type_name FROM " . table('bonus_user') . " bonus_user left join  " . table('bonus_type') . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id WHERE bonus_user.order_id=:order_id",array(":order_id"=>$orderid));

			}

			$dispatchlist = mysqld_selectall("SELECT * FROM " . table('dispatch')." where sendtype=0" );

			$payments = mysqld_selectall("SELECT * FROM " . table('payment') . " WHERE enabled = 1");
			$dispatchs = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') );
			$dispatchdata=array();
			if(is_array($dispatchs)) {
				foreach($dispatchs as $disitem) {
					$dispatchdata[$disitem['id']]=$disitem;
				}
			}
			$goods = mysqld_selectall("SELECT g.id,o.total, g.title, g.status,g.thumb, g.weight, g.goodssn,g.productsn,g.marketprice,h.pcate,g.type,o.optionname,o.aid,o.optionid,o.price as orderprice FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.shopgoodsid=g.id "
				. " left join ". table('shop_dish') . " h on o.goodsid = h.id WHERE o.orderid='{$orderid}'");
			$list[$key]['order']['goods'] = $goods;
		}
		include page('order_print');
	}
}