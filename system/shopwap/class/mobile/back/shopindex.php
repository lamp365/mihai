 <?php
if (is_mobile_request()|| $_GET['wap'] == 1){
$title = $cfg['shop_title'];
$advs = mysqld_selectall("select * from " . table('shop_adv') . " where enabled=1 and type = 2 and page = 1 order by displayorder desc");
$advs_mid = mysqld_selectall("select * from " . table('shop_adv') . " where enabled=1 and type = 2 and page = 4 order by displayorder desc");
$shop_menu_list = mysqld_selectall("SELECT * FROM " . table('shop_menu')." where menu_type='fansindex' and type = 2 order by torder desc" );	
//$c = get_categories_tree();
$first_good_list = array();
$recommandcategory = array();
// 获取推荐目录
$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " WHERE deleted=0 and enabled=1 and isrecommand =1 and parentid = 0 ORDER BY displayorder DESC");
$best_goods = index_c_goods($category,1,4);
$messnotice ="暂无";
$messid=0;
$member = get_member_account(false);
$member = member_get($member['openid']);
if (empty($member['openid'])) {
    $member = get_member_account(false);
    $member['createtime'] = time();
}
$is_login = is_login_account();

//$shitan =serialize(array('mess_name'=> '', 'mess_id'=>$messid));
// $_COOKIE['mess'] = array('mess_name'=> $mess['title'], 'mess_id'=>$mess['id']);
//setcookie("mess",$shitan,time()+3600*24*365*10);

}else{
//$ps1 = mysqld_selectall("SELECT * FROM ".table("shop_category")." WHERE isrecommand = 1 and parentid = 0 and enabled = 1 and deleted = 0");
$op    = $_GP['op'];
$title = $cfg['shop_title'];
$article_main = getArticle(4,2);
$advs = mysqld_selectall("select * from " . table('shop_adv') . " where enabled=1 and type = 1 and page = 1 order by displayorder desc");
$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " WHERE deleted=0 and enabled=1  ORDER BY parentid ASC, displayorder DESC");
foreach ($category as $index => $row) {
		if (!empty($row['parentid'])) {
			$children[$row['parentid']][$row['id']] = $row;
			unset($category[$index]);
		}
}
$category_index = array();
$brand_index = mysqld_selectall("SELECT * FROM ".table('shop_brand'). " WHERE isindex = 1 and deleted = 0 ");
foreach ($category as $c_index){
   if ( $c_index['isrecommand'] == 1 ){
	   $key = array();
	   if ( function_exists(getHottpoic) ){
	       $key = getHottpoic($c_index['id']);
	   }
	   $c_index['key'] = $key;
	   $bs = unserialize($c_index['brands']) ? unserialize($c_index['brands']) : array() ;
	   $brands = array();
	   if ( is_array($bs) ){
	      foreach ( $bs as $b_value ){
              $b = mysqld_select("SELECT * FROM " . table('shop_brand') . " WHERE deleted=0 and id = ".$b_value);
			  $brands[] = $b;
		  }
	   }
	   $c_index['brands'] = $brands;
       $category_index[] = $c_index;
   }
} 
$category_index = index_c_goods($category_index,1);
$category_index = index_c_goods($category_index,2);
$first_good_list = array();
$recommandcategory = array();
$limts = get_limits(4);
// 获取进行中的数据
/*
$c_goods = get_goods(array(
     'table'=>'addon7_award',
	 'where' => 'a.dicount = 0 and a.state = 1  ',
	 'order' => 'a.confirm_time'
));
foreach($c_goods as &$c){
	// 计算开奖时间
	$c['open'] = get_open_time($c['confirm_time']);
}
// 获取可以开奖的数据
$category = get_goods(array(
     'table'=>'addon7_award',
	 'where' => 'a.isrecommand=1 and a.dicount > 0 and a.deleted=0 AND a.state = 0  AND a.endtime <=' .time(),
	 'order' => 'a.endtime'
));
foreach($category as &$c){
	$c['seller'] = $c['amount'] - $c['dicount'];
	$c['per'] = $c['seller'] / $c['amount'];
}
$user_num = mysqld_selectcolumn("SELECT COUNT(*) FROM ".table("member"));
$goods = get_goods(array(
    'table'=>'addon7_award',
    'where' => 'a.isrecommand=1 and a.dicount > 0 and a.deleted=0 AND a.state = 0  AND a.endtime >' .time(),
    'order' => 'a.endtime',
	'limit' => 10
 )); 
 */
 /*
foreach ( $ps1 as &$val ){
    $goods = get_goods(array(
		 'table'=>'addon7_award',
		 'where' => 'a.isrecommand=1 and a.p1 = '.$val['id'].' and a.dicount > 0 and a.deleted=0 AND a.state = 0  AND a.endtime > ' .time(),
		 'order' => 'a.endtime',
		 'limit' => '1,8'
    )); 
	foreach($goods as &$c){
	    $c['seller'] = $c['amount'] - $c['dicount'];
	    $c['per'] = $c['seller'] / $c['amount'];
    }
	$ps2 = mysqld_selectall("SELECT * FROM ".table("shop_category")." WHERE isrecommand = 1 and parentid = ".$val['id']." and enabled = 1 and deleted = 0");
	$val['goods'] = $goods;
	$val['ps2'] = $ps2;
}*/
}
tosaveloginfrom();
$all_new_bonus = array(); //得到新手礼券
$isNewUserBonus = isNewMemberBonus($all_new_bonus);		//是否显示新手礼
include themePage('shopindex');

/**
 * 是否显示新手礼  并且返回新手礼券
 * @return boolean
 * 
 */
function isNewMemberBonus(&$all_new_bonus)
{
	//获取所有新手礼券
	$time = time();
	$all_new_bonus = mysqld_selectall("SELECT type_money,min_goods_amount FROM " . table('bonus_type')." where send_type=0 and deleted = 0 and use_end_date>{$time}");

	$is_login = is_login_account();
	
	if($is_login)
	{
		$openid = $_SESSION[MOBILE_ACCOUNT]['openid'];
		
		/*//是否有订单  去掉订单验证
		$order = mysqld_selectall("SELECT id FROM " . table('shop_order')." where openid='".$openid."' ");
		
		if($order)
		{
			return false;
		}*/
		//是否已经领过券
		$bonusUser = mysqld_selectall("SELECT u.bonus_id FROM " . table('bonus_user')." u left join ". table('bonus_type')." t on t.type_id=u.bonus_type_id where u.openid='".$openid."' and t.send_type=0");

		if($bonusUser)
		{
			return false;
		}
		return true;
	}else{
		return true;
	}
}