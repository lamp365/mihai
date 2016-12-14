<?php
$title = '批发采购';
$is_login = is_vip_account();
// 获取用户的参数
$member = get_vip_member_account(true, true);
$user_a = get_user_identity($member['mobile']);
$openid =$member['openid'] ;
// [parent_roler_id] => 2 [son_roler_id] => 3
// 验证用户是否是批发商
$rolers = mysqld_select("SELECT * FROM ".table('rolers')." WHERE id = ".$member['parent_roler_id']." and (type=2 or type=3) ");
if ( empty($member['parent_roler_id']) || !$rolers || empty($member['son_roler_id']) ){
     header("location:".mobile_url('vip_logout'));
}
$addresslist      =   mysqld_selectall("SELECT * FROM " . table('shop_address') . " WHERE  deleted = 0 and openid = :openid order by isdefault desc ", array(':openid' => $openid));
$page = max(1, $_GP['page']);
$psize = max(20,$_GP['psize']);
$limit =  " limit ".($page-1)*$psize.','.$psize;
$condition = '';
if (!empty($_GP['keyword'])){
     switch ( $_GP['key_type'] ){
		 case 'title':
			 $condition = " and b.title like '%".$_GP['keyword']."%' ";
			 break;
		 default:
			 $condition = " and c.goodssn = '".$_GP['keyword']."' ";
			 break;
	 }
}
// 根据用户的角色获取产品数据
if ( $user_a['type'] == 2 ){
     $dish_list = mysqld_selectall("SELECT a.*,b.*,c.goodssn FROM ".table('shop_dish_vip')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.dish_id = b.id LEFT JOIN ".table('shop_goods')." as c on b.gid = c.id WHERE b.deleted = 0 and b.status = 1 $condition and a.v1 = ".$member['parent_roler_id']." and a.v2 =  0 ".$limit);
	 $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_dish_vip') . " as a left join ".table('shop_dish')." as b on a.dish_id = b.id WHERE a.v1 = ".$member['parent_roler_id']." and a.v2 = 0  $condition and b.deleted=0  AND b.status = '1' ");
     $currency = 2;
}else{
     $dish_list = mysqld_selectall("SELECT b.*,c.goodssn FROM ".table('shop_dish')." AS b  LEFT JOIN ".table('shop_goods')." as c on b.gid = c.id WHERE b.deleted = 0 and b.status = 1 $condition ".$limit);
	 $total = mysqld_selectcolumn("SELECT count(*) FROM ".table('shop_dish')." AS b LEFT JOIN ".table('shop_goods')." as c on b.gid = c.id WHERE b.deleted = 0 and b.status = 1 $condition ");
     $currency = 1;
}
// 开始进行标记选中事件selected
$purchase_goods = new LtCookie();
$purchase = $purchase_goods->getCookie('purchase');
if ( !empty($purchase) ){
	$purchase = unserialize($purchase);
}
unset($purchase_goods);
// 已选商品数量
$max_purchase = count($purchase);
foreach( $dish_list as &$dish_list_value){
	  if ( isset( $purchase[$dish_list_value['id']] ) ){
			 $dish_list_value['selected'] = 1;
	  }else{
			 $dish_list_value['selected'] = 0;
	  }
	  unset($dish_list_value['content']);
	  $dish_list_value = price_check($dish_list_value, $member['parent_roler_id'],$member['son_roler_id'], $user_a['type']);
	  $dish_list_value['currency'] = $currency;
}
unset($dish_list_value);
$pager  = pagination($total, $page, $psize);
// 设置汇率
$exchange_rate = mysqld_select("SELECT * FROM ".table('config')." WHERE name = 'exchange_rate' limit 1 ");
if ( $exchange_rate ){
    $exchange_rate_value =  $exchange_rate['value'] > 5 ? $exchange_rate['value'] : 6.8972;
}else{
    $exchange_rate_value = 6.8972;
}
$op = $_GP['type']; 
switch ( $op ){
	case 'get_content':
		if ( empty($_GP['id']) ){
              die(showAjaxMess('1002', '商品参数异常')); 
     	}
        $content = mysqld_select("SELECT b.content,a.title FROM ".table('shop_dish')." as a LEFT JOIN ".table('shop_goods')." as b on a.gid = b.id WHERE a.id = ".$_GP['id']." limit 1");
		die(showAjaxMess('200', $content)); 
		break;
	case 'add_goods':
		// 批量添加
	    if ( empty($_GP['goods']) || !is_array($_GP['goods']) ){
           die(json_encode(array(
			   "result"=>1,
			   "info"=> count($_GP['goods'])
		   )));
		}
		$purchase_goods = new LtCookie();
		$purchase = $purchase_goods->getCookie('purchase');
        if ( !empty($purchase) ){
            $purchase = unserialize($purchase);
		}else{
            $purchase = array();
		}
		foreach ($_GP['goods'] as $key=>$value){
			if ( $user_a['type'] == 2 ){
					// 查找批发产品,是否存在
					$check_goods = check_goods($key,$member['parent_roler_id'],$member['son_roler_id']);
					if ( !$check_goods ){
						continue;
					}
			}
			$model = model_good($key,$member['parent_roler_id'],$member['son_roler_id'],$user_a['type']);
			$model['num'] = $value;
			if ( isset($purchase[$key]) ){
                unset($purchase[$key]);
			}
			$purchase[$key] = $model;
		}
		$max_purchase = count($purchase);
        $purchase_goods->setCookie('purchase',serialize($purchase));
		die(json_encode(array(
			   "result"=>0,
			   "max_purchase" => $max_purchase,
			   "info"=> '添加成功'
		    )));
	    exit;
		break;
	case 'add_good':
		// 分2种情况，一种是没有这个产品，一种是有这个产品修正数量
	    if ( empty($_GP['id']) || empty($_GP['num']) ){
           die(json_encode(array(
			   "result"=>1,
			   "info"=> '参数不正确'
		   )));
		}
		// 查找产品,是否存在
		if ( $user_a['type'] == 2 ){
				$check_goods = check_goods($_GP['id'],$member['parent_roler_id'],$member['son_roler_id']);
				if ( !$check_goods ){
					die(json_encode(array(
					   "result"=>1,
					   "info"=> '该产品不在批发列表中'
					)));
				}
		}
        $purchase = add_goods($_GP['id'], $_GP['num'],$member['parent_roler_id'],$member['son_roler_id'], $user_a['type']);
		if ( !empty($purchase) ){
             $purchase = unserialize($purchase);
		}
        $max_purchase = count($purchase);
        if ( $max_purchase > 0 ){
             foreach( $purchase as $key=>$purchase_value ){
                  $query = mysqld_select("SELECT a.gid,b.weight,b.coefficient FROM ".table('shop_dish')." as a left join ".table('shop_goods')." as b on a.gid=b.id where a.id =".$key);
                  $coefficient = $query['coefficient'] > 0 ? $query['coefficient'] : 1.2;
		          $freight    =  $query['weight'] * $purchase_value['num'] * $coefficient * 2.2046 * 3.25 ;
				  $purchase[$key]['freight'] = $freight;
			 }
		}
		die(json_encode(array(
			   "result"=>0,
			   "max_purchase" => $max_purchase,
			   'purchase'=>$purchase,
			   "info"=> '添加成功'
		    )));
	    exit;
		break;
	
	case 'del_good':
		if ( empty($_GP['id']) ){
           die(json_encode(array(
			   "result"=>1,
			   "info"=> '参数不正确'
		   )));
		}
		// 查找产品,是否存在
		$check_goods = check_goods($_GP['id'],$member['parent_roler_id'],$member['son_roler_id']);
		if ( !$check_goods ){
            die(json_encode(array(
			   "result"=>1,
			   "info"=> '该产品不在批发列表中'
		    )));
		}
		 $max_purchase = del_goods($_GP['id']);
		 die(json_encode(array(
			   "result"=>0,
			   "max_purchase" => $max_purchase,
			   "info"=> '删除成功'
		    )));
	    exit;
		break;
	case 'get_goods':
		$purchase_goods = new LtCookie();
	    $purchase = $purchase_goods->getCookie('purchase');
		if ( !empty($purchase) ){
            $purchase = unserialize($purchase);
		}else{
            $purchase = array();
		}
		$max_purchase = count($purchase);
	
		// 开始设置运费
		if ( $max_purchase > 0 ){
             foreach( $purchase as $key=>$purchase_value ){
                  $query = mysqld_select("SELECT a.gid,b.weight,b.coefficient FROM ".table('shop_dish')." as a left join ".table('shop_goods')." as b on a.gid=b.id where a.id =".$key);
                  $coefficient = $query['coefficient'] > 0 ? $query['coefficient'] : 1.2;
		          $freight    =  $query['weight'] * $purchase_value['num'] * $coefficient * 2.2046 * 3.25 ;
				  $purchase[$key]['freight'] = $freight;
			 }
		}
		echo json_encode(array(
			   'result' => 0,
			   "max_purchase" => $max_purchase,
			   "shiprice" => 2,
			   'purchase'=>$purchase
			));
	    exit;
		break;
	default:
		break;
}
function del_goods($id){
     $purchase_goods = new LtCookie();
	 $purchase = $purchase_goods->getCookie('purchase');
	 if ( !empty($purchase) ){
          $purchase = unserialize($purchase);
	 }else{
          $purchase = array();
	 }
	 if ( isset($purchase[$id]) ){ 
          unset($purchase[$id]);
		  $max = count($purchase);
		  $purchase = serialize($purchase);
		  $purchase_goods->setCookie('purchase', $purchase);
	 }
	 return $max;
}
function add_goods($id,$num,$v1,$v2,$type=2){
     $purchase_goods = new LtCookie();
	 $model = model_good($id,$v1,$v2,$type);
	 $model['num'] = $num;
	 $purchase = $purchase_goods->getCookie('purchase');
	 if ( !empty($purchase) ){
          $purchase = unserialize($purchase);
	 }else{
          $purchase = array();
	 }
	 if ( isset($purchase[$id] ) ){
		 unset($purchase[$id]);
	 }
	 $purchase[$id] = $model;
	 $max = count($purchase);
	 $purchase = serialize($purchase);
     $purchase_goods->setCookie('purchase',$purchase);
	 return $purchase;
}
function model_good($id,$v1,$v2,$type=2){
	 if ( $type == 2 ){
			 $find_good = mysqld_select("SELECT a.*,b.* FROM ".table('shop_dish_vip')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.dish_id = b.id WHERE b.id = ".$id." and a.v1 = ".$v1." and a.v2 =  ".$v2);
			 if ( $find_good ){
				  $model_good    = array('id'=>$id,'title'=>$find_good['title'],'total'=>$find_good['total'],'price'=>$find_good['vip_price'],'img'=>$find_good['thumb']);
				  return $model_good;
			 }else{
                  $find_good = mysqld_select("SELECT a.*,b.* FROM ".table('shop_dish_vip')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.dish_id = b.id WHERE b.id = ".$id." and a.v1 = ".$v1." and a.v2 = 0 ");
				  if ( $find_good ){
                      $model_good    = array('id'=>$id,'title'=>$find_good['title'],'total'=>$find_good['total'],'price'=>$find_good['vip_price'],'img'=>$find_good['thumb']);
				  }else{
				      return false;
				  }
			 }
	 }else{
             $find_good = mysqld_select("SELECT * FROM ".table('shop_dish')." WHERE deleted = 0 and status = 1 and id = ".$id);
			 if ( $find_good ){
				  $model_good    = array('id'=>$id,'title'=>$find_good['title'],'total'=>$find_good['total'],'price'=>$find_good['marketprice'],'img'=>$find_good['thumb']);
				  // 进行价格校验
                  // 首先判断是否有指定价格设置
                  $check_price = mysqld_select("SELECT * FROM ".table('shop_dish_vip')." WHERE v1 = ".$v1." and v2 = ".$v2." and dish_id = ".$id);
                  if ( $check_price ){
                      $model_good['price'] = $check_price['vip_price'];
                  }else{
                  // 开始判断有没批量价格的设定
				       $check_price = mysqld_select("SELECT * FROM ".table('rolers')." WHERE discount > 0 and type = 3  and ( id= ".$v1." or id = ".$v2.") order by pid desc limit 1 " );
					   if ( $check_price ){
                           $model_good['price'] = $check_price['discount'] * $model_good['price'];
					   }
				  }
				  return $model_good;
			 }else{
				  return false;
			 }
	 }
}
function price_check($goods=array(), $v1, $v2, $type = 2){
    if ( $type == 2 ){
         // 开始查找是否有相应权限的特殊价格.批量的价格在外围设置完毕
         $check_price = mysqld_select("SELECT * FROM ".table('shop_dish_vip')." WHERE dish_id = :dish_id and v1 = :v1 and v2 = :v2 ", array(':dish_id'=>$goods['id'], ':v1'=> $v1, ':v2'=>$v2 ));
		 if ( $check_price ){
             $goods['vip_price'] = $check_price['vip_price'];
		 }
		 return $goods;
	}else{
         // 开始查找是否有特定的价格
		  $goods['vip_price'] = $goods['marketprice'];
		  
		  $check_price = mysqld_select("SELECT * FROM ".table('shop_dish_vip')." WHERE v1 = ".$v1." and v2 = ".$v2." and dish_id = ".$id);
		  if ( $check_price ){
			  $goods['vip_price'] = $check_price['vip_price'];

		  }else{
		  // 开始判断有没批量价格的设定
			   $check_price = mysqld_select("SELECT * FROM ".table('rolers')." WHERE discount > 0 and type = 3  and ( id= ".$v1." or id = ".$v2.") order by pid desc limit 1 " );
			   if ( $check_price ){
				   $goods['vip_price'] = $check_price['discount'] * $goods['vip_price'];
			   }
		  }

		  return $goods;
	}
}
function check_goods($id,$v1,$v2){
    $find_good = mysqld_select("SELECT a.*,b.* FROM ".table('shop_dish_vip')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.dish_id = b.id WHERE b.id = ".$id." and a.v1 = ".$v1." and a.v2 =  ".$v2);
	if ( $find_good ){
        return $find_good;
	}else{
        return false;
	}
}
include themePage('purchase_order');