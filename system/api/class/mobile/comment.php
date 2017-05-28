<?php
	/**
	 * app 评论
	 * @author WZW
	 * 
	 */

	$result = array();
	
	$op = $_GP['op'];

	if ($op == 'add') {
		$order_id = intval($_GP['order_id']);
		$comment = $_GP['comment'];
		$rate = $_GP['rate'];
		// $express = $_GP['express'];
		// $serve = $_GP['serve'];
		$goods_id = intval($_GP['goods_id']);
		// $img1 = $_GP['img1'];
		// $img2 = $_GP['img2'];
		// $img3 = $_GP['img3'];
		// $img4 = $_GP['img4'];
		// $img5 = $_GP['img5'];

		if (empty($order_id) or empty($rate) or empty($goods_id)) {
			$result['message'] 	= "订单ID、评分、商品ID不能为空!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$where = "id = '" . $order_id . "'";
		$order = get_order(
	    	array('where' => $where)
	    	);

		if (empty($order)) {
			$result['message'] 	= "查询订单信息失败!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$order_good = mysqld_select("SELECT iscomment FROM ".table('shop_order_goods')." WHERE orderid=".$order_id." AND goodsid=".$goods_id);
		if ($order_good['iscomment'] == '1') {
			$result['message'] 	= "该订单商品已评论过";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$gid = mysqld_select("SELECT gid FROM ".table('shop_dish')." WHERE id=".$goods_id);
		$gid = $gid['gid'];
		// 评论信息
		$d = array('createtime' => time(), 'orderid' => $order_id, 'ordersn' => $order['ordersn'], 'openid' => $order['openid'], 'comment' => $comment, 'rate' => $rate, 'goodsid' => $gid);
		$d['system'] = getSystemType();
		mysqld_insert('shop_goods_comment', $d);
		$comment_id = mysqld_insertid();
		// 设置is_comment
		mysqld_query("UPDATE ".table('shop_order_goods')." SET iscomment=1 WHERE orderid=".$order_id." AND goodsid=".$goods_id);

		// 评论图片上传
		for ($i=1; $i < 6; $i++) { 
			upload_imgs($i, $comment_id);
		}

		$result['message'] = "评论成功!";
		$result['code']    = 1;
		echo apiReturn($result);
		exit;
	}elseif ($op == 'get') {
		$good_id = $_GP['good_id'];
		if (empty($good_id)) {
			$result['message'] 	= "商品ID为空!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}
		$pindex = max(1, intval($_GP['page']));
		$psize = intval($_GP['limit'] ? $_GP['limit'] : 20);

    	$gid = mysqld_select("SELECT gid FROM ".table('shop_dish')." WHERE id=".$good_id);
		$gid = $gid['gid'];
		$comments = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.id as comment_id, a.orderid, a.ordersn, a.createtime, a.comment, a.reply, a.replytime, a.praise_num, b.openid, b.nickname, b.avatar, b.mobile, a.username, a.face FROM " . table('shop_goods_comment') . " as a LEFT JOIN ". table('member') ." as b on a.openid=b.openid WHERE a.goodsid=".$gid." ORDER BY a.istop DESC,a.praise_num DESC,a.createtime DESC LIMIT ".($pindex - 1) * $psize . ',' . $psize);
		// 总记录数
		$total = mysqld_select("SELECT FOUND_ROWS() as total;");
		// if (empty($comments)) {
		// 	$result['message'] 	= "评论查询为空!";
		// 	$result['code'] 	= 0;
		// 	echo apiReturn($result);
		// 	exit;
		// }
		if (!empty($comments)) {
			foreach ($comments as $c_k => &$c_v) {
				if (empty($c_v['nickname'])) {
					$c_v['nickname'] = $c_v['mobile'];
				}
				if (!empty($c_v['username'])) {
					$c_v['nickname'] = $c_v['username'];
				}
				$c_v['nickname'] = substr_cut($c_v['nickname']);
				if (!empty($c_v['face'])) {
					$c_v['avatar'] = $c_v['face'];
				}
				$c_img = mysqld_selectall("SELECT img FROM ".table('shop_comment_img')." WHERE comment_id=".$c_v['comment_id']." ORDER BY id ASC LIMIT 5");
				foreach ($c_img as $cmv) {
					$comments[$c_k]['img'][] = $cmv['img'];
				}
				// $comments[$c_k]['img'] = $c_img;
			}
		}
		unset($c_v);
		
		$result['data']['comments'] = $comments;
		$result['data']['total'] = $total['total'];
		$result['code'] = 1;
		// dump($result);
		echo apiReturn($result);
		exit;
	}

	$result['message'] = "操作类型错误!";
	$result['code']    = 0;
	echo apiReturn($result);
	exit;

function upload_imgs($num, $commentid) {
	if (!empty($_FILES['img'.$num])) {
		if ($_FILES['img'.$num]['error']==0) {
			$upload = file_upload($_FILES['img'.$num]);
			//出错时
			if (is_error($upload)) {
				$result['message'] 	= $upload['message'];
				$result['code'] 	= 0;
				echo apiReturn($result);
				exit;
			}else{
				$m = array('comment_id' => $commentid, 'img' => $upload['path']);
				mysqld_insert('shop_comment_img', $m);
			}
		}else{
			$result['message'] 	= "图片".$num."上传失败。";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}
	}
}