<?php
$op = empty($_GP['op']) ? 'healty' : $_GP['op'];
$cfg=globaSetting();

    $psize =  5;
	$pindex = max(1, intval($_GP["page"]));
    $limit = ' limit '.($pindex-1)*$psize.','.$psize;

   if($op == 'healty'){
	   //健康文化
	   $title  = '健康文化';
	   $sql    = "SELECT * FROM".table('addon8_article')." where state =6  order by displayorder desc,id desc {$limit}";
	   $sqlnum = "SELECT count(id) FROM".table('addon8_article')." where state =6";
   }else if($op == 'headline'){
	   //觅海头条
	   $title  = '觅海头条';
	   $sql    = "SELECT * FROM".table('headline')."  order by isrecommand desc,headline_id desc {$limit}";
	   $sqlnum = "SELECT count(headline_id) FROM".table('headline');
   }else if($op == 'note'){
	   //晒物笔记
	   $title  = '晒物笔记';
	   $sql    = "SELECT * FROM".table('note')." order by isrecommand desc,note_id desc {$limit}";
	   $sqlnum = "SELECT count(note_id) FROM".table('note');
   }

	$article_list =  mysqld_selectall($sql);
	$total        = mysqld_selectcolumn($sqlnum);
	$pager        = pagination($total, $pindex, $psize);

	//获取文章对应的用户名和头像
    if(!empty($article_list)){
		foreach($article_list as $key => &$item){
			$article_list[$key] = get_article_member($item);
		}
	}

    //当手机端滑动的时候加载下一页
	if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
		// error  1: 失败  0:成功
		if ( empty($article_list) ){
			die(showAjaxMess(1002,'查无数据！'));
		}else{
			die(showAjaxMess(200,$article_list));
		}
	}

	if (is_mobile_request()){
		include addons_page('wap_article_list');
	}else{
		$jp_goods = cs_goods('', 1, 4, 10);
		include addons_page('pc_article_list');
	}


