<?php
  $op = empty($_GP['op']) ? 'display' : $_GP['op'];
  $status_ary = array('1'=>'上架', '2'=>'下架');

  if ($op == 'display') {
    $list = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS * FROM ".table('openshop')." ORDER BY createtime DESC");
    $total = mysqld_select("SELECT FOUND_ROWS() as total;");
    if (!empty($list)) {
      foreach ($list as $lk => &$lv) {
        $mem = mysqld_select("SELECT realname, nickname, mobile FROM ".table('member')." WHERE openid='".$lv['openid']."'");
        $lv['username'] = $mem['nickname'];
        $lv['mobile'] = $mem['mobile'];
        $goods = mysqld_select("SELECT count(*) as num FROM ".table('openshop_relation')." WHERE openshopid=".$lv['id']);
        $lv['goods_num'] = $goods['num'];
        $money_ary = mysqld_selectall("SELECT commision FROM ".table('shop_order_goods')." WHERE status IN (0,-1,-2) AND seller_openid='".$lv['openid']."'");
        $lv['all_commision'] = 0;
        foreach ($money_ary as $mav) {
          $lv['all_commision'] += (float)$mav['commision'];
        }
        $lv['all_commision'] = round($lv['all_commision'], 2);
      }
      unset($lv);
    }
    $total = $total['total'];
    include page('distributor');
  }elseif ($op == 'search') {
    if (!empty($_GP['start_time']) AND !empty($_GP['end_time'])) {
      $b_time = strtotime($_GP['start_time']);
      $e_time = strtotime($_GP['end_time']);
      $where1 = " AND operatetime>".$b_time." AND operatetime<".$e_time;
      $where2 = " AND createtime>".$b_time." AND createtime<".$e_time;
    }
    if (!empty($_GP['u_name'])) {
      $u_name = $_GP['u_name'];
      $where = "WHERE b.nickname='".$_GP['u_name']."'";
    }elseif (!empty($_GP['mobile'])) {
      $mobile = $_GP['mobile'];
      $where = "WHERE b.mobile=".$_GP['mobile'];
    }
    $list = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.*, b.mobile, b.nickname as username FROM ".table('openshop')." as a left join ".table('member')." as b on a.openid=b.openid ".$where." ORDER BY a.createtime DESC");
    $total = mysqld_select("SELECT FOUND_ROWS() as total;");
    if (!empty($list)) {
      foreach ($list as $lk => &$lv) {
        $goods = mysqld_select("SELECT count(*) as num FROM ".table('openshop_relation')." WHERE openshopid=".$lv['id'].$where1);
        $lv['goods_num'] = $goods['num'];
        $lv['all_commision'] = 0;
        $money_ary = mysqld_selectall("SELECT commision FROM ".table('shop_order_goods')." WHERE status IN (0,-1,-2) AND seller_openid='".$lv['openid']."'".$where2);
        foreach ($money_ary as $mav) {
          $lv['all_commision'] += (float)$mav['commision'];
        }
        $lv['all_commision'] = round($lv['all_commision'], 2);
      }
      unset($lv);
    }
    $total = $total['total'];
    include page('distributor');
  }elseif ($op == 'detail') {
    $shopid = $_GP['shopid'];
    if (!empty($_GP['stime']) AND !empty($_GP['etime'])) {
      $s_time = date('Y-m-d',$_GP['stime']);
      $e_time = date('Y-m-d',$_GP['etime']);
      $where1 = " AND operatetime>".$_GP['stime']." AND operatetime<".$_GP['etime'];
      $where2 = " AND createtime>".$_GP['stime']." AND createtime<".$_GP['etime'];
    }

    $list = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS * FROM ".table('openshop_relation')." WHERE openshopid=".$shopid.$where1." ORDER BY operatetime DESC");
    $total = mysqld_select("SELECT FOUND_ROWS() as total;");
    if (!empty($list)) {
      foreach ($list as $lk => &$lv) {
        $good = mysqld_select("SELECT title FROM ".table('shop_dish')." WHERE id=".$lv['goodid']);
        $lv['good_name'] = $good['title'];
        $sale_ary = mysqld_selectall("SELECT commision,total FROM ".table('shop_order_goods')." WHERE status IN (0,-1,-2) AND seller_openid='".$lv['openid']."' AND goodsid=".$lv['goodid'].$where2);
        $lv['sale_num'] = 0;
        $lv['all_commision'] = 0;
        foreach ($sale_ary as $sav) {
          $lv['sale_num'] += intval($sav['total']);
          $lv['all_commision'] += (float)$sav['commision'];
        }
        $lv['all_commision'] = round($lv['all_commision'], 2);
      }
      unset($lv);
    }
    $total = $total['total'];
    include page('distributor_detail');
  }
