<?php
$title = '批发采购';
$is_login = is_vip_account();
// 获取用户的参数
$member = get_vip_member_account(true, true);
$openid =$member['openid'] ;
// [parent_roler_id] => 2 [son_roler_id] => 3
// 验证用户是否是批发商
$rolers = mysqld_select("SELECT * FROM ".table('rolers')." WHERE id = ".$member['parent_roler_id']." and type=2 ");
if ( empty($member['parent_roler_id']) || !$rolers || empty($member['son_roler_id']) ){
     header("location:".mobile_url('vip_logout'));
}
$addresslist      =   mysqld_selectall("SELECT * FROM " . table('shop_address') . " WHERE  deleted = 0 and openid = :openid order by isdefault desc ", array(':openid' => $openid));
$page = max(1, $_GP['page']);
$psize = max(20,$_GP['psize']);
$limit =  " limit ".($page-1)*$psize.','.$psize;
// 根据用户的角色获取产品数据
$dish_list = mysqld_selectall("SELECT a.*,b.* FROM ".table('shop_dish_vip')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.dish_id = b.id WHERE b.deleted = 0 and b.status = 1 and a.v1 = ".$member['parent_roler_id']." and a.v2 =  ".$member['son_roler_id'].$limit);
// 开始进行标记选中事件selected
$purchase_goods = new LtCookie();
$purchase = $purchase_goods->getCookie('purchase');
if ( !empty($purchase) ){
	$purchase = unserialize($purchase);
}
$max_purchase = count($purchase);
foreach( $dish_list as &$dish_list_value){
	  if ( isset( $purchase[$dish_list_value['id']] ) ){
			 $dish_list_value['selected'] = 1;
	  }else{
			 $dish_list_value['selected'] = 0;
	  }
	  unset($dish_list_value['content']);
}
unset($dish_list_value);
$total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table(shop_dish_vip) . " as a left join ".table('shop_dish')." as b on a.dish_id = b.id WHERE a.v1 = ".$member['parent_roler_id']." and a.v2 =  ".$member['son_roler_id']." $condition and b.deleted=0  AND b.status = '1' ");
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
        $content = mysqld_select("SELECT content FROM ".table('shop_dish')." WHERE id = ".$_GP['id']." limit 1");
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
		    // 查找产品,是否存在
			$check_goods = check_goods($key,$member['parent_roler_id'],$member['son_roler_id']);
			if ( !$check_goods ){
				continue;
			}
			$model = model_good($key,$member['parent_roler_id'],$member['son_roler_id']);
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
		$check_goods = check_goods($_GP['id'],$member['parent_roler_id'],$member['son_roler_id']);
		if ( !$check_goods ){
            die(json_encode(array(
			   "result"=>1,
			   "info"=> '该产品不在批发列表中'
		    )));
		}
        $max_purchase = add_goods($_GP['id'], $_GP['num'],$member['parent_roler_id'],$member['son_roler_id']);
		die(json_encode(array(
			   "result"=>0,
			   "max_purchase" => $max_purchase,
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
		echo json_encode(array(
			   'result' => 0,
			   "max_purchase" => $max_purchase,
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
function add_goods($id,$num,$v1,$v2){
     $purchase_goods = new LtCookie();
	 $model = model_good($id,$v1,$v2);
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
	 return $max;
}
function model_good($id,$v1,$v2){
     $find_good = mysqld_select("SELECT a.*,b.* FROM ".table('shop_dish_vip')." AS a LEFT JOIN ".table('shop_dish')." AS b ON a.dish_id = b.id WHERE b.id = ".$id." and a.v1 = ".$v1." and a.v2 =  ".$v2);
	 if ( $find_good ){
	      $model_good = array('id'=>$id,'title'=>$find_good['title'],'total'=>$find_good['total'],'price'=>$find_good['vip_price'],'img'=>$find_good['thumb']);
		  return $model_good;
	 }else{
          return false;
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