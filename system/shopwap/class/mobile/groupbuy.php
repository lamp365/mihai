<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/11/15
 * Time: 14:55
 */
$member=get_member_account(false);
$openid =$member['openid'] ;
$pindex = max(1, intval($_GP['page']));
$psize  = 15;
$total  = 0;
$op = $_GP['op'];
// 无论是参团还是详情，都需要宝贝id
$goodsid = $_GP['id'];
if(empty($goodsid))
	message('地址访问有误！',refresh(),'error');

update_group_status($goodsid);
$hstory_goods = get_hstory($goodsid);
// 获取产品信息，判断是否是团购，如果不是，则跳转到普通商品详情
$table = 'shop_dish';
$product = array(
      'table' => $table, 
      'where' => 'a.id = '.$goodsid
);
// 设置时间开关
$timeout = 0;
$goods = get_good($product);
if (empty($goods)) {
      message('抱歉，团购商品不存在或是已经被删除！');
}
if ( $goods['status'] == 0 ){
      message('抱歉，该商品已经下架');
}
if ( $goods['total'] <= 0 ){
      message('抱歉，团购商品库存不足');
}
if ( $goods['timeend'] <= time() ){
     $timeout = 1;
}
if ( $goods['type'] == 1 && $timeout == 0 ){
    switch($op){
		case 'join_group' : //参团
		     // 判断队伍状态
			 $group_id = $_GP['group_id'];
			 if ( $group_id ){
			     $group = mysqld_select("SELECT * FROM ".table('team_buy_group')." WHERE group_id=".$group_id);
				 if (( strtotime($group['createtime']) + TEAM_BUY_EXPIRY ) <= time() || ( $group['status'] == 0 || $group['status'] == 1 ) ) {
                      message('团购队伍已失效');
				 }
				 if (empty($group)) {
					  message('查询团购队伍失败');
				 }else{
					 $group_member = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.id, a.openid, a.createtime as jointime, b.nickname, b.avatar FROM ".table('team_buy_member')." as a left join ".table('member')." as b on a.openid=b.openid WHERE a.group_id=".$group_id." ORDER BY a.createtime ASC");
					 $member_total = mysqld_select("SELECT FOUND_ROWS() as member_total;");
					 $goods['residue_num'] = $goods['team_buy_count'] - $member_total['member_total'];
					 $brand = mysqld_select("SELECT a.brand,a.country_id,b.name,b.id,b.icon FROM ".table("shop_brand")." as a LEFT JOIN ".table("shop_country"). " as b on a.country_id = b.id WHERE a.id = ".$goods['brand']);
				 }
			 }else{
                 message('获取团购成员出错');
			 } 
			$piclist = array(array("attachment" => $goods['thumb'],"small"=> $goods['small']));
			// 获取细节图
			$goods_piclist = mysqld_selectall("SELECT * FROM " . table('shop_goods_piclist') . " WHERE goodid = :goodid", array(':goodid' => $goods['gid']));
			$goods_piclist_count = mysqld_selectcolumn("SELECT count(*) FROM " . table('shop_goods_piclist') . " WHERE goodid = :goodid", array(':goodid' => $goods['gid']));
			if($goods_piclist_count>0)
			{
				 foreach ($goods_piclist as &$item) {
						$piclist[]=array("attachment" =>$item['picurl'],"small"=> download_pic($item['picurl'],'400','400'));
				 }
			}  
			 $group['end_time'] = date('Y-m-d H:i:s', strtotime($group['createtime']) + TEAM_BUY_EXPIRY);
			 include  themePage('join_group');
		     break;
		case 'detail_group' :
             // 更新团购状态
		     if ( $timeout == 0 ){   
				 $brand = mysqld_select("SELECT a.brand,a.country_id,b.name,b.id,b.icon FROM ".table("shop_brand")." as a LEFT JOIN ".table("shop_country"). " as b on a.country_id = b.id WHERE a.id = ".$goods['brand']);
				$comments = array();
				$comments = mysqld_selectall("SELECT * FROM " . table('shop_goods_comment') . "  WHERE goodsid={$goods['gid']} ORDER BY istop desc, createtime desc limit ". ($pindex - 1) * $psize . ',' . $psize);
				$pager    = '';
				// 获取推荐产品   看了最终买
				$best_goods = cs_goods($goods['p1'], 1, 1,10);
				// 获取热卖产品  相关推荐
				$jp_goods = cs_goods($goods['p1'], 1, 4, 6);
				 //获取其他团购商品
				 $group_goods = cs_goods($goods['p1'], 1, 5, 6);
				 // 获取团购队伍信息
				 $group = mysqld_selectall("SELECT a.*, b.nickname, b.avatar,b.mobile FROM ".table('team_buy_group')." as a left join ".table('member')." as b on a.creator=b.openid WHERE a.dish_id=".$goodsid." AND a.status=2 ORDER BY a.modifiedtime DESC limit 2");
				 foreach ($group as &$g_v) {
						$group_man = mysqld_select("SELECT COUNT(*) as num FROM ".table('team_buy_member')." WHERE group_id=".$g_v['group_id']);
					 	//增加团购队伍信息
						$g_v['residue_num'] = (int)$goods['team_buy_count'] - $group_man['num'];
						$g_v['now_num'] = (int)$group_man['num'];
						$g_v['end_time'] =  strtotime($g_v['createtime']) + TEAM_BUY_EXPIRY;
				 }
				 unset($g_v);
				 // 团购有效期//TEAM_BUY_EXPIRY;
				 // 获取团购总参与人数
				 $allnum = mysqld_select("SELECT COUNT(*) as allnum FROM ".table('team_buy_group')." as a left join ".table('team_buy_member')." as b on a.group_id=b.group_id WHERE a.finish=0 and a.dish_id=".$goodsid);
				 $goods['allnum'] = $allnum['allnum'];
			 }
			// 获取仓库信息
			$depot = mysqld_select("SELECT name,displayorder FROM " . table('dish_list') . "  WHERE id=:depotid", array(':depotid' => $goods['pcate']));
			$goods['depot'] = $depot['name'];
			// 获取所有仓库信息
			$depots = mysqld_selectall("SELECT * FROM " . table('dish_list') . "  WHERE deleted=0 and enabled = 1");
			$goods['content'] = strip_tags($goods['content'],'<img>');
			// 免运费申明
		    $promotion=mysqld_select("select * from ".table('shop_pormotions')." where starttime<=:starttime and endtime>=:endtime",array(':starttime'=>TIMESTAMP,':endtime'=>TIMESTAMP));
			$tax = mysqld_select("select tax from " . table('shop_tax') . " where  id=:id ", array(":id" => $goods['taxid']));
			if ( !empty($tax) ){
			  $tax = '本商品适用税率为'.number_format($tax['tax'] *100,2,'.','')."%";
			}else{
			  $tax = '';
			}
			$detail = mysqld_selectall("SELECT * FROM " .table('config')." where name = 'detail_head' or name = 'detail_foot' or name ='detail_pc_head'");
			foreach ( $detail as $d_v){
				 $cfg[$d_v['name']] = $d_v['value'];
			}
		     $qqarr = getQQ_onWork($cfg);
             // 获取团购的评论信息
			 if(!empty($comments)){
				//获取评论对应的图片
				foreach($comments as $k=> &$row){
					$user_info = getUserFaceAndName($row['openid'],$row['username'],$row['face']);
					$row['username'] = $user_info['username'];
					$row['face']     = $user_info['face'];
					$comments[$k]['piclist'] = mysqld_selectall("select img from ". table('shop_comment_img') ." where comment_id={$row['id']}");
			   }
			   if(!empty($_POST['page'])) {  //wap端手机页面上会滚动加载评论数据
				   $html = '';
				   foreach($comments as $key=>$rows){
					   $system    =  getSystemType($rows['system']);
					   $html .= "<li>
									<div class='user-name'>用户名：{$rows['username']}</div> <span class='date-time'>来自 {$system} 版</span>
									<h4 class='detail-content'>{$rows['comment']}</h4>";
					   if(!empty($rows['piclist'])){
						   $html .= "<ul class='img-list' data-clicked='0' data-key='{$key}'>";

						   foreach($rows['piclist'] as $picurl){
							   $max_pic   = download_pic($picurl['img'],650);
							   $small_pic = download_pic($picurl['img'],50,50,1);
							   $html .= "<li>
											<a class='fancybox_{$key}' href='{$max_pic}' data-fancybox-group='gallery'>
												<img src='{$small_pic}'>
											</a>
										</li>";
						   }
						   $html .= "</ul>";
					   }
					   $html .= "</li>";
				   }
				   echo $html;
				   die();
			   }else{
				   // 获取评论数量
				   $total = $goods['count'] = mysqld_selectcolumn('SELECT COUNT(*) FROM '. table('shop_goods_comment') .'WHERE goodsid = '.$goods['gid']);
				   $pager = pagination($total, $pindex, $psize,'.show_comment');
			   }
			}
			 $piclist = array(array("attachment" => $goods['thumb'],"small"=> $goods['small']));
			// 获取细节图
			$goods_piclist = mysqld_selectall("SELECT * FROM " . table('shop_goods_piclist') . " WHERE goodid = :goodid", array(':goodid' => $goods['gid']));
			$goods_piclist_count = mysqld_selectcolumn("SELECT count(*) FROM " . table('shop_goods_piclist') . " WHERE goodid = :goodid", array(':goodid' => $goods['gid']));
			if($goods_piclist_count>0)
			{
				 foreach ($goods_piclist as &$item) {
						$piclist[]=array("attachment" =>$item['picurl'],"small"=> download_pic($item['picurl'],'400','400'));
				 }
			} 
			include themePage('detail_group');
			break;
		default:
			break;
    }
	tosaveloginfrom();
}else{
	if(empty($_GP['accesskey'])){
		$goodlist = create_url('mobile', array('name' => 'shopwap','do' => 'detail','id'=>$goodsid));
	}else{
		$goodlist = create_url('mobile', array('name' => 'shopwap','do' => 'detail','id'=>$goodsid,'accesskey'=>$_GP['accesskey']));
	}
	header("location:" . $goodlist);
}
