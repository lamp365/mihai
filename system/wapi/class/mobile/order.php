<?php

namespace wapi\controller;

class order extends base {

  public function __construct()
  {
      parent::__construct();
      if(!checkIsLogin()){
          ajaxReturnData(0,'请授权登录！');
      }

      //未付款订单将会自动关闭
      order_auto_close();
  }

  // 订单列表
  public function my_order()
  {
    $_GP = $this->request;
    $member = get_member_account(true, true);
    if (empty($member['openid'])) {
      ajaxReturnData(0,'用户信息获取失败');
    }
    // $member['openid'] = '2017060510703';
    $status = intval($_GP['order_status']);
    $pindex = max(1, intval($_GP['page']));
    $psize = intval($_GP['limit'] ? $_GP['limit'] : 20);

    if (empty($status)) {
      $status = 'all';
    }

    $where = "a.openid='".$member['openid']."' AND a.deleted=0";

    if ($status == 'all') {
      // no
    }elseif ($status == 1) {
      $where.=" AND a.status=0";
    }elseif ($status == 2) {
      $where.=" AND a.status=2";
    }else{
      ajaxReturnData(0,'订单状态错误');
    }

    $result = array();
    $list = get_orders($where,$pindex,$psize);
    $result['orders'] = $list;
    $result['total']['all'] = get_order_num("a.openid='".$member['openid']."' AND a.deleted=0");
    $result['total']['1'] = get_order_num("a.openid='".$member['openid']."' AND a.deleted=0 AND a.status=0");
    $result['total']['2'] = get_order_num("a.openid='".$member['openid']."' AND a.deleted=0 AND a.status=2");
    // dump($result);
    ajaxReturnData(1,'获取成功',$result);
  }

  // 订单操作
  public function order_operation() 
  {
    $_GP = $this->request;
    $member = get_member_account(true, true);

    // $member['openid'] = '2017060510703';

    if (empty($member['openid'])) {
      ajaxReturnData(0,'用户信息获取失败');
    }
    $order_id = intval($_GP['order_id']);
    $op = $_GP['operation'];
    if (empty($order_id)) {
      ajaxReturnData(0,'订单ID为空');
    }

    $order = get_orders("a.openid='".$member['openid']."' AND a.deleted=0 AND a.id=".$order_id);
    if (empty($order[0])) {
      ajaxReturnData(0,'订单查询失败');
    }
    $orderGoodInfo = mysqld_selectall("SELECT * FROM ".table('shop_order_goods')." WHERE orderid=".$order_id);
    if (empty($orderGoodInfo)) {
      ajaxReturnData(0,'查询订单商品失败');
    }

    if ($op == 'cancel') {
      // 取消订单
      if ($order[0]['status'] == 0) {
        update_order_status($order_id, -1);
        ajaxReturnData(1,'订单取消成功');
      }else{
        ajaxReturnData(0,'订单取消失败');
      }
    }elseif ($op == 'notarize') {
      // 确认收货
      if ($order[0]['status'] == 2 AND isSureGetGoods($orderGoodInfo)) {
        update_order_status($order_id, 3);
        ajaxReturnData(1,'确认收货成功');
      }else{
        ajaxReturnData(0,'确认收货失败');
      }
    }elseif ($op == 'delete') {
      // 删除订单
      if ($order[0]['status'] == 3 or $order[0]['status'] == -1) {
        mysqld_update('shop_order', array('deleted' => 1), array('id'=> $order_id));
        ajaxReturnData(1,'删除订单成功');
      }else{
        ajaxReturnData(0,'删除订单失败');
      }
    }else{
      ajaxReturnData(0,'操作类型错误');
    }
  }

  // 订单详情
  public function order_detail()
  {
    $_GP = $this->request;
    $member = get_member_account(true, true);
    if (empty($member['openid'])) {
      ajaxReturnData(0,'用户信息获取失败');
    }
    $order_id = intval($_GP['order_id']);
    if (empty($order_id)) {
      ajaxReturnData(0,'订单ID为空');
    }

    $list = get_orders("a.deleted=0 AND a.id=".$order_id);
    $order = $list[0];
    if (empty($order)) {
      ajaxReturnData(0,'订单获取失败');
    }
    if (!empty($order['hasbonus'])) {
      $bouns_mem = mysqld_select("SELECT * FROM ".table('store_coupon_member')." WHERE scmid=".$order['hasbonus']);
      if (!empty($bouns_mem)) {
        $bouns = mysqld_select("SELECT * FROM ".table('store_coupon')." WHERE scid=".$bouns_mem['scid']);
        if (!empty($bouns)) {
          $order['bouns_gold'] = $bouns['coupon_amount'];
        }else{
          $order['bouns_gold'] = 0;
        }
      }else{
        $order['bouns_gold'] = 0;
      }
    }else{
      $order['bouns_gold'] = 0;
    }
    ajaxReturnData(1,'订单获取成功',$order);
  }

  // 申请售后
  public function create_refund()
  {
    $_GP = $this->request;
    $member = get_member_account(true, true);
    if (empty($member['openid'])) {
      ajaxReturnData(0,'用户信息获取失败');
    }

    $order_good_id = intval($_GP['order_good_id']);
    if (empty($order_good_id)) {
      ajaxReturnData(0,'订单商品ID为空');
    }
    $refund_type = intval($_GP['refund_type']);
    if (empty($refund_type)) {
      ajaxReturnData(0,'售后类型为空');
    }

    $order_good = mysqld_select("SELECT * FROM ".table('shop_order_goods')." WHERE id=".$order_good_id);
    if (empty($order_good)) {
      ajaxReturnData(0,'订单商品获取失败');
    }
    if (intval($order_good['status']) != 0 AND intval($order_good['status']) != -1) {
      ajaxReturnData(0,'该商品已在售后流程中');
    }

    $af_ary = array();
    $af_ary['order_goods_id'] = $order_good_id;
    // 售后原因
    if (empty($_GP['reason'])) {
      ajaxReturnData(0,'售后原因不能为空');
    }
    $af_ary['reason'] = $_GP['reason'];
    // 说明
    if (!empty($_GP['description'])) {
      $af_ary['description'] = $_GP['description'];
    }
    if (!empty($_GP['evidence_pic'])) {
      $af_ary['evidence_pic'] = $_GP['evidence_pic'];
    }
    $af_ary['createtime'] = time();
    $af_ary['modifiedtime'] = time();

    if ($refund_type == 1) {
      // 退款退货
      if (intval($order_good['isreback']) == 0) {
        ajaxReturnData(0,'该商品不支持7天退换');
      }else{
        if ((intval($order_good['createtime'])+(24*60*60*7))<time()) {
          ajaxReturnData(0,'该商品已超过7天退换期');
        }
      }
      if (empty($_GP['refund_price'])) {
        ajaxReturnData(0,'售后金额不能为空');
      }
      $rf_price = FormatMoney((float)$_GP['refund_price']);
      $rf_total = intval($_GP['refund_total'] ? $_GP['refund_total'] : 1);
      if ($rf_total > intval($order_good['total'])) {
        ajaxReturnData(0,'数量超过上限');
      }
      if ($rf_price>($order_good['price']*$rf_total)) {
        ajaxReturnData(0,'金额超过上限');
      }
      $af_ary['refund_price'] = $rf_price;
      $af_ary['refund_num'] = $rf_total;
    }elseif ($refund_type == 2) {
      // 换货
      $rf_total = intval($_GP['refund_total'] ? $_GP['refund_total'] : 1);
      if ($rf_total > intval($order_good['total'])) {
        ajaxReturnData(0,'数量超过上限');
      }
      $af_ary['refund_num'] = $rf_total;
    }elseif ($refund_type == 3) {
      // 仅退款
      if (empty($_GP['refund_price'])) {
        ajaxReturnData(0,'售后金额不能为空');
      }
      $rf_price = FormatMoney((float)$_GP['refund_price']);
      if ($rf_price>($order_good['price']*intval($order_good['total']))) {
        ajaxReturnData(0,'金额超过上限');
      }
      $af_ary['refund_price'] = $rf_price;
    }else{
      ajaxReturnData(0,'售后类型错误');
    }

    // 创建售后申请
    mysqld_insert('aftersales', $af_ary);
    $after_id = mysqld_insertid();

    // 生成售后记录
    $log_ary = array();
    $log_ary['aftersales_id'] = $after_id;
    $log_ary['order_goods_id'] = $order_good_id;
    $log_ary['status'] = 1;
    if ($refund_type == 1) {
        $t_type = '退款退货';
    }elseif ($refund_type == 2) {
        $t_type = '换货';
    }elseif ($refund_type == 3) {
        $t_type = '仅退款';
    }
    $log_ary['title'] = "买家发起了".$t_type."申请";
    $log_ary['content'] = serialize($af_ary);
    $log_ary['createtime'] = time();
    $log_ary['type'] = 2;

    // 创建售后记录
    mysqld_insert('aftersales_log', $log_ary);

    $update_shop_order_goods = array(
        'status'=>1,
        'type'=>$refund_type,
        'return_num'=>intval($_GP['refund_total'] ? $_GP['refund_total'] : 0)
    );
    // 更新订单商品状态
    mysqld_update('shop_order_goods',$update_shop_order_goods,array('id'=>$order_good_id));

    ajaxReturnData(1,'申请售后成功');
  }

}