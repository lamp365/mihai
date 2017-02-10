<?php
	/**
	 * app 图文笔记操作接口
	 * @var unknown
	 *
	 */

	$result = array();
	
	$op = $_GP['op'];
	
	$member=get_member_account(true,true);
	
	//图文笔记列表(无需登录也能查看)
	if($op=='list')
	{
		$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
		$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
		$openid = checkIsLogin();								// 账户验证
	
		$sql = "SELECT SQL_CALC_FOUND_ROWS n.note_id,n.openid,n.title,n.pic,n.description,n.address,n.createtime,m.nickname,m.avatar FROM " . table('note') . " as n,".table('member')." as m ";
		$sql.= " WHERE n.openid=m.openid and n.deleted=0 ";
		
		//推荐文章
		if(isset($_GP['isrecommand']))
		{
			$sql.= " and n.isrecommand= ".(int)$_GP['isrecommand'];
		}
		
		if(!empty($openid))
		{
			$sql.= " and (n.`check`=1 or n.openid='".$openid."') ";
		}
		else{
			$sql.= " and n.`check`=1 ";
		}
		
		$sql.= " order by n.createtime desc";
		$sql.= " limit ".(($page-1)*$limit).','.$limit;

		$arrNote = mysqld_selectall($sql);
		
		$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
		
		if(!empty($arrNote))
		{
			foreach($arrNote as $key => $value)
			{
				$arrNote[$key]['collectionCnt']	= getNoteCollectionCount($value['note_id']);			    //收藏数
				$arrNote[$key]['isCollection']	= isCollection($value['note_id'],$member);				//是否已收藏
				// 内容截取
				$arrNote[$key]['description'] = msubstr($value['description'],0,120);
			}
		}
		
		$result['data']['note'] = $arrNote;
		$result['data']['total']= $total['total'];
		$result['code'] 		= 1;
	}
	//他人笔记列表
	elseif($op=='other_list')
	{
		$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
		$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
		$openid = trim($_GP ['openid']);						//他人的用户ID
		
		if(empty($openid))
		{
			$result ['message'] = '他人的用户ID不能为空';
			$result ['code'] 	= 0;
		}
		else{
			$sql = "SELECT SQL_CALC_FOUND_ROWS n.note_id,n.openid,n.title,n.pic,n.description,n.address,n.createtime,m.nickname,m.avatar FROM " . table('note') . " as n,".table('member')." as m ";
			$sql.= " WHERE n.openid=m.openid and n.deleted=0 ";
			$sql.= " and n.openid='".$openid."' ";
			$sql.= " and n.`check`=1 ";
			$sql.= " order by n.createtime desc";
			$sql.= " limit ".(($page-1)*$limit).','.$limit;
			
			$arrNote = mysqld_selectall($sql);
			
			$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
			
			if(!empty($arrNote))
			{
				foreach($arrNote as $key => $value)
				{
					$arrNote[$key]['collectionCnt']	= getNoteCollectionCount($value['note_id']);					//收藏数
					$arrNote[$key]['isCollection']	= isCollection($value['note_id'],$member);				//是否已收藏
					// 内容截取
				     $arrNote[$key]['description'] = msubstr($value['description'],0,120);
				}
			}
			
			$result['data']['note'] = $arrNote;
			$result['data']['total']= $total['total'];
			$result['code'] 		= 1;
		}
	}
	//笔记详情
	elseif($op=='detail')
	{
		$note_id = intval($_GP['note_id']);
		
		//note_id为空时
		if(empty($note_id))
		{
			$result ['message'] = '笔记ID不能为空';
			$result ['code'] 	= 0;
		}
		else{
			$sql = "SELECT n.note_id,n.openid,n.title,n.description,n.pic,n.address,n.createtime,m.nickname,m.avatar FROM " . table('note') . " as n,".table('member')." as m ";
			$sql.= " WHERE n.openid=m.openid and n.note_id={$note_id} ";
			$sql.= " and n.deleted=0 ";
			$sql.= " order by n.createtime desc ";
			
			$noteInfo = mysqld_select($sql);
			
			//笔记信息不存在时
			if(empty($noteInfo))
			{
				$result ['message'] = '笔记不存在';
				$result['code'] 	= 0;
			}
			else{
				$commentCnt 	= mysqld_select("SELECT count(comment_id) cnt FROM " . table('note_comment') . " where note_id={$note_id} ");
				
				$noteInfo['isFollow']		= isFollowed ( $noteInfo ['openid'],$member );		//是否已经被关注
				$noteInfo['isCollection']	= isCollection($note_id,$member);					//是否已收藏
				$noteInfo['collectionCnt']	= getNoteCollectionCount($note_id);					//收藏数
				$noteInfo['commentCnt']		= $commentCnt['cnt'];								//评论数
				$noteInfo['share_url']		= getArticleUrl($note_id,'note');
				
				$result['data']['noteInfo'] 	= $noteInfo;
				$result['code'] 				= 1;
			}
		}
	}
	else{
		
		if(!empty($member) AND $member != 3)
		{
			switch($op)
			{
				case 'insert':			//新增笔记
					
					$objValidator = new Validator();
						
					$title 			= trim($_GP ['title']);
					$description 	= trim ( $_GP ['description'] );
						
					// 标题
					if ($title == '') {
					
						$result ['message'] = '标题不能为空';
						$result ['code'] 	= 0;
					
					} elseif ($description == '') {
					
						$result ['message'] = '内容不能为空';
						$result ['code'] 	= 0;
					
					} elseif (!empty ( $title ) && !$objValidator->lengthValidator($title, '0,30')) {
					
						$result ['message'] = '标题最多输入30个字';
						$result ['code'] 	= 0;
						
					} else {
						
						$data = array ('openid'			=> $member['openid'],
										'title' 		=> $title,
										'description' 	=> $description,
										'address'		=> trim($_GP ['address']),
										'createtime' 	=> time(),
										'modifiedtime' 	=> time()
						);

						$arrFile = array ();
								
						for($i = 1; $i <= 5; $i ++) {
							// 图片上传成功时
							if (isset($_FILES['pic'. $i]) && $_FILES ['pic' . $i] ['error'] == 0) {
										
								$upload = file_upload ( $_FILES ['pic' . $i] );
										
								// 向七牛上传成功时
								if (! is_error ( $upload )) {
					
									$arrFile [] = $upload ['path'];
								}
							}
						}
								
						// 有图片时
						if (! empty ( $arrFile )) {
							$data ['pic'] = implode ( ";", $arrFile );
						}
						else{
							$data ['pic'] = '';
						}
								
						//新增笔记信息
						if (mysqld_insert ( 'note', $data )) {
					
								$result ['message'] 			= "笔记新增成功";
								$result ['data']['share_url'] 	= getArticleUrl(mysqld_insertid(),'note');
								$result ['data']['pic'] 		= $data ['pic'];
								$result ['code'] 				= 1;
						} else {
								$result ['message'] = "笔记新增失败。";
								$result ['code'] 	= 0;
						}
				
					}
					
					break;
		
				case 'update':			//笔记更新
		
					$objValidator = new Validator();
						
					$title 			= trim($_GP ['title']);
					$description 	= trim ( $_GP ['description'] );
					$note_id 		= intval($_GP['note_id']);

					// 标题
					if ($title == '') {
					
						$result ['message'] = '标题不能为空';
						$result ['code'] 	= 0;
					
					} elseif ($description == '') {
					
						$result ['message'] = '内容不能为空';
						$result ['code'] 	= 0;
					
					} elseif (!empty ( $title ) && !$objValidator->lengthValidator($title, '0,30')) {
					
						$result ['message'] = '标题最多输入30个字';
						$result ['code'] 	= 0;
						
					} else {
						
						$data = array ('title' 			=> $title,
										'description' 	=> $description,
										'address'		=> trim($_GP ['address']),
										'modifiedtime' 	=> time());
						
						$arrFile = json_decode($_REQUEST ['pic_url'], true);		//图片;
						
						for($i = 1; $i <= 5; $i ++) {
							// 上传成功时
							if (isset($_FILES['pic'. $i]) && $_FILES ['pic' . $i] ['error'] == 0) {
						
								$upload = file_upload ( $_FILES ['pic' . $i] );
						
								// 向七牛上传成功时
								if (! is_error ( $upload )) {
										
									$arrFile [] = $upload ['path'];
								}
							}
						}
						
						// 有图片时
						if (! empty ( $arrFile )) {
							$data ['pic'] = implode ( ";", $arrFile );
						}
						else{
							$data ['pic'] = '';
						}
						
						//更新笔记信息
						if(mysqld_update ( 'note', $data ,array('note_id' =>$note_id,'openid'=>$member['openid'],'deleted'=>0)))
						{
							$result ['message'] 			= "笔记编辑成功";
							$result ['data']['share_url'] 	= getArticleUrl($note_id,'note');
							$result ['data']['pic'] 		= $data ['pic'];
							$result ['code'] 				= 1;
						}
						else{
							$result ['message'] = "笔记编辑失败";
							$result ['code'] 	= 0;
						}
					}
		
					break;
		
				case 'remove':		//笔记删除
		
					$note_id = intval($_GP['note_id']);
		
					$noteInfo = mysqld_select("SELECT note_id FROM " . table('note') . " WHERE deleted=0 and openid =:openid and note_id=:note_id", array(':openid' => $member['openid'],':note_id'=>$note_id));
		
					//笔记信息不存在时
					if(empty($noteInfo))
					{
						$result['message'] 	= "笔记信息不存在";
						$result['code'] 	= 0;
					}
					else{
						mysqld_update('note', array('deleted' => 1), array('note_id' => $note_id));
							
						$result['message'] 	= "删除笔记成功";
						$result['code'] 	= 1;
					}
		
					break;
					
				case 'self_list':		//自己创建的笔记列表
					
					$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
					$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
					
					$sql = "SELECT SQL_CALC_FOUND_ROWS n.note_id,n.openid,n.title,n.pic,n.createtime,n.description,n.address,m.nickname,m.avatar FROM " . table('note') . " as n,".table('member')." as m ";
					$sql.= " WHERE n.openid=m.openid and n.openid='".$member['openid']."'";
					$sql.= " and n.deleted=0 ";
					$sql.= " order by n.createtime desc ";
					$sql.= " limit ".(($page-1)*$limit).','.$limit;
					
					$arrNote = mysqld_selectall($sql);
					
					$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
						
					if(!empty($arrNote))
					{
						foreach($arrNote as $key => $value)
						{
							$arrNote[$key]['collectionCnt']	= getNoteCollectionCount($value['note_id']);					//收藏数
							// 内容截取
				            $arrNote[$key]['description'] = msubstr($value['description'],0,120);
						}
					}
					
					
					
					$result['data']['note'] = $arrNote;
					$result['data']['total']= $total['total'];
					$result['code'] 		= 1;
					
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

	
	/**
	 * 笔记是否已经收藏
	 *
	 * @param $note_id:int 笔记ID
	 * @param $member:用户登录信息
	 *
	 * @return boolean
	 */
	function isCollection($note_id,$member)
	{
		//已登录
		if(!empty($member) AND $member != 3)
		{
			//收藏信息
			$collection = mysqld_select("SELECT collection_id FROM " . table('note_collection') . " where note_id={$note_id} and openid=:openid",array(':openid' => $member['openid']));
				
			//未收藏时
			if(empty($collection))
			{
				return 0;
			}
			else{
				return 1;
			}
		}
		//未登录
		else{
			return 0;
		}
	}
	
	/**
	 * 笔记的收藏数量
	 * 
	 * @param $note_id:int 笔记ID
	 */
	function getNoteCollectionCount($note_id)
	{
		$collectionCnt 	= mysqld_select("SELECT count(collection_id) cnt FROM " . table('note_collection') . " where note_id={$note_id} ");
		
		return $collectionCnt['cnt'];							//收藏数
	}