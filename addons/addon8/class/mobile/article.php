<?php
		$member = get_member_account(false);
		$op = empty($_GP['op']) ? 'display' : $_GP['op'];
		if($op == 'display'){
			$article = mysqld_select("SELECT * FROM " . table('addon8_article')." where id=:id ",array(":id"=>intval($_GP['id'])) );
			//把文章内容中有关联的商品解析出来
			$article['content'] = analyzeShopContent($article['content']);
			if(!empty($article['id'])){
				mysqld_update('addon8_article',array('readcount'=>intval($article['readcount'])+1),array('id'=>intval($_GP['id'])));
			}
			$cfg=globaSetting();
			$article_comment = $is_guanzhu = '';
			$notApp = true;
			if(!empty($_GP['is_app'])){
				//页面部分被app嵌套，故需要标记识别是app访问
				$notApp = false;
				//查找是否已经关注
				if(!empty($member['openid'])){
					$info = mysqld_select("select follow_id from ".table('follow')." where follower_openid='{$member['openid']}' and followed_openid='{$article['openid']}'");
					if(!empty($info))
						$is_guanzhu = true;
				}
			}

			if (is_mobile_request()){
				if($article['state'] == '6'){
					//获取评论
					$article_comment = mysqld_selectall("select * from ".table('article_comment')." where article_id={$_GP['id']} order by istop desc,comment_id desc limit 3");
					if(!empty($article_comment)){
						foreach($article_comment as $key=>$item){
							$article_comment[$key] = get_article_member($item);
						}
					}
				}
				if(empty($article['mobileTheme'])){
					include addons_page('wap_article');
				}else{
					include addons_page('wap_article'.$article['mobileTheme']);
				}
			}else{
				if($article['state'] == '1'){
					//如果是底部显示的文章，需要加载一些 底部的分类
					$article_foot = getArticle(4,1);
					$artile_tree  =  get_artile_tree($article_foot);
					$json_artile_tree = json_encode($artile_tree);
				}
				if($article['state'] == '6'){
					//如果是健康系列的文章，右侧要放一些其他关联的文章或者商品
					$tuijian_article = mysqld_selectall("SELECT * FROM".table('addon8_article')." where state =6 and (iscommend = 1 or ishot = 1) order by displayorder desc,id desc limit 4");
					$tuijian_shop    = cs_goods('','',4,8);
				}
				$theme = 'default';
				include addons_page('pc_article');
			}
			tosaveloginfrom();
		}else if($op == 'guanzhu'){  //关注
			$openid         = $_GP['openid'];
			$article_openid = $_GP['article_openid'];
			//是否已经关注过
			$info = mysqld_select("select follow_id from ".table('follow')." where followed_openid={$article_openid} and follower_openid = {$openid}");
			if(!empty($info)){
				die(showAjaxMess('200','你已经关注过'));
			}
			$data = array(
				'followed_openid' => $article_openid,
				'follower_openid' => $openid,
				'createtime'      => time(),
				'modifiedtime'    => time(),
			);
			mysqld_insert('follow',$data);
			if($last_id = mysqld_insertid()){
				//是否被对方关注过
				$info = mysqld_select("select follow_id from ".table('follow')." where followed_openid={$openid} and follower_openid = {$article_openid}");
				if(!empty($info)){
					//更新为互相关注
					mysqld_update('follow',array('mutual_attention'=>'1'),array('follow_id'=>$last_id));
					mysqld_update('follow',array('mutual_attention'=>'1'),array('follow_id'=>$info['follow_id']));
				}
				die(showAjaxMess('200','关注成功！'));
			}else{
				die(showAjaxMess('1002','关注失败！'));
			}

		}else if($op == 'comment_list'){ //评论更多
			$psize  =  5;
			$pindex = max(1, intval($_GP["page"]));
			$limit  = ' limit '.($pindex-1)*$psize.','.$psize;
			$table         = $_GP['table'];
			$table_comment = $table."_comment";
			//文章评论  有三种类型 各有三种表
			switch($table){
				case 'article':
					$sql = "select article_id as id , comment_id,openid,at_openid,createtime,comment from ".table($table_comment)." where article_id={$_GP['id']} order by istop desc,comment_id desc ".$limit;
					break;
				case 'note':
					$sql = "select note_id as id, comment_id,openid,at_openid,createtime from ".table($table_comment)."  where note_id={$_GP['id']} order by comment_id desc ".$limit;
					break;
				case 'headline':
					$sql = "select headline_id as id,comment_id,openid,at_openid,createtime from ".table($table_comment)."  where headline_id={$_GP['id']} order by comment_id desc ".$limit;

			}

			$comment_list = mysqld_selectall($sql);
			//获取文章对应的用户名和头像
			if(!empty($comment_list)){
				foreach($comment_list as $key => &$item){
					$comment_list[$key] = get_article_member($item);
				}
			}

			//当手机端滑动的时候加载下一页
			if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
				// error  1: 失败  0:成功
				if ( empty($comment_list) ){
					die(showAjaxMess(1002,'查无数据！'));
				}else{
					die(showAjaxMess(200,$comment_list));
				}
			}
			include addons_page('wap_comment_list');

		}else if($op == 'note'){ //笔记内容页面
			if(empty($_GP['id'])){
				$sql = "SELECT * FROM " . table('note')." order by note_id desc limit 1 ";
			}else{
				$sql = "SELECT * FROM " . table('note')." where note_id= {$_GP['id']} ";
			}
			$article_note   = mysqld_select($sql);
			if(empty($article_note))
				message('对不起，该文章已不存在！',refresh(),'error');

			$article_member = member_get($article_note['openid']);
			//收藏数
			$collect_num = mysqld_selectcolumn("select count(collection_id) from ".table('note_collection')." where note_id={$_GP['id']}");
			//获取三条评论
			$article_comment = mysqld_selectall("select * from ".table('note_comment')." where note_id={$_GP['id']} order by createtime desc limit 3");

			if (is_mobile_request()){
				include addons_page('wap_note');
			}else{
				include addons_page('pc_note');
			}
		}else if($op == 'headline'){ //头条内容页面
			if(empty($_GP['id'])){
				$sql = "SELECT * FROM " . table('headline')." order by headline_id desc limit 1 ";
			}else{
				$sql = "SELECT * FROM " . table('headline')." where headline_id= {$_GP['id']} ";
			}
			$article_headline = mysqld_select($sql);
			if(empty($article_headline))
				message('对不起，该文章已不存在！',refresh(),'error');
			$article_member = mysqld_select("select * from ".table('user')." where id={$article_headline['uid']}");
			//收藏数
			$collect_num = mysqld_selectcolumn("select count(collection_id) from ".table('headline_collection')." where headline_id={$_GP['id']}");

			if (is_mobile_request()){
				include addons_page('wap_headline');
			}else{
				include addons_page('pc_headline');
			}
		}else if($op == 'ajax_note'){
			if(empty($_GP['id'])){
				$sql = "SELECT * FROM " . table('note')." order by note_id desc limit 1 ";
			}else{
				$sql = "SELECT * FROM " . table('note')." where note_id= {$_GP['id']} ";
			}
			$article_note   = mysqld_select($sql);
			if(empty($article_note))
				message('对不起，该文章已不存在！',refresh(),'error');

			//获取文章的用户头像
			$article_note = get_article_member($article_note);
			//评论数目
			$article_note['comment_num']     = mysqld_selectcolumn("select count(comment_id) from ".table('note_comment')." where note_id={$_GP['id']}");
			//获取10条评论
			$article_comment = mysqld_selectall("select * from ".table('note_comment')." where note_id={$_GP['id']} order by createtime desc limit 10");
			//获取评论的用户头像
			if(!empty($article_comment)){
				foreach($article_comment as $key => $item){
					$article_comment[$key] = get_article_member($item);
				}
			}

			$article_note['article_comment'] = $article_comment;
			die(showAjaxMess(200,$article_note));

		}else if($op == 'del_comment'){
			//删除评论
			$table         = $_GP['table'];
			$table_comment = $table."_comment";
			$res           = '';
			switch($table){
				case 'article':
					$res = mysqld_delete($table_comment,array('comment_id'=>$_GP['comment_id']));
					break;
				case 'note':
					$res = mysqld_delete($table_comment,array('comment_id'=>$_GP['comment_id']));
					break;
				case 'headline':
					$res = mysqld_delete($table_comment,array('comment_id'=>$_GP['comment_id']));
					break;
			}
			if($res){
				die(showAjaxMess('200','删除成功！'));
			}else{
				die(showAjaxMess(1002,'删除失败！'));
			}

		}else if($op = 'ajax_articleComment'){
			//wap端只要三条显示
			$comment = '';
			if(!empty($_GP['id'])){
				$comment = mysqld_selectall("select * from ".table('article_comment')." where article_id={$_GP['id']} order by comment_id desc limit 3");
				if(!empty($comment)){
					foreach($comment as $key => $row){
						$comment[$key] = get_article_member($row);
					}
				}
			}
			die(showAjaxMess(200,$comment));
		}

  	 	 