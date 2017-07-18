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
            $setting = globaSetting();
            foreach ($order_lists as $key=>$v){
                $goodinfo = $this->ordersService->getOrderGoodsDetail($v['id']);
                if (!empty($goodinfo)){
                    $goods_total = 0;
                    $data = array();
                    foreach ($goodinfo as $k=>$val){
                        $data[$k]['thumb'] = $val['thumb'];
                        $data[$k]['title'] = $val['title'];
                        $data[$k]['total'] = $val['total'];
                        $data[$k]['status_name'] = $val['status_name'];
                        $data[$k]['goods_status'] = $val['order_status'];
                        $spec_key_name = json_decode($val['spec_key_name'],1);
                        if (!empty($spec_key_name)) $data[$k]['spec_key_name'] = array_values($spec_key_name);
                        $goods_total += $val['total'];
                        if ($val['shop_type'] == 4){
                            $data[$k]['marketprice'] = FormatMoney($val['orderprice'],0);
                            $data[$k]['productprice'] = FormatMoney($val['marketprice'],0);
                            
                        }else{
                            $data[$k]['marketprice'] = FormatMoney($val['orderprice'],0);
                            $data[$k]['productprice'] = FormatMoney($val['productprice'],0);
                        }
                    }
                    $temp['goodinfo'] = $data;
                    $temp['orderid'] = $v['id'];
                    $temp['remark'] = $v['beizhu'];
                    $temp['openid'] = $v['openid'];
                    $temp['ordersn'] = $v['ordersn'];
                    $temp['status_name'] = $v['status_name'];
                    $temp['createtime'] = $v['createtime'];
                    $temp['nickname'] = $v['nickname'];
                    //实收
                    $pay_rate    = intval($setting['pay_rate'])/100;
                    $price = FormatMoney($v['price'],1);
                    $temp['price'] = FormatMoney($price-$v['store_earn_price']-$pay_rate*$price,0);//订单实收
                    //$temp['price'] = $v['price'];
                    $temp['dispatchprice'] = $v['dispatchprice'];
                    $temp['status'] = $v['status'];
                    $temp['goods_total'] = $goods_total;
                    $allPrice = $allPrice+$temp['price'];
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
                $shopInfo = $ShopDishService->getDishContent(array('dish_id'=>$v['dishid']),"id,title,thumb,marketprice,productprice");
                if (empty($shopInfo['id'])) continue;
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
                $goodsInfo['total'] = $v['goods_num'];
                if ($v['shop_type'] == 4){
                    $goodsInfo['marketprice'] = $v['price'];//促销价
                    $goodsInfo['productprice'] = FormatMoney($shopInfo['marketprice'],0);//市场价
                }else{
                    $goodsInfo['marketprice'] = $v['price'];//促销价
                    $goodsInfo['productprice'] = FormatMoney($shopInfo['productprice'],0);//市场价
                }
                
                $spec_key_name = json_decode($v['spec_key_name'],1);
                $goodsInfo['spec_key_name'] = '';
                if (!empty($spec_key_name)) $goodsInfo['spec_key_name'] = array_values($spec_key_name);
                $temp['return_price'] = '';//退款金额
                $temp['reason'] = '';//退款原因
                $info = $this->ordersService->afterReturn($v['odgid']);
                if ($info){
                    $temp['return_price'] = FormatMoney($info['refund_price'],0);
                    $temp['reason'] = $info['reason'];
                    $temp['reply_return_time'] = $info['createtime'];
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
            $setting = globaSetting();
            $returnprice = 0;
            foreach ($info['goods'] as $key=>$v){
                $goodsInfo[$key]['title'] = $v['title'];
                $goodsInfo[$key]['thumb'] = $v['thumb'];
                if ($v['shop_type'] == 4){
                    $goodsInfo[$key]['marketprice'] = FormatMoney($v['orderprice'],0);
                    $goodsInfo[$key]['productprice'] = FormatMoney($v['marketprice'],0);
                }else{
                    $goodsInfo[$key]['marketprice'] = FormatMoney($v['orderprice'],0);
                    $goodsInfo[$key]['productprice'] = FormatMoney($v['productprice'],0);
                }
                /* $goodsInfo[$key]['marketprice'] = FormatMoney($v['marketprice'],0);
                $goodsInfo[$key]['productprice'] = FormatMoney($v['productprice'],0); */
                $goodsInfo[$key]['type'] = $v['order_type'];
                $goodsInfo[$key]['type_name'] = $v['type_name'];
                $goodsInfo[$key]['status_name'] = $v['status_name'];
                $goodsInfo[$key]['goods_status'] = $v['order_status'];
                $goodsInfo[$key]['total'] = $v['total'];
                if ($v['order_status'] == 4 && in_array($v['order_type'], array(1,3))){
                    $aftersales = mysqld_select("select * from ".table('aftersales')." where order_goods_id={$v['order_shop_id']}");
                    $returnprice += $aftersales['refund_price'];
                }
                $spec_key_name = json_decode($v['spec_key_name'],1);
                if (!empty($spec_key_name)) $goodsInfo[$key]['spec_key_name'] = array_values($spec_key_name);
            }
            $return['goodinfo'] = $goodsInfo;
            $return['nickname'] = $info['nickname'];
            $return['remark'] = $info['remark'];
            $return['goodsAllPrice'] = FormatMoney($info['goodsprice'],0);//商品总价
            $return['dispatchprice'] = FormatMoney($info['dispatchprice'],0);//物流
            $return['bonusprice'] = FormatMoney($info['bonusprice'],0);//优惠卷抵消金额
            
            $return['orderid'] = $info['id'];
            $return['status'] = $info['status'];
            $return['status_name'] = $info['status_name'];
            $return['ordersn'] = $info['ordersn'];
            $return['createtime'] = $info['createtime'];
            $return['paytime'] = $info['paytime'];
            $return['sendtime'] = $info['sendtime'];
            $return['completetime'] = $info['completetime'];
            //佣金抽成
            $return['store_earn_price'] = FormatMoney($info['store_earn_price'],0);
            //平台抽成
            $pay_rate    = intval($setting['pay_rate'])/100;
            $price = FormatMoney($info['price'],1);
            $return['platform_price'] = FormatMoney($pay_rate*$price,0);
            $return['price'] = FormatMoney($price-$info['store_earn_price']-$pay_rate*$price-$returnprice,0);//订单实收
            $return['return_price'] = FormatMoney($returnprice,0);
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
    //退单同意/拒绝处理接口
    public function returnDeal(){
        $_GP = $this->request;
        $odgid = intval($_GP['id']);
        $status = intval($_GP['status']); // 2同意，-1拒绝
        if (empty($status) || !in_array($status, array(-1,2))) ajaxReturnData(0,'状态有误');
        if (empty($odgid)) ajaxReturnData(0,'订单商品id有误');
        $isCanReturn = $this->ordersService->checkIsCanReturn($_GP);
        //判断是否可以退单
        if ($isCanReturn['status'] < 0){
            ajaxReturnData(0,$isCanReturn['mes']);
        }
        $odginfo = $isCanReturn['data'];
        $tip = $this->ordersService->getTitleByOdgType($odginfo['type'],$status);
        $title = $tip['title'];
        $des = $tip['des'];
        $res = $this->ordersService->updateOrderGoods(array('id'=>$odgid),array('status'=>$status));
        if ($res){
            //插入一条log记录
            $arrLogContent                 = array();
            if ($status ==2){
                $arrLogContent['description']  = $des;
            }else{
                $arrLogContent['description']  = $_GP['description'];
            }
            $data = array(
                'aftersales_id'  => $odginfo['aftersales_id'],
                'order_goods_id' => $odgid,
                'status' 		 => $status,
                'content'        => serialize($arrLogContent),
                'createtime' 	 => time(),
                'title' 	 => $title,
                'type' 	 => 1,
            );
            mysqld_insert('aftersales_log',$data);
            //拒绝的时候再加入一条数据
            if ($status == -1){
                $dess['description']  = '如果你对此次处理结果有任何疑义，请联系小城市客服，我们将继续为您服务';
                $data1 = array(
                    'aftersales_id'  => $odginfo['aftersales_id'],
                    'order_goods_id' => $odgid,
                    'status' 		 => $status,
                    'content'        => serialize($dess),
                    'createtime' 	 => time(),
                    'title' 	 => '退款/换货关闭',
                    'type' 	 => 1,
                );
                mysqld_insert('aftersales_log',$data1);
            }
            
            ajaxReturnData(1,'提交成功');
        }else{
            ajaxReturnData(0,'系统错误');
        } 
    }
    //退单详情
    public function returnDetail(){
        $_GP = $this->request;
        $odgid = intval($_GP['id']);//订单商品id
        //订单商品表的状态是申请中的才可以退货
        if (empty($odgid)) ajaxReturnData(0,'订单商品id有误');
        $odginfo = $this->ordersService->getOrderGoodsByCon(array('id'=>$odgid),'id,orderid,status,type,reply_return_time');
        if (empty($odginfo)) ajaxReturnData(0,'暂时不能退单,订单商品不存在');
        //订单表的状态是已发货或者已付款的才可以退货
        $orderinfo = $this->ordersService->getOrderInfo($odginfo['orderid'],'status,id');
        //if (empty($orderinfo) || (!in_array($orderinfo['status'], array(1,2)))) ajaxReturnData(0,'暂时不能退单，订单状态不为已付款或者已发货状态');
        //退单表数据不为空和退单日志表状态为正在申请才可以退货
        $aftersale = $this->ordersService->getAftersalesByCon(array('order_goods_id'=>$odgid));
        if (empty($aftersale)) ajaxReturnData(0,'暂时不能退单，退单表查无数据');
        
        $data0 = $data1 = $temp = array();
        $data0['reason'] = $aftersale['reason'];
        $data0['description'] = $aftersale['description'];
        if ($aftersale['evidence_pic']){
            $evidence_pic = explode(";", $aftersale['evidence_pic']);
        }
        $data0['evidence_pic'] = $evidence_pic;
        $data0['refund_num'] = $aftersale['refund_num'];;
        $data0['refund_price'] = FormatMoney($aftersale['refund_price'],0);
        //$data0['createtime'] = $aftersale['createtime'];
        $data0['createtime'] = $aftersale['createtime'];
        $data0['status'] = $odginfo['status'];
        if ($odginfo['type'] == 1){
            $tt = "退货退款";
            
        }elseif ($odginfo['type'] == 2){
            $tt = "换货";
            
        }elseif ($odginfo['type'] == 3){
            $tt = "仅退款";
            
        }
        $data0['type_name'] = $tt;
        $data0['type'] = $odginfo['type'];
        if ($odginfo['status'] == 1){
            $temp['createtime'] = $aftersale['createtime'];
            $temp['title'] = '等待卖家处理';
            $temp['type'] = 1;//1卖家，2买家
            $temp['description']='如果48小时内卖家不处理，系统将默认同意';
            $data1[] = $temp;
        }
        $aftersalelog = $this->ordersService->getAftersalesLogByConAll(array('order_goods_id'=>$odgid,'aftersales_id'=>$aftersale['aftersales_id']));
        if ($aftersalelog){
            foreach ($aftersalelog as $v){
                if (in_array($v['status'], array(-1,2,3,4))){
                    $temp['createtime'] = $v['createtime'];
                    $temp['title'] = $v['title'];
                    $temp['type'] = $v['type'];//1卖家，2买家
                    $temp['description']='';
                    if ($v['content']){
                        $content = unserialize($v['content']);
                        if ($content['description']) $temp['description'] = $content['description'];
                        
                    }
                    $data1[] = $temp;
                    //退货或者换货需要加入退回地址
                    if (($v['status'] == 2) && (in_array($odginfo['type'], array(1,2)))){
                        $service = new \service\seller\StoreShopService();
                        $info = $service->viewreturnAddress();
                        $tempadd['createtime'] = $v['createtime'];
                        $tempadd['title'] = '退货地址';
                        $tempadd['type'] = 1;
                        if ($info['address_province']){
                            $tempadd['description'] = $info['address_province']." ".$info['address_city']." ".$info['address_area']." ".$info['address_address']." ".$info['address_realname']." ".$info['address_mobile']." ".$info['postcode'];
                        }else {
                            $tempadd['description'] = '卖家暂未设置默认退货地址，请联系卖家。';
                        }
                        $data1[] = $tempadd;
                    }
                    if (($v['status'] == 4)){
                        if ($odginfo['type']==1){
                            $tempadd1['createtime'] = $v['createtime'];
                            $tempadd1['title'] = '退款成功';
                            $tempadd1['type'] = 1;
                            $data1[] = $tempadd1;
                        }elseif ($odginfo['type']==2){
                            $tempadd2['createtime'] = $v['createtime'];
                            $tempadd2['title'] = '换货成功';
                            $tempadd2['type'] = 1;
                            $data1[] = $tempadd2;
                        }
                    }
                }
            }
            
        }
        ajaxReturnData(1,'',array('detail'=>$data0,'lists'=>$data1));
    }
    //确认收货
    public function confirmReturn(){
        $_GP = $this->request;
        $odgid = intval($_GP['id']);//订单商品id
        if (empty($odgid)) ajaxReturnData(0,'订单商品id有误');
        //订单商品表的状态是申请中的才可以退货
        if (empty($odgid)) ajaxReturnData(0,'订单商品id有误');
        $odginfo = $this->ordersService->getOrderGoodsByCon(array('id'=>$odgid),'id,orderid,dishid,ac_dish_id,status,type,reply_return_time');
        if (empty($odginfo)) ajaxReturnData(0,'暂时不能退单,订单商品不存在');
        /* if ($odginfo['type'] == 1 || $odginfo['type'] == 2){
            if ($odginfo['status'] != 3){
                ajaxReturnData(0,'订单还未处理完，不能确认退单');
            }
            $aftersalelog = $this->ordersService->getAftersalesLogByCon(array('order_goods_id'=>$odgid,'status'=>3));
            if (empty($aftersalelog)) ajaxReturnData(0,'订单还未处理完，不能确认退单');
        }else{
            $aftersalelog = $this->ordersService->getAftersalesLogByCon(array('order_goods_id'=>$odgid,'status'=>2));
            if (empty($aftersalelog)) ajaxReturnData(0,'订单还未处理完，不能确认退单');
        } */
        if ($odginfo['status'] == 4){
            ajaxReturnData(0,'订单已经确认，不能重复确认');
        }
        $aftersalelog = $this->ordersService->getAftersalesLogByCon(array('order_goods_id'=>$odgid,'status'=>2));
        if (empty($aftersalelog)) ajaxReturnData(0,'订单还未处理完，不能确认退单');
        //订单表的状态是已发货或者已付款的才可以退货
        $orderinfo = $this->ordersService->getOrderInfo($odginfo['orderid'],'status,id');
        //if (empty($orderinfo) || (!in_array($orderinfo['status'], array(1,2)))) ajaxReturnData(0,'暂时不能退单，订单状态不为已付款或者已发货状态');
        //退单表数据不为空和退单日志表状态为正在申请才可以退货
        $aftersale = $this->ordersService->getAftersalesByCon(array('order_goods_id'=>$odgid));
        if (empty($aftersale)) ajaxReturnData(0,'暂时不能确认退单，退单表无数据');
        
        //更改shop_order_goods表状态
        mysqld_update('shop_order_goods',array('status'=>4),array('id'=>$odgid));
        //更改日志表的状态
        $tip = $this->ordersService->getTitleByOdgType($odginfo['type'],4);
        $title = $tip['title'];
        $des = $tip['des'];
        //插入一条log记录
        $arrLogContent                 = array();
        $arrLogContent['description']  = $des;
        $data = array(
            'aftersales_id'  => $aftersale['aftersales_id'],
            'order_goods_id' => $odgid,
            'status' 		 => 4,
            'content'        => serialize($arrLogContent),
            'createtime' 	 => time(),
            'title' 	 => $title,
            'type' 	 => 1,
        );
        mysqld_insert('aftersales_log',$data);
        if ($odginfo['type'] == 1 || $odginfo['type'] == 3){
            $seting = globaSetting();
            returnProcess($odgid,$seting);
            //库存释放
            operateStoreCount($odginfo['dishid'],$odginfo['ac_dish_id'],$odginfo['ac_dish_id'],2);
        }
        //关闭订单
        $this->ordersService->checkIsCloseOrder($odginfo['orderid']);
        
        
        ajaxReturnData(1,'确认成功');
    }
    //订单搜索
    public function searchOrder(){
        $_GP = $this->request;
        if (empty($_GP['search'])) ajaxReturnData(0,'请填写搜索词');
        $res = $this->ordersService->OrderListsSearchPage($_GP);
        if($res){
            $order_lists=$res['order_lists'];
            //$allPrice = '';
            $setting = globaSetting();
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
                        if ($val['shop_type'] == 4){
                            $data[$k]['marketprice'] = FormatMoney($val['orderprice'],0);
                            $data[$k]['productprice'] = FormatMoney($val['marketprice'],0);
                            
                        }else{
                            $data[$k]['marketprice'] = FormatMoney($val['marketprice'],0);
                            $data[$k]['productprice'] = FormatMoney($val['productprice'],0);
                        }
                    }
                    $temp['goodinfo'] = $data;
                    $temp['orderid'] = $v['id'];
                    $temp['remark'] = $v['beizhu'];
                    $temp['openid'] = $v['openid'];
                    $temp['ordersn'] = $v['ordersn'];
                    $temp['status_name'] = $v['status_name'];
                    $temp['createtime'] = $v['createtime'];
                    $temp['nickname'] = $v['nickname'];
                    //实收
                    $pay_rate    = intval($setting['pay_rate'])/100;
                    $price = FormatMoney($v['price'],1);
                    $temp['price'] = FormatMoney($price-$v['store_earn_price']-$pay_rate*$price,0);//订单实收
                    //$temp['price'] = $v['price'];
                    $temp['dispatchprice'] = $v['dispatchprice'];
                    $temp['status'] = $v['status'];
                    $temp['goods_total'] = $goods_total;
                    //$allPrice = $allPrice+$temp['price'];
                    $return[]=$temp;
                }
            }
            if (empty($return)) $return=array();
            $returnAll = array(
                'total'=>$res['total'],
                //'allPrice'=>$allPrice,
                'data'=>$return,
            );
            ajaxReturnData(1,'',$returnAll);
        }else{
            ajaxReturnData(1,$this->ordersService->getError());
        }
    }
}