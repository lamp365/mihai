<?php

namespace wapi\controller;

class good_detail extends base {

  // 商品详情首页
  public function index()
  {
    $_GP = $this->request;
    $dish_id = intval($_GP['dish_id']);
    $is_contont = $_GP['is_contont'];
    $member = get_member_account(true, false);

    if (empty($dish_id)) {
      ajaxReturnData(0,'商品ID不能为空');
    }

    $good = mysqld_select("SELECT * FROM ".table('shop_dish')." WHERE id=$dish_id");

    if (empty($good)) {
      ajaxReturnData(0,'商品查询失败');
    }

    // 现在进行中的活动
    $now_ac = getCurrentAct();
    $activity_dish = mysqld_select("SELECT * FROM ".table('activity_dish')." WHERE ac_shop_dish=".$good['id']." AND ac_action_id=".$now_ac['ac_id']);
    // if (empty($activity_dish)) {
    //   ajaxReturnData(0,'该商品不在限时购之内');
    // }

    $shop = mysqld_select("SELECT a.*,b.free_dispatch as bfree,b.express_fee,b.limit_send FROM ".table('store_shop')." as a left join ".table('store_extend_info')." as b on a.sts_id=b.store_id WHERE a.sts_id=".$good['sts_id']);
    if (empty($shop)) {
      ajaxReturnData(0,'店铺信息获取失败');
    }

    // 获取主图
    $piclist = array();
    $piclist[] = $good['thumb'];
    // 获取细节图
    $goods_piclist = mysqld_select("SELECT * FROM " . table('shop_dish_piclist') . " WHERE goodid = :goodid", array(':goodid' => $good['id']));
    if (!empty($goods_piclist)) {
      $pic_ary = explode(",",$goods_piclist['picurl']);
      if (!empty($pic_ary)) {
        foreach ($pic_ary as $picv) {
          // echo '<img src="'.$picv.'" class="img-ks-lazyload"/>';
          $piclist[] = $picv;
        }
      }
      // 获取详情图
      $contents = explode(",",$goods_piclist['contentpicurl']);
    }
    
    $list = array();
    // 商品ID 
    $list['id'] = $good['id'];
    // 销量
    $list['sales'] = $good['sales_num'];
    // 标题
    $list['title'] = $good['title'];
    // 简单描述
    $list['description'] = $good['description'];
    // 产品库价格
    $list['productprice'] = FormatMoney($good['productprice'],0);
    // 价格
    $list['marketprice'] =FormatMoney($good['marketprice'],0);
    // 库存
    $list['total']   = $good['store_count'];
    // 展示图片
    $list['piclist'] = $piclist;
    // type
    $list['type'] = $good['type'];
    // timestart
    $list['timestart'] = $good['timestart'];
    // timeend
    $list['timeend'] = $good['timeend'];
    // 单笔最大购买数量
    // $list['max_buy_quantity'] = $good['max_buy_quantity'];
    // 品牌
    $brand = mysqld_select("SELECT * FROM ".table('shop_brand')." WHERE id=".$good['brand']);
    $list['brand'] = $brand['brand'];
    $list['brand_icon'] = $brand['icon'];
    // 购物车商品数量
    if (!empty($member)) {
      $list['shoppingcart_num'] = getCartTotal();
    }
    // 是否收藏
    if (!empty($member['openid'])) {
      $where = "openid='".$member['openid']."' AND dish_id=".$good['id'];
      $have_c = mysqld_select("SELECT * FROM ".table('goods_collection')." WHERE ".$where);
      if (!empty($have_c)) {
        if ($have_c['deleted'] == 0) {
          $list['is_collection'] = 1;
        }else{
          $list['is_collection'] = 0;
        }
      }else{
        $list['is_collection'] = 0;
      }
    }else{
      $list['is_collection'] = 0;
    }
    // 商品状态(上架/下架)
    $list['status'] = $good['status'];
    // 免邮价格
    $list['free_dispatch'] = FormatMoney($shop['bfree'],0);
    // 邮费
    $list['express_fee'] = FormatMoney($shop['express_fee'],0);
    // 最低起送金额
    $list['limit_send'] = FormatMoney($shop['limit_send'],0);
    // 店名
    $list['shop_name'] = $shop['sts_name'];
    // 店铺头像
    $list['shop_avatar'] = $shop['sts_avatar'];
    // 店铺等级
    // $list['shop_level'] = $shop['sts_shop_level'];
    if (!empty($activity_dish)) {
      // 当前为活动商品
      $list['activity'] = 1;
      // 分类名
      $category_p1 = mysqld_select("SELECT name FROM ".table('shop_category')." WHERE id=".$activity_dish['ac_p1_id']);
      $list['category_p1'] = $category_p1['name'];
      $category_p2 = mysqld_select("SELECT name FROM ".table('shop_category')." WHERE id=".$activity_dish['ac_p2_id']);
      $list['category_p2'] = $category_p2['name'];
      // 限时购特价
      $list['timeprice'] = FormatMoney($activity_dish['ac_dish_price'],0);
      // 时段促销时间
      if ($activity_dish['ac_area_id'] == 0) {
        // 全天时段
        $list['in_area'] = 0;
        $area = mysqld_select("SELECT * FROM ".table('activity_list')." WHERE ac_id=".$activity_dish['ac_action_id']);
        $list['ac_str_time'] = $area['ac_time_str'];
        $list['ac_end_time'] = $area['ac_time_end'];
        $list['ac_status'] = $area['ac_status'];
      }else{
        // 具体时段
        $list['in_area'] = 1;
        $area = mysqld_select("SELECT * FROM ".table('activity_area')." WHERE ac_area_id=".$activity_dish['ac_area_id']);
        $list['ac_str_time'] = getTodayTimeByActtime($area['ac_area_time_str']);
        $list['ac_end_time'] = getTodayTimeByActtime($area['ac_area_time_end']);
        $list['ac_status'] = $area['ac_area_status'];
      }
      // 限时购库存
      $list['ac_total'] = $activity_dish['ac_dish_total'];
      // 当前时间
      $list['now_time'] = time();
      // 活动状态
      if ($list['ac_str_time'] > time()) {
        // 活动未开始
        $list['ac_type'] = 0;
      }elseif ($list['ac_str_time'] <= time() AND $list['ac_end_time'] >= time()) {
        // 活动进行中
        $list['ac_type'] = 1;
      }elseif ($list['ac_end_time'] < time()) {
        // 活动结束
        $list['ac_type'] = 2;
      }
    }else{
      // 当前不为活动商品
      $list['activity'] = 0;
      // 分类名
      $category_p1 = mysqld_select("SELECT name FROM ".table('store_shop_category')." WHERE id=".$good['store_p1']);
      $list['category_p1'] = $category_p1['name'];
      $category_p2 = mysqld_select("SELECT name FROM ".table('store_shop_category')." WHERE id=".$good['store_p2']);
      $list['category_p2'] = $category_p2['name'];
      // 限时购特价
      $list['timeprice'] = $list['marketprice'];
      // 限时购库存
      $list['ac_total'] = 0;
      // 活动结束
      $list['ac_type'] = 2;
      $list['ac_status'] = 0;
    }

    // 详情图
    if ($is_contont == 'yes') {
      // 通用详情头尾 
      $head = mysqld_select("SELECT picurl FROM ".table('shop_dish_commontop')." WHERE sts_id=".$good['sts_id']." AND position=1 and is_default=1");
      $foot = mysqld_select("SELECT picurl FROM ".table('shop_dish_commontop')." WHERE sts_id=".$good['sts_id']." AND position=2 and is_default=1");
      $list['content_head'] = array();
      $list['content_foot'] = array();
      $list['content'] = array();
      if (!empty($head)) {
        $list['content_head'][] = $head;
      }
      if (!empty($foot)) {
        $list['content_foot'][] = $foot;
      }
      if (!empty($contents)) {
        $list['content'] = $contents;
      }
    }

    // dump($list);
    ajaxReturnData(1,'获取成功',$list);
    exit;
  }

  // 获取商品评论
  public function get_comment()
  {
    $result = array();
    $_GP = $this->request;
    $dish_id = intval($_GP['dish_id']);
    if (empty($dish_id)) {
      ajaxReturnData(0,'商品ID不能为空');
    }
    $pindex = max(1, intval($_GP['page']));
    $psize = intval($_GP['limit'] ? $_GP['limit'] : 20);

    $good = mysqld_select("SELECT * FROM ".table('shop_dish')." WHERE id=$dish_id");

    $comments = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.*, b.openid, b.realname, b.nickname, b.avatar, b.mobile, b.member_description FROM " . table('shop_goods_comment') . " as a LEFT JOIN ". table('member') ." as b on a.openid=b.openid WHERE a.dishid=".$good['id']." ORDER BY a.createtime DESC LIMIT ".($pindex - 1) * $psize . ',' . $psize);
    // 总记录数
    $total = mysqld_select("SELECT FOUND_ROWS() as total;");

    if (!empty($comments)) {
      foreach ($comments as $c_k => &$c_v) {
        $c_v['mobile'] = substr_cut($c_v['mobile']);
        $c_img = mysqld_selectall("SELECT img FROM ".table('shop_comment_img')." WHERE comment_id=".$c_v['id']." ORDER BY id ASC LIMIT 5");
        foreach ($c_img as $cmv) {
          $comments[$c_k]['img'][] = $cmv['img'];
        }
      }
      unset($c_v);
    }

    $result['comments'] = $comments;
    $result['total'] = $total['total'];
    ajaxReturnData(1,'获取成功',$result);
    exit;
  }

  // 提交商品评论
  public function add_comment()
  {
    $_GP = $this->request;
    $result = array();
    // 订单ID
    $order_id = intval($_GP['order_id']);
    // 评论内容
    $comment = $_GP['comment'];
    // dishID
    $dish_id = intval($_GP['dish_id']);
    // 系统ID（1pc，2wap，3安卓，4ios）
    // $system = intval($_GP['system_id']);
    // 评价ID（1好评，2差评）
    $type = intval($_GP['type']);
    // 物流评分
    $wl_rate = (float)$_GP['wl_rate'];
    // 服务分
    $fw_rate = (float)$_GP['fw_rate'];
    // 产品评分
    $cp_rate = (float)$_GP['cp_rate'];
    // 店铺ID
    $sts_id = intval($_GP['sts_id']);

    if (empty($order_id) or empty($comment) or empty($dish_id) or empty($sts_id)) {
      ajaxReturnData(0,'必填项不可为空');
    }

    $order = get_orders("a.deleted=0 AND a.id=".$order_id);
    if (empty($order[0])) {
      ajaxReturnData(0,'订单查询失败');
    }

    $order_good = mysqld_select("SELECT iscomment FROM ".table('shop_order_goods')." WHERE orderid=".$order_id." AND dishid=".$dish_id);
    if ($order_good['iscomment'] == '1') {
      ajaxReturnData(0,'该商品已评论过');
    }

    // 评论信息
    $d = array('createtime' => time(), 'orderid' => $order_id, 'ordersn' => $order[0]['ordersn'], 'openid' => $order[0]['openid'], 'comment' => $comment, 'wl_rate' => $wl_rate, 'fw_rate' => $fw_rate, 'cp_rate' => $cp_rate, 'dishid' => $dish_id, 'sts_id' => $sts_id, 'type' => $type);
    $d['system'] = getSystemType();
    mysqld_insert('shop_goods_comment', $d);
    $comment_id = mysqld_insertid();

    // 设置is_comment
    mysqld_query("UPDATE ".table('shop_order_goods')." SET iscomment=1 WHERE orderid=".$order_id." AND dishid=".$dish_id);

    // 评论图片保存
    for ($i=1; $i < 6; $i++) { 
      if (!empty($_GP['img'.$i])) {
        $m = array('comment_id' => $comment_id, 'img' => $_GP['img'.$i]);
        mysqld_insert('shop_comment_img', $m);
      }
      // $this->upload_imgs($i, $comment_id);
    }

    ajaxReturnData(1,'评论成功');
  }

  function upload_imgs($num, $commentid) {
    if (!empty($_FILES['img'.$num])) {
      if ($_FILES['img'.$num]['error']==0) {
        $upload = file_upload($_FILES['img'.$num]);
        //出错时
        if (is_error($upload)) {
          ajaxReturnData(0,$upload['message']);
          exit;
        }else{
          $m = array('comment_id' => $commentid, 'img' => $upload['path']);
          mysqld_insert('shop_comment_img', $m);
        }
      }else{
        ajaxReturnData(0,"图片".$num."上传失败。");
        exit;
      }
    }
  }
}