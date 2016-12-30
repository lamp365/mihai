<?php
        $member=get_member_account(false);
		$openid =$member['openid'] ;

		$pindex = max(1, intval($_GP['page']));
		$psize  = 15;
		$total  = 0;

		//获取商家openid
		$saller_openid = getOpenshopSellerOpenid();
		if($saller_openid){
			$shopInfo = mysqld_select("select * from ". table('openshop') . " where openid=:openid",array(
					'openid' => $saller_openid
		));
		}else if($saller_openid && !empty($_GP['accesskey'])){
			message("对不起，参数有误！");
		}

		$memberinfo=member_get($openid);
        $goodsid = intval($_GP['id']);
		if(empty($goodsid))
			message('地址访问有误！',refresh(),'error');

		$op = $_GP['op'];
		$selleid = 0;
		//判断是否是卖家分享商品url进来的
		if(!empty($_GP['accesskey'])){
			$selleid  = checkOpenshopAccessKey();  //获取卖家id
		}
		$toshangjia   		  = $_GP['toshangjia'];       //用于显示是否是卖家要进去上下架商品，而不是卖商品
		if(!empty($toshangjia)){
			if(!checkIsOpenshop()){  //防止恶意改参数近来的用户，则判断是否是开店商家用户，若不是商家会影响到后续的上下架操作
				$toshangjia = '';
			}else{
				$dish_shangjia_status = getGoodsShangjiaStatusBygoodId($goodsid,$openid);
			}

		}

		switch ($op){
		  case 'group':
			  $table = 'shop_mess';
		      $table= array(
                  'table'=>$table,
	              'where' => 'a.id = '.$goodsid
              );
			  $comment = 'shop_mess_comment';
			  $depotlist    = 'mess_list';
			  $key = 'messid';
			  $com_text ='团购评价';
			  break;
		  case 'pager':
		  	  $table= array(
                  'table'=>'shop_dish',
	              'where' => 'a.id = '.$goodsid
              );
              $goods = get_good($table);
			  $comments = mysqld_selectall("SELECT * FROM " . table('shop_goods_comment') . "  WHERE goodsid={$goods['gid']} ORDER BY istop desc, createtime desc limit ". ($pindex - 1) * $psize . ',' . $psize);
			  //获取评论对应的图片
			   if (!empty($comments)) {
				   	foreach($comments as $k=> $row){
						$comments[$k]['piclist'] = mysqld_selectall("select img from ". table('shop_comment_img') ." where comment_id={$row['id']}");
					}
			   }
				
			  echo json_encode($comments);
			  return;
			  break;
		  default:
			  $table = 'shop_dish';
		      $table= array(
                  'table'=>$table,
	              'where' => 'a.id = '.$goodsid
              );
			  $depotlist    = 'dish_list';
			  $comment = 'shop_goods_comment';
			  $key = 'goodsid';
			  $com_text ='商品评价';
			  break;
		}

        $goods = get_good($table);
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
	    $comments = mysqld_selectall("SELECT * FROM " . table($comment) . "  WHERE {$key}={$goods['gid']} ORDER BY istop desc, createtime desc limit ". ($pindex - 1) * $psize . ',' . $psize);
		$pager    = '';
	   if(!empty($comments)){
			//获取评论对应的图片
			foreach($comments as $k=> $row){
				$comments[$k]['piclist'] = mysqld_selectall("select img from ". table('shop_comment_img') ." where comment_id={$row['id']}");
			}

		   if(!empty($_POST['page'])) {  //wap端手机页面上会滚动加载评论数据
			   $html = '';
			   foreach($comments as $key=>$rows){
				   $username = getUserFaceAndNameHtml($rows['openid'],$rows['username']);
				   $system   = getSystemType($rows['system']);
				   $html .= "<li>
								<div class='user-name'>用户名：{$username}</div> <span class='date-time'>来自 {$system} 版</span>
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
			   $total = $goods['count'] = mysqld_selectcolumn('SELECT COUNT(*) FROM '. table($comment) .'WHERE '.$key.' = '.$goods['gid']);
			    $tpage = ceil($total / $psize);
			    if ($tpage > 1) {
			    	$beforesize = 5;
    				$aftersize = 4;
			    	$cindex = $pindex;
			    	$rastart = max(1, $cindex - $beforesize);
					$raend = min($tpage, $cindex + $aftersize);
					if ($raend - $rastart < $beforesize + $aftersize) {
						$raend = min($tpage, $rastart + $beforesize + $aftersize);
						$rastart = max(1, $raend - $beforesize - $aftersize);
					}
			    }
			    
			   $pager = pagination($total, $pindex, $psize,'.show_comment');
		   }
		}





	   // 获取仓库信息
        $depot = mysqld_select("SELECT name,displayorder FROM " . table($depotlist) . "  WHERE id=:depotid", array(':depotid' => $goods['pcate']));
		$goods['depot'] = $depot['name'];
	    // 获取所有仓库信息
		$depots = mysqld_selectall("SELECT * FROM " . table($depotlist) . "  WHERE deleted=0 and enabled = 1");
	   // 获取产品活动时间
		$ccate = intval($goods['ccate']);
	    if (empty($goods)) {
            message('抱歉，商品不存在或是已经被删除！');
        }
		if ( $goods['status'] == 0 ){
            message('抱歉，该商品已经下架');
		}
		$istime = 0;
        if ($goods['istime'] == 1 && $goods['type'] == 4) {
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
		$goods['content'] = strip_tags($goods['content'],'<img>');
	    $goods['timelaststr'] = $arr[0];
	    $goods['timelast'] = $arr[1];
		$hstory_goods = get_hstory($goodsid);

		// 获取推荐产品
		$best_goods = cs_goods($goods['p1'], 1, 1,10);
		// 获取热卖产品
		$jp_goods = cs_goods($goods['p1'], 1, 4, 5);
        mysqld_update('shop_goods',array('viewcount'=>$goods['viewcount']+1),array('id'=>$goodsid));
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
		// 免运费申明
		 $promotion=mysqld_select("select * from ".table('shop_pormotions')." where starttime<=:starttime and endtime>=:endtime",array(':starttime'=>TIMESTAMP,':endtime'=>TIMESTAMP));
		 // 为避免误操作 我们只找出金额最大的免运费金额

		/*****************暂时没用
		 $shiprice = 0;
		 $shiparr = array();
		 foreach($promotion as $pro){
			if($pro['promoteType']==1){
				 if ($pro['condition']>= $shiprice){
                     $shiparr = $pro;
				 }
			}	
         }*/
		// 税率申明
		$tax = mysqld_select("select tax from " . table('shop_tax') . " where  id=:id ", array(":id" => $goods['taxid']));
	    if ( !empty($tax) ){
		  $tax = '本商品适用税率为'.number_format($tax['tax'] *100,2,'.','')."%";
		}else{
          $tax = '';
		}
        //规格及规格项
           $allspecs = mysqld_selectall("select * from " . table('shop_goods_spec') . " where goodsid=:id order by displayorder asc", array(':id' => $goodsid));
           foreach ($allspecs as &$s) {
                 $s['items'] = mysqld_selectall("select * from " . table('shop_goods_spec_item') . " where  `show`=1 and specid=:specid order by displayorder asc", array(":specid" => $s['id']));
           }
           unset($s);
           //处理规格项
           $options = mysqld_selectall("select id,title,thumb,marketprice,productprice, stock,weight,specs from " . table('shop_goods_option') . " where goodsid=:id order by id asc", array(':id' => $goodsid));
           //排序好的specs
          $specs = array();
                //找出数据库存储的排列顺序
          if (count($options) > 0) {
                    $specitemids = explode("_", $options[0]['specs'] );
                    foreach($specitemids as $itemid){
                        foreach($allspecs as $ss){
                             $items=  $ss['items'];
                             foreach($items as $it){
                                 if($it['id']==$itemid){
                                     $specs[] = $ss;
                                     break;
                                 }
                             }
                        }
         }
        }
		$detail = mysqld_selectall("SELECT * FROM " .table('config')." where name = 'detail_head' or name = 'detail_foot' or name ='detail_pc_head'");
		foreach ( $detail as $d_v){
             $cfg[$d_v['name']] = $d_v['value'];
		}
		$qqarr = getQQ_onWork($cfg);
		tosaveloginfrom();
	    include themePage('detail');