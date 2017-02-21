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
										'tmallid' => $dv[1],
										'memberid' => $dv[0],
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
  // 商品页
  $pindex = max(1, intval($_GP['page']));
  $psize = 30;
  $where = "";
  $title = $_GP['title'];
  $dishsn = $_GP['dishsn'];
  if (!empty($title)) {
    $where.=" AND title='".$title."'";
  }
  if (!empty($dishsn)) {
    $where.=" AND dishsn='".$dishsn."'";
  }
  $dish = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS * FROM ".table('tmall_dish')." WHERE deleted<>1".$where." ORDER BY createtime DESC"." LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
  // 总记录数
  $data_total = mysqld_select("SELECT FOUND_ROWS() as total;");
  $total = $data_total['total'];

  $pager = pagination($total, $pindex, $psize);
  dump(web_url('productsalestatistics', array('op' => 'display')));
  include addons_page('productsalestatistics_tempale');
}elseif ($operation == 'refresh_goods') {
  // 更新商品
  $order_goods = mysqld_selectall("SELECT * FROM ".table('tmall_order_goods'));
  if (!empty($order_goods)) {
    foreach ($order_goods as $ogv) {
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
              'createtime'=>time()
              );
            mysqld_insert('tmall_dish',$data);
          }
        }
      }elseif (!empty($ogv['tit'])) {
        $dish = mysqld_select("SELECT * FROM ".table('tmall_dish')." WHERE title='".$ogv['tit']."'");
        if (empty($dish)) {
          $data = array(
            'title'=>$ogv['tit'],
            'createtime'=>time()
            );
          mysqld_insert('tmall_dish',$data);
        }
      }
    }
    message('刷新完成！',refresh(),'success');
  }else{
    message('当前无符合条件的订单商品！',refresh(),'error');
  }
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
