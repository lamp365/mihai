<?php
//购物车的公共函数文件



/**
 * 获取购物车商品信息(购物车不会有团购，秒杀，今日特价商品)
 * @param $openid:用户ID
 * @return array 商品信息数组，总价
 */
/*function getCartProducts($openid)
{
	$sql = "SELECT c.id,d.id as dish_id,d.title,c.total,g.thumb,d.marketprice,d.app_marketprice,d.timeprice,d.type,d.timestart,d.timeend,d.max_buy_quantity,c.seller_openid,s.id as shop_id,s.shopname FROM " . table('shop_cart') . " c ";
	$sql.= " left join " . table('shop_dish') . " d on d.id=c.goodsid ";
	$sql.= " left join " . table('shop_goods') . " g on d.gid=g.id ";
	$sql.= " left join " . table('openshop') . " s on s.openid=c.seller_openid ";
	$sql.= " WHERE c.session_id = '" . $openid . "' ";
	$sql.= " and d.status = 1 ";
	$sql.= " and d.deleted = 0 ";
	//$sql.= " and g.status = 1 ";
	$sql.= " and g.deleted = 0 ";
	$sql.= " and d.total > 0 ";
	$sql.= " order by c.seller_openid ";
	
	$list = mysqld_selectall($sql);
	
	$result = array('product_list'=>$list);
	
	return $result;
}*/

/**
 * 计算购物车件数
 * @param $openid:用户ID
 * @return array 商品信息数组，总价
 */
function countCartProducts($openid)
{
    $openid = $openid ?: get_sessionid();
	$sql = "SELECT count(id) FROM " . table('shop_cart');
	$sql.= " WHERE session_id = '" . $openid . "' ";
	$count = mysqld_selectcolumn($sql);
	return intval($count);
}

/**
 * 更新购物车商品数量
 * 
 * @param $openid 用户ID
 * @param $productId 商品ID（dish id）
 * @param $qty 购买的商品数量
 * @param $seller_openid 卖家openid
 * 
 * @return string 错误信息
 */
/*function updateCartProducts($openid,$productId, $qty,$seller_openid){
	
	$sql = "SELECT d.total,d.max_buy_quantity FROM " . table('shop_dish') . " d ";
	$sql.= " left join " . table('shop_goods') . " g on d.gid=g.id ";
	$sql.= " WHERE d.status = 1 ";
	$sql.= " and d.id = {$productId} ";
	$sql.= " and d.deleted = 0 ";
	//$sql.= " and g.status = 1 ";
	$sql.= " and g.deleted = 0 ";
	$sql.= " and d.total > 0 ";

	$productInfo= mysqld_select($sql);
	$errMsg		= '';

	if($productInfo)
	{
		//库存不足时
		if($productInfo['total']<$qty)
		{
			$errMsg = '库存不足，无法继续更新';
		}
		//超过单笔最大购买数量
		elseif($productInfo['max_buy_quantity']>0 && $productInfo['max_buy_quantity']<$qty)
		{
			$errMsg = '超过单笔最大购买数量，无法继续更新';
		}
		else{
			$data = array('total'=>$qty);
			
			$seller_openid = !empty($seller_openid) ? $seller_openid : 0;
	
			mysqld_update('shop_cart', $data,array('session_id' =>$openid,'goodsid'=>$productId,'seller_openid'=>$seller_openid));
		}
	}
	//商品不存在
	else{
		$errMsg = '商品不存在，无法继续更新';
	}
	
	return $errMsg;
}*/

/**
 * 添加商品到购物车
 * @param $openid 用户ID
 * @param $productId 商品ID（dish id）
 * @param $qty 购买的商品数量
 * @param $seller_openid 卖家openid
 * 
 * @return string 错误信息
 * 
 */
function addProductsToCart($openid,$productId, $qty,$seller_openid){
	
	$sql = "SELECT d.total,d.max_buy_quantity FROM " . table('shop_dish') . " d ";
	$sql.= " left join " . table('shop_goods') . " g on d.gid=g.id ";
	$sql.= " WHERE d.status = 1 ";
	$sql.= " and d.id = {$productId} ";
	$sql.= " and d.deleted = 0 ";
	//$sql.= " and g.status = 1 ";
	$sql.= " and g.deleted = 0 ";
	$sql.= " and d.total > 0 ";

	$productInfo= mysqld_select($sql);
	$errMsg		= '';
	
	if($productInfo)
	{
		$seller_openid = !empty($seller_openid) ? $seller_openid : 0;
		
		$cartInfo = mysqld_select("SELECT total FROM " . table('shop_cart')." WHERE session_id = '{$openid}' and goodsid={$productId} and seller_openid={$seller_openid}");
		
		//购物车中已存在该商品
		if($cartInfo)
		{
			$qty+= $cartInfo['total'];
			
			//库存不足时
			if($productInfo['total']<$qty)
			{
				$errMsg = '库存不足，无法添加';
			}
			//超过单笔最大购买数量
			elseif($productInfo['max_buy_quantity']>0 && $productInfo['max_buy_quantity']<$qty)
			{
				$errMsg = '超过单笔最大购买数量，无法添加';
			}
			else{
				$data = array('total'=>$qty);
			
				mysqld_update('shop_cart', $data,array('session_id' =>$openid,'goodsid'=>$productId,'seller_openid'=>$seller_openid));
			}
		}
		else{
			//库存不足时
			if($productInfo['total']<$qty)
			{
				$errMsg = '库存不足，无法添加';
			}
			//超过单笔最大购买数量
			elseif($productInfo['max_buy_quantity']>0 && $productInfo['max_buy_quantity']<$qty)
			{
				$errMsg = '超过单笔最大购买数量，无法添加';
			}
			else{
				
				$data = array('total'			=>$qty,
								'session_id' 	=>$openid,
								'goodsid'		=>$productId,
								'seller_openid'	=>$seller_openid);
				
				mysqld_insert('shop_cart', $data);
			}
		}
	}
	//商品不存在
	else{
		$errMsg = '商品不存在，无法添加';
	}
	
	return $errMsg;
}

/**
 * 根据用户ID和商品ID，删除购物车中商品,可批量删除
 * 
 * @param $openid 用户ID
 * @param $cartIds 购物车ID组
 * 
 * 
 */
function deleteCartProducts($openid, $cartIds=''){
	
	$sql = "delete FROM ".table('shop_cart')." WHERE session_id = '{$openid}'";
	
	if(!empty($cartIds))
	{
		$sql.= " and id in({$cartIds}) ";
	}
	
	return mysqld_query($sql);
}

function getCartTotal(){
	$member   = get_member_account(false);
	$openid   = $member['openid'] ?: get_sessionid();
	$cartotal = '';
	if(!empty($openid))
		$cartotal = mysqld_selectcolumn("select sum(total) from " . table('shop_cart') . " where session_id='" . $openid . "'");

	return empty($cartotal) ? 0 : $cartotal;
}
?>