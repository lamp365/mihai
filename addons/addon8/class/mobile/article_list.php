<?php
$op = empty($_GP['op']) ? 'healty' : $_GP['op'];
$cfg=globaSetting();
if($_GP['id'] && $op !== 'healty' && is_mobile_request()){
	$parame = array('id' => $_GP['id'],'name'=>'addon8','do'=>'article','op'=>$op);
	$url = create_url('mobile', $parame);
	header("location:{$url}");
}
   if($op == 'healty'){
	   //健康文化
	   $psize =  12;
	   $pindex = max(1, intval($_GP["page"]));
	   $limit = ' limit '.($pindex-1)*$psize.','.$psize;
	   $title  = '健康文化';
	   $sql    = "SELECT * FROM".table('addon8_article')." where state =6  order by displayorder desc,id desc {$limit}";
	   $sqlnum = "SELECT count(id) FROM".table('addon8_article')." where state =6";
	   if(!is_mobile_request()){
		   //pc健康文化，有精选10个商品
		   $jp_goods = cs_goods('', 1, 4, 10);
	   }
   }else if($op == 'headline'){
	   //觅海头条
	   //取出8个带有视频的觅海头条文章  这里不应该用video为空不为空来获取视频文章，后续应该在后台做修改，加一个类型表示视频文章
	   $video_sql     = "SELECT * FROM ".table('headline')."  where ischeck=1 and video !='' and deleted=0 order by isrecommand desc,headline_id desc limit 8";
	   $video_article = mysqld_selectall($video_sql);
	   //取出后台设置的banner主图
	   $banner_sql    = "select * from ".table('app_banner')." where position=4 order by banner_id desc";
	   $banner        = mysqld_select($banner_sql);

	   $psize =  10;
	   $pindex = max(1, intval($_GP["page"]));
	   $limit = ' limit '.($pindex-1)*$psize.','.$psize;
	   $title  = '觅海头条';
	   $sql    = "SELECT * FROM ".table('headline')."  where ischeck=1 and video=''  and deleted=0 order by isrecommand desc,headline_id desc {$limit}";
	   $sqlnum = "SELECT count(headline_id) FROM".table('headline');

   }else if($op == 'note'){
	   //晒物笔记
	   $psize =  8;
	   $pindex = max(1, intval($_GP["page"]));
	   $limit = ' limit '.($pindex-1)*$psize.','.$psize;
	   $title  = '晒物笔记';
	   $sql    = "SELECT * FROM".table('note')." where check=1 and deleted=0 order by isrecommand desc,note_id desc {$limit}";
	   $sqlnum = "SELECT count(note_id) FROM".table('note');

   }else if($op=='headline_view'){
	   $psize  =  12;
	   $pindex = max(1, intval($_GP["page"]));
	   $limit  = ' limit '.($pindex-1)*$psize.','.$psize;
	   $sql    = "SELECT * FROM ".table('headline')."  where ischeck=1 and deleted=0 and video!='' order by isrecommand desc,headline_id desc {$limit}";
	   $video_list = mysqld_selectall($sql);
	   if(!empty($video_list)){
		   foreach($video_list as &$video){
			   $video['collent_num'] = mysqld_selectcolumn("select count(collection_id) from ".table('headline_collection')." where headline_id={$video['headline_id']}");
			   $article_member       = mysqld_select("select * from ".table('user')." where id={$video['uid']}");
			   $video['avatar']      = $article_member['avatar'];
			   $video['nickname']    = $article_member['nickname'];
		   }
	   }

	   //当手机端滑动的时候加载下一页
	   if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
		   if ( empty($video_list) ){
			   die(showAjaxMess(1002,'查无数据！'));
		   }else{
			   die(showAjaxMess(200,$video_list));
		   }
	   }

	   if (is_mobile_request()) {
		   include addons_page('wap_headline_view');
	   }
	   die();
   }

	$article_list =  mysqld_selectall($sql);
	$total        = mysqld_selectcolumn($sqlnum);
	$pager        = pagination($total, $pindex, $psize);



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


