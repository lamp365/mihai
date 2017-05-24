<?php
	/**
	 * app 图文笔记评论操作接口
	 * @var unknown
	 *
	 */

	$result = array();
	
	$op = $_GP['op'];
	
	//图文笔记评论列表(无需登录也能查看)
	if($op=='list')
	{
		$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
		$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
		$note_id= intval($_GP['note_id']);						//笔记ID
		
		if (empty($note_id)) {
			
			$result ['message'] = '笔记ID不能为空';
			$result ['code'] 	= 0;
		}
		else{
			
			$sql = "SELECT SQL_CALC_FOUND_ROWS n.comment_id,n.note_id,n.openid,n.comment,n.at_openid,n.createtime,m.nickname,m.avatar,at_m.nickname as at_nickname FROM " . table('note_comment') . " as n ";
			$sql.= " left join ".table('member')." as m on m.openid =n.openid ";
			$sql.= " left join ".table('member')." as at_m on at_m.openid =n.at_openid ";
			$sql.= " WHERE n.note_id={$note_id} order by n.createtime desc";
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
					$note_id 	= (int) $_GP ['note_id'];				//笔记ID
					
					if (empty($note_id)) {
							
						$result ['message'] = '笔记ID不能为空';
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
										'note_id' 	=> $note_id,
										'createtime'=> time()
						);

						// 有@用户ID时
						if (! empty ( $at_openid )) {
							$data ['at_openid'] = $at_openid;
						}
								
						//新增评论信息
						if (mysqld_insert ( 'note_comment', $data )) {
					
								$result ['data']['comment_id'] 	= mysqld_insertid();		//评论ID
								$result ['message'] 			= "评论新增成功";
								$result ['code'] 				= 1;
						} else {
								$result ['message'] = "评论新增失败";
								$result ['code'] 	= 0;
						}
				
					}
					
					break;
		
				case 'remove':		//删除评论
		
					$comment_id = intval($_GP['comment_id']);
		
					mysqld_delete("note_comment", array('openid' => $member['openid'],'comment_id'=>$comment_id));

					$result['message'] 	= "删除评论成功";
					$result['code'] 	= 1;
		
					break;
		
				default:

					$result['message'] 	= '操作不合法';
					$result['code'] 	= 2;
		
					break;
			}
		}elseif ($member == 3) {
			$result['message'] 	= "该账号已在别的设备上登录";
			$result['code'] 	= 3;
		}
		else{
			$result['message'] 	= "用户还未登陆";
			$result['code'] 	= 2;
		}
	}
	
	echo apiReturn($result);
	exit;
			