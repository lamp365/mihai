<?php
$op = empty($_GP['op']) ? 'healty' : $_GP['op'];
$cfg=globaSetting();

   if($op == 'healty'){
	   //健康文化
	   $psize =  12;
	   $pindex = max(1, intval($_GP["page"]));
	   $limit = ' limit '.($pindex-1)*$psize.','.$psize;
	   $title  = '健康文化';
	   $sql    = "SELECT * FROM".table('addon8_article')." where state =6  order by displayorder desc,id desc {$limit}";
	   $sqlnum = "SELECT count(id) FROM".table('addon8_article')." where state =6";
	   if(empty($_GP['id'])){
		   $one_sql = "SELECT * FROM " . table('addon8_article')." where state =6 order by id desc limit 1 ";
	   }else{
		   $one_sql = "SELECT * FROM " . table('addon8_article')." where id= {$_GP['id']} and state =6";
	   }
	   if(!is_mobile_request()){
		   //pc健康文化，有精选10个商品
		   $jp_goods = cs_goods('', 1, 4, 10);
	   }
   }else if($op == 'headline'){
	   //觅海头条
	   $psize =  10;
	   $pindex = max(1, intval($_GP["page"]));
	   $limit = ' limit '.($pindex-1)*$psize.','.$psize;
	   $title  = '觅海头条';
	   $sql    = "SELECT * FROM".table('headline')."  order by isrecommand desc,headline_id desc {$limit}";
	   $sqlnum = "SELECT count(headline_id) FROM".table('headline');
	   if(empty($_GP['id'])){
		   $one_sql = "SELECT * FROM " . table('headline')." order by headline_id desc limit 1 ";
	   }else{
		   $one_sql = "SELECT * FROM " . table('headline')." where headline_id= {$_GP['id']} ";
	   }

   }else if($op == 'note'){
	   //晒物笔记
	   $psize =  8;
	   $pindex = max(1, intval($_GP["page"]));
	   $limit = ' limit '.($pindex-1)*$psize.','.$psize;
	   $title  = '晒物笔记';
	   $sql    = "SELECT * FROM".table('note')." order by isrecommand desc,note_id desc {$limit}";
	   $sqlnum = "SELECT count(note_id) FROM".table('note');
	   if(empty($_GP['id'])){
		   $one_sql = "SELECT * FROM " . table('note')." order by note_id desc limit 1 ";
	   }else{
		   $one_sql = "SELECT * FROM " . table('note')." where note_id= {$_GP['id']} ";
	   }
   }

	$article_list =  mysqld_selectall($sql);
	$total        = mysqld_selectcolumn($sqlnum);
	$pager        = pagination($total, $pindex, $psize);

	//获取一篇文章
	$one_article  = mysqld_select($one_sql);
	$comment_num     = 0;
	$article_comment = '';
    if(!empty($one_article)){
		$one_article = get_article_member($one_article);

		if(!is_mobile_request() && $op == 'headline'){
			//pc觅海头条需要有 评论
			$comment_num     = mysqld_selectcolumn("select count(comment_id) from ".table('headline_comment')." where headline_id={$one_article['headline_id']}");
			//获取6条评论
			$article_comment = mysqld_selectall("select * from ".table('headline_comment')." where headline_id={$one_article['headline_id']} order by createtime desc limit 6");
			if(!empty($article_comment)){
				foreach($article_comment as $key => $item){
					$article_comment[$key] = get_article_member($item);
				}
			}
		}
	}


	//获取文章对应的用户名和头像
    if(!empty($article_list)){
		foreach($article_list as $key => &$item){
			$article_list[$key] = get_article_member($item);
		}
	}

    //当手机端滑动的时候加载下一页
	if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
		if ( empty($article_list) ){
			die(showAjaxMess(1002,'查无数据！'));
		}else{
			die(showAjaxMess(200,$article_list));
		}
	}

	if (is_mobile_request()){
		include addons_page('wap_article_list');
	}else{
		include addons_page('pc_article_list');
	}


