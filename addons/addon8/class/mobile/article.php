<?php
		$op = empty($_GP['op']) ? 'display' : $_GP['op'];
		if($op == 'display'){
			$article = mysqld_select("SELECT * FROM " . table('addon8_article')." where id=:id ",array(":id"=>intval($_GP['id'])) );
			//把文章内容中有关联的商品解析出来
			$article['content'] = analyzeShopContent($article['content']);
			if(!empty($article['id'])){
				mysqld_update('addon8_article',array('readcount'=>intval($article['readcount'])+1),array('id'=>intval($_GP['id'])));
			}
			$cfg=globaSetting();
			$article_comment = '';
			$notApp = true;
			if(!empty($_GP['is_app'])){
				//页面部分被app嵌套，故需要标记识别是app访问
				$notApp = false;
			}

			if (is_mobile_request()){
				if($article['state'] == '6'){
					//获取评论
					$article_comment = mysqld_selectall("select * from ".table('article_comment')." where article_id={$_GP['id']} order by istop desc,comment_id desc limit 3");
				}
				if(empty($article['mobileTheme'])){
					include addons_page('article');
				}else{
					include addons_page('article'.$article['mobileTheme']);
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
				include addons_page('webarticle');
			}
			tosaveloginfrom();
		}else if($op == 'guanzhu'){  //关注

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
			//当手机端滑动的时候加载下一页
			if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
				// error  1: 失败  0:成功
				if ( empty($comment_list) ){
					die(showAjaxMess(1002,'查无数据！'));
				}else{
					die(showAjaxMess(200,$comment_list));
				}
			}
			include addons_page('comment_list');

		}else if($op == 'note'){ //笔记内容页面
			$article_note   = mysqld_select("SELECT * FROM " . table('note')." where note_id=:id ",array(":id"=>intval($_GP['id'])) );
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
			$article_headline = mysqld_select("SELECT * FROM " . table('headline')." where headline_id=:id ",array(":id"=>intval($_GP['id'])) );
			$article_member = member_get($article_headline['openid']);
			//收藏数
			$collect_num = mysqld_selectcolumn("select count(collection_id) from ".table('headline_collection')." where headline_id={$_GP['id']}");
			//获取三条评论
			$article_comment = mysqld_selectall("select * from ".table('headline_comment')." where note_id={$_GP['id']} order by createtime desc limit 3");
			if (is_mobile_request()){
				include addons_page('wap_headline');
			}else{
				include addons_page('pc_headline');
			}
		}

  	 	 