<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2017/4/19
 * Time: 14:35
 */
namespace api\controller;
use service\seller\ordersService;
use service\seller\ShopDishService;
use service\seller\wuliuService;
class order extends base
{
    public function __construct(){
         parent::__construct();
         $this->ordersService = new ordersService();
    }
    //订单主页接口
    public function orderIndex(){
        $return = $this->ordersService->OrderIndexPage();
        if($return){
            ajaxReturnData(1,'',$return);
        }else{
            ajaxReturnData(1,$this->ordersService->getError());
        }
    }
    //订单列表接口
    public function orderList(){
        $_GP = $this->request;
        $res = $this->ordersService->OrderListsPage($_GP);
        if($res){
            $order_lists=$res['order_lists'];
            $allPrice = '';
            foreach ($order_lists as $key=>$v){
                $goodinfo = $this->ordersService->getOrderGoodsDetail($v['id']);
                if (!empty($goodinfo)){
                    $goods_total = 0;
                    foreach ($goodinfo as $k=>$val){
                        $data[$k]['thumb'] = $val['thumb'];
                        $data[$k]['title'] = $val['title'];
                        $data[$k]['total'] = $val['total'];
                        $data[$k]['status_name'] = $val['status_name'];
                        $data[$k]['goods_status'] = $val['order_status'];
                        $spec_key_name = json_decode($val['spec_key_name'],1);
                        if (!empty($spec_key_name)) $data[$k]['spec_key_name'] = array_values($spec_key_name);
                        $goods_total += $val['total'];
                        $data[$k]['marketprice'] = FormatMoney($val['marketprice'],0);
                        $data[$k]['productprice'] = FormatMoney($val['productprice'],0);
                    }
                    $temp['goodinfo'] = $data;
                    $temp['orderid'] = $v['id'];
                    $temp['remark'] = $v['beizhu'];
                    $temp['openid'] = $v['openid'];
                    $temp['ordersn'] = $v['ordersn'];
                    $temp['status_name'] = $v['status_name'];
                    $temp['createtime'] = $v['createtime'];
                    $temp['nickname'] = $v['nickname'];
                    $temp['price'] = $v['price'];
                    $temp['dispatchprice'] = $v['dispatchprice'];
                    $temp['status'] = $v['status'];
                    $temp['goods_total'] = $goods_total;
                    $allPrice = $allPrice+$v['price'];
                    $return[]=$temp;
                }
            }
            if (empty($return)) $return=array();
            $returnAll = array(
                'total'=>$res['total'],
                'allPrice'=>$allPrice,
                'data'=>$return,
            );
            ajaxReturnData(1,'',$returnAll);
        }else{
            ajaxReturnData(1,$this->ordersService->getError());
        }
    }
    //退货单列表接口
    public function returnList(){
        $_GP = $this->request;
        $result = $this->ordersService->returnListPage($_GP);
        if (!empty($result) && !empty($result['returnList'])){
            $returnList = $result['returnList'];
            $ShopDishService = new ShopDishService();
            foreach ($returnList as $key=>$v){
                $shopInfo = $ShopDishService->getDishContent(array('dish_id'=>$v['dishid']),"title,thumb,marketprice,productprice");
                if (empty($shopInfo)) continue;
                $goodsInfo['title'] = $shopInfo['title'];
                $goodsInfo["id"] = $v['odgid'];
                $goodsInfo["thumb"] = $shopInfo['thumb'];
                $temp['orderid'] = $v['orderid'];
                $temp['ordersn'] = $v['ordersn'];
                $temp['openid'] = $v['openid'];
                $temp['status_name'] = $v['status_name'];
                $temp['status'] = $v['status'];
                $temp['type'] = $v['type'];
                $temp['type_name'] = $v['type_name'];
                $temp['nickname'] = $v['nickname'];
                $temp['reply_return_time'] = $v['reply_return_time'];
                $goodsInfo['total'] = $v['goods_num'];
                $goodsInfo['marketprice'] = FormatMoney($shopInfo['marketprice'],0);//促销价
                $goodsInfo['productprice'] = FormatMoney($shopInfo['productprice'],0);//市场价
                $spec_key_name = json_decode($v['spec_key_name'],1);
                $goodsInfo['spec_key_name'] = '';
                if (!empty($spec_key_name)) $goodsInfo['spec_key_name'] = array_values($spec_key_name);
                $temp['return_price'] = '';//退款金额
                $temp['reason'] = '';//退款原因
                $info = $this->ordersService->afterReturn($v['orderid']);
                if ($info){
                    $temp['return_price'] = FormatMoney($info['refund_price'],0);
                    $temp['reason'] = $info['reason'];
                }
                $temp['goodsInfo'] = $goodsInfo;
                $return[] = $temp;
            }
            if (empty($return)) $return = array();
            $returnAll = array(
                'total'=>$result['total'],
                'data'=>$return,
            );
            ajaxReturnData(1,'',$returnAll);
        }else {
            ajaxReturnData(1,'',$this->ordersService->getError());
        }
    }
    //订单详细接口
    public function orderDetail(){
        $_GP = $this->request;
        $orderid = intval($_GP['id']);
        if (empty($orderid)) ajaxReturnData(0,'订单id有误');
        $info = $this->ordersService->OrderDetailPage($orderid);
        if (!empty($info) && !empty($info['goods']) && is_array($info['goods'])){
            //商品信息
            foreach ($info['goods'] as $key=>$v){
                $goodsInfo[$key]['title'] = $v['title'];
                $goodsInfo[$key]['thumb'] = $v['thumb'];
                $goodsInfo[$key]['marketprice'] = FormatMoney($v['marketprice'],0);
                $goodsInfo[$key]['productprice'] = FormatMoney($v['productprice'],0);
                $goodsInfo[$key]['type'] = $v['order_type'];
                $goodsInfo[$key]['type_name'] = $v['type_name'];
                $goodsInfo[$key]['status_name'] = $v['status_name'];
                $goodsInfo[$key]['goods_status'] = $v['order_status'];
                $goodsInfo[$key]['total'] = $v['total'];
                $spec_key_name = json_decode($v['spec_key_name'],1);
                if (!empty($spec_key_name)) $goodsInfo[$key]['spec_key_name'] = array_values($spec_key_name);
            }
            $return['goodinfo'] = $goodsInfo;
            $return['nickname'] = $info['nickname'];
            $return['remark'] = $info['remark'];
            $return['goodsAllPrice'] = FormatMoney($info['goodsprice'],0);//商品总价
            $return['dispatchprice'] = FormatMoney($info['dispatchprice'],0);//物流
            $return['bonusprice'] = FormatMoney($info['bonusprice'],0);//优惠卷抵消金额
            $return['price'] = FormatMoney($info['price'],0);//订单实收
            $return['orderid'] = $info['id'];
            $return['status'] = $info['status'];
            $return['status_name'] = $info['status_name'];
            $return['ordersn'] = $info['ordersn'];
            $return['createtime'] = $info['createtime'];
            $return['paytime'] = $info['paytime'];
            $return['sendtime'] = $info['sendtime'];
            $return['completetime'] = $info['completetime'];
            //收货人信息
            $address['address_realname'] = $info['address_realname'];
            $address['address_mobile'] = $info['address_mobile'];
            $address['address_province'] = $info['address_province'];
            $address['address_city'] = $info['address_city'];
            $address['address_area'] = $info['address_area'];
            $address['address_address'] = $info['address_address'];
            $return['address'] = $address;
            ajaxReturnData(1,'',$return);
        }else {
            ajaxReturnData(0,'','订单id无效');
        }
    }
    //添加备注
    public function addOrderRemark(){
        $_GP = $this->request;
        $orderid = intval($_GP['id']);
        if (empty($orderid)) ajaxReturnData(0,'订单id有误');
        if (empty($_GP['remark'])) ajaxReturnData(0,'备注内容为空');
        $order = $this->ordersService->getOrderInfo($orderid);
        if (empty($order) || (!is_array($order))) ajaxReturnData(0,'订单id有误');
        $return = $this->ordersService->addOrderRemark(array('id'=>$orderid,'remark'=>$_GP['remark']));
        if ($return){
            ajaxReturnData(1,'添加/修改备注成功');
        }else {
            ajaxReturnData(0,'添加/修改备注失败');
        }
    }
    //添加物流信息
    public function confirmsend(){
        $_GP = $this->request;
        $orderid = intval($_GP['id']);
        $is_dispatch = intval($_GP['is_dispatch']);
        if (empty($orderid)) ajaxReturnData(0,'订单id有误');
        if (empty($is_dispatch) || (!in_array($is_dispatch, array(1,2)))) ajaxReturnData(0,'参数有误');
        if ($is_dispatch==1 && empty($_GP['expresssn'])) ajaxReturnData(0,'参数有误');
        
        //判断是否可以发货
        $order = $this->ordersService->getOrderInfo($orderid);
        if (empty($order) || (!is_array($order))) ajaxReturnData(0,'参数有误,查无该订单信息');
        if (!empty($order['is_dispatch'])) ajaxReturnData(0,'该订单已经发货');
        if ($order['status'] != 1) ajaxReturnData(0,'该订单暂时不能发货!');
        $orderGoodInfo = $this->ordersService->getOrderGoodsInfo($orderid);
        if(!isSureSendGoods($orderGoodInfo)) ajaxReturnData(0,'不能发货，该订单有部分商品还没处理完!');
        
        $data = array(
            'id'=>$orderid,
        );
        if ($is_dispatch == 1) {
            //获取物流code
            $expresscode = get_expresscode($_GP['expresssn']);
            if (empty($expresscode) || empty($expresscode['auto'][0]['comCode'])) ajaxReturnData(0,'查不到该物流信息');
            $data['express'] = $expresscode['auto'][0]['comCode'];
            //获取物流名称
            $wuliService = new wuliuService();
            $data['expresscom'] = '';
            $info = $wuliService->getExpressNameByCode($data['express']);
            if ($info) $data['expresscom'] = $info['name'];
            $data['expresssn'] = $_GP['expresssn'];
        }
        
        //做备注
        $order = $this->ordersService->getOrderInfo($orderid);
        $json_retag = setOrderRetagInfo($order['retag'], '发货：已经确认发货');
        //修改订单表
        $data['retag'] = $json_retag;
        $res = $this->ordersService->AddSendInfo($data,$is_dispatch);
        if($res){
            ajaxReturnData(1,'发货操作成功');
        }else{
            ajaxReturnData(0,'操作失败!');
        }
    }
    //查看物流
    public function viewExpress(){
        $_GP = $this->request;
        $orderid = intval($_GP['id']);
        if (empty($orderid)) ajaxReturnData(0,'订单id有误');
        $info = $this->ordersService->getOrderInfo($orderid,'id,expresscom,expresssn,express,status,is_dispatch');
        if (empty($info) || (!in_array($info['status'], array(2,3))) || empty($info['is_dispatch'])) ajaxReturnData(0,'该订单暂时不能查看物流信息');//已发货和已完成才可以查看物流信息
       
        if ($info['is_dispatch'] == 2) ajaxReturnData(1,'',array('is_dispatch'=>2));
        
        if (empty($info['expresssn']) || empty($info['express'])) ajaxReturnData(0,'查无物流信息');
        
        $dispatch = fetch_expressage($info['express'],$info['expresssn']);
        if (empty($dispatch) || (!is_array($dispatch))) ajaxReturnData(0,'查无物流信息');
        $data = array(
            'is_dispatch'=>1,
            'expresscom' => $info['expresscom'],
            'expresssn' => $info['expresssn'],
            'data'=>$dispatch['data'],
            'orderid'=>$orderid,
        );
        ajaxReturnData(1,'',$data);
    }
    //根据物流单号查看物流名称
    public function returnExpresscom(){
        $_GP = $this->request;
        $expresssn = $_GP['expresssn'];
        if (empty($expresssn)) ajaxReturnData(0,'物流单号有误');
        //获取物流code
        $expresscode = get_expresscode($expresssn);
        if (empty($expresscode) || empty($expresscode['auto'][0]['comCode'])) ajaxReturnData(0,'查不到该物流信息');
        $express = $expresscode['auto'][0]['comCode'];
        //获取物流名称
        $wuliService = new wuliuService();
        $data['expresscom'] = '';
        $info = $wuliService->getExpressNameByCode($express);
        if ($info) $data['expresscom'] = $info['name'];
        ajaxReturnData(1,'',$data);
    }
    //同意退单接口
    public function agreeReturn(){
        $_GP = $this->request;
        $odgid = intval($_GP['id']);
        if (empty($odgid)) ajaxReturnData(0,'订单商品id有误');
        //订单商品表的状态是申请中的才可以退货
        $odginfo = $this->ordersService->getOrderGoodsByCon(array('id'=>$odgid),'id,orderid,status');
        if (empty($odginfo)) ajaxReturnData(0,'暂时不能退单,订单商品不存在');
        if ($odginfo['status'] != 1) ajaxReturnData(0,'暂时不能退单,订单商品的状态不为申请状态');
        //订单表的状态是已发货或者已付款的才可以退货
        $orderinfo = $this->ordersService->getOrderInfo($odginfo['orderid'],'status,id');
        if (empty($orderinfo) || (!in_array($orderinfo['status'], array(2,3)))) ajaxReturnData(0,'暂时不能退单，订单状态不为已付款或者已发货状态');
        //退单表数据不为空和退单日志表状态为正在申请才可以退货
        $aftersale = $this->ordersService->getAftersalesByCon(array('order_goods_id'=>$odgid));
        if (empty($aftersale)) ajaxReturnData(0,'暂时不能退单，退单表查无数据');
        
        $aftersalelog = $this->ordersService->getAftersalesLogByCon(array('order_goods_id'=>$odgid,'aftersales_id'=>$aftersale['aftersales_id']));
        if (empty($aftersalelog)) ajaxReturnData(0,'暂时不能退单，退单日志表查无数据');
        if ($aftersalelog['status'] != 1) ajaxReturnData(0,'暂时不能退单，退单日志表状态不对');
        
        $res = $this->ordersService->updateOrderGoods(array('id'=>$odgid),array('status'=>2));
        if ($res) ajaxReturnData(1,'同意退单成功');
    }
}