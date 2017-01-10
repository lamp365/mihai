<?php
	/**
	 * app 头条收藏接口
	 * @var unknown
	 *
	 */
	$result = array ();
	
	$op = $_GP ['op'];
	
	$member = get_member_account ( true, true );
	
	if (! empty ( $member ) and $member != 3) {
		
		switch ($op) {
			
			case 'remove' : 	// 删除收藏
				
				$headline_id = intval($_GP['headline_id']);
		
				if(empty($headline_id))
				{
					$result ['message'] = '头条ID不能为空';
					$result ['code'] 	= 0;
				}
				elseif(mysqld_delete("headline_collection", array('openid' => $member['openid'],'headline_id'=>$headline_id))){
					
					$result['message'] 	= "取消收藏成功";
					$result['code'] 	= 1;
				}
				else{
					$result['message'] 	= "取消收藏失败";
					$result['code'] 	= 0;
				}
				
				break;
			
			case 'insert' : 	// 新增收藏
				
				$headline_id = (int) $_GP ['headline_id'];				//头条ID
				
				if (empty($headline_id)) {
						
					$result ['message'] = '头条ID不能为空';
					$result ['code'] 	= 0;
				}
				else{
					$headlineInfo = mysqld_select("SELECT headline_id FROM " . table('headline') . " where headline_id=".$headline_id.' and deleted=0 ');
					
					if(empty($headlineInfo))
					{
						$result ['message'] = '觅海头条不存在';
						$result ['code'] 	= 0;
					}
					else{
						$data = array ('openid' 		=> $member ['openid'],
										'headline_id' 	=> $headline_id,
										'createtime'	=> time()
						);
							
						//新增收藏信息
						if (mysqld_insert ( 'headline_collection', $data )) {
								
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
				
				$sql = "SELECT SQL_CALC_FOUND_ROWS c.collection_id,h.headline_id,h.openid,h.title,h.pic,m.nickname,m.avatar FROM " . table('headline_collection') . " as c,".table('headline') . " as h,".table('member')." as m ";
				$sql.= " WHERE h.headline_id=c.headline_id and h.openid=m.openid ";
				$sql.= " and c.openid='".$member ['openid']."' ";
				$sql.= " and h.deleted=0 order by h.createtime desc";
				$sql.= " limit ".(($page-1)*$limit).','.$limit;

				$arrHeadlineCollection = mysqld_selectall($sql);
				
				$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
				
				$result['data']['collection'] 	= $arrHeadlineCollection;
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
			