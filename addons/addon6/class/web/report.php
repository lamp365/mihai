<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 百家威信 <QQ:2752555327> <http://www.squdian.com>
// +----------------------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');
require_once WEB_ROOT.'/includes/lib/phpexcel/PHPExcel.php';

ini_set('memory_limit', '350M');
	
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("觅海网络")
							 ->setLastModifiedBy("觅海网络")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("report file");

if($report=='salereport01')
{

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '月份')
            ->setCellValue('B1', '交易量')
            ->setCellValue('C1', '比例');	

					
$i=2;
foreach($list as $v){
	
$objPHPExcel->setActiveSheetIndex(0)		
            ->setCellValue('A'.$i, $v['month'])
            ->setCellValue('B'.$i, $v['count'])
            ->setCellValue('C'.$i, $v['persent'].'%');
			
$i++;		
}					

$objPHPExcel->setActiveSheetIndex(0)		
            ->setCellValue('D'.$i, "总".($radioMonthForSaleType==1?"交易额":"交易量"))
            ->setCellValue('E'.$i, $allcount);
		$i++;				
		
$objPHPExcel->setActiveSheetIndex(0)		
            ->setCellValue('D'.$i, "最高峰".($radioMonthForSaleType==1?"交易额":"交易量"))
            ->setCellValue('E'.$i, $topcount);
		
$objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12); 
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12); 
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12); 
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12); 
$objPHPExcel->getActiveSheet()->setTitle('零售生意报告_按月统计');

}


if($report=='salereport02')
{



// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '日期')
            ->setCellValue('B1', '交易量')
            ->setCellValue('C1', '比例');	
					
					
$i=2;
foreach($list as $v){
	
$objPHPExcel->setActiveSheetIndex(0)		
            ->setCellValue('A'.$i, $v['day'])
            ->setCellValue('B'.$i, $v['count'])
            ->setCellValue('C'.$i, $v['persent'].'%');
			
$i++;		
}					

$objPHPExcel->setActiveSheetIndex(0)		
            ->setCellValue('D'.$i, "总".($radiodayForSaleType==1?"交易额":"交易量"))
            ->setCellValue('E'.$i, $dayallcount);
		$i++;				
		
$objPHPExcel->setActiveSheetIndex(0)		
            ->setCellValue('D'.$i, "最高峰".($radiodayForSaleType==1?"交易额":"交易量"))
            ->setCellValue('E'.$i, $daytopcount);
		
$objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12); 
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12); 
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12); 
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12); 


$objPHPExcel->getActiveSheet()->setTitle('零售生意报告_按日统计');
}

 



if($report=='orderstatistics')
{
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '订单号')
            ->setCellValue('B1', '下单时间')
          //  ->setCellValue('C1', '订单总额')
            //->setCellValue('D1', '运费')
           // ->setCellValue('E1', '付款方式')
            ->setCellValue('C1', '商品总价')
          //  ->setCellValue('G1', '收货地址')	
            ->setCellValue('D1', '收货电话')
            ->setCellValue('E1', '分类名称')
            ->setCellValue('F1', '产品名称')
          //  ->setCellValue('L1', '规格')
            ->setCellValue('G1', '商品单价')
            ->setCellValue('H1', '购买数量')
            ->setCellValue('I1', '收货人');	
					
$i=2;
$index=0;
$countmoney1=0;
$countmoney2=0;
$countmoney3=0;
foreach($list as $item){
	$countmoney1=$countmoney1+$item['price'];
	$priceother='';
	$index++;
	if(!empty($item['dispatchprice'])&&$item['dispatchprice']>0)
	{
			$priceother=$item['dispatchprice'];
	}else
	{
		$priceother="0";
		}
	//$paytype='';
	//if($item['paytype'] == 1)
	//{
		//$paytype='余额支付';
	//}
	//if($item['paytype'] == 2)
	//{
		//$paytype='在线支付';
	//}
	//if($item['paytype'] == 3)
	//{
		//$paytype='货到付款';
	//}
	$countmoney2=$countmoney2+$priceother;  
	$itemdatas=array();
	$itemdline=0;
	$cs = 0;
	foreach($item['ordergoods'] as $itemgoods){
		$cs++;
		$itemdatas['goodstotal']= round(($itemgoods['total']*$itemgoods['price']),2);
	    $objPHPExcel->setActiveSheetIndex(0)		
            ->setCellValue('A'.$i, $item['ordersn'])
            ->setCellValue('B'.$i, date('Y-m-d  H:i:s',$item['createtime']))
            ->setCellValue('I'.$i, $item['tdrealname'])
            ->setCellValue('D'.$i, $item['tdmobile'])
             ->setCellValue('E'.$i, $itemgoods['categoryname'])
            ->setCellValue('F'.$i, $itemgoods['title'])
            ->setCellValue('G'.$i, $itemgoods['price'])
            ->setCellValue('H'.$i, $itemgoods['total'])
            ->setCellValue('C'.$i,$itemdatas['goodstotal']);
		     $i++;	
	}
	//->setCellValue('C'.$i, $item['price'])		
				
		//		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);  
//$objPHPExcel->getActiveSheet()->getStyle( 'J'.$i.':O'.$i)->getAlignment()->setWrapText(true);  
			
	$objPHPExcel->setActiveSheetIndex(0)		
            ->setCellValue('A'.$i, "共计".$cs."件产品")
            ->setCellValue('B'.$i, "订单总额：")
            ->setCellValue('C'.$i, "".$item['price']);
/*
$objBorderA5 = $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getBorders();
$objBorderA5->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objBorderA5->getTop()->getColor()->setARGB('FFFF0000'); 
$objBorderA5->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objBorderA5->getBottom()->getColor()->setARGB('FFFF0000');
 $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
 */
 	$i++;	
}					
 	
$objPHPExcel->setActiveSheetIndex(0)		
->setCellValue('A'.$i, "共计".$index."单")
            ->setCellValue('B'.$i, "订单总额：")
            ->setCellValue('C'.$i, "".$countmoney1)
                 ->setCellValue('D'.$i, "运费：".$countmoney2)
             ->setCellValue('E'.$i, "商品总价：")
            ->setCellValue('F'.$i, "".$countmoney3);

//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':P'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
 


		
$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15); 
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10); 
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15); 
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15); 
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(50); 
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(18); 
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15); 
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15); 
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(13); 
$objPHPExcel->getActiveSheet()->setTitle('订单统计');
}



if($report=='saledetails')
{
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '订单号')
            ->setCellValue('B1', '商品名称')
            ->setCellValue('C1', '供应商')
            ->setCellValue('D1', '数量')
            ->setCellValue('E1', '价格')	
            ->setCellValue('F1', '成交时间');	
					
					
$i=2;
$index=0;
$countmoney=0;
foreach($list as $item){
	
$objPHPExcel->setActiveSheetIndex(0)		
            ->setCellValue('A'.$i, $item['ordersn'])
            ->setCellValue('B'.$i, $item['titles'])
	        ->setCellValue('C'.$i, $item['Suppliers'])
            ->setCellValue('D'.$i, $item['total'])
            ->setCellValue('E'.$i, $item['price'])
            ->setCellValue('F'.$i,date('Y-m-d  H:i:s',$item['createtime']));
			$i++;	

			
			
}					


		
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12); 
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20); 
$objPHPExcel->getActiveSheet()->setTitle('商品销售明细');
}


if($report=='productsaleranking')
{
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '排行')
            ->setCellValue('B1', '商品名称')
	        ->setCellValue('C1', '供应商')
            ->setCellValue('D1', '销售量')
            ->setCellValue('E1', '销售额');	
					
					
$i=2;
$index=1;
$countmoney=0;
foreach($list as $item){
	
$objPHPExcel->setActiveSheetIndex(0)		
            ->setCellValue('A'.$i, $index)
            ->setCellValue('B'.$i, $item['title'])
	        ->setCellValue('C'.$i, $item['Supplier'])
            ->setCellValue('D'.$i, $item['salescount']==0?0:$item['salescount'])
            ->setCellValue('E'.$i,($item['salesmoney']==0||empty($item['salesmoney']))?0:$item['salesmoney']);
			$i++;	
$index++;
			
			
}					


		
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12); 
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20); 
$objPHPExcel->getActiveSheet()->setTitle('商品销售排行');
}




if($report=='memberranking')
{
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '排行')
            ->setCellValue('B1', '会员手机号')
            ->setCellValue('C1', '会员昵称')
            ->setCellValue('D1', '订单数')
            ->setCellValue('E1', '消费金额');	
					
					
$i=2;
$index=1;
$countmoney=0;
foreach($list as $item){
	
$objPHPExcel->setActiveSheetIndex(0)		
            ->setCellValue('A'.$i, $index)
           ->setCellValue('B'.$i, $item['mobile'])
            ->setCellValue('C'.$i, $item['realname'])
            ->setCellValue('D'.$i, $item['ordercount'])
            ->setCellValue('E'.$i,($item['ordermoney']==0||empty($item['ordermoney']))?0:$item['ordermoney']);
			$i++;	
$index++;
			
			
}					


		
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12); 
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20); 
$objPHPExcel->getActiveSheet()->setTitle('会员消费排行');
}

// Rename worksheet



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