<?php
require_once WEB_ROOT.'/includes/lib/phpexcel/PHPExcel/IOFactory.php';
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';

$result = array();
if ($operation == 'into') {
  // 批量导入
  $myxls = '';
  if ($_FILES['myxls']['error'] != 4) {
    $upload = file_upload($_FILES['myxls'], false, NULL, NULL,$type='other');
    if (is_error($upload)) {
        message($upload['message'], '', 'error');
    }
    $myxls = $upload['path'];

    if (!file_exists($myxls)) {
      message('文件上传失败，请重试!',refresh(),'error');
    }

    //根据不同类型分别操作
    if($upload['extention'] == 'xlsx' || $upload['extention'] == 'xls') {
      $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
    }elseif($upload['extention'] == 'csv') {
      $reader = PHPExcel_IOFactory::createReader('CSV')
        ->setDelimiter(',')
        ->setInputEncoding('GBK') //不设置将导致中文列内容返回boolean(false)或乱码
        ->setEnclosure('"')
        ->setLineEnding("\r\n")
        ->setSheetIndex(0);
    }else{
      message('文件格式不正确!',refresh(),'error');
    }

    $PHPExcel = $reader->load($myxls); // 载入excel文件
    $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
    $highestRowNum = $sheet->getHighestRow(); // 取得总行数
    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
    $highestColumnNum = PHPExcel_Cell::columnIndexFromString($highestColumm);

    //取得字段，这里测试表格中的第一行为数据的字段，因此先取出用来作后面数组的键名
    $filed = array();
    for($i=0; $i<$highestColumnNum;$i++){
      $cellName = PHPExcel_Cell::stringFromColumnIndex($i).'1';
      $cellVal = $sheet->getCell($cellName)->getValue();//取得列内容
      $filed []= $cellVal;
    }

    //开始取出数据并存入数组
    $data = array();
    for($i=2;$i<=$highestRowNum;$i++){//ignore row 1
      $row = array();
      for($j=0; $j<$highestColumnNum;$j++){
        $cellName = PHPExcel_Cell::stringFromColumnIndex($j).$i;
        $cellVal = $sheet->getCell($cellName)->getValue();
        $row[ $filed[$j] ] = $cellVal;
      }
      $data []= $row;
    }
    foreach ($data as $dv) {
      $have_cus = mysqld_select("SELECT mobile FROM ".table('shop_customers')." WHERE mobile=".$dv["手机"]);
      if (!empty($have_cus)) {
        continue;
      }
      $have_mem = mysqld_select("SELECT mobile FROM ".table('member')." WHERE mobile=".$dv["手机"]);
      if (!empty($have_mem)) {
        $status = 1;
      }else{
        $status = 0;
      }
      $xdata = array(
        'username' => $dv["姓名"],
        'wanwan' => $dv["旺旺"],
        'mobile' => $dv["手机"],
        'email' => $dv["邮箱"],
        'review' => $dv["给过差评"],
        'refund' => $dv["退过款"],
        'blacklist' => $dv["营销黑名单"],
        'city' => $dv["城市"],
        'address' => $dv["地址"],
        'lasttime' => strtotime($dv["上次购买时间"]),
        'buytimes' => $dv["购买次数"],
        'price' => $dv["购买金额"],
        'level' => $dv["会员等级"],
        'shop' => $dv["店铺"],
        'updatetime' => time(),
        'status' => $status,
        );
      $re = mysqld_insert ( 'shop_customers', $xdata );
    }
    message('导入完成!',refresh(),'success');      
  }else{
    message('请上传退款表单!',refresh(),'error');
  }
}elseif ($operation == 'display') {
  // 展示及查询
  $pindex = max(1, intval($_GP['page']));
  $psize = 30;
  $where = "";
  $city = $_GP['city'];
  $level = $_GP['member'];
  $shop = $_GP['shop'];
  $manager = $_GP['department'];
  $review = $_GP['bad'];
  $refund = $_GP['refund'];
  $blacklist = $_GP['blacklist'];
  if (!empty($city)) {
    $where.=" AND city='".$city."'";
  }
  if (!empty($level)) {
    $where.=" AND level='".$level."'";
  }
  if (!empty($shop)) {
    $where.=" AND shop='".$shop."'";
  }
  if (!empty($manager)) {
    $man = mysqld_select("SELECT manager FROM ".table('shop_department')." WHERE department='".$manager."'");
    $where.=" AND salesman=".$man['manager'];
  }
  if (!empty($review) AND $review!='false') {
    $where.=" AND review='是'";
  }
  if (!empty($refund) AND $refund!='false') {
    $where.=" AND refund='是'";
  }
  if (!empty($blacklist) AND $blacklist!='false') {
    $where.=" AND blacklist='是'";
  }
  $al_member = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS * FROM ".table('shop_customers')." WHERE id>0".$where." ORDER BY price DESC"." LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
  // 总记录数
  $data_total = mysqld_select("SELECT FOUND_ROWS() as total;");
  // if (empty($al_member)) {
  //   message('查询顾客失败，请检查查询条件!',refresh(),'error');
  // }
  $total = $data_total['total'];
  $city_a = array();
  $level_a = array();
  $shop_a = array();
  $manager_a = array();
  foreach ($al_member as &$amv) {
    $manager_name = mysqld_select("SELECT name FROM ".table('shop_department_staff')." WHERE id=".$amv['salesman']);
    if (!empty($manager_name)) {
      $amv['salesman'] = $manager_name['name'];
    }else{
      $amv['salesman'] = '未分配';
    }
    if ($amv['status'] == '0') {
      $amv['status'] = '未入驻';
    }else{
      $amv['status'] = '已入驻';
    }
  }
  unset($amv);
  $all_m = mysqld_selectall("SELECT * FROM ".table('shop_customers'));
  foreach ($all_m as $alm) {
    $city_a[] = $alm['city'];
    $level_a[] = $alm['level'];
    $shop_a[] = $alm['shop'];
    $department_al = mysqld_selectall("SELECT department FROM ".table('shop_department')." ORDER BY createtime ASC");
    foreach ($department_al as $dalv) {
      $manager_a[] = $dalv['department'];
    }
  }

  $city_a = array_unique($city_a);
  $level_a = array_unique($level_a);
  $shop_a = array_unique($shop_a);
  $manager_a = array_unique($manager_a);

  $pager = pagination($total, $pindex, $psize);
  include page('memberinto');
}elseif ($operation == 'check_allot') {
  // 检查分配
  $where = "";
  $city = $_GP['city'];
  $level = $_GP['member'];
  $shop = $_GP['shop'];
  $manager = $_GP['department'];
  $review = $_GP['bad'];
  $refund = $_GP['refund'];
  $blacklist = $_GP['blacklist'];

  if (!empty($city)) {
    $where.=" AND city='".$city."'";
  }
  if (!empty($level)) {
    $where.=" AND level='".$level."'";
  }
  if (!empty($shop)) {
    $where.=" AND shop='".$shop."'";
  }
  if (!empty($review) AND $review!='false') {
    $where.=" AND review='是'";
  }
  if (!empty($refund) AND $refund!='false') {
    $where.=" AND refund='是'";
  }
  if (!empty($blacklist) AND $blacklist!='false') {
    $where.=" AND blacklist='是'";
  }
  $al_member = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS * FROM ".table('shop_customers')." WHERE salesman=0".$where);
  // 总记录数
  $data_total = mysqld_select("SELECT FOUND_ROWS() as total;");
  if (empty($al_member)) {
    $data_total['total'] = 0;
  }
  echo json_encode($data_total);
}elseif ($operation == 'allot_ones') {
  // 单个分配
  $client_id = $_GP['data_id'];
  $department = $_GP['department'];

  if (empty($client_id) or empty($department)) {
    $result['message'] = '部门和客户不能为空!';
    echo json_encode($result);
    exit;
  }
  $man = mysqld_select("SELECT manager FROM ".table('shop_department')." WHERE department='".$department."'");
  mysqld_update('shop_customers', array('salesman' => $man['manager'], 'updatetime' => time()), array('id'=> $client_id));
  $manager_name = mysqld_select("SELECT name FROM ".table('shop_department_staff')." WHERE id=".$man['manager']);
  
  $result['message'] = '分配完成!';
  $result['manager_name'] = $manager_name['name'];
  echo json_encode($result);
}elseif ($operation == 'allot_all') {
  // 批量分配
  $where = "";
  $city = $_GP['city'];
  $level = $_GP['member'];
  $shop = $_GP['shop'];
  $department = $_GP['department'];
  $review = $_GP['bad'];
  $refund = $_GP['refund'];
  $blacklist = $_GP['blacklist'];

  if (!empty($city)) {
    $where.=" AND city='".$city."'";
  }
  if (!empty($level)) {
    $where.=" AND level='".$level."'";
  }
  if (!empty($shop)) {
    $where.=" AND shop='".$shop."'";
  }
  if (!empty($review) AND $review!='false') {
    $where.=" AND review='是'";
  }
  if (!empty($refund) AND $refund!='false') {
    $where.=" AND refund='是'";
  }
  if (!empty($blacklist) AND $blacklist!='false') {
    $where.=" AND blacklist='是'";
  }

  $al_member = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS * FROM ".table('shop_customers')." WHERE salesman=0".$where);
  if (empty($al_member)) {
    $result['message'] = '客户查询失败!';
  }else{
    foreach ($al_member as $almv) {
      $man = mysqld_select("SELECT manager FROM ".table('shop_department')." WHERE department='".$department."'");
      mysqld_update('shop_customers', array('salesman' => $man['manager'], 'updatetime' => time()), array('id'=> $almv['id']));
    }
    $result['message'] = '批量分配完成!';
  }
  echo json_encode($result);
}
