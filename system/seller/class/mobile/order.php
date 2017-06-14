<?php
namespace seller\controller;
use service\seller\ordersService;
use service\seller\wuliuService;
use service\seller\ShopDishService;
class order extends base
{
    public function __construct(){
        parent::__construct();
        $this->ordersService = new ordersService();
    }
	//订单列表
	public function lists()
	{
		$_GP = $this->request;
        $res = $this->ordersService->OrderListsPage($_GP);
        if($res){
            $order_lists=$res['order_lists'];
            $total=$res['total'];
            $pager=$res['pager'];
        }
        include page('order/orderList');
	}

    //退换单
    public function return_list()
    {
        $_GP = $this->request;
        $status = $_GP['status'];
        $result = $this->ordersService->returnListPage($_GP);//$type=1取退单处理中的，2取退换完成
        if (!empty($result) && !empty($result['returnList'])){
            $returnList = $result['returnList'];
            $ShopDishService = new ShopDishService();
            foreach ($returnList as $key=>$v){
                $shopInfo = $ShopDishService->getDishContent(array('dish_id'=>$v['dishid']),"title,marketprice");
                $returnList[$key]["title"] = $shopInfo['title'];
                $returnList[$key]["marketprice"] = FormatMoney($shopInfo['marketprice'],0);
            }
            $total=$result['total'];
            $pager=$result['pager'];
        }
        include page('order/return_list');
    }

	//订单详情
	public function detail()
	{
		$_GP = $this->request;
        if($_GP['id']){
            $info = $this->ordersService->OrderDetailPage($_GP['id']);
            $wuliuService = new wuliuService();
            $dispatchList = $wuliuService->getStoreDispatchList(2);
        }
		include page('order/orderDetail');
	}
	//修改订单地址
	public function modifyaddress(){
	    $_GP = $this->request;
	    $id = intval($_GP['id']);
	    if(empty($id)) message('参数有误！',refresh(),'error');
	    //做备注
	    $order = $this->ordersService->getOrderInfo($_GP['id'],"retag");
	    $json_retag = setOrderRetagInfo($order['retag'], '修改订单：修改了订单的收货人信息');
	    //修改订单表
	    $_GP['retag'] = $json_retag;
	    $flag = $this->ordersService->modifyAddress($_GP);
	    
	    if ($flag) message('修改成功！',refresh(),'success');
	}
    //修改物流信息
    public function confirmsend(){
        $_GP = $this->request;
        $orderid = intval($_GP['id']);
        if ($_GP['express']=="" || empty($_GP['expresssn']) || empty($_GP['expresscom'])) {
            message('请选择快递并输入快递单号！');
        }
        //判断是否可以发货
        $order = $this->ordersService->getOrderInfo($orderid);
        if (empty($order) || (!is_array($order))) message('参数有误,查无该订单信息',refresh(),'error');
        if ($order['status'] != 1) message('该订单暂时不能发货!',refresh(),'error');
        $orderGoodInfo = $this->ordersService->getOrderGoodsInfo($orderid);
        if(!isSureSendGoods($orderGoodInfo)) message('不能发货，该订单有部分商品还没处理完!',refresh(),'error');
        //做备注
        $json_retag = setOrderRetagInfo($order['retag'], '发货：已经确认发货');
        //修改订单表
        $_GP['retag'] = $json_retag;
        $res = $this->ordersService->AddSendInfo($_GP);
        if($res){
            message('发货操作成功！', refresh(), 'success');
        }else{
            message('操作失败！', refresh(), 'error');
        }
    }
    //修改订单状态
    public function check_status(){
        $_GP = $this->request;
        $id = intval($_GP['id']);
        if (empty($id)) message('操作失败！', refresh(), 'error');
        $type = $_GP['type'];
        if ($type == 'finish'){
            //完成相当于确认收货
            $data = hasFinishGetOrder($id);
            if($data['errno'] == 200){
                message($data['message'],refresh(),'success');
            }else{
                message($data['message'],refresh(),'error');
            }
        }elseif ($type == 'close'){//关闭订单
            //退还余额和优惠卷 并关闭订单
            update_order_status($_GP['id'],-1);
            //记录管理员操作日志
            $order      = mysqld_select("select retag from ". table('shop_order') ." where id={$_GP['id']}");
            $json_retag = setOrderRetagInfo($order['retag'], '关闭订单：关闭了订单');
            mysqld_update('shop_order', array('retag'=>$json_retag), array('id' => $_GP['id']));
            message('订单关闭操作成功！', refresh(), 'success');
        }elseif ($type == 'open'){ //开启订单
            $orderGoodInfo = mysqld_selectall("select * from ". table('shop_order_goods') ." where orderid={$_GP['id']}");
            if(!isSureOpenGoods($orderGoodInfo))
                message("该订单的所有商品都退款退货了，不允许开启订单",refresh(),'error');
            
            $order      = mysqld_select("select retag from ". table('shop_order') ." where id={$_GP['id']}");
            $json_retag = setOrderRetagInfo($order['retag'], '开启订单：开启了订单');
            mysqld_update('shop_order', array('status' => 0,'retag'=>$json_retag,'closetime'=>0), array('id' => $_GP['id']));
            message('开启订单操作成功！', refresh(), 'success');
        }
    }
}