<?php

namespace wapi\controller;

class good_detail extends base {

  public function index()
  {
    $result = array();
    $_GP = $this->request;
    $dish_id = intval($_GP['dish_id']);
    $is_contont = $_GP['is_contont'];
    $member = get_member_account(true, false);

    if (empty($dish_id)) {
      ajaxReturnData(0,'商品ID不能为空');
    }

    $good = mysqld_select("SELECT * FROM ".table('shop_dish')." WHERE id=$dish_id");
    // dump($good);
    // return;

    if (empty($good)) {
      ajaxReturnData(0,'商品查询失败');
    }

    $activity_dish = mysqld_select("SELECT * FROM ".table('activity_dish')." WHERE ac_shop_dish=".$good['id']);
    // if (empty($activity_dish)) {
    //   ajaxReturnData(0,'该商品不在限时购之内');
    // }

    // 获取主图
    $piclist = array();
    $piclist[] = $good['thumb'];
    // 获取细节图      有误有误有误有误有误有误有误有误有误有误有误有误有误有误   shop_dish_piclist不是good_piclist
    $goods_piclist = mysqld_select("SELECT * FROM " . table('shop_goods_piclist') . " WHERE goodid = :goodid", array(':goodid' => $good['id']));
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
    // foreach ($contents as $cv) {
    //   echo '<img src="'.$cv.'" class="img-ks-lazyload"/>';
    // }
    // dump($piclist);
    // dump($contents);

    // 获取仓库信息   没有没有没有没有没有没有没有没有没有没有没有没有没有没有没有没有
    $depot = mysqld_select("SELECT name FROM " . table('dish_list') . "  WHERE id=:depotid", array(':depotid' => $good['pcate']));

    $list = array();
    // 当前是否已登陆      没有没有没有没有没有没有没有没有没有没有不用判断，，用户一定是登录状态的，没有登录在base父类中已经告知app
    if (empty($member['openid'])) {
      $list['login'] = 0;
    }else{
      $list['login'] = 1;
    }
    // 商品ID 
    $list['id'] = $good['id'];
    // 销量
    $list['sales'] = $good['sales_num'];
    // 标题
    $list['title'] = $good['title'];
    // 简单描述
    $list['description'] = $good['description'];
    // 产品库价格
    $list['productprice'] = (float)$good['productprice'];
    // 价格
    $list['marketprice'] =(float)$good['marketprice'];
    // 限时购特价
    $list['timeprice'] = empty($activity_dish['ac_dish_price'])?0:(float)$activity_dish['ac_dish_price'];
    // 库存
    $list['total'] = $good['store_count'];
    // 展示图片
    $list['piclist'] = $piclist;
    // 仓库  没有没有没有没有没有没有没有没有没有没有没有没有
    if (!empty($depot)) {
      $list['depot'] = $depot['name'];
    }else{
      $list['depot'] = null;
    }
    $use_tax = get_tax($good['taxid']);
    // 税率   没有没有没有没有没有没有没有没有没有没有
    if (!empty($use_tax)) {
      $list['tax'] = $use_tax['tax'];
    }else{
      $list['tax'] = null;
    }
    // type
    $list['type'] = $good['type'];
    // timestart
    $list['timestart'] = $good['timestart'];
    // timeend
    $list['timeend'] = $good['timeend'];
    // 单笔最大购买数量  没有没有没有没有没有没有没有没有没有
    $list['max_buy_quantity'] = $good['max_buy_quantity'];
    // 品牌
    $brand = mysqld_select("SELECT * FROM ".table('shop_brand')." WHERE id=".$good['brand']);
    $list['brand'] = $brand['brand'];
    // 国家  品牌没有对应国家  没有没有没有没有没有没有没有没有
    $country = mysqld_select("SELECT * FROM ".table('shop_country')." WHERE id=".$brand['country_id']);
    $list['country'] = $country['name'];
    $list['country_icon'] = download_pic($country['icon']);
    // 分类名       有误有误有误有误有误有误有误有误有误有误有误有误  activity_dish 从该表中 取出 对应分类ac_p2_id
    $category = mysqld_select("SELECT name FROM ".table('shop_category')." WHERE id=".$good['store_p1']);
    $list['category'] = $category['name'];
    // 购物车商品数量
    if (!empty($member)) {
      $list['shoppingcart_num'] = countCartProducts($member['openid']);
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
    // 详情图
    if ($is_contont == 'yes') {
      // 通用详情头尾       有误 有误有误有误 有误有误有误 有误有误有误 有误有误有误  从这里取 shop_dish_commontop
      $head = mysqld_select("SELECT * FROM ".table('config')." where name=:uname", array('uname' => 'detail_head'));
      $foot = mysqld_select("SELECT * FROM ".table('config')." where name=:uname", array('uname' => 'detail_foot'));
      $list['content_head'] = $head['value'];
      $list['content_foot'] = $foot['value'];
      $list['content'] = $contents;
    }

    // dump($list);
    ajaxReturnData(1,'获取成功',$list);
    exit;
  }
}