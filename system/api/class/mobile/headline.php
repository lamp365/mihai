<?php
	/**
	 * app 觅海头条操作接口
	 * @var unknown
	 *
	 */

	$result = array();
	
	$op = $_GP['op'];
	
	$member=get_member_account(true,true);
	
	//觅海头条列表(无需登录也能查看)
	if($op=='list')
	{
		$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
		$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS h.headline_id,h.openid,h.title,h.pic,h.description,h.createtime,m.nickname,m.avatar FROM " . table('headline') . " as h,".table('member')." as m ";
		$sql.= " WHERE h.openid=m.openid and h.deleted=0 ";
		
		//推荐
		if(isset($_GP['isrecommand']))
		{
			$sql.= " and h.isrecommand= ".(int)$_GP['isrecommand'];
		}
		
		$sql.= " order by h.createtime desc";
		$sql.= " limit ".(($page-1)*$limit).','.$limit;
	
		$arrHeadline = mysqld_selectall($sql);
		
		$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
		
		$result['data']['headline'] = $arrHeadline;
		$result['data']['total'] 	= $total['total'];
		$result['code'] 			= 1;
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
			$sql = "SELECT SQL_CALC_FOUND_ROWS h.headline_id,h.openid,h.title,h.pic,h.description,h.createtime,m.nickname,m.avatar FROM " . table('headline') . " as h,".table('member')." as m ";
			$sql.= " WHERE h.openid=m.openid and h.deleted=0 ";
			$sql.= " and h.openid='".$openid."' ";
			$sql.= " order by h.createtime desc";
			$sql.= " limit ".(($page-1)*$limit).','.$limit;
			
			$arrHeadline = mysqld_selectall($sql);
			
			$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
			
			$result['data']['headline'] = $arrHeadline;
			$result['data']['total'] 	= $total['total'];
			$result['code'] 			= 1;
		}
	}
	//觅海头条详情
	elseif($op=='detail')
	{
		$headline_id = intval($_GP['headline_id']);
		
		//headline_id为空时
		if(empty($headline_id))
		{
			$result ['message'] = '头条ID不能为空';
			$result ['code'] 	= 0;
		}
		else{
			$sql = "SELECT h.headline_id,h.openid,h.title,h.description,h.pic,h.address,h.createtime,m.nickname,m.avatar FROM " . table('headline') . " as h,".table('member')." as m ";
			$sql.= " WHERE h.openid=m.openid and h.headline_id={$headline_id} ";
			$sql.= " and h.deleted=0 ";
			$sql.= " order by h.createtime desc ";
			
			$headlineInfo = mysqld_select($sql);
			
			//觅海头条信息不存在时
			if(empty($headlineInfo))
			{
				$result['message'] 	= '觅海头条不存在';
				$result['code'] 	= 0;
			}
			else{
				$collectionCnt = mysqld_select("SELECT count(collection_id) cnt FROM " . table('headline_collection') . " where headline_id={$headline_id} ");
				$commentCnt 	= mysqld_select("SELECT count(comment_id) cnt FROM " . table('headline_comment') . " where headline_id={$headline_id} ");
				
				$headlineInfo['isFollow'] 		= isFollowed( $headlineInfo ['openid'],$member);		//是否已关注
				$headlineInfo['isCollection'] 	= isCollection($headline_id,$member);					//是否已收藏
				$headlineInfo['collectionCnt'] 	= $collectionCnt['cnt'];								//收藏数
				$headlineInfo['commentCnt']		= $commentCnt['cnt'];									//评论数
				$headlineInfo['share_url'] 		= getArticleUrl($headline_id,'headline');
				
				$result['data']['headlineInfo'] = $headlineInfo;
				$result['code'] 				= 1;
			}
		}
	}
	else{
		
		if(!empty($member) AND $member != 3)
		{
			switch($op)
			{
				case 'insert':			//新增觅海头条
					
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
								
						for($i = 1; $i <= 3; $i ++) {
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
								
						//新增觅海头条信息
						if (mysqld_insert ( 'headline', $data )) {
					
								$result ['message'] 			= "觅海头条新增成功";
								$result ['data']['share_url'] 	= getArticleUrl(mysqld_insertid(),'headline');
								$result ['data']['pic'] 		= $data ['pic'];
								$result ['code'] 				= 1;
						} else {
								$result ['message'] = "觅海头条新增失败";
								$result ['code'] 	= 0;
						}
				
					}
					
					break;
		
				case 'update':			//头条更新
		
					$objValidator = new Validator();
						
					$title 			= trim($_GP ['title']);
					$description 	= trim ( $_GP ['description'] );
					$headline_id 	= intval($_GP['headline_id']);

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
						
						for($i = 1; $i <= 3; $i ++) {
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
						
						//更新觅海头条信息
						if(mysqld_update ( 'headline', $data ,array('headline_id' =>$headline_id,'openid'=>$member['openid'],'deleted'=>0)))
						{
							$result ['message'] 			= "觅海头条编辑成功";
							$result ['data']['share_url'] 	= getArticleUrl($headline_id,'headline');
							$result ['data']['pic'] 		= $data ['pic'];
							$result ['code'] 				= 1;
						}
						else{
							$result ['message'] = "觅海头条编辑失败";
							$result ['code'] 	= 0;
						}
					}
		
					break;
		
				case 'remove':		//头条删除
		
					$headline_id = intval($_GP['headline_id']);
		
					$headlineInfo = mysqld_select("SELECT headline_id FROM " . table('headline') . " WHERE deleted=0 and openid =:openid and headline_id=:headline_id", array(':openid' => $member['openid'],':headline_id'=>$headline_id));
		
					//头条信息不存在时
					if(empty($headlineInfo))
					{
						$result['message'] 	= "觅海头条信息不存在";
						$result['code'] 	= 0;
					}
					else{
						mysqld_update('headline', array('deleted' => 1), array('headline_id' => $headline_id));
							
						$result['message'] 	= "删除觅海头条成功";
						$result['code'] 	= 1;
					}
		
					break;
					
				case 'self_list':		//自己创建的觅海头条列表
					
					$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
					$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
					
					$sql = "SELECT SQL_CALC_FOUND_ROWS h.headline_id,h.openid,h.title,h.pic,h.description,h.createtime,m.nickname,m.avatar FROM " . table('headline') . " as h,".table('member')." as m ";
					$sql.= " WHERE h.openid=m.openid and h.openid='".$member['openid']."'";
					$sql.= " and h.deleted=0 ";
					$sql.= " order by h.createtime desc ";
					$sql.= " limit ".(($page-1)*$limit).','.$limit;
			
					$arrHeadline = mysqld_selectall($sql);
					
					$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
					
					$result['data']['headline'] = $arrHeadline;
					$result['data']['total'] 	= $total['total'];
					$result['code'] 			= 1;
					
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
	 * 头条是否已经收藏
	 * 
	 * @param $headline_id:int 头条ID
	 * @param $member:用户登录信息
	 * 
	 * @return boolean
	 */
	function isCollection($headline_id,$member)
	{
		//已登录
		if(!empty($member) AND $member != 3)
		{
			//收藏信息
			$collection = mysqld_select("SELECT collection_id FROM " . table('headline_collection') . " where headline_id={$headline_id} and openid=:openid",array(':openid' => $member['openid']));
			
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
	