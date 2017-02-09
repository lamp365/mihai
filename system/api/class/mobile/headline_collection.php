<?php
	/**
	 * app 头条收藏接口
	 * @var unknown
	 *
	 */
	$result = array ();
	
	$op = $_GP ['op'];
	
	$member = get_member_account ( true, true );

	$lastid = $_GP ['lastid'];
	
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
			
			case 'pic_list' :			//图片收藏列表显示
				
				$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
				$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
				$u_where = '';
				if (!empty($lastid)) {
					$page = 1;
					$u_where = " and c.collection_id<".$lastid;
				}
				
				$sql = "SELECT SQL_CALC_FOUND_ROWS c.collection_id,h.headline_id,h.uid,h.title,h.pic,h.description,h.preview,u.nickname,u.avatar FROM " . table('headline_collection') . " as c,".table('headline') . " as h,".table('user')." as u ";
				$sql.= " WHERE h.headline_id=c.headline_id and h.uid=u.id ";
				$sql.= " and c.openid='".$member ['openid']."' ".$u_where;
				$sql.= " and h.deleted=0 and (h.video IS NULL or h.video='') order by c.createtime desc";
				$sql.= " limit ".(($page-1)*$limit).','.$limit;

				$arrHeadlineCollection = mysqld_selectall($sql);
				
				$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
				
				if(!empty($arrHeadlineCollection))
				{
					foreach($arrHeadlineCollection as $key => $value)
					{
						$arrHeadlineCollection[$key]['collectionCnt']	= getHeadlineCollectionCount($value['headline_id']);			//收藏数
					}
				}
				
				$result['data']['collection'] 	= $arrHeadlineCollection;
				$result['data']['total'] 		= $total['total'];
				$result['code'] 				= 1;
				
				break;
				
			case 'video_list' :			//视频收藏列表显示
				
				$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
				$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
				$u_where = '';
				if (!empty($lastid)) {
					$page = 1;
					$u_where = " and c.collection_id<".$lastid;
				}
				
				$sql = "SELECT SQL_CALC_FOUND_ROWS c.collection_id,h.headline_id,h.uid,h.title,h.video,h.video_img,h.description,h.preview,u.nickname,u.avatar FROM " . table('headline_collection') . " as c,".table('headline') . " as h,".table('user')." as u ";
				$sql.= " WHERE h.headline_id=c.headline_id and h.uid=u.id ";
				$sql.= " and c.openid='".$member ['openid']."' ".$u_where;
				$sql.= " and h.deleted=0 and (h.pic IS NULL or h.pic='') order by c.createtime desc";
				$sql.= " limit ".(($page-1)*$limit).','.$limit;
			
				$arrHeadlineCollection = mysqld_selectall($sql);
				
				$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
				
				if(!empty($arrHeadlineCollection))
				{
					foreach($arrHeadlineCollection as $key => $value)
					{
						$arrHeadlineCollection[$key]['collectionCnt']	= getHeadlineCollectionCount($value['headline_id']);			//收藏数
					}
				}
				
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
	
	
	/**
	 * 头条的收藏数量
	 *
	 * @param $headline_id: int 头条ID
	 */
	function getHeadlineCollectionCount($headline_id)
	{
		$collectionCnt = mysqld_select("SELECT count(collection_id) cnt FROM " . table('headline_collection') . " where headline_id={$headline_id} ");
	
		return $collectionCnt['cnt'];							//收藏数
	}
			