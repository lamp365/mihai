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
			include addons_page('comment_list');
		}

  	 	 