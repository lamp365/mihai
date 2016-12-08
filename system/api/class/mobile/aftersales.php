<?php
/**
 * app 申请退款接口
 */
$result = array ();

$member = get_member_account ( true, true );
$openid = $member ['openid'];

$operation = $_GP ['op'];

if (!empty($member) AND $member != 3) {
	switch ($operation) {
		
		case 'insert' : 	// 新增
			
			$objValidator = new Validator();
			
			$type 			= ( int ) $_GP ['type'];
			$reason 		= trim ( $_GP ['reason'] );
			$order_goods_id = ( int ) $_GP ['order_goods_id'];
			$description	= trim ( $_GP ['description'] );
			
			// 退款类型
			if ($type == 0) {
				
				$result ['message'] = '退款类型不能为空';
				$result ['code'] 	= 0;
				
			} elseif ($reason == '') {
				
				$result ['message'] = '退款原因不能为空';
				$result ['code'] 	= 0;
				
			} elseif (empty ( $order_goods_id )) {
				$result ['message'] = '订单商品参数不能为空';
				$result ['code'] 	= 0;
				
			} elseif (!empty ( $_GP ['description'] ) && !$objValidator->lengthValidator($_GP ['description'], '0,200')) {
				
				$result ['message'] = '退款说明最多输入200字';
				$result ['code'] 	= 0;
			} else {
				$sql ="SELECT og.price,og.total FROM " . table ( 'shop_order_goods' ) . " as og left join ".table ( 'shop_order' ) ." as o on o.id=og.orderid ";
				$sql.=" WHERE og.id = {$order_goods_id} ";
				$sql.=" and o.openid = {$openid} ";
				$sql.=" and o.status in(1,2) ";				//订单状态为已付款或已发货
				$sql.=" and og.status = 0 ";				//未申请过退款的订单商品

				// 商品订单
				$order_goods = mysqld_select ( $sql);
				
				// 商品订单不存在时
				if (empty ( $order_goods )) {
					$result ['message'] = '允许申请退款的订单商品不存在';
					$result ['code'] 	= 0;
				} else {
					$data = array (
									'order_goods_id'=> $order_goods_id,
									'reason' 		=> $reason,
									'description' 	=> $description,
									'createtime' 	=> date ( 'Y-m-d H:i:s' ),
									'modifiedtime' 	=> date ( 'Y-m-d H:i:s' ) 
					);
					
					$arrFile = array ();
					
					for($i = 1; $i <= 5; $i ++) {
						// 凭证1上传成功时
						if ($_FILES ['evidence_pic' . $i] ['error'] == 0) {
							
							$upload = file_upload ( $_FILES ['evidence_pic' . $i] );
							
							// 向七牛上传成功时
							if (! is_error ( $upload )) {
								
								$arrFile [] = $upload ['path'];
							}
						}
					}
					
					// 有退货凭证时
					if (! empty ( $arrFile )) {
						$data ['evidence_pic'] = implode ( ";", $arrFile );
					}
					else{
						$data ['evidence_pic'] = '';
					}
					
					//新增退款信息
					if (mysqld_insert ( 'aftersales', $data )) {
						
						$aftersales_id = mysqld_insertid();
						
						$update_data = array ('status'=> 1,
												'type'=> $type);
						
						//更新訂單商品狀態
						mysqld_update('shop_order_goods', $update_data,array('id' =>$order_goods_id));
						
						//退款日志内容
						$arrLogContent = array();
						
						//仅退款
						if($type==3)
						{
							$title = '买家发起了仅退款申请';
						}
						//退款退货
						elseif($type==1)
						{
							$title = '买家发起了退款退货申请';
						}
						
						$arrLogContent['type']			= $type;
						$arrLogContent['reason'] 		= $reason;
						$arrLogContent['description'] 	= $description;
						$arrLogContent['evidence_pic'] 	= $data ['evidence_pic'];
						
						$arrLog = array('aftersales_id'	=> $aftersales_id,
										'order_goods_id'=> $order_goods_id,
										'status'		=> 1,
										'title'			=> $title,
										'content'		=> serialize($arrLogContent),
										'createtime' 	=> date ( 'Y-m-d H:i:s' )
						);
						
						//新增退款日志记录
						mysqld_insert ( 'aftersales_log', $arrLog );
						
						//买家申请退款后，推送相应信息
						pushAfterSalesImMsg($order_goods_id,$data);
						
						$result ['message'] = "退款申请新增成功。";
						$result ['code'] 	= 1;
					} else {
						$result ['message'] = "退款申请新增失败。";
						$result ['code'] 	= 0;
					}
				}
			}
			
			break;
			
		case 'update' : 	// 编辑
					
				$objValidator = new Validator();
					
				$type 			= ( int ) $_GP ['type'];
				$reason 		= trim ( $_GP ['reason'] );
				$order_goods_id = ( int ) $_GP ['order_goods_id'];
				$description	= trim ( $_GP ['description'] );
					
				// 退款类型
				if ($type == 0) {
			
					$result ['message'] = '退款类型不能为空';
					$result ['code'] 	= 0;
			
				} elseif ($reason == '') {
			
					$result ['message'] = '退款原因不能为空';
					$result ['code'] 	= 0;
			
				} elseif (empty ( $order_goods_id )) {
					$result ['message'] = '订单商品参数不能为空';
					$result ['code'] 	= 0;
			
				} elseif (!empty ( $_GP ['description'] ) && !$objValidator->lengthValidator($_GP ['description'], '0,200')) {
			
					$result ['message'] = '退款说明最多输入200字';
					$result ['code'] 	= 0;
				} else {
					$sql ="SELECT og.price,og.total FROM " . table ( 'shop_order_goods' ) . " as og left join ".table ( 'aftersales' ) ." as a on a.order_goods_id=og.id ";
					$sql.=" WHERE og.status = 1 ";				//正在申请退款
					$sql.=" and og.id = {$order_goods_id} ";
			
					// 商品订单
					$order_goods = mysqld_select ( $sql);
			
					if (empty ( $order_goods )) {
						$result ['message'] = '允许编辑的申请退款信息不存在';
						$result ['code'] 	= 0;
					} else {
						$data = array (
										'order_goods_id'=> $order_goods_id,
										'reason' 		=> $reason,
										'description' 	=> $description,
										'modifiedtime' 	=> date ( 'Y-m-d H:i:s' ));
							
						$arrFile = json_decode($_REQUEST ['evidence_url'], true);		//凭证图片;
							
						for($i = 1; $i <= 5; $i ++) {
							// 凭证1上传成功时
							if ($_FILES ['evidence_pic' . $i] ['error'] == 0) {
									
								$upload = file_upload ( $_FILES ['evidence_pic' . $i] );
									
								// 向七牛上传成功时
								if (! is_error ( $upload )) {
			
									$arrFile [] = $upload ['path'];
								}
							}
						}
							
						// 有退货凭证时
						if (! empty ( $arrFile )) {
							$data ['evidence_pic'] = implode ( ";", $arrFile );
						}
						else{
							$data ['evidence_pic'] = '';
						}
							
						//更新退款信息
						mysqld_update ( 'aftersales', $data ,array('order_goods_id' =>$order_goods_id));
			
						//退款日志数组
						$arrLogContent = array();
			
						//仅退款
						if($type==3)
						{
							$arrLogContent['type'] = '仅退款';
						}
						//退款退货
						elseif($type==1)
						{
							$arrLogContent['type'] = '退款退货';
						}
			
			
						$arrLogContent['reason'] 		= $reason;
						$arrLogContent['description'] 	= $description;
						$arrLogContent['evidence_pic'] 	= $data ['evidence_pic'];
			
						$arrLog = array('content'		=> serialize($arrLogContent),
										'createtime' 	=> date ( 'Y-m-d H:i:s' )
						);
			
						//更新退款日志记录
						mysqld_update ( 'aftersales_log', $arrLog,array('order_goods_id' =>$order_goods_id));
			
						//买家申请退款后，推送相应信息
						pushAfterSalesImMsg($order_goods_id,$data);
						
						$result ['message'] = "退款申请编辑成功。";
						$result ['code'] 	= 1;
					}
				}
					
			break;
			
		case 'insert_dialog':		//新增协商留言
			
			$aftersales_id= ( int )$_GP['aftersales_id'];
			
			$sql ="SELECT og.price,og.total FROM " . table ( 'shop_order_goods' ) . " as og , ".table ( 'aftersales' ) ." as a ";
			$sql.=" WHERE a.order_goods_id=og.id ";		
			$sql.=" and og.status = 1 ";						//正在申请退款
			$sql.=" and a.aftersales_id = {$aftersales_id} ";
			
			$order_goods = mysqld_select ( $sql);
				
			if (empty ( $order_goods )) {
				
				$result ['message'] = '允许留言的退款信息不存在';
				$result ['code'] 	= 0;
			}
			else{
				$data = array(
								'aftersales_id' => $aftersales_id,
								'role'		    => 2,
								'content'	    => $_GP['content'],
								'createtime'	=> date('Y-m-d H:i:s')
				);
				mysqld_insert('aftersales_dialog',$data);
				if(mysqld_insertid()){
				
					$result ['message'] = "留言成功。";
					$result ['code'] 	= 1;
				}else{
				
					$result ['message'] = "留言失败。";
					$result ['code'] 	= 0;
				}
			}
			
			break;
			
		case 'sendback' : 	//提交物流信息
			
			$order_goods_id = ( int ) $_GP ['order_goods_id'];	//订单商品ID
			$delivery_corp 	= trim($_GP['delivery_corp']);		//快递公司
			$delivery_no	= trim($_GP['delivery_no']);		//快递单号
			
			$sql ="SELECT price,total FROM " . table ( 'shop_order_goods' );
			$sql.=" WHERE status = 2 ";				//退款申请审核通过
			$sql.=" and id = {$order_goods_id} ";
			$sql.=" and type = 1 ";					//退款退货
			
			// 订单商品
			$order_goods = mysqld_select ( $sql);
			
			if (empty ( $order_goods )) {
				$result ['message'] = '允许提交物流信息的退款申请不存在';
				$result ['code'] 	= 0;
				
			} else {
				
				$sendback_data['delivery_corp'] = $delivery_corp;
				$sendback_data['delivery_no'] 	= $delivery_no;
					
				$data = array ('sendback_data'	=> serialize($sendback_data),
								'modifiedtime' 	=> date ( 'Y-m-d H:i:s' ));
					
				//更新退款信息
				mysqld_update ( 'aftersales', $data ,array('order_goods_id' =>$order_goods_id));
				
				//更新订单商品信息
				mysqld_update ( 'shop_order_goods', array('status'=>3) ,array('id' =>$order_goods_id));
				
				//退款信息
				$aftersales = mysqld_select ( "SELECT aftersales_id FROM " . table ( 'aftersales' )." WHERE order_goods_id = {$order_goods_id} ");
				
				//退款日志数组
				$arrLog = array('aftersales_id'	=> $aftersales['aftersales_id'],
								'order_goods_id'=> $order_goods_id,
								'status'		=> 3,
								'title'			=> '买家已经退货',
								'content'		=> serialize($sendback_data),
								'createtime'	=> date ( 'Y-m-d H:i:s' )
				);
				
				//新增退款日志记录
				mysqld_insert ( 'aftersales_log', $arrLog);
				
				$result ['message'] = "提交物流信息成功。";
				$result ['code'] 	= 1;
			}
			
			break;
			
		case 'remove' : 	//取消退款申请
			
			$order_goods_id = ( int ) $_GP ['order_goods_id'];	//订单商品ID
			
			$sql ="SELECT price,total FROM " . table ( 'shop_order_goods' );
			$sql.=" WHERE status = 1 ";				//退款申请审核通过
			$sql.=" and id = {$order_goods_id} ";
			
			// 订单商品
			$order_goods = mysqld_select ( $sql);
			
			if (empty ( $order_goods )) {
				$result ['message'] = '允许取消申请的退款信息不存在';
				$result ['code'] 	= 0;
			
			} else {
				
				$update_data = array ('status'=> '-2');			//撤销申请状态
				
				//更新訂單商品狀態
				mysqld_update('shop_order_goods', $update_data,array('id' =>$order_goods_id));
				
				//退款信息
				$aftersales = mysqld_select ( "SELECT aftersales_id FROM " . table ( 'aftersales' )." WHERE order_goods_id = {$order_goods_id} ");
				
				//退款日志数组
				$arrLog = array('aftersales_id'	=> $aftersales['aftersales_id'],
								'order_goods_id'=> $order_goods_id,
								'status'		=> '-2',
								'title'			=> '买家已经取消退款申请',
								'createtime'	=> date ( 'Y-m-d H:i:s' )
				);
				
				//新增退款日志记录
				mysqld_insert ( 'aftersales_log', $arrLog);
				
				$result ['message'] = "取消退款申请成功。";
				$result ['code'] 	= 1;
				
			}
			
			break;
			
		case 'log' : 		//获取日志信息
			
			$order_goods_id = (int)$_GP['order_goods_id'];
			
			$aftersalesLog = mysqld_selectall("SELECT aftersales_id,order_goods_id,status,title,content,createtime FROM " . table('aftersales_log') . " WHERE order_goods_id = :order_goods_id ", array(':order_goods_id' => $order_goods_id));
			
			if(!empty($aftersalesLog))
			{
				$arrTemp 		= array();
				$aftersales_id 	= 0;
				
				foreach($aftersalesLog as $value)
				{
					$value['content'] = $value['content'] ? unserialize($value['content']) : array();
					
					$arrTemp[] 		= $value;
					$aftersales_id 	= $value['aftersales_id'];
				}
				
				$dialog = mysqld_selectall ( "SELECT aftersales_id,role,content,createtime FROM " . table ( 'aftersales_dialog' ) . " WHERE aftersales_id = {$aftersales_id} ");
				
				if (empty ( $dialog )) {
				
					$result ['data']['dialog'] = array();
				}
				else{
					$result ['data']['dialog'] = $dialog;
				}
				
				$result['data']['aftersalesLog'] = $arrTemp;
			}
			else{
				$result['data']['aftersalesLog']= $aftersalesLog;
				$result['data']['dialog'] 		= array();
			}
			
			$result['code'] = 1;
			
			break;
			
		default:			//退款详情
			
			$order_goods_id = (int)$_GP['order_goods_id'];
				
			$aftersales = mysqld_select("SELECT a.aftersales_id,a.order_goods_id,a.reason,a.description,a.evidence_pic,a.admin_explanation,a.sendback_data,og.type FROM " . table('aftersales') . " a, ".table('shop_order_goods') ." og WHERE og.id=a.order_goods_id and og.id = :order_goods_id ", array(':order_goods_id' => $order_goods_id));
				
			//退貨信息不存在时
			if(empty($aftersales))
			{
				$result['data']['aftersales'] 	= array();
				$result['code'] 				= 1;
			}
			else{
				$aftersales['evidence_pic'] 	= empty($aftersales['evidence_pic']) ? array() : explode(";", $aftersales['evidence_pic']);
				$aftersales['sendback_data'] 	= empty($aftersales['sendback_data']) ? array() : unserialize($aftersales['sendback_data']);
				
				$result['data']['aftersales'] 	= $aftersales;
				$result['code'] 				= 1;
			}
				
			break;
	}
}elseif ($member == 3) {
	$result['message'] 	= "该账号已在别的设备上登录！";
	$result['code'] 	= 3;
}else {
	$result ['message'] = "用户还未登陆。";
	$result ['code'] = 2;
}

echo apiReturn ( $result );
exit ();


/**
 * 买家申请退款后，推送相应信息
 *
 * @param $order_goods_id: int 订单商品ID
 * @param $aftersalesData: array 退款信息
 *
 */
function pushAfterSalesImMsg($order_goods_id,$aftersalesData)
{
	// 订单商品
	$order_goods = mysqld_select ( "SELECT seller_openid,orderid,goodsid FROM " . table ( 'shop_order_goods' )." WHERE id = {$order_goods_id} ");
	
	$objOpenIm = new OpenIm();
	
	if(!empty($order_goods['seller_openid']) && $objOpenIm->isImUser($order_goods['seller_openid']))
	{
		//商品详情
		$dishInfo = mysqld_select( "SELECT title FROM " . table ( 'shop_dish' )." WHERE id = {$order_goods['goodsid']} ");

		//订单详情
		$orderInfo = mysqld_select( "SELECT ordersn,address_realname,address_mobile FROM " . table ( 'shop_order' )." WHERE id = {$order_goods['orderid']} ");
		
		$immsg['from_user']	= IM_ORDER_FROM_USER;
		$immsg['to_users']	= $order_goods['seller_openid'];
		$immsg['context']	= "老板 买家申请退款了
退款商品
{$dishInfo['title']}
订单编号:{$orderInfo['ordersn']}
收货人:{$orderInfo['address_realname']}
联系方式:{$orderInfo['address_mobile']}
退款原因:".$aftersalesData['reason'];
		
		$objOpenIm->imMessagePush($immsg);
	}
}