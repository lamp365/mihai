<?php
$op = empty($_GP['op']) ? 'healty' : $_GP['op'];
$cfg=globaSetting();

    $psize =  8;
	$pindex = max(1, intval($_GP["page"]));
    $limit = ' limit '.($pindex-1)*$psize.','.$psize;

   if($op == 'healty'){
	   //健康文化
	   $sql    = "SELECT * FROM".table('addon8_article')." where state =6  order by displayorder desc,id desc {$limit}";
	   $sqlnum = "SELECT count(id) FROM".table('addon8_article')." where state =6";
   }else if($op == 'headline'){
	   //觅海头条
	   $sql    = "SELECT * FROM".table('addon8_article')." where state =6  order by displayorder desc,id desc {$limit}";
	   $sqlnum = "SELECT count(id) FROM".table('addon8_article')." where state =6";
   }else if($op == 'note'){
	   //觅海笔记
	   $sql    = "SELECT * FROM".table('addon8_article')." where state =6  order by displayorder desc,id desc {$limit}";
	   $sqlnum = "SELECT count(id) FROM".table('addon8_article')." where state =6";
   }

	$article_list =  mysqld_selectall($sql);
	$total        = mysqld_selectcolumn($sqlnum);
	$pager        = pagination($total, $pindex, $psize);

	if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
		// error  1: 失败  0:成功
		if ( empty($article_list) ){
			$error = 1;
		}else{
			$error = 0;
		}
		$result = array(
			'info' => $error,
			'result'=> $article_list
		);
		echo  json_encode($result);
		exit;
	}

	if (is_mobile_request()){
		include addons_page('wap_article_list');
	}else{
		$jp_goods = cs_goods('', 1, 4, 10);
		include addons_page('pc_article_list');
	}


