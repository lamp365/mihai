<?php			
		$pindex = max(1, intval($_GP["page"]));
		$carttotal = $this->getCartTotal();
		$psize =  20;
		$condition = '';
	    $op = $_GP['op'];
	
	    $table = 'shop_dish';
	    $comment = 'shop_goods_comment';
		$key  = 'goodsid';
		$template = 'dishlist';
	    $citylist = 'dish_list';
		if ( $op == 'limit' ){
			$template = 'limitlist';
		    $list = get_limits(0);
			$advs = mysqld_selectall("select * from " . table('shop_adv') . " where enabled=1 and type = 1 and page = 5 order by displayorder desc");
            include themePage($template); 
			exit;
		}
	    $title = '';
	    $sort = empty($_GP['sort']) ? 0 : $_GP['sort'];
        $sortfield = "displayorder asc";
        $sortb0 = empty($_GP['sortb0']) ? "desc" : $_GP['sortb0'];
        $sortb1 = empty($_GP['sortb1']) ? "desc" : $_GP['sortb1'];
        $sortb2 = empty($_GP['sortb2']) ? "desc" : $_GP['sortb2'];
        $sortb3 = empty($_GP['sortb3']) ? "asc" : $_GP['sortb3'];
        if ($sort == 0) {
            $sortb00 = $sortb0 == "desc" ? "asc" : "desc";
            $sortfield = "displayorder " . $sortb0;//时间改成后台设置的排序字段
            $sortb11 = "desc";
            $sortb22 = "desc";
            $sortb33 = "asc";
        } else if ($sort == 1) {
            $sortb11 = $sortb1 == "desc" ? "asc" : "desc";
            $sortfield = "weight " . $sortb1;
            $sortb00 = "desc";
            $sortb22 = "desc";
            $sortb33 = "asc";
        } else if ($sort == 2) {
            $sortb22 = $sortb2 == "desc" ? "asc" : "desc";
            $sortfield = "viewcount " . $sortb2;
            $sortb00 = "desc";
            $sortb11 = "desc";
            $sortb33 = "asc";
        } else if ($sort == 3) {
            $sortb33 = $sortb3 == "asc" ? "desc" : "asc";
            $sortfield = "a.marketprice " . $sortb3 . ","."b.marketprice " . $sortb3;
            $sortb00 = "desc";
            $sortb11 = "desc";
            $sortb22 = "desc";
      }
	  $condition = ' a.status = 1 ';
      if (!empty($_GP['p3'])) {
            $cid = intval($_GP['p3']);
            //$condition .= " AND a.p3 = '{$cid}'";
            
            $dishIds = getCategoryExtendDishId($cid);
            
            if(!empty($dishIds))
            {
            	$condition .=' AND (a.p3='.$cid.' or a.id in('.implode(",", $dishIds).') ) ';
            }
            else{
            	$condition .= " AND a.p3 = '{$cid}'";
            }
            
      } elseif (!empty($_GP['p2'])) {
            $cid = intval($_GP['p2']);
			$p2 = get_category($cid);
			$p1 = get_category($p2['parentid']);
			$parent = $p2['parentid'];
            //$condition .= " AND a.p2 = '{$cid}'";
            
            $dishIds = getCategoryExtendDishId($cid);
            
            if(!empty($dishIds))
            {
            	$condition .=' AND (a.p2='.$cid.' or a.id in('.implode(",", $dishIds).') ) ';
            }
            else{
            	$condition .= " AND a.p2 = '{$cid}'";
            }
            
      } elseif (!empty($_GP['pcate'])) {
            $cid = intval($_GP['pcate']);
			$p1 = get_category($cid);
			$parent = $cid;
            //$condition .= " AND a.p1 = '{$cid}'";
            
            $dishIds = getCategoryExtendDishId($cid);
            
            if(!empty($dishIds))
            {
            	$condition .=' AND (a.p1='.$cid.' or a.id in('.implode(",", $dishIds).') ) ';
            }
            else{
            	$condition .= " AND a.p1 = '{$cid}'";
            }
      }

//模板和下文都没用到该变量
//      $categorys = mysqld_selectall("SELECT * FROM ".table('shop_category')." where parentid = ".$parent);
	  if ( !empty($_GP['bid']) ){
            $bid = intval($_GP['bid']);
			$brand = mysqld_select("SELECT a.*,b.name,b.icon as cicon FROM ".table('shop_brand')." a left join ".table('shop_country')." b on a.country_id = b.id where a.id = ".$bid);
			$brand['content'] = strip_tags($brand['content']);
			$brand['num'] = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table($table) . " as a left join ".table('shop_goods')." as b on a.gid = b.id WHERE brand = :beid  and a.deleted=0  AND a.status = '1' ", array(':beid'=>$bid));
            $condition .= " AND b.brand = '{$bid}'";
	  }
      if (!empty($_GP['keyword'])) {
             $condition .= " AND a.title LIKE '%{$_GP['keyword']}%' ";
      }
      $sorturl = mobile_url('goodlist', array("keyword" => $_GP['keyword'], 'bid'=> $_GP['bid'] ,"pcate" => $_GP['pcate'], "p2" => $_GP['p2']));
      if (!empty($_GP['isnew'])) {
            $condition .= " AND isnew = 1";
            $sorturl.="&isnew=1";
      }
        if (!empty($_GP['ishot'])) {
            $condition .= " AND ishot = 1";
            $sorturl.="&ishot=1";
        }
        if (!empty($_GP['isdiscount'])) {
            $condition .= " AND isdiscount = 1";
            $sorturl.="&isdiscount=1";
        }
        if (!empty($_GP['istime'])) {
            $condition .= " AND istime = 1 ";
            $sorturl.="&istime=1";
        }

	  $list = get_goods(array(
			 'table'=> $table,
			 'where' => $condition,
			 'limit'=>  ($pindex-1)*$psize.','.$psize,
			 'order' => $sortfield
	  ));
      if ( empty($list) && !empty($_GP['keyword'])){
             $word = get_word($_GP['keyword']);
			 if ( !empty($word) ){
		     foreach ($word as $word_value ) {
	               $keys[] = " b.title like '%".$word_value."%' ";
		      }
		     $keys = implode(' or ' , $keys);
		     $condition .= ' AND ('.$keys.')';
			 $list = get_goods(array(
				 'table'=> $table,
				 'where' => $condition,
				 'limit'=>  ($pindex-1)*$psize.','.$psize,
				 'order' => $sortfield
	         ));
			 }
	  }
	  $brands = array();
	  $dish_brand = mysqld_selectall("SELECT b.brand FROM ". table('shop_goods') . " as b left join ". table($table) ." as a on b.id = a.gid where ".$condition." group by b.brand");
      foreach ( $dish_brand as $key=>$b_value ){
           $brands[$b_value['brand']] = mysqld_select('SELECT * FROM '. table('shop_brand') . ' WHERE id = :id ', array(":id"=> $b_value['brand']));
		   $brands[$b_value['brand']]['url'] = mobile_url('goodlist', array("keyword" => $_GP['keyword'], 'bid'=> $b_value['brand'] ,"pcate" => $_GP['pcate'], "p2" => $_GP['p2']));
	  }
	  foreach ( $list as &$comments){
          //评论是goodsid=gid
          $comments['count'] = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table($comment) . " WHERE  goodsid = ".$comments['gid']);
          //商品列表需要展示 国家和ico
          $good_brand = mysqld_select("SELECT a.brand,b.name,b.icon FROM ".table("shop_brand")." as a LEFT JOIN ".table("shop_country"). " as b on a.country_id = b.id WHERE a.id = ".$comments['brand']);
          $comments['brand_icon'] = $good_brand['icon'];
          $comments['brand_name'] = $good_brand['name'];
      }

	  if ( !empty($bid) ){
          unset($brands[$bid]);
	  }
	  
      if ( !empty($_POST['page']) ){
             // 处理异步数据
			 $theme = $_SESSION["theme"];
			   if(is_array($list)) { foreach($list as $item) { 
				   if ( $op == 'es'){
                     $html .='
                          <li>				  
						  	<div class="item" style="position:relative;">       
                                  <div class="img">
                                  	<a href="'. mobile_url('detail', array('id' => $item['id'])).'">
	                                  <img src="'. $item['small'] .'" width="100%" alt="">
	                                   </a>
                                  </div>								  							  
                                  <div class="txt">'.$item['title'].'</div>                                 
								  <div class="price">
                                      <em><i>¥</i>'.$item['marketprice'].'</em>
									  <em class="del"><i>¥</i>'.$item['productprice'].'</em>                   
                                  </div>
                              </div>
                          </li>';
				   }else{
                         $html .= '<li>				  
						  	<div class="item" style="position:relative;">       
                                  <div class="img">
                                  	<a href="'. mobile_url('detail', array('id' => $item['id'])).'">
	                                  <img src="'. $item['small'] .'" width="100%" alt="">
	                                   </a>
                                  </div>								  							  
                                  <div class="txt">'.$item['title'].'</div>                                 
								  <div class="price">
                                      <em><i>¥</i>'.$item['marketprice'].'</em>
									  <em class="del"><i>¥</i>'.$item['productprice'].'</em>                   
                                  </div>
                              </div>
                          </li>';
				   }
			   }}
			   echo $html;
		}else{
			$total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table($table) . " as a left join ".table('shop_goods')." as b on a.gid = b.id WHERE $condition and a.deleted=0  AND a.status = '1' ");
			$pager  = pagination($total, $pindex, $psize,'.os_box_list');
			$pager2 = pagination($total, $pindex, $psize,'.os_box_list','1');
			$best_goods = cs_goods('', 1, 1,10);
			$id = $profile['id'];
			if($profile['status']==0){
				$profile['flag']=0;
			}	
			//添加浏览记录
			$hstory = get_hstory();
		    tosaveloginfrom();
			include themePage($template);
		}
