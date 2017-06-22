<?php

namespace wapi\controller;

class order extends base {
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
    if (empty($list)) {
      ajaxReturnData(0,'订单列表为空');
    }else{
      ajaxReturnData(1,'获取成功',$result);
    }
  }

  // 订单详情
  public function order_detail()
  {
    # code...
  }
}