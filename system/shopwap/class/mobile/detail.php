<?php
        $member=get_member_account(false);
		$openid =$member['openid'] ;

		$pindex = max(1, intval($_GP['page']));
		$psize  = 15;
		$total  = 0;

		//获取商家openid
		$saller_openid = getOpenshopSellerOpenid();

		$memberinfo=member_get($openid);
        $goodsid = intval($_GP['id']);
		if(empty($goodsid))
			message('地址访问有误！',refresh(),'error');

		$op    = $_GP['op'];
		$table = array(
			'table'=>'shop_dish',
			'where' => 'a.id = '.$goodsid
		);

		switch ($op){
		  case 'pager':
		  	  $table= array(
                  'table'=>'shop_dish',
	              'where' => 'a.id = '.$goodsid
              );
              $goods = get_good($table);
			  $comments = mysqld_selectall("SELECT * FROM " . table('shop_goods_comment') . "  WHERE dishid={$goods['id']} ORDER BY istop desc, createtime desc limit ". ($pindex - 1) * $psize . ',' . $psize);
			  //获取评论对应的图片
			   if (!empty($comments)) {
				   	foreach($comments as $k=> &$row){
						$user_info = getUserFaceAndName($row['openid'],$row['username'],$row['face']);
				   		$row['username'] = $user_info['username'];
				   		$row['face']     = $user_info['face'];
						$comments[$k]['piclist'] = mysqld_selectall("select img from ". table('shop_comment_img') ." where comment_id={$row['id']}");
					}
					unset($row);
			   }
				
			  echo json_encode($comments);
			  die();
			  break;
		}

        $goods = get_good($table);
		if (empty($goods)) {
			message('抱歉，商品不存在或是已经被删除！');
		}
		if ( $goods['status'] == 0 ){
			message('抱歉，该商品已经下架');
		}
		if ( $goods['type'] == 1 && $goods['timeend']>time()){
			//是团购类型，并且团购时间还没过期
			if(!empty($_GP['accesskey'])){
				$goodlist = create_url('mobile', array('name' => 'shopwap','do' => 'groupbuy','id'=>$goodsid,'op'=>'detail_group','accesskey'=>$_GP['accesskey']));
			}else{
				$goodlist = create_url('mobile', array('name' => 'shopwap','do' => 'groupbuy','id'=>$goodsid,'op'=>'detail_group'));

			}
			header("location:" . $goodlist);
		}
        $goods['count'] = 0;
	    $brand = mysqld_select("SELECT a.brand,a.country_id,b.name,b.id,b.icon FROM ".table("shop_brand")." as a LEFT JOIN ".table("shop_country"). " as b on a.country_id = b.id WHERE a.id = ".$goods['brand']);
	   // 获取评论 订单评论时用的是goods表中的id   详情页面的$_gp['id']是dish表中的id
	    $comments = array();
	    $comments = mysqld_selectall("SELECT * FROM " . table('shop_goods_comment') . "  WHERE dishid={$goods['gid']} ORDER BY istop desc, createtime desc limit ". ($pindex - 1) * $psize . ',' . $psize);
		$pager    = '';
	   if(!empty($comments)){
		   $pager =  get_dishs_comment($comments);
		}

	   // 获取运费模板信息
        $depot = mysqld_select("SELECT name,kuaidi,displayorder FROM " . table('dish_list') . "  WHERE id=:depotid", array(':depotid' => $goods['transport_id']));
		// 免运费申明
		$promotion=mysqld_select("select * from ".table('shop_pormotions')." where starttime<=:starttime and endtime>=:endtime",array(':starttime'=>TIMESTAMP,':endtime'=>TIMESTAMP));

	   // 获取产品活动时间
		$istime = 0;
        if ($goods['istime'] != 0) {
			$istime = 1;
            if (time() < $goods['timestart']) {
                $istime = 0;
            }
            if (time() > $goods['timeend'] && !empty($goods['timeend']) ) {
                $istime = 0;
            }
        }
		 if ($goods['istime'] == 1 && $goods['isdiscount'] == 1) {
			$istime = 2;
            if (time() < $goods['timestart']) {
                $istime = 3;
            }
            if (time() > $goods['timeend'] && !empty($goods['timeend']) ) {
                $istime = 0;
            }
        }
		$arr = $this->time_tran($goods['timeend']);
        if ( $istime == 1 or $istime == 2 ){
             $arr = $this->time_tran($goods['timeend']);
		}
	    if ( $istime == 3){
             $arr = $this->time_tran($goods['timestart']);
		}
		$goods['content']     = strip_tags($goods['content'],'<img>');
	    $goods['timelaststr'] = $arr[0];
	    $goods['timelast']    = $arr[1];

		//获取访问历史
		$hstory_goods         = get_hstory($goodsid);

		// 获取推荐产品  看了最终买  是否热门的
		$best_goods = cs_goods($goods['p1'], 1, 1,10);
		// 获取热卖产品  相关推荐  是否精品
		$jp_goods = cs_goods($goods['p1'], 1, 4, 5);
		//获取最新上架的
		$goods_bytime = cs_goods_bytime($goods['p1'],1,5);

        mysqld_update('shop_dish',array('viewcount'=>$goods['viewcount']+1),array('id'=>$goodsid));

        $piclist = array(array("attachment" => $goods['thumb'],"small"=> $goods['small']));

		// 获取细节图
        $goods_piclist = mysqld_select("SELECT * FROM " . table('shop_dish_piclist') . " WHERE goodid = :goodid", array(':goodid' => $goods['id']));
		if(!empty($goods_piclist['picurl'])){
			$goods_piclist = explode(',',$goods_piclist['picurl']);
		}else{
			$goods_piclist = array();
		}

		foreach ($goods_piclist as $one_pic) {
			$piclist[]=array("attachment" =>$one_pic,"small"=> download_pic($one_pic,'400','400'));
		}

		$cfg   = globaSetting();
		$qqarr = getQQ_onWork($cfg);
		tosaveloginfrom();
	    include themePage('detail');