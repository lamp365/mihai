<?php
	/**
	 * app 文章评论操作接口
	 * @var unknown
	 *
	 */

	$result = array();
	
	$op = $_GP['op'];
	
	//文章评论列表(无需登录也能查看)
	if($op=='list')
	{
		$page 		= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
		$limit 		= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
		$article_id	= intval($_GP['article_id']);					//健康文化ID
		
		if (empty($article_id)) {
			
			$result ['message'] = '健康文化ID不能为空';
			$result ['code'] 	= 0;
		}
		else{
			
			$sql = "SELECT SQL_CALC_FOUND_ROWS c.comment_id,c.article_id,c.openid,c.comment,c.at_openid,c.createtime,m.nickname,m.avatar,at_m.nickname as at_nickname FROM " . table('article_comment') . " as c ";
			$sql.= " left join ".table('member')." as m on m.openid =c.openid ";
			$sql.= " left join ".table('member')." as at_m on at_m.openid =c.at_openid ";
			$sql.= " WHERE c.article_id={$article_id} order by c.createtime desc";
			$sql.= " limit ".(($page-1)*$limit).','.$limit;
			
			$arrNoteComment = mysqld_selectall($sql);
			
			$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
			
			$result['data']['comment'] 	= $arrNoteComment;
			$result['data']['total'] 	= $total['total'];
			$result['code'] 			= 1;
		}
	}
	else{
		$member=get_member_account(true,true);
		
		if(!empty($member) AND $member != 3)
		{
			switch($op)
			{
				case 'insert':			//新增评论
					
					$objValidator = new Validator();
						
					$comment 	= trim($_GP ['comment']);				//评论
					$at_openid 	= trim ( $_GP ['at_openid'] );			//@用户ID
					$article_id = (int) $_GP ['article_id'];			//文章ID
					
					if (empty($article_id)) {
							
						$result ['message'] = '健康文化ID不能为空';
						$result ['code'] 	= 0;
					// 评论
					}elseif ($comment == '') {
					
						$result ['message'] = '评论不能为空';
						$result ['code'] 	= 0;
					
					} elseif (!empty ( $comment ) && !$objValidator->lengthValidator($comment, '0,30')) {
					
						$result ['message'] = '评论最多输入30个字';
						$result ['code'] 	= 0;
						
					} else {
						
						$data = array ('openid'		=> $member['openid'],
										'comment' 	=> $comment,
										'article_id'=> $article_id,
										'createtime'=> time()
						);

						// 有@用户ID时
						if (! empty ( $at_openid )) {
							$data ['at_openid'] = $at_openid;
						}
								
						//新增评论信息
						if (mysqld_insert ( 'article_comment', $data )) {
					
								$result ['data']['comment_id'] 	= mysqld_insertid();		//评论ID
								$result ['message'] 			= "评论新增成功。";
								$result ['code'] 				= 1;
						} else {
								$result ['message'] = "评论新增失败。";
								$result ['code'] 	= 0;
						}
				
					}
					
					break;
		
				case 'remove':		//删除评论
		
					$comment_id = intval($_GP['comment_id']);
		
					mysqld_delete("article_comment", array('openid' => $member['openid'],'comment_id'=>$comment_id));

					$result['message'] 	= "删除评论成功。";
					$result['code'] 	= 1;
		
					break;
		
				default:

					$result['message'] 	= '操作不合法';
					$result['code'] 	= 2;
		
					break;
			}
		}elseif ($member == 3) {
			$result['message'] 	= "该账号已在别的设备上登录！";
			$result['code'] 	= 3;
		}
		else{
			$result['message'] 	= "用户还未登陆。";
			$result['code'] 	= 2;
		}
	}
	
	echo apiReturn($result);
	exit;
			