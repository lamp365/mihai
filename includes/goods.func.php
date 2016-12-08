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
		$field = ' a.*,b.title as btitle,b.thumb as imgs,b.content as bcontent,b.productprice as price, b.marketprice as market, b.brand ';
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
		 $result[$key]['img'] = empty($val['thumb']) ? $val['imgs'] : $val['thumb'];  //dish表中的图片
		 $result[$key]['thumb'] = $val['imgs']; //good表中的图片
		 $result[$key]['small'] = download_pic($val['imgs'],'400','400');
		 $val['marketprice'] = get_limit_price($val);
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
     //  hots = 1  isrecommand  =2  isnew =3  isjingping =4
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
		default:
			break;
	 }
	 if ($is_guess) {
	 	$where .= ' AND a.type = 0';
	 }
	 
	 $c_goods = get_goods(array(
		 'table'  =>   'shop_dish',
		 'where' =>  $where,
		 'limit'   =>  $num,
		 'order'  =>  'a.displayorder '
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
		 $change_goods = mysqld_selectall("SELECT * FROM ". table ('shop_mess'). " WHERE pcate in ({$category_id}) order by marketprice");
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
function getGoodsProductPlace($pcate){
	$sql = "select name from ".table('dish_list')." where id={$pcate}";
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
     if ( !empty( $data ) && is_array($data) ){
          // 开始寻找是否有记录
		  foreach ( $data as $data_value ){
		     $find = mysqld_select('SELECT * FROM '.table('shop_dish_vip'). ' WHERE dish_id = '.$data_value['dish_id'].' and v2 = '.$data_value['v2'].' limit 1');
			 $v1  = mysqld_select('SELECT * FROM '.table('rolers').' WHERE id = '.$data_value['v2'].' limit 1');
			 $data_value['v1'] = $v1['pid'];
			 if ( $find ){
                 mysqld_update('shop_dish_vip', $data_value, array('vid'=>$find['vid']));
			 }else{
                 mysqld_insert('shop_dish_vip',$data_value);
			 }
		  }
	 }
	 return false;
}