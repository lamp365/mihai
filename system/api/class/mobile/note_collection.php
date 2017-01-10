<?php
	/**
	 * app 图文笔记收藏接口
	 * @var unknown
	 *
	 */
	$result = array ();
	
	$op = $_GP ['op'];
	
	$member = get_member_account ( true, true );
	
	if (! empty ( $member ) and $member != 3) {
		
		switch ($op) {
			
			case 'remove' : 	// 删除收藏
				
				$note_id = intval($_GP['note_id']);
		
				if(empty($note_id))
				{
					$result ['message'] = '笔记ID不能为空';
					$result ['code'] 	= 0;
				}
				elseif(mysqld_delete("note_collection", array('openid' => $member['openid'],'note_id'=>$note_id))){
					
					$result['message'] 	= "取消收藏成功";
					$result['code'] 	= 1;
				}
				else{
					$result['message'] 	= "取消收藏失败";
					$result['code'] 	= 0;
				}
				
				break;
			
			case 'insert' : 	// 新增收藏
				
				$note_id = (int) $_GP ['note_id'];				//笔记ID
				
				if (empty($note_id)) {
						
					$result ['message'] = '笔记ID不能为空';
					$result ['code'] 	= 0;
				}
				else{
					$noteInfo = mysqld_select("SELECT note_id FROM " . table('note') . " where note_id=".$note_id.' and deleted=0 ');
						
					if(empty($noteInfo))
					{
						$result ['message'] = '笔记不存在';
						$result ['code'] 	= 0;
					}
					else{
						$data = array ('openid' 	=> $member ['openid'],
										'note_id' 	=> $note_id,
										'createtime'=> time()
										);
						
						//新增评论信息
						if (mysqld_insert ( 'note_collection', $data )) {
								
							$result ['message'] = "添加收藏成功";
							$result ['code'] 	= 1;
						} else {
							$result ['message'] = "添加收藏失败";
							$result ['code'] 	= 0;
						}
					}
				}
				
				break;
			
			default :			//收藏列表显示
				
				$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
				$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
				
				$sql = "SELECT SQL_CALC_FOUND_ROWS c.collection_id,n.note_id,n.openid,n.title,n.pic,m.nickname,m.avatar FROM " . table('note_collection') . " as c,".table('note') . " as n,".table('member')." as m ";
				$sql.= " WHERE n.note_id=c.note_id and n.openid=m.openid ";
				$sql.= " and c.openid='".$member ['openid']."' ";
				$sql.= " and n.deleted=0 order by n.createtime desc";
				$sql.= " limit ".(($page-1)*$limit).','.$limit;
				
				$arrNoteCollection = mysqld_selectall($sql);
				
				$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
				
				$result['data']['collection'] 	= $arrNoteCollection;
				$result['data']['total'] 		= $total['total'];
				$result['code'] 				= 1;
				
				break;
		}
	} elseif ($member == 3) {
		$result ['message'] = "该账号已在别的设备上登录";
		$result ['code'] = 3;
	} else {
		$result ['message'] = "用户还未登陆";
		$result ['code'] = 2;
	}
	
	echo apiReturn ( $result );
	exit ();
			