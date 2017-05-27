<?php
	/**
	 * app 健康文化收藏接口
	 * @var unknown
	 *
	 */
	$result = array ();
	
	$op = $_GP ['op'];
	
	$member = get_member_account ( true, true );
	
	if (! empty ( $member ) and $member != 3) {
		
		switch ($op) {
			
			case 'remove' : 	// 删除收藏
				
				$article_id = intval($_GP['article_id']);
		
				if(empty($article_id))
				{
					$result ['message'] = '健康文化ID不能为空';
					$result ['code'] 	= 0;
				}
				elseif(mysqld_delete("article_collection", array('openid' => $member['openid'],'article_id'=>$article_id))){
					
					$result['message'] 	= "取消收藏成功。";
					$result['code'] 	= 1;
				}
				else{
					$result['message'] 	= "取消收藏失败。";
					$result['code'] 	= 0;
				}
				
				break;
			
			case 'insert' : 	// 新增收藏
				
				$article_id = (int) $_GP ['article_id'];				//文章ID
				
				if (empty($article_id)) {
						
					$result ['message'] = '健康文化ID不能为空';
					$result ['code'] 	= 0;
				}
				else{
					$articleInfo = mysqld_select("SELECT id FROM " . table('addon8_article') . " where id=".$article_id.' and state=6 ');
					
					if(empty($articleInfo))
					{
						$result ['message'] = '健康文化不存在';
						$result ['code'] 	= 0;
					}
					else{
						$data = array ('openid' 		=> $member ['openid'],
										'article_id' 	=> $article_id,
										'createtime'	=> time());
							
						//新增收藏信息
						if (mysqld_insert ( 'article_collection', $data )) {
								
							$result ['message'] = "添加收藏成功。";
							$result ['code'] 	= 1;
						} else {
							$result ['message'] = "添加收藏失败。";
							$result ['code'] 	= 0;
						}
					}
				}
				
				break;
			
			default :			//收藏列表显示
				
				$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
				$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
				
				$sql = "SELECT SQL_CALC_FOUND_ROWS c.collection_id,a.id,a.openid,a.title,a.thumb,m.nickname,m.avatar FROM " . table('article_collection') . " as c,".table('addon8_article') . " as a,".table('member')." as m ";
				$sql.= " WHERE a.id=c.article_id and a.openid=m.openid ";
				$sql.= " and c.openid='".$member ['openid']."' ";
				$sql.= " order by a.createtime desc";
				$sql.= " limit ".(($page-1)*$limit).','.$limit;

				$arrArticleCollection = mysqld_selectall($sql);
				
				$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
				
				$result['data']['collection'] 	= $arrArticleCollection;
				$result['data']['total'] 		= $total['total'];
				$result['code'] 				= 1;
				
				break;
		}
	} elseif ($member == 3) {
		$result ['message'] = "该账号已在别的设备上登录！";
		$result ['code'] = 3;
	} else {
		$result ['message'] = "用户还未登陆。";
		$result ['code'] = 2;
	}
	
	echo apiReturn ( $result );
	exit ();
			