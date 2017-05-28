<?php
/**
 * app购物车接口
 * @var unknown
 */
$result = array ();

$operation = $_GP ['op'];

switch ($operation) {
	
	case 'update' : // 更新购物车商品数量
		
		$member = get_member_account ( true, true );
		
		if (!empty($member) AND $member != 3) {
			
			$errMsg = updateCartProducts ( $member ['openid'], ( int ) $_GP ['dish_id'], ( int ) $_GP ['total'], trim ( $_GP ['seller_openid'] ) );
			
			if ($errMsg == '') {
				$result ['message'] = "购物车更新成功。";
				$result ['code'] 	= 1;
				
			} else {
				$result ['message'] = $errMsg;
				$result ['code'] 	= 0;
			}
		}elseif ($member == 3) {
			$result['message'] 	= "该账号已在别的设备上登录！";
			$result['code'] 	= 3;
		}else {
			$result ['message'] = "用户还未登陆。";
			$result ['code'] 	= 2;
		}
		
		break;
	
	case 'insert' : // 添加商品到购物车
		
		$member = get_member_account ( true, true );
		
		if (!empty($member) AND $member != 3) {
			
			$errMsg = addProductsToCart ( $member ['openid'], ( int ) $_GP ['dish_id'], ( int ) $_GP ['total'], trim ( $_GP ['seller_openid'] ) );
			
			if ($errMsg == '') {
				$result ['message'] = "加入购物车成功。";
				$result ['code'] 	= 1;
			} else {
				
				$result ['message'] = $errMsg;
				$result ['code'] 	= 0;
			}
		}elseif ($member == 3) {
			$result['message'] 	= "该账号已在别的设备上登录！";
			$result['code'] 	= 3;
		}else {
			$result ['message'] = "用户还未登陆。";
			$result ['code'] 	= 2;
		}
		
		break;
	
	case 'delete' : // 删除购物车商品
		
		$member = get_member_account ( true, true );
		
		if (!empty($member) AND $member != 3) {
			
			if (deleteCartProducts ( $member ['openid'], ( int ) $_GP ['cart_id'] )) {
				$result ['message'] = "删除购物车商品成功。";
				$result ['code'] 	= 1;
			} else {
				$result ['message'] = "删除购物车商品失败。";
				$result ['code'] 	= 0;
			}
		}elseif ($member == 3) {
			$result['message'] 	= "该账号已在别的设备上登录！";
			$result['code'] 	= 3;
		}else {
			$result ['message'] = "用户还未登陆。";
			$result ['code'] 	= 2;
		}
		
		break;
	
	case 'count' : // 计算购物车件数
		
		$member = get_member_account ( true, true );
		
		if (!empty($member) AND $member != 3) {
			$result ['data'] ['cnt']= countCartProducts ( $member ['openid'] );
			$result ['code'] 		= 1;
		} else {
			$result ['data'] ['cnt']= 0;
			$result ['code'] 		= 1;
		}
		
		break;
	
	default : // 显示购物车信息
		
		$member = get_member_account ( true, true );
		
		if (!empty($member) AND $member != 3) {
			$result ['data'] = getCartProducts ( $member ['openid'] );
			$result ['code'] = 1;
		}elseif ($member == 3) {
			$result['message'] 	= "该账号已在别的设备上登录！";
			$result['code'] 	= 3;
		}else {
			$result ['message'] = "用户还未登陆。";
			$result ['code'] 	= 2;
		}
		
		break;
}

echo apiReturn ( $result );
exit ();