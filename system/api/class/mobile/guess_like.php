<?php
	/**
	 * app 猜你喜欢接口
	 * @author WZW
	 * 
	 */

	$result = array();
	
	$op = $_GP['op'];

	if ($op == 'detail') {
		if (empty($_GP['id'])) {
			$result['message'] 	= "商品ID不存在。";
			$result['code'] 	= 0;
		}else{
			$id = $_GP['id'];
			$good = get_good(array(
	                "table"=>"shop_dish",
					"where"=>"a.id = ".$id,
				));
			$guess = cs_goods($good['p1'], 1, 2, 20, true);
			$total = mysqld_select("SELECT FOUND_ROWS() as total;");
			
	      	$guess_ary = array();
	      	foreach ($guess as $g_k => $g_v) {
	      		$ary = array();
	      		$ary['id'] = $g_v['id'];
	      		$ary['title'] = $g_v['title'];
	      		// $ary['img'] = imgThumb($g_v['thumb'], 400, 400);
	      		$ary['thumb'] = $g_v['thumb'];
	      		$ary['productprice'] = $g_v['productprice'];
	      		$ary['marketprice'] = $g_v['marketprice'];
	      		$ary['app_marketprice'] = $g_v['app_marketprice'];
	      		$ary['timeprice'] = $g_v['timeprice'];
	      		// 品牌
				$brand = mysqld_select("SELECT * FROM ".table('shop_brand')." WHERE id=".$g_v['brand']);
				$ary['brand'] = $brand['brand'];
				// 国家
				$country = mysqld_select("SELECT * FROM ".table('shop_country')." WHERE id=".$brand['country_id']);
				$ary['country'] = $country['name'];
				$ary['country_icon'] = download_pic($country['icon']);
				// 分类名
		    	$category = mysqld_select("SELECT name FROM ".table('shop_category')." WHERE id=".$g_v['p1']);
		    	$ary['category'] = $category['name'];
	      		$guess_ary[] = $ary;
	      	}

	      	$result['data']['guess'] = $guess_ary;
	      	$result['data']['total'] = $total['total'];
	      	$result['code'] = 1;
		}
	}elseif ($op == 'shopping_cart') {
		// 购物车的猜你喜欢返回最新上架20个商品
		$dish = mysqld_selectall("SELECT id FROM ".table('shop_dish')." ORDER BY createtime DESC LIMIT 20");
		$guess_ary = array();
		foreach ($dish as $dv) {
			$good = get_good(array(
	                "table"=>"shop_dish",
					"where"=>"a.id = ".$dv['id'],
				));
			$ary = array();
			$ary['id'] = $good['id'];
      		$ary['title'] = $good['title'];
      		$ary['thumb'] = $good['thumb'];
      		$ary['productprice'] = $good['productprice'];
      		$ary['marketprice'] = $good['marketprice'];
      		$ary['app_marketprice'] = $good['app_marketprice'];
      		$ary['timeprice'] = $good['timeprice'];
      		// 品牌
			$brand = mysqld_select("SELECT * FROM ".table('shop_brand')." WHERE id=".$good['brand']);
			$ary['brand'] = $brand['brand'];
			// 国家
			$country = mysqld_select("SELECT * FROM ".table('shop_country')." WHERE id=".$brand['country_id']);
			$ary['country'] = $country['name'];
			$ary['country_icon'] = download_pic($country['icon']);
			// 分类名
	    	$category = mysqld_select("SELECT name FROM ".table('shop_category')." WHERE id=".$good['p1']);
	    	$ary['category'] = $category['name'];
      		$guess_ary[] = $ary;
		}
		$result['data']['guess'] = $guess_ary;
      	$result['code'] = 1;
	}else{
		if (empty($_GP['p_id'])) {
			$result['message'] 	= "ID不存在。";
			$result['code'] 	= 0;
		}else{
			$p_id = $_GP['p_id'];
			$p_type = $_GP['p_type'] ? $_GP['p_type'] : 1;
			$num = $_GP['num'] ? $_GP['num'] : 20;

			$guess = cs_goods($p_id, $p_type, 2, $num, true);
			$total = mysqld_select("SELECT FOUND_ROWS() as total;");

	      	$guess_ary = array();
	      	foreach ($guess as $g_k => $g_v) {
	      		$ary = array();
	      		$ary['id'] = $g_v['id'];
	      		$ary['title'] = $g_v['title'];
	      		// $ary['img'] = imgThumb($g_v['thumb'], 400, 400);
	      		$ary['thumb'] = $g_v['thumb'];
	      		$ary['productprice'] = $g_v['productprice'];
	      		$ary['marketprice'] = $g_v['marketprice'];
	      		$ary['app_marketprice'] = $g_v['app_marketprice'];
	      		$ary['timeprice'] = $g_v['timeprice'];
	      		// 品牌
				$brand = mysqld_select("SELECT * FROM ".table('shop_brand')." WHERE id=".$g_v['brand']);
				$ary['brand'] = $brand['brand'];
				// 国家
				$country = mysqld_select("SELECT * FROM ".table('shop_country')." WHERE id=".$brand['country_id']);
				$ary['country'] = $country['name'];
				$ary['country_icon'] = download_pic($country['icon']);
				// 分类名
		    	$category = mysqld_select("SELECT name FROM ".table('shop_category')." WHERE id=".$g_v['p1']);
		    	$ary['category'] = $category['name'];
	      		$guess_ary[] = $ary;
	      	}

	      	$result['data']['guess'] = $guess_ary;
	      	$result['data']['total'] = $total['total'];
	      	$result['code'] = 1;
		}
	}

	// dump($result);
	echo apiReturn($result);
	exit;