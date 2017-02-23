<?php

// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 百家威信 <QQ:2752555327> <http://www.squdian.com>
// +----------------------------------------------------------------------
defined('SYSTEM_IN') or exit('Access Denied');
require_once WEB_ROOT.'/includes/readcsv.class.php';
require_once WEB_ROOT.'/includes/lib/arrayiconv.class.php';
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
$admin = $_CMS['account']['username'];
$tmall_id = mysqld_select("SELECT a.id as tma_id,b.id as sta_id FROM ".table('tmall')." as a left join ".table('tmall_staff')." as b on a.id=b.department WHERE b.admin='".$admin."'");
if (empty($tmall_id)) {
  message('抱歉，非店铺人员无法查看！',refresh(),'error');
}

$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " where deleted=0 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
if (! empty($category)) {
  $childrens = '';
  foreach ($category as $cid => $cate) {
    if (! empty($cate['parentid'])) {
      $childrens[$cate['parentid']][$cate['id']] = array(
        $cate['id'],
        $cate['name']
      );
    }
  }
}

$result = array();
if ($operation == 'into') {
  // 批量导入
  $myxls = '';
  set_time_limit(0);
  // 多订单导入
  if ( $_FILES ){
      foreach($_FILES as $files_value){
           if ($files_value['error'] != 4 && !empty($files_value['tmp_name']) ) {
                 $upload = file_upload($files_value, false, NULL, NULL,$type='other');
				 if (is_error($upload)) {
                      message($upload['message'], '', 'error');
                 }
                 $myxls = $upload['path'];
				 if (!file_exists($myxls)) {
                      $error = '文件上传失败一份';
					  continue;
                 }
				$csvreader = new CsvReader($myxls);
				$line_number = $csvreader->get_lines();
				$arrobj = new arrayiconv();
				$rows = ceil($line_number / 20);
				$cf_num = 0;
				for ( $i = 0; $i < $rows; $i++ ){
				      $arr = $csvreader->get_data(20,$i*20+1);
				      $arr = $arrobj->Conversion($arr,"GBK","utf-8");
				      if ($i == 0){
					      array_shift($arr);
				      }
				      foreach ($arr as $dv) {
						    if ( count($dv < 30 )){
								//商品数据
								  $table = 'tmall_order_goods';
								  $where = "orderid = '".$dv[0]."' and sn= '".$dv[9]."'";
                                  $type = 1;
							}else{
								  $table = 'tmall_order';
								  $where = "ordersn = '".$dv[0]."'";
                                  $type = 2;
							}
					        $have_cus = mysqld_select("SELECT * FROM ".table($table)." WHERE ".$where);
					        if (!empty($have_cus)) {
					               continue;
					        }
							if (!empty($dv) AND !empty($dv[0])) {
							   if ( $type == 2){
								       // 开始处理地址信息，地址信息可能有修正，所以要判断修正是否为空
									   $address = !empty($dv[39])?$dv[39]:$dv[13];
									   list($address_province, $address_city, $address_area, $address_address) = explode(" ",$address);
									   $xdata = array(
										// 'tmallid' => $dv[1],
										// 'memberid' => $dv[0],
										'ordersn' => $dv[0],
										'price' => $dv[8],
										'identity_id' => $dv[4],  // 身份证ID 需要跟客服沟通是用哪个备注信息
										'tag' => 0,      // 物流信息
										'goodsprice' => $dv[3],
										'dispatchprice' => $dv[4],
										'createtime' => $dv[18],
										'address_address' => $address_address,
										'address_area' => $address_area,
										'address_city' => $address_city,
										'address_province' => $address_province,
										'address_realname' => $dv[12],
										'address_mobile' => $dv[2],
										'bonusprice' => 0 ,  // 优惠卷使用金额
										'deleted' => 0,   //订单是否已导出 0 否 1 是
                    'tmallid' => $tmall_id['tma_id'],
                    'memberid' => $tmall_id['sta_id'],
										);
							   }else{
                    $xdata = array(
										'orderid' => $dv[0],
										'tit' => $dv[1],
										'total' => $dv[3],
										'sn' => $dv[9]
										);
							   }
							   mysqld_insert ( $table, $xdata );
							}
							
				      }
				}
		   }
	  }
  }
 
}elseif ($operation == 'display') {
  if ($_GP['nowpage'] == '2') {
    $now_page = 2;
  }else{
    $now_page = 1;
  }
  // 商品页
  $pindex = max(1, intval($_GP['page']));
  $psize = 30;
  $where = "";
  $title = $_GP['sg_title'];
  $dishsn = $_GP['sg_dishsn'];
  $orderby = '';
  $oname = $oorigin = $oweight = $ounit = $olists = $otype = $op1 = $op2 = $oprice = 'asc';
  // 只可以看到自己店铺的商品
  if ($admin!='root') {
    $where.=" AND tmallid=".$tmall_id['tma_id'];
  }
  
  if ( isset($_GP['ordername']) ){
    if ( $_GP['ordername'] == 'asc' ){
      $oname = 'desc';
    }else{
      $oname = 'asc';
    }
    $orderby = " ORDER BY name ".$_GP['ordername'];
  }
  if ( isset($_GP['orderorigin']) ){
    if ( $_GP['orderorigin'] == 'asc' ){
      $oorigin = 'desc';
    }else{
      $oorigin = 'asc';
    }
    $orderby = " ORDER BY origin ".$_GP['orderorigin'];
  }
  if ( isset($_GP['orderweight']) ){
    if ( $_GP['orderweight'] == 'asc' ){
      $oweight = 'desc';
    }else{
      $oweight = 'asc';
    }
    $orderby = " ORDER BY weight ".$_GP['orderweight'];
  }
  if ( isset($_GP['orderunit']) ){
    if ( $_GP['orderunit'] == 'asc' ){
      $ounit = 'desc';
    }else{
      $ounit = 'asc';
    }
    $orderby = " ORDER BY unit ".$_GP['orderunit'];
  }
  if ( isset($_GP['orderlists']) ){
    if ( $_GP['orderlists'] == 'asc' ){
      $olists = 'desc';
    }else{
      $olists = 'asc';
    }
    $orderby = " ORDER BY lists ".$_GP['orderlists'];
  }
  if ( isset($_GP['ordertype']) ){
    if ( $_GP['ordertype'] == 'asc' ){
      $otype = 'desc';
    }else{
      $otype = 'asc';
    }
    $orderby = " ORDER BY type ".$_GP['ordertype'];
  }
  if ( isset($_GP['orderp1']) ){
    if ( $_GP['orderp1'] == 'asc' ){
      $op1 = 'desc';
    }else{
      $op1 = 'asc';
    }
    $orderby = " ORDER BY p1 ".$_GP['orderp1'];
  }
  if ( isset($_GP['orderp2']) ){
    if ( $_GP['orderp2'] == 'asc' ){
      $op2 = 'desc';
    }else{
      $op2 = 'asc';
    }
    $orderby = " ORDER BY p2 ".$_GP['orderp2'];
  }
  if ( isset($_GP['orderprice']) ){
    if ( $_GP['orderprice'] == 'asc' ){
      $oprice = 'desc';
    }else{
      $oprice = 'asc';
    }
    $orderby = " ORDER BY productprice ".$_GP['orderprice'];
  }

  if (!empty($title)) {
    $where.=" AND title LIKE '%".$title."%'";
  }
  if (!empty($dishsn)) {
    $where.=" AND dishsn='".$dishsn."'";
  }
  
  $dish = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS * FROM ".table('tmall_dish')." WHERE deleted<>1".$where.$orderby." LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
  // 总记录数
  $data_total = mysqld_select("SELECT FOUND_ROWS() as total;");
  $total = $data_total['total'];
  $pager = pagination($total, $pindex, $psize);

  // 订单页
  $where2 = "";
  $order_number = $_GP['order_number'];
  $order_tag = intval($_GP['order_tag']);
  $begintime = $_GP['begintime'];
  $endtime = $_GP['endtime'];
  // 只可以看到自己店铺的订单
  if ($admin!='root') {
    $where2.=" AND tmallid=".$tmall_id['tma_id'];
  }
  if (!empty($order_number)) {
    $where2.=" AND ordersn='".$order_number."'";
  }
  if (!empty($order_tag)) {
    $where2.=" AND tag='".$order_tag."'";
  }
  if (!empty($begintime)) {
    $where2.=" AND createtime>".strtotime($begintime);
  }
  if (!empty($endtime)) {
    $where2.=" AND createtime<".strtotime($endtime);
  }

  $order = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS * FROM ".table('tmall_order')." WHERE deleted<>1".$where2." ORDER BY createtime DESC");
  // $ordersn_ary = array();
  // foreach ($order as $ov) {
  //   $ordersn_ary[] = $ov['ordersn'];
  // }
  // $ordersn_ary = array_unique($ordersn_ary);
  // $al_order = array();
  // foreach ($ordersn_ary as $osnv) {
  //   foreach ($order as $ooov) {
  //     if ($ooov['ordersn'] == $osnv) {
  //       $al_order[$osnv][] = $ooov;
  //     }
  //   }
  // }

  include addons_page('productsalestatistics_tempale');
}elseif ($operation == 'refresh_goods') {
  // 更新商品
  $order_goods = mysqld_selectall("SELECT * FROM ".table('tmall_order_goods'));
  if (!empty($order_goods)) {
    foreach ($order_goods as $ogv) {
      $order = mysqld_select("SELECT tmallid,memberid FROM ".table('tmall_order')." WHERE id=".$ogv['orderid']);
      if (!empty($ogv['sn'])) {
        $dish = mysqld_select("SELECT * FROM ".table('tmall_dish')." WHERE dishsn='".$ogv['sn']."'");
        if (empty($dish)) {
          $goods = mysqld_select("SELECT * FROM ".table('shop_goods')." WHERE goodssn='".$ogv['sn']."'");
          if (!empty($goods)) {
            $data = array(
              'gid'=>$goods['id'],
              'title'=>$goods['title'],
              'brand'=>$goods['brand'],
              'dishsn'=>$goods['goodssn'],
              'createtime'=>time(),
              'tmallid'=>$order['tmallid'],
              'memberid'=>$order['memberid'],
              );
            mysqld_insert('tmall_dish',$data);
          }
        }
      }elseif (!empty($ogv['tit'])) {
        $dish = mysqld_select("SELECT * FROM ".table('tmall_dish')." WHERE title='".$ogv['tit']."'");
        if (empty($dish)) {
          $data = array(
            'title'=>$ogv['tit'],
            'createtime'=>time(),
            'tmallid'=>$order['tmallid'],
            'memberid'=>$order['memberid'],
            );
          mysqld_insert('tmall_dish',$data);
        }
      }
    }
    message('刷新完成！',refresh(),'success');
  }else{
    message('当前无符合条件的订单商品！',refresh(),'error');
  }
}elseif ($operation == 'edit_data') {
  // 编辑数据
  $data_id = $_GP['ajax_id'];
  $data_name = $_GP['field_name'];
  $data_value = $_GP['ajax_value'];

  $table = 'tmall_dish';
  if ($data_name == 'good_name') {
    $data_name = 'name';
  }
  if ($data_name == 'identity_id') {
    $table = 'tmall_order';
  }

  $re = mysqld_update($table, array($data_name=>$data_value), array('id'=>$data_id));
  if ($re) {
    $result['message'] = 1;
    $result['value'] = $data_value;
  }else{
    $result['message'] = '更新失败，可能内容没有改动！';
  }

  echo json_encode($result);
}elseif ($operation == 'get_good_val') {
  // 获取产品属性
  $good_id = intval($_GP['good_id']);

  $good = mysqld_select("SELECT * FROM ".table('shop_goods')." WHERE id=".$good_id);
  if (!empty($good)) {
    $result['brand'] = get_brand($good['brand']);
    $result['dishsn'] = $good['goodssn'];
    $result['brandid'] = $good['brand'];
    $result['message'] = 1;
  }else{
    $result['message'] = '产品获取失败！';
  }

  echo json_encode($result);
}elseif ($operation == 'add_good') {
  // 添加商品
  $c_goods = intval($_GP['c_goods']);
  $ad_name = $_GP['ad_name'];
  $ad_brand = $_GP['add_brand_hidden'];
  $ad_sn = $_GP['ad_sn'];
  $ad_origin = $_GP['ad_origin'];
  $ad_weight = $_GP['ad_weight'];
  $ad_unit = $_GP['ad_unit'];
  $ad_lists = $_GP['ad_lists'];
  $ad_type = $_GP['ad_type'];
  $type_p1 = $_GP['type-p1'];
  $type_p2 = $_GP['type-p2'];
  $ad_price = $_GP['ad_price'];

  if (!empty($c_goods)) {
    $data = array();
    $good = mysqld_select("SELECT * FROM ".table('shop_goods')." WHERE id=".$c_goods);
    $data['title'] = $good['title'];
    $data['gid'] = $good['id'];
    if (!empty($ad_name)) {
      $data['name'] = $ad_name;
    }
    if (!empty($ad_brand)) {
       $data['brand'] = $ad_brand;
    }
    if (!empty($ad_sn)) {
      $data['dishsn'] = $ad_sn;
    }
    if (!empty($ad_origin)) {
      $data['origin'] = $ad_origin;
    }
    if (!empty($ad_weight)) {
      $data['weight'] = $ad_weight;
    }
    if (!empty($ad_unit)) {
      $data['unit'] = $ad_unit;
    }
    if (!empty($ad_lists)) {
      $data['lists'] = $ad_lists;
    }
    $data['type'] = $ad_type;
    if (!empty($type_p1)) {
      $data['p1'] = $type_p1;
    }
    if (!empty($type_p2)) {
      $data['p2'] = $type_p2;
    }
    if (!empty($ad_price)) {
      $data['marketprice'] = $ad_price;
      $data['productprice'] = $ad_price;
    }
    $data['createtime'] = time();
    if (!empty($tmall_id)) {
      $data['tmallid'] = $tmall_id['tma_id'];
    }
    if (!empty($tmall_staff)) {
      $data['memberid'] = $tmall_id['sta_id'];
    }

    mysqld_insert('tmall_dish', $data);
    message('添加完成！',refresh(),'success');
  }else{
    message('商品不能为空！',refresh(),'error');
  }
}elseif ($operation == 'mark') {
  // 保存标记
  $id = $_GP['mark_id'];
  $mark = $_GP['mark_val'];

  if (!empty($id) AND !empty($mark)) {
    mysqld_update('tmall_order', array('tag'=>$mark),array('id'=>$id));
    $result['message'] = 1;
  }else{
    $result['message'] = '标记不能为空！';
  }
  echo json_encode($result);
}

// 获取品牌名
function get_brand($id=null) {
  if (!empty($id)) {
    $brand = mysqld_select("SELECT brand FROM ".table('shop_brand')." WHERE id=".$id);
    if (!empty($brand)) {
      return $brand['brand'];
    }
  }
}

// 获取订单商品
function get_order_goods($order_sn) {
  $ogs = mysqld_selectall("SELECT a.*, b.productprice FROM ".table('tmall_order_goods')." as a left join ".table('tmall_dish')." as b on a.sn=b.dishsn WHERE a.orderid='".$order_sn."'");
  return $ogs;
}
