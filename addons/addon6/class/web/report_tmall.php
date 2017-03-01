<?php
error_reporting(E_ALL);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');
	
	
require_once WEB_ROOT.'/includes/lib/phpexcel/PHPExcel.php';

	
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("觅海环球购")
							 ->setLastModifiedBy("觅海环球购")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("report file");


if($report=='orderreport')
{
		switch ( $_GP['template'] ){
			//平潭保税区发货单
			case 1:
			   $objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A1', '店铺编号')
							->setCellValue('B1', '编号')
							->setCellValue('C1', '收货人名称')
							->setCellValue('D1', '证件号码')
							->setCellValue('E1', '收货人地址')
							->setCellValue('F1', '收货人电话')
							->setCellValue('G1', '商品货号')
							->setCellValue('H1', '成交单价')	
							->setCellValue('I1', '购买数量')
							->setCellValue('J1', '成交总价')
							->setCellValue('K1', '所属地域')
							->setCellValue('L1', '支付交易号')
							->setCellValue('M1', '付款时间')
							->setCellValue('N1', '商品名称或类别-中文')
							->setCellValue('O1', '邮编');				
				$i=2;
				$sn = 0;
				$index=0;
				$countmoney=0;
				foreach($list as $item){
					$itemdline=0;
					$sn++;
					preg_match_all("/(?:\()(.*)(?:\))/i",$item['address_address'], $yb);
					if (empty($item['address_area'])) {
						$item['address_area'] = '其它区';
					}
					$tmall_code = mysqld_select("SELECT code FROM ".table('tmall')." WHERE id=".$item['tmallid']);
					$tmall_code = $tmall_code['code'];
					// 根据订单里的产品进行行设置，如果$itemdline =0 ; 则代表是第一行
					foreach($item['goods'] as $itemgoods){
						    $itemdatas['title'] = $itemgoods['title'];
							$k = $item['address_province'].'#'.$item['address_city'].'#'.$item['address_area'];
							$itemdatas['categoryname']=$itemdatas['categoryname'].$sline.$itemgoods['categoryname'];
							$itemdatas['optionname']=$itemdatas['optionname'].$sline.$itemgoods['optionname'];
							$itemdatas['price']=$itemdatas['price'].$sline.$itemgoods['price'];
							$itemdatas['total']=$itemdatas['total'].$sline.$itemgoods['total'];
							$itemdatas['goodstotal']=$itemdatas['goodstotal'].$sline.round(($itemgoods['total']*$itemgoods['price']),2);
							$itemdline=$itemdline+1;
							$countmoney=$countmoney+$item['price'];
							$priceother='';
							$index++;
							if(!empty($item['dispatchprice'])&&$item['dispatchprice']>0)
							{
									$priceother=$item['dispatchprice'];
							}else{
								$priceother="0";
							}
							$num = $itemgoods['total'];

							if (empty($itemgoods['sn'])){
							   // 如果组合中没有数据则忽视
                               //Array ( [tit] => 美国进口Now foods玛卡片250粒男性成人补肾玛咖秘鲁正品maca延时 [sn] => [total] => 2 [id] => 2 [gid] => 0 [title] => 美国进口Now foods玛卡片250粒男性成人补肾玛咖秘鲁正品maca延时 [name] => [tmallid] => 0 [memberid] => 0 [brand] => 0 [origin] => [dishsn] => [weight] => 0.00 [unit] => [lists] =>  schiffbluex2 *2 [p1] => [p2] => [p3] => [type] => 1 [status] => 0 [productsn] => [marketprice] => 0.00 [productprice] => 0.00 [createtime] => 1488263601 [deleted] => 0 ) 
							   if ( empty($itemgoods['lists']) ){
                                     message('组合商品:'.$itemdatas['title'].'，未标记组合,格式【货号*数量,货号*数量】，订单导出失败！','','error');
							   }
							   $itemgoods['lists'] = str_replace('，',',',$itemgoods['lists']);
							   $goods = explode(',',$itemgoods['lists']);
							   foreach ( $goods as $goods_value ){
								       list($goods_id,$goods_num) = explode('*', $goods_value );
									   if ( empty($goods_id ) or  !is_numeric($goods_num ) ){
                                            continue;
									   }
									   $nums = $num * $goods_num;
									   $list_goods = mysqld_select("SELECT * FROM ".table('tmall_dish')." WHERE dishsn='".$goods_id."'");
									   if ( empty($list_goods) ){
                                            message('组合商品中有未定义的商品，货号为:'.$goods_id.'订单导出失败！','','error');
									   }
									   $objPHPExcel->setActiveSheetIndex(0)		
											->setCellValue('A'.$i, $tmall_code)
											->setCellValue('B'.$i, $sn)
											->setCellValue('C'.$i, $item['address_realname'])
											->setCellValue('D'.$i, ' '.$item['identity_id'])
											->setCellValue('E'.$i, $item['address_province'].' '.$item['address_city'].' '.$item['address_area'].' '.$item['address_address'])
											->setCellValue('F'.$i, $item['address_mobile'])
											->setCellValue('G'.$i, $goods_id)
											->setCellValue('H'.$i, $list_goods['productprice'])
											->setCellValue('I'.$i, $nums)
											->setCellValue('J'.$i, $item['price'])
											->setCellValue('K'.$i, $k)
											->setCellValue('L'.$i, '')
											->setCellValue('M'.$i, '')
											->setCellValue('N'.$i, $list_goods['name'])
											->setCellValue('O'.$i, $yb[1][0]);
										 $i++;
							   }
						 }else{
					     	$objPHPExcel->setActiveSheetIndex(0)		
								->setCellValue('A'.$i, $tmall_code)
								->setCellValue('B'.$i, $sn)
								->setCellValue('C'.$i, $item['address_realname'])
								->setCellValue('D'.$i, ' '.$item['identity_id'])
								->setCellValue('E'.$i, $item['address_province'].' '.$item['address_city'].' '.$item['address_area'].' '.$item['address_address'])
								->setCellValue('F'.$i, $item['address_mobile'])
								->setCellValue('G'.$i, $itemgoods['dishsn'])
								->setCellValue('H'.$i, $itemgoods['productprice'])
								->setCellValue('I'.$i, $num)
								->setCellValue('J'.$i, $item['price'])
								->setCellValue('K'.$i, $k)
								->setCellValue('L'.$i, '')
								->setCellValue('M'.$i, '')
								->setCellValue('N'.$i, $itemgoods['name'])
								->setCellValue('O'.$i, $yb[1][0]);
								//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':P'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);  
								//$objPHPExcel->getActiveSheet()->getStyle( 'J'.$i.':P'.$i)->getAlignment()->setWrapText(true);  
								//$objBorderA5 = $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getBorders();
								//$objBorderA5->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								//$objBorderA5->getTop()->getColor()->setARGB('FFFF0000'); 
								//$objBorderA5->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								//$objBorderA5->getBottom()->getColor()->setARGB('FFFF0000');
								//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':P'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
						$i++;	
						}
				}}	
				$objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(70); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(5); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(70); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->setTitle('订单统计');
				break;
			case 2:
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A1', '包裹号')
							->setCellValue('B1', '*包裹重量(LB/KG)')
							->setCellValue('C1', '渠道')
							->setCellValue('D1', '渠道订单号')
							->setCellValue('E1', '发件人姓名')
							->setCellValue('F1', '发件人联系方式')
							->setCellValue('G1', '*收件人姓名')
							->setCellValue('H1', '*收件人详细地址')	
							->setCellValue('I1', '*省')
							->setCellValue('J1', '*市')
							->setCellValue('K1', '*地区')
							->setCellValue('L1', '街道')
							->setCellValue('M1', '*收件人邮编')
							->setCellValue('N1', '*收件人手机号码')
							->setCellValue('O1', '收件人身份证号码')	
							->setCellValue('P1', '收件人其他联系方式')
							->setCellValue('Q1', '配货标签')
							->setCellValue('R1', '卖家备注')
							->setCellValue('S1', '买家备注')
							->setCellValue('T1', '收件人Email')
							->setCellValue('U1', '保险金额（CNY）')
							->setCellValue('V1', '收件人缴税')			
							->setCellValue('W1', '一级商品类名')
							->setCellValue('X1', '二级商品类名')
							->setCellValue('Y1', '*物品名称（品名）')
							->setCellValue('Z1', '*品牌')
							->setCellValue('AA1', '规格')
							->setCellValue('AB1', '材质')
							->setCellValue('AC1', '型号')
							->setCellValue('AD1', '*发货数量')
							->setCellValue('AE1', '*发货成交/申报单价（CNY）');
				$i=2;
				$sn = 0;
				$index=0;
				$countmoney=0;
				foreach($list as $item){
					$itemdline=0;
					$sn++;
					// 根据订单里的产品进行行设置，如果$itemdline =0 ; 则代表是第一行
					foreach($item['goods'] as $itemgoods){
						    $itemdatas['title'] = $itemgoods['title'];
							$k = $item['address_province'].'#'.$item['address_city'].'#'.$item['address_area'];
							$itemdatas['categoryname']=$itemdatas['categoryname'].$sline.$itemgoods['categoryname'];
							$itemdatas['Supplier']=$itemdatas['Supplier'].$sline.$itemgoods['Supplier'];
							$itemdatas['optionname']=$itemdatas['optionname'].$sline.$itemgoods['optionname'];
							$itemdatas['price']=$itemdatas['price'].$sline.$itemgoods['price'];
							$itemdatas['total']=$itemdatas['total'].$sline.$itemgoods['total'];
							$itemdatas['goodstotal']=$itemdatas['goodstotal'].$sline.round(($itemgoods['total']*$itemgoods['price']),2);
							$itemdline=$itemdline+1;
							$countmoney=$countmoney+$item['price'];
							$priceother='';
							$index++;
							if(!empty($item['dispatchprice'])&&$item['dispatchprice']>0)
							{
									$priceother=$item['dispatchprice'];
							}else{
								$priceother="0";
							}
							 $num = $itemgoods['total'];
							if (!empty($itemgoods['brand'])) {
							 	$brand =  mysqld_select('SELECT brand FROM '.table('shop_brand').' WHERE id = '.$itemgoods['brand']);
							}
							preg_match_all("/(?:\()(.*)(?:\))/i",$item['address_address'], $yb);
							if (!empty($itemgoods['Supplier']) or empty($item['ordersn'])){
							   // 如果组合中没有数据则忽视
							   if ( empty($itemgoods['Supplier']) ){
                                     message('组合商品:'.$itemdatas['title'].'，有未标记单品的产品，订单导出失败！',refresh(),'error');
							   }
							   $itemgoods['Supplier'] = str_replace('，',',',$itemgoods['Supplier']);
							   $goods = explode(',',$itemgoods['Supplier']);
							   foreach ( $goods as $goods_value ){
								       list($goods_id,$goods_num) = explode('*', $goods_value );
									   if ( !is_numeric($goods_id ) &&  !is_numeric($goods_num ) ){
                                            continue;
									   }
									   $nums = $num * $goods_num;
									   $goods_title = mysqld_select('SELECT subtitle FROM '.table('shop_goods').' WHERE id = '.$goods_id);
									   $objPHPExcel->setActiveSheetIndex(0)		
											->setCellValue('A'.$i, '')
											->setCellValue('B'.$i, '')
											->setCellValue('C'.$i, '')
											->setCellValue('D'.$i, '')
											->setCellValue('E'.$i, '')
											->setCellValue('F'.$i, '')
											->setCellValue('G'.$i, $item['address_realname'])
											->setCellValue('H'.$i, $item['address_province'].' '.$item['address_city'].' '.$item['address_area'].' '.$item['address_address'])
											->setCellValue('I'.$i, $item['address_province'])
											->setCellValue('J'.$i, $item['address_city'])
											->setCellValue('K'.$i, $item['address_area'])
											->setCellValue('L'.$i, '')
											->setCellValue('M'.$i, $yb[1][0])
											->setCellValue('N'.$i, $item['address_mobile'])
											->setCellValue('O'.$i, $item['identity_id'])
											->setCellValue('P'.$i, '')
											->setCellValue('Q'.$i, '')
											->setCellValue('R'.$i, '')
											->setCellValue('S'.$i, '')
											->setCellValue('T'.$i, '')
											->setCellValue('U'.$i, '')
											->setCellValue('V'.$i, '')
											->setCellValue('W'.$i, $itemgoods['p1'])
											->setCellValue('X'.$i, $itemgoods['p2'])
											->setCellValue('Y'.$i, $itemgoods['name'])
											->setCellValue('Z'.$i, $brand['brand'])
											->setCellValue('AA'.$i, $itemgoods['origin'])
											->setCellValue('AB'.$i, '')
											->setCellValue('AC'.$i, '')
											->setCellValue('AD'.$i, intval($itemgoods['total']))
											->setCellValue('AE'.$i, $item['price']);
										 $i++;
							   }
						}else{
					     	$objPHPExcel->setActiveSheetIndex(0)		
											->setCellValue('A'.$i, '')
											->setCellValue('B'.$i, $itemgoods['weight'])
											->setCellValue('C'.$i, '')
											->setCellValue('D'.$i, $item['ordersn'])
											->setCellValue('E'.$i, '')
											->setCellValue('F'.$i, '')
											->setCellValue('G'.$i, $item['address_realname'])
											->setCellValue('H'.$i, $item['address_province'].' '.$item['address_city'].' '.$item['address_area'].' '.$item['address_address'])
											->setCellValue('I'.$i, $item['address_province'])
											->setCellValue('J'.$i, $item['address_city'])
											->setCellValue('K'.$i, $item['address_area'])
											->setCellValue('L'.$i, '')
											->setCellValue('M'.$i, $yb[1][0])
											->setCellValue('N'.$i, $item['address_mobile'])
											->setCellValue('O'.$i, $item['identity_id'])
											->setCellValue('P'.$i, '')
											->setCellValue('Q'.$i, '')
											->setCellValue('R'.$i, '')
											->setCellValue('S'.$i, '')
											->setCellValue('T'.$i, '')
											->setCellValue('U'.$i, '')
											->setCellValue('V'.$i, '')
											->setCellValue('W'.$i, $itemgoods['p1'])
											->setCellValue('X'.$i, $itemgoods['p2'])
											->setCellValue('Y'.$i, $itemgoods['name'])
											->setCellValue('Z'.$i, $brand['brand'])
											->setCellValue('AA'.$i, $itemgoods['origin'])
											->setCellValue('AB'.$i, '')
											->setCellValue('AC'.$i, '')
											->setCellValue('AD'.$i, intval($itemgoods['total']))
											->setCellValue('AE'.$i, $item['price']);
						$i++;	
						}
				}}	
				$objPHPExcel->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(70); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(5); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(70); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->setTitle('订单统计');
				break;
			default:
				//彩虹快递发货订单
				$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A1', '商家订单号')
							->setCellValue('B1', '收件人')
							->setCellValue('C1', '身份证号码')
							->setCellValue('D1', '收件人省')
							->setCellValue('E1', '收件人市')
							->setCellValue('F1', '收件人区')
							->setCellValue('G1', '收货地址')
							->setCellValue('H1', '收件人电话')	
							->setCellValue('I1', '寄件人姓名')
							->setCellValue('J1', '寄件人地址')
							->setCellValue('K1', '寄件人电话')
							->setCellValue('L1', '商品代码(选填)')
							->setCellValue('M1', '品牌')
							->setCellValue('N1', '品名')
							->setCellValue('O1', '规格型号')
					        ->setCellValue('P1', '单位')
							->setCellValue('Q1', '单件重量(KG)')
							->setCellValue('R1', '数量')
							->setCellValue('S1', '单价(元)')
							->setCellValue('T1', '币制代码')
							->setCellValue('U1', '备注');				
				$i=2;
				foreach($list as $item){
					$itemdline=0;
					$ii = $i;
					// 初始化总件数
				   $goods_nums = 0;
					// 根据订单里的产品进行行设置，如果$itemdline =0 ; 则代表是第一行
					foreach($item['goods'] as $itemgoods){
						
						$num = $itemgoods['total'];
						// 开始考虑组合产品
						if (!empty($itemgoods['Supplier'])){
                               $itemgoods['Supplier'] = str_replace('，',',',$itemgoods['Supplier']);
							   $goods = explode(',',$itemgoods['Supplier']);
							   foreach ( $goods as $goods_value ){
								      if($itemdline!=0){
										    $item['identity']=$item['ordersn']=$item['address_realname']=$item['address_province']=$item['address_city']=$item['address_area']=$item['address_address']=$item['address_mobile']='';
											$itemdline=$itemdline+1;
									  }else{
											$itemdline=$itemdline+1;
									  }
								       list($goods_id,$goods_num) = explode('*', $goods_value );
									   if ( !is_numeric($goods_id ) &&  !is_numeric($goods_num ) ){
                                            continue;
									   }
									    $nums = $num * $goods_num;
									    $goods_title = mysqld_select('SELECT * FROM '.table('shop_goods').' WHERE id = '.$goods_id);
										// 开始获取品牌
										$brand =  mysqld_select('SELECT brand FROM '.table('shop_brand').' WHERE id = '.$goods_title['brand']);
									    $objPHPExcel->setActiveSheetIndex(0)	
													->setCellValue('A'.$i, $item['ordersn'])
													->setCellValue('B'.$i, $item['address_realname'])
													->setCellValue('C'.$i, ' '.$item['identity_id'])
													->setCellValue('D'.$i, $item['address_province'])
													->setCellValue('E'.$i, $item['address_city'])
													->setCellValue('F'.$i, $item['address_area'])
													->setCellValue('G'.$i, $item['address_address'])
													->setCellValue('H'.$i, $item['address_mobile'])
													->setCellValue('I'.$i, $itemdline==1?'Mark':'')
													->setCellValue('J'.$i, $itemdline==1?'15-17 132RD ST COLLAGEN POINT':'')
													->setCellValue('K'.$i, $itemdline==1?'1718-445-2118':'')
													->setCellValue('L'.$i, '')
													->setCellValue('M'.$i, $brand['brand'])
													->setCellValue('N'.$i, $goods_title['subtitle'])
													->setCellValue('O'.$i, $goods_title['origin'])
													->setCellValue('P'.$i, $goods_title['unit'])
													->setCellValue('Q'.$i, $goods_title['weight'])
													->setCellValue('R'.$i, $nums)
													->setCellValue('S'.$i, '')
													->setCellValue('T'.$i, 'RMB');	
										 $i++;
										 $goods_nums += $nums;
							  }     
						}else{
						  // 进入非组合产品
						   if($itemdline!=0){
								 $item['identity']=$item['ordersn']=$item['address_realname']=$item['address_province']=$item['address_city']=$item['address_area']=$item['address_address']=$item['address_mobile']='';
								 $itemdline=$itemdline+1;
						  }else{
								$itemdline=$itemdline+1;
						  }
						if (!empty($itemgoods['brand'])) {
						 	$brand =  mysqld_select('SELECT brand FROM '.table('shop_brand').' WHERE id = '.$itemgoods['brand']);
						}
						$objPHPExcel->setActiveSheetIndex(0)	
								->setCellValue('A'.$i, $item['ordersn'])
								->setCellValue('B'.$i, $item['address_realname'])
								->setCellValue('C'.$i, ' '.$item['identity_id'])
								->setCellValue('D'.$i, $item['address_province'])
								->setCellValue('E'.$i, $item['address_city'])
								->setCellValue('F'.$i, $item['address_area'])
								->setCellValue('G'.$i, $item['address_address'])
								->setCellValue('H'.$i, $item['address_mobile'])
								->setCellValue('I'.$i, $itemdline==1?'Mark':'')
								->setCellValue('J'.$i, $itemdline==1?'15-17 132RD ST COLLAGEN POINT':'')
								->setCellValue('K'.$i, $itemdline==1?'1718-445-2118':'')
								->setCellValue('L'.$i, '')
								->setCellValue('M'.$i, $brand['brand'])
								->setCellValue('N'.$i, $itemgoods['name'])
								->setCellValue('O'.$i, $itemgoods['origin'])
								->setCellValue('P'.$i, $itemgoods['unit'])
								->setCellValue('Q'.$i, $itemgoods['weight'])
								->setCellValue('R'.$i, $num)
								->setCellValue('S'.$i, '')
								->setCellValue('T'.$i, 'RMB');		
								//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':P'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);  
								//$objPHPExcel->getActiveSheet()->getStyle( 'J'.$i.':P'.$i)->getAlignment()->setWrapText(true);  
								//$objBorderA5 = $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getBorders();
								//$objBorderA5->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								//$objBorderA5->getTop()->getColor()->setARGB('FFFF0000'); 
								//$objBorderA5->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								//$objBorderA5->getBottom()->getColor()->setARGB('FFFF0000');
								// $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':P'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
								$i++;	
								$goods_nums += $num;
						}
				   }
				   // 进行备注的操作
				   if (( $i-1 ) > $ii){
					  $objPHPExcel->setActiveSheetIndex(0)	
								   ->setCellValue('U'.$ii, $goods_nums);
                      $objPHPExcel->getActiveSheet()->mergeCells('U'.$ii.':U'.($i-1));
					  for ( $icolor = $ii; $icolor < $i; $icolor ++ ){
					       $objPHPExcel->getActiveSheet()->getStyle('A'.$icolor.':U'.$icolor)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                           $objPHPExcel->getActiveSheet()->getStyle('A'.$icolor.':U'.$icolor)->getFill()->getStartColor()->setARGB('FFFF00');
						   $styleArray = array(  
                                  'borders' => array(  
                                        'allborders' => array(  
                                           //'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的  
                                            'style' => PHPExcel_Style_Border::BORDER_THIN,//细边框  
                                           //'color' => array('argb' => 'FFFF0000'),  
                                        )
                                     )
                            );  
                             $objPHPExcel->getActiveSheet()->getStyle('A'.$icolor.':U'.$icolor)->applyFromArray($styleArray);
					  }
				   }else{
				    $objPHPExcel->setActiveSheetIndex(0)	
								 ->setCellValue('U'.($i-1), $goods_nums);
				   }
				}		
				$objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(55); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(35); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(8); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(8); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(8); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(8); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(8); 
				$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(8); 
				$objPHPExcel->getActiveSheet()->setTitle('订单统计');
				break;
		}
}
if($report=='dishreport')
{
      $objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('A1', '宝贝ID')
							->setCellValue('B1', '产品库编号')
							->setCellValue('C1', '产品名称')
							->setCellValue('D1', '价格')
							->setCellValue('E1', '特别价格')
							->setCellValue('F1', '库存')
							->setCellValue('G1', '状态')
		                    ->setCellValue('H1', '条型码');

	  $i = 2;
	  if (is_array($list)){
		     foreach ( $list as $item ){
				      if ($item['status'] == 1 ){
                           $status = '上架中';
					  }else{
                           $status = '已下架';
					  }
					  $objPHPExcel->setActiveSheetIndex(0)		
									->setCellValue('A'.$i, $item['id'])
									->setCellValue('B'.$i, $item['gid'])
									->setCellValue('C'.$i, $item['title'])
									->setCellValue('D'.$i, ' '.$item['marketprice'])
									->setCellValue('E'.$i, $item['timeprice'])
									->setCellValue('F'.$i, $item['total'])
									->setCellValue('G'.$i, $status)
					                ->setCellValue('H'.$i, ' '.$item['goodssn']);
					  $i++;
			 }
	  }
	  $objPHPExcel->getActiveSheet()->getStyle('H')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
      $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10); 
      $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10); 
	  $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60); 
	  $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10); 
	  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10); 
	  $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10); 
	  $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10); 
	  $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20); 
	  $objPHPExcel->getActiveSheet()->setTitle('订单统计');
}
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

ob_end_clean();
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="report_'.time().'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

	