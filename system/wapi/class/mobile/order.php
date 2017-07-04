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
}