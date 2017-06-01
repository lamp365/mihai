<?php
/*
产品数据标签参数说明:
img: 广告图
thumb: 商品主图
productprice: 参考价格
marketprice:销售价格
title : 商品标题
content: 商品详情
其余的根据数据库字段来显示
*/
// 得到部分产品
function get_goods($array=array()){
	if ( empty($array['table']) ){
         return array();
	}else{
         $tables = $array['table'];
	}
	
	if (isset($array['field'])){
		$field = $array['field'];
	}
	else{
		$field = ' a.*,b.title as btitle,b.thumb as imgs,b.goodssn,b.content as bcontent,b.description as goodesc, b.productprice as price, b.marketprice as market, b.brand ';
	}
	
	$where = $limit = $order = '';
    if (!empty($array['where'])){
        $where = ' where '.$array['where'];
	}
	if (!empty($array['limit'])){
        $limit = ' limit '.$array['limit'];
	}
	if (!empty($array['order'])){
        $order = ' ORDER BY '.$array['order'];
	}
	$result = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS {$field} FROM " . table($tables) . " as a LEFT JOIN ". table('shop_goods') ." as b on a.gid=b.id".$where.$order.$limit);
	foreach ( $result as $key=>$val){
		 if (!empty($val['brand'])){
		     $brand = mysqld_select("SELECT a.brand, b.* FROM ".table('shop_brand')." as a LEFT JOIN ".table('shop_country')." as b on a.country_id = b.id WHERE a.id=".$val['brand']);
		 }
		 $result[$key]['brands'] = $brand;
		 $result[$key]['img'] = empty($val['thumb']) ? $val['imgs'] : $val['thumb'];  //dish表中的图片
		 $result[$key]['thumb'] = $val['imgs']; //good表中的图片
		 $result[$key]['small'] = download_pic($val['imgs'],'400','400');
		 $val['marketprice'] = get_limit_price($val);
		 $result[$key]['desc'] = !empty($val['description'])?$val['description']:$val['goodesc'];
		 $result[$key]['productprice'] = ($val['productprice']==0.00)?$val['price']:$val['productprice'];
		 $result[$key]['marketprice'] = ($val['marketprice']==0.00)?$val['market']:$val['marketprice'];
		 $result[$key]['title'] = !empty($val['title'])?$val['title']:$val['btitle'];
		if(isset($val['content']) && isset($val['bcontent']))
		{
		 	$result[$key]['content'] = !empty($val['content'])?$val['content']:$val['bcontent'];
		}
	}
	
	return $result;
}
// 得到一个产品
function get_good($array=array()){
    if ( empty($array['table']) ){
         return array();
	}else{
         $tables = $array['table'];
	}
	
	if (isset($array['field'])){
		$field = $array['field'];
	}
	else{
		$field = ' a.*,b.title as btitle,b.thumb as imgs,a.timeprice,a.commision,b.content as bcontent,b.productprice as price, b.marketprice as market, b.description as desc2, b.brand ';
	}
	
	$where = $limit = $order = '';
    if (!empty($array['where'])){
        $where = ' where '.$array['where'];
	}
	if (!empty($array['limit'])){
        $limit = ' limit '.$array['limit'];
	}
	if (!empty($array['order'])){
        $order = ' ORDER BY '.$array['order'];
	}
	$result = mysqld_select("SELECT {$field} FROM " . table($tables) . " as a LEFT JOIN ". table('shop_goods') ." as b on a.gid=b.id".$where.$order.$limit);
	if($result)
	{
		$result['img'] = $result['thumb'];
		$result['thumb'] = $result['imgs'];
		$result['small'] = download_pic($result['imgs'],'400','400');
		$result['marketprice'] = get_limit_price($result);
		$result['description'] = empty($result['description'])?$result['desc2']:$result['description'];
		$result['productprice'] = ($result['productprice']==0.00)?$result['price']:$result['productprice'];
		$result['marketprice'] = ($result['marketprice']==0.00)?$result['market']:$result['marketprice'];
		$result['title'] = !empty($result['title'])?$result['title']:$result['btitle'];
		if(isset($result['content']) && isset($result['bcontent']))
		{
			$result['content'] = $result['content'].$result['bcontent'];
		}
	}
	else{
		return false;
	}
	return $result;
}

// 监察库存
function check_stock($id,$stock){
    $count = mysqld_select("select sum(count) as nums from ".table('addon7_request')." where award_id = ".$id);
	$sellers = !empty($count['nums'])?$count['nums']:0;
	return $stock - $sellers;
}

// 根据产品库，取得分类
// 数据结构  array( p1key=>array('title','img','p2'=>array('title','img'
function get_categorys($table="shop_goods", $zd = "pcate,ccate,ccate2", $groupby= "ccate2",  $where='', $having=''){
   if ( !empty($where) ){
      $where = ' WHERE '.$where.' ';
   }
    if ( !empty($having) ){
      $having = ' HAVING '.$having.' ';
   }
   $zds = explode(',',$zd);
   $p1 = $zds[0];
   $p2 = $zds[1];
   $p3 = $zds[3];
   $c_list = array();

   $c = mysqld_selectall("SELECT ".$zd." FROM ".table($table).$where." GROUP BY ".$groupby.$having);
   foreach ( $c as $value ){
	  if ( !empty($value[$p1]) ){
        if (empty($c_list[$value[$p1]])){
			$c_info = get_category($value[$p1]);
            $c_list[$value[$p1]] = array(
                'title' => $c_info['name'],
				'img' => $c_info['thumb']
			);
			$c_info = get_category($value[$p2]);
			$c_list[$value[$p1]]['p2'][$value[$p2]] = array(
                'title' => $c_info['name'],
				'img' => $c_info['thumb']
			);
			$c_info = get_category($value[$p3]);
			$c_list[$value[$p1]]['p2'][$value[$p2]]['p3'][$value[$p3]] = array(
                'title' => $c_info['name'],
				'img' => $c_info['thumb']
			);
		}else{
           if ( empty($c_list[$value[$p1]]['p2'][$value[$p2]]) ){
                $c_info = get_category($value[$p2]);
				$c_list[$value[$p1]]['p2'][$value[$p2]] = array(
					'title' => $c_info['name'],
					'img' => $c_info['thumb']
				);
				$c_info = get_category($value[$p3]);
				$c_list[$value[$p1]]['p2'][$value[$p2]]['p3'][$value[$p3]] = array(
					'title' => $c_info['name'],
					'img' => $c_info['thumb']
				);
		   }else{
               if ( empty($c_list[$value[$p1]]['p2'][$value[$p2]]['p3'][$value[$p3]])){
                   $c_info = get_category($value[$p3]);
				   $c_list[$value[$p1]]['p2'][$value[$p2]]['p3'][$value[$p3]] = array(
					'title' => $c_info['name'],
					'img' => $c_info['thumb']
				   );
			   }
		   }
		}}
   }
   return $c_list;
}
// 获得首页相关产品
function index_c_goods($categoryid,$ts=1,$num=5,$p=1){
	switch($ts){
       case 1:
		   $tip = 'hots';
		   break;
	   case 2:
		   $tip = 'recommand';
		   break;
	   case 4:
		   $tip = 'best';
		   break;
	   default:
		   $tip = 'new';
		   break;
	}
    if (!empty($categoryid)){
		if (is_array($categoryid)){
             foreach($categoryid as $key=>$value){
                $goods = cs_goods($value['id'],$p,$ts,$num);
				$categoryid[$key][$tip] = $goods;
			 }
		}else{


		}
        return $categoryid;
	}
	
}
// 根据属性返回产品数据
function cs_goods($categoryid,$p=0, $tip=0, $num=0, $is_guess=false){
     //  hots = 1  isrecommand  =2  isnew =3  isjingping =4  5表示团购商品
	 $where = ' a.status = 1 ';
	 if ( !empty($categoryid) ){
		 switch ($p){
			case 1:
				$where .=" and a.p1 =".$categoryid;
				break;
			case 2:
				$where .=" and a.p2 =".$categoryid;
				break;
			default:
				$where .=" and a.p3 =".$categoryid;
				break;
		 }
	 }
     switch ($tip){
        case 1:
			$where .= ' AND a.ishot = 1';
			break;
		case 2:
			$where .= ' AND a.isrecommand = 1';
			break;
		case 3:
			$where .= ' AND a.isnew = 1';
			break;
		case 4:
			$where .= ' AND a.isjingping = 1';
			break;
		 case 5:
			 $where .= ' AND a.type = 1';
			 break;
		default:
			break;
	 }
	 if ($is_guess) {
	 	$where .= ' AND a.type = 0 AND a.status = 1';
	 }
	 
	 $c_goods = get_goods(array(
		 'table'  =>   'shop_dish',
		 'where' =>  $where,
		 'limit'   =>  $num,
		 'order'  =>  'a.displayorder desc'
     ));
	 return $c_goods;
}
function get_category($id){
    $result = mysqld_select("SELECT * FROM ".table('shop_category')."  WHERE id = ".$id);
	return $result;
}

/* 通过产品来计算运费
*  运费计算规则
*  1. 产品组中只要有免运费的，则直接跳过运费计算
*  2. 根据产品组中的仓库进行分段，计算仓库数量及运费金额
*  3. 返回运费金额 
*/
function shipcost($goods){
    // 开始先审查一遍，是否有免运费的产品
	$shiptax = array(); // 初始化运费金额
	$shiparr = array();
	$issendfree = 0; // 设置为需要运费
    foreach ( $goods as $value ){
        if ( !empty($value['issendfree']) ){
             $issendfree = 1;
		}
		// 将不同的仓库数据存入数组
        if ( !in_array($value['pcate'], $shiparr ) ){
             $shiparr[] = $value['pcate'];
		}
	}
	// 设置清关功能
	$ifcustoms = 0;
	// 开始计算运费
	foreach ( $shiparr as $value ){
        $shiprice = mysqld_select("select isrecommand,displayorder from ".table('dish_list')." where id =:shipprice ",array(':shipprice'=>$value));
		if ($shiprice['isrecommand'] == 1){
             $ifcustoms = 1;
		}
		if ( $issendfree != 1 ){
		     $shiptax['price'] += $shiprice['displayorder'];
		}else{
             $shiptax['price'] = 0;
		}
	}
	$shiptax['ifcustoms'] = $ifcustoms;
	return $shiptax;
}
// 获取用户浏览记录
function get_hstory($goodsid = 0){
        //添加浏览记录
		$hstory_goods = array();
		$c_hstory = new LtCookie();
		//获取浏览记录
        $hstory = $c_hstory->getCookie('hstory');
		if ( $goodsid != 0 ){
			if ( is_array($hstory) ){
				if (!in_array($goodsid,$hstory)){
					 while (count($hstory) >= 5){
						array_shift($hstory);
					 }
					 $hstory[] = $goodsid;
				}
			}else{
				$hstory[] = $goodsid;
			}
		}
		$c_hstory->delCookie('hstory');
	    $c_hstory->setCookie('hstory', $hstory);
		if ( is_array($hstory) ){
		      $hstory = implode(',', $hstory);
		}
		if ( !empty($hstory) ){
				$hstory_goods = get_goods(array(
				   'table' => 'shop_dish',
				   'where' => 'a.id in ('.$hstory.')')
				);
		}
		return $hstory_goods;
}

function get_limits($num=4){
	 $where = ' a.istime = 1 and a.timeend > '.time().' and a.timestart <= '.time(). ' and a.type = 4 and a.status = 1 and a.isdiscount != 1';
	 $datas = array(
         'table'  =>   'shop_dish',
		 'where' =>  $where,
		 'order' =>  ' a.displayorder desc '
	 );
	 if ( $num > 0 ){
         $datas['limit'] = $num; 
	 }
     $limit_goods = get_goods($datas);
	 return $limit_goods;
}
function get_limit_price($goods=array()){
	   $istime = 0;
       if (($goods['istime'] == 1 && $goods['type'] == 4) or ($goods['istime'] == 1 && $goods['isdiscount'] == 1)) {
			$istime = 1;
            if (time() < $goods['timestart']) {
                $istime = 0;
            }
            if (time() > $goods['timeend'] && !empty($goods['timeend']) ) {
                $istime = 0;
            }
      }
	  if ( $istime == 1 ){
           return $goods['timeprice'];
	  }else{
           return $goods['marketprice'];
	  }
}

// 根据订单金额获取换购产品的信息
function get_change_goods($orderprice = 0){
    // 根据传入进来的订单金额来获取换购信息
	$data = array();
    $category = mysqld_selectall("SELECT * FROM ".table('mess_list')." WHERE name <= ".$orderprice);
	if ( $category ){
         foreach ( $category as $category_value ){
             $category_id[] = $category_value['id'];
		 }
		 $category_id = implode(',', $category_id );
		 // 根据区间来获取换购产品
		 $change_goods = mysqld_selectall("SELECT * FROM ". table ('shop_mess'). " WHERE deleted=0 and status=1 and  pcate in ({$category_id}) order by marketprice");
		if(!empty($change_goods)){
			 foreach($change_goods as $row) {
				 $data[$row['id']] = $row;
			 }
		 }else{
			$data = $change_goods;
		}
		 return $data;
	}else{
         return $data;  //这里要返回空数组，不然外面可能会报错
	}
}
// 存储和删除换购产品信息
// $op 0 添加 1 删除
//$result 1 添加成功 2删除成功 0一个单一个
function set_change_goods($goods, $op = 0,$check=''){
	// 把换购的产品加入到COOKIE，并通过COOKIE进行操作
	$change_goods = new LtCookie();
	// 判断换购里面是否有产品
	$result = 1;
	switch ( $op ){
        case 0:
			$good = $change_goods->getCookie('change_goods');
			if ( empty($good) ){
				 $good = $change_goods->setCookie('change_goods', $goods);
				 $text  = get_change_good($goods,$check);
			}else{
                 $result = 0;
				 $text = '一个订单只能添加一个换购产品';
			}
			break;
		default:
            $change_goods->delCookie('change_goods');
            $text = '删除成功';
			$result = 2;
			break;
	}
	
    $result = json_encode(array(
         "result" => $result,
		 "text" => $text
    ));
	echo $result;
}
// 获得单个换购产品的信息
//$check  是所有换购商品的数组
function get_change_good($id,$check=''){
	if ( empty($id) ){
         return ;
	}
	if ( empty($check) ){
			//说明，订单商品不符合换购条件，没从数据库中获取出换购商品
			set_change_goods($id, 1);
			return;
	}else{
       if ( is_array($check)){
				if(!array_key_exists($id['id'],$check)){
					set_change_goods($id, 1);
			    	return;
		    }
	   }
	}
    $change_goods = mysqld_select("SELECT * FROM ". table ('shop_mess'). " WHERE id =".$id['id']);
	if ($change_goods){
		$shipname     = mysqld_select("select name from ".table('dish_list')." where id =:shipprice ",array(':shipprice'=>$change_goods['pcate']));
		if (is_mobile_request()||$_GET['wap']==1){
			$good = '
					<li class="member-browse-li change_goods">
						<div class="row member-browse-summey">
							<a class="member-browse-summey-info" href="'. mobile_url('detail',array('id'=>$change_goods['gid'],'op'=>'dish')) .'">
								<div class="member-browse-nt">
										<span class="member-browse-name"> <div style="padding-bottom:5px;"><span class="btn btn-danger btn-xs">换购</span>&nbsp;'.$change_goods['title'].'</div></span>
								</div>
							</a>
						</div>
						<div class="member-browser-pro-list">

							<div class="member-browser-pro-a" href="#"><span class="pro-img">
								<img src="'.download_pic($change_goods['thumb'],60,60).'"></span>
								<p class="pro-info">
									<span class="pro-name">'. $change_goods['marketprice'] .' × '. $id['num'] .'</span>
									<span class="pro-price">税费：商家包税</span><br>
									<span class="pro-price">小计：'.round($change_goods['marketprice']*$id['num'],2).'</span>
								</p>
								<p class="ziying">
									<span style="font-size:10px;border:1px solid #E4393C;color:#E4393C;padding:1px 3px;">'.$shipname['name'].'</span><br/>
									<span  class="btn btn-danger btn-xs" href="javascript:void(0)" onclick="addredemption('.$id['id'].',1)" style="color:#fff;margin-top:8px;">删除商品</span>
								</p>
							</div>

						</div>
					</li>';
		}else{
			$good = '
				<li class="member-browse-li change_goods">
				<div class="col col-img"><img src="'.$change_goods['thumb'].'" height="50" /></div>
									<div class="col col-name" style="text-align:left;"><a target="_blank"  class="member-browse-summey-info" href="'.mobile_url('detail',array('id'=>$change_goods['gid'],'op'=>'dish')).'"><span class="label label-danger">换购</span>&nbsp;<span id="member-browse-name" class="member-browse-name">'.$change_goods['title'].'</span></a></div>
									<div class="col col-price"><span class="pro-name">¥ '.$change_goods['marketprice'].'</span></div>
									<div class="col col-num">'.$id['num'].'</div>
									<div class="col col-price">¥ '.$change_goods['marketprice']*$id['num'].'</div>
									<div class="col col-price">商家包税</div>
									<div class="col col-price"><span class="pro-name">¥ '.$change_goods['marketprice']*$id['num'].'</span><a  class="label label-danger" href="javascript:void(0)" onclick="addredemption('.$id['id'].',1)" style="color:#fff;margin-left:5px;">删除</a></div>
				</li>';
		}

        return $good;
	}else{
        return false;
	}
}

/**
 * @param $pcate
 * @return mixed
 * @content返回产地仓库 如自营美国一号
 */
function getGoodsProductPlace($transport_id){
	$sql = "select name from ".table('dish_list')." where id={$transport_id}";
	$info = mysqld_select($sql);
	return $info['name'];
}

function getGoupBuyNum($group_id){
	$total = mysqld_selectcolumn("select count(group_id) from ". table('team_buy_member')." where group_id={$group_id} and order_id<>0");
	return $total;
}

function getGoodsType($shop_type){
	$typeArr = array(-1=>'换购商品',1=>'团购商品','2'=>'秒杀商品','3'=>'今日特价','4'=>'限时促销');
	if($shop_type != 0){
		return $typeArr[$shop_type];
	}
}

//获取产品图
function getGoodsThumb($gid){
	$goods = mysqld_select("select thumb from ".table('shop_goods')." where id={$gid}");
	$thumb = $goods['thumb'];
	return $thumb;
}

/**
 * 获得扩展分类的商品ID
 * @param $cate_id:类目ID
 *
 * @return array 商品ID
 */
function getCategoryExtendDishId($cate_id)
{
	$arrDish = mysqld_selectall("SELECT dishid FROM " . table('shop_category_extend') . " where p1={$cate_id} or p2={$cate_id} or p3={$cate_id}");
	$result  = array();

	if(!empty($arrDish))
	{
		foreach($arrDish as $value)
		{
			$result[] = $value['dishid'];
		}
	}

	return $result;
}
/*
   对扩展价格进行操作
   $data = array(
      'dish_id' => 'dishid',
	  'v2' => 'v2',
	  'vip_price' => 'vip_price'
   );
*/
function setExtendPrice($data = array() ){
	 $v2_now = array();
	 $dish_id  = '';
     if ( !empty( $data ) && is_array($data) ){
          // 开始寻找是否有记录 array_diff
		  foreach ( $data as $data_value ){
		     $find = mysqld_select('SELECT * FROM '.table('shop_dish_vip'). ' WHERE dish_id = '.$data_value['dish_id'].' and v2 = '.$data_value['v2'].' limit 1');
			 $v1  = mysqld_select('SELECT * FROM '.table('rolers').' WHERE id = '.$data_value['v2'].' limit 1');
			 $data_value['v1'] = $v1['pid'];
			 $dish_id = $data_value['dish_id'];
			 if ( $find ){
                 mysqld_update('shop_dish_vip', $data_value, array('vid'=>$find['vid']));
			 }else{
                 mysqld_insert('shop_dish_vip',$data_value);
			 }
			 $v2_now[] = $data_value['v2'];
		  }
		  // 开始处理差集
		  $v2after = mysqld_selectall("SELECT v2 FROM ".table('shop_dish_vip'). " WHERE dish_id = ".$dish_id);
		  foreach ( $v2after as $key=>$value ){
                $v2_after[] = $value['v2'];
		  }
		  $diff = array_diff($v2_after,$v2_now);
		  if ( !empty($diff) ){
                foreach ( $diff as $diff_val){
                     mysqld_delete('shop_dish_vip', array('dish_id'=>$dish_id,'v2'=>$diff_val));
				}
		  }
	 }
	 return false;
}

/**
 * @param $data 数组数据
 * @param $key  数据字段
 * @param $val  对应的值
 * @return mixed
 * 对数组数据进行塞入值，用于对产品字段设定权限后，页面该如金额字段会不显示，导致管理员做修改时，提交表单，获取不到该金额
 * 会修改原来的金额价格为0.故有涉及到权限字段的需要赋值给数组时，进行调用。
 */
function getDataIsNotNull($data,$key,$val){
	if($val !== null){
		if($key == 'commision'){
			//佣金要处以100
			$val = $val/100;
		}else if($key == 'timestart' || $key == 'timeend'){
			//时间特殊处理
			$val = strtotime($val);
		}
		$data[$key] = $val;
	}
	return $data;
}
// 查找批发和代购的购物车数据
function get_purchase_cart($openid, $type = 2 ){
	$purchase = array();
    if ( !empty( $openid ) ){
		$purchase_data = array(":openid"=>$openid, ":goodstype"=>$type);
        $purchase_cart = mysqld_selectall("SELECT * FROM ".table('shop_purchase_cart')." WHERE openid = :openid and goodstype = :goodstype", $purchase_data);
		if ( is_array($purchase_cart)){
            foreach ( $purchase_cart as $value ){
                $purchase[$value['goodsid']] = $value;
			}
		}
		return $purchase;
	}else{
        return $purchase;
	}
}
// 插入批发和代购的购物车数据
function set_purchase_cart($goods, $openid, $type = 2 ){
	if ( is_array($goods) && !empty($openid) ){
         foreach ( $goods as $goods_value ){
               // 进行更新或插入的判断
			   if ( !empty($goods_value['id']) ){
					   $purchase_data = array(":openid"=>$openid, ":goodstype"=>$type, ":goodsid"=>$goods_value['id']);
					   $find_goods = mysqld_select("SELECT * FROM ".table('shop_purchase_cart')." WHERE openid = :openid and goodstype = :goodstype and goodsid = :goodsid limit 1", $purchase_data);
					   $set_purchase_data = array("goodsid"=>$goods_value['id'],"goodstype"=>$type,"openid"=>$openid,"total"=>$goods_value['num'],"marketprice"=>$goods_value['price'], "creatime"=>time());
					   if ( $find_goods ){
							 mysqld_update("shop_purchase_cart", $set_purchase_data, array('id'=>$find_goods['id']));
					   }else{
							 mysqld_insert("shop_purchase_cart",  $set_purchase_data);
					   }
	           }
		 }
	}
}
// 删除批发和代购的购物车数据
function del_purchase_cart($id,$openid,$type=2){
    if ( !empty($id) && !empty($openid) ){
         mysqld_delete('shop_purchase_cart', array('goodsid'=> $id, 'openid'=>$openid, 'goodstype'=>$type ));
	}
}
// 批发或一件代发的
function price_check($goods=array(), $v1, $v2, $type = 2){
    if ( $type == 2 ){
         //开始查找是否有相应权限的特殊价格.批量的价格在外围设置完毕
         $check_price = mysqld_select("SELECT * FROM ".table('shop_dish_vip')." WHERE dish_id = :dish_id and v1 = :v1 and v2 = :v2 ", array(':dish_id'=>$goods['id'], ':v1'=> $v1, ':v2'=>$v2 ));
		 if ( $check_price ){
             $goods['price'] = $check_price['vip_price'];
		 }else{
             $check_price = mysqld_select("SELECT * FROM ".table('shop_dish_vip')." WHERE dish_id = :dish_id and v1 = :v1 and v2 = :v2 ", array(':dish_id'=>$goods['id'], ':v1'=> 0, ':v2'=>$v1));
			 $goods['price'] = $check_price['vip_price'];
		 }
		 return $goods;
	}else{
		  $check_price = mysqld_select("SELECT * FROM ".table('shop_dish_vip')." WHERE v1 = ".$v1." and v2 = ".$v2." and dish_id = ".$goods['id']);
		  if ( $check_price ){
			  $goods['price'] = $check_price['vip_price'];
		  }else{
		  // 开始判断有没批量价格的设定
			   $check_price = mysqld_select("SELECT * FROM ".table('rolers')." WHERE discount > 0 and type = 3  and ( id= ".$v1." or id = ".$v2.") order by pid desc limit 1 " );
			   if ( $check_price ){
				   $goods['price'] = $check_price['discount'] * $goods['price'];
			   }
		  }
		  return $goods;
	}
}
function model_good($id,$v1,$v2,$type=2){
	 if ( $type == 2 ){
			 $find_good = mysqld_select("SELECT a.*,b.* FROM ".table('shop_dish_vip')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.dish_id = b.id WHERE b.id = ".$id." and a.v1 = 0 and a.v2 = ".$v1);
			 if ( $find_good ){
				  $model_good    = array('id'=>$id,'total'=>$find_good['total'],'price'=>$find_good['vip_price']);
				  $model_good    = price_check($model_good, $v1, $v2, $type);
				  return $model_good;
			 }else{
				  return false;
			 }
	 }else{
             $find_good = mysqld_select("SELECT * FROM ".table('shop_dish')." WHERE deleted = 0 and status = 1 and id = ".$id);
			 if ( $find_good ){
				  $model_good    = array('id'=>$id,'total'=>$find_good['total'],'price'=>$find_good['marketprice']);
				  $model_good    = price_check($model_good, $v1, $v2, $type);
				  return $model_good;
			 }else{
				  return false;
			 }
	 }
}

// 搜索分词引擎
function get_word($text=''){
	$sh = scws_new();
	$sh->set_charset('utf8');
	$sh->set_ignore(true); 
	$sh->set_multi(true);
	$sh->set_duality(true); 
	$sh->send_text($text);
	$words = $sh->get_tops(5);
	$sh->close();
	$word = array();
	foreach($words as $word_value){
         $word[] = $word_value['word'];
	}
	return $word;
}

function getDishId($id){
	$dish = mysqld_select("select id from ".table('shop_dish')." where gid={$id}");
	return $dish['id'];
}

function getGoodsCategory($pcate){
	$catecory = mysqld_select("select name from ".table('shop_category')." where id={$pcate}");
	return $catecory['name'];
}

/**
 * 根据 brand_id获取来自哪个国家
 * @param $brand_id
 * @return array|bool|mixed
 */
function getGoodsFromCountry($brand_id){
	if(empty($brand_id)){
		return array();
	}
	$brand = mysqld_select("SELECT a.brand,b.name,b.icon FROM ".table("shop_brand")." as a LEFT JOIN ".table("shop_country"). " as b on a.country_id = b.id WHERE a.id = ".$brand_id);
	return $brand;
}