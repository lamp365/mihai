<?php
	/**
	 * app 社交关注接口
	 * @var unknown
	 *
	 */
	$result = array ();
	
	$op = $_GP ['op'];
	
	$member = get_member_account ( true, true );
	
	//他人用户粉丝列表
	if($op=='other_fans_list')
	{
		$openid = trim($_GP['openid']);
		
		if(empty($openid))
		{
			$result['message'] 	= '他人用户ID不能为空';
			$result['code'] 	= 0;
		}
		else
		{
			$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
			$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
			
			$sql = "SELECT SQL_CALC_FOUND_ROWS f.follower_openid,m.nickname,m.avatar,m.member_description FROM " . table('follow') . " as f,".table('member')." as m ";
			$sql.= " WHERE f.follower_openid = m.openid ";
			$sql.= " and f.followed_openid = '".$openid."' ";
			$sql.= " order by f.createtime desc ";
			$sql.= " limit ".(($page-1)*$limit).','.$limit;
			
			$arrFan = mysqld_selectall($sql);
			
			$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
			
			if(!empty($arrFan))
			{
				//登录时
				if (! empty ( $member ) and $member != 3) {
					
					foreach ($arrFan as $key => $value)
					{
						$arrFan[$key]['attention'] = getAttention($value['follower_openid'],$member ['openid']);
					}
				}
				//未登录时
				else{

					foreach ($arrFan as $key => $value)
					{
						$arrFan[$key]['attention'] = 0;		//未关注
					}
				}
			}
			
			
			$result['data']['follow'] 	= $arrFan;
			$result['data']['total'] 	= $total['total'];
			$result['code'] 			= 1;
		}
	}
	//他人用户关注列表
	elseif($op=='other_list'){
		
		$openid = trim($_GP['openid']);
		
		if(empty($openid))
		{
			$result['message'] 	= '他人用户ID不能为空';
			$result['code'] 	= 0;
		}
		//用户ID不为空时
		else
		{
			$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
			$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
			
			$sql = "SELECT SQL_CALC_FOUND_ROWS f.followed_openid,f.mutual_attention,m.nickname,m.avatar,m.member_description FROM " . table('follow') . " as f,".table('member')." as m ";
			$sql.= " WHERE f.followed_openid = m.openid ";
			$sql.= " and f.follower_openid = '".$openid."' ";
			$sql.= " order by f.createtime desc ";
			$sql.= " limit ".(($page-1)*$limit).','.$limit;
			
			$arrFollow = mysqld_selectall($sql);
			
			$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
			
			if(!empty($arrFollow))
			{
				//登录时
				if (! empty ( $member ) and $member != 3) {
						
					foreach ($arrFollow as $key => $value)
					{
						$arrFollow[$key]['attention'] = getAttention($value['followed_openid'],$member ['openid']);
					}
				}
				//未登录时
				else{
					foreach ($arrFollow as $key => $value)
					{
						$arrFollow[$key]['attention'] = 0;		//未关注
					}
				}
			}
			
			$result['data']['follow'] 	= $arrFollow;
			$result['data']['total'] 	= $total['total'];
			$result['code'] 			= 1;
		}
	}
	else{
		if (! empty ( $member ) and $member != 3) {
		
			switch ($op) {
				
				case 'fans_list':		//用户粉丝列表
					
					$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
					$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
					$openid = $member ['openid'];
					
					$sql = "SELECT SQL_CALC_FOUND_ROWS f.follower_openid,f.mutual_attention,m.nickname,m.avatar,m.member_description FROM " . table('follow') . " as f,".table('member')." as m ";
					$sql.= " WHERE f.follower_openid = m.openid ";
					$sql.= " and f.followed_openid = '".$openid."' ";
					$sql.= " order by f.createtime desc ";
					$sql.= " limit ".(($page-1)*$limit).','.$limit;
						
					$arrFollow = mysqld_selectall($sql);
						
					$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
						
					$result['data']['follow'] 	= $arrFollow;
					$result['data']['total'] 	= $total['total'];
					$result['code'] 			= 1;
					
					break;
					
					
				case 'list':		//用户关注列表
					
					$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
					$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
					$openid = $member ['openid'];
						
					$sql = "SELECT SQL_CALC_FOUND_ROWS f.followed_openid,f.mutual_attention,m.nickname,m.avatar,m.member_description FROM " . table('follow') . " as f,".table('member')." as m ";
					$sql.= " WHERE f.followed_openid = m.openid ";
					$sql.= " and f.follower_openid = '".$openid."' ";
					$sql.= " order by f.createtime desc ";
					$sql.= " limit ".(($page-1)*$limit).','.$limit;
						
					$arrFollow = mysqld_selectall($sql);
						
					$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
						
					$result['data']['follow'] 	= $arrFollow;
					$result['data']['total'] 	= $total['total'];
					$result['code'] 			= 1;
					
					break;
					
				case 'remove' : 	// 取消关注
		
					$followed_openid = trim($_GP ['followed_openid']);
		
					if (empty($followed_openid)) {
		
						$result ['message'] = '被关注的用户ID不能为空';
						$result ['code'] 	= 0;
					}
					else{
						//取消关注成功时
						if(mysqld_delete("follow", array('follower_openid' => $member['openid'],'followed_openid'=>$followed_openid)))
						{
							//取消相互关注
							mysqld_update('follow', array('mutual_attention'=>0,'modifiedtime'=>time()),array('follower_openid' =>$followed_openid,'followed_openid'=>$member['openid']));
								
							$result['message'] 	= "取消关注成功。";
							$result['code'] 	= 1;
						}
						else{
							$result['message'] 	= "取消关注失败。";
							$result['code'] 	= 0;
						}
					}
		
					break;
						
				case 'insert' : 	// 关注用户
		
					$followed_openid = trim($_GP ['followed_openid']);				//被关注的用户ID
		
					if (empty($followed_openid)) {
		
						$result ['message'] = '被关注的用户ID不能为空';
						$result ['code'] 	= 0;
					}
					//自己关注自己
					elseif($followed_openid == $member ['openid'])
					{
						$result ['message'] = '自己不能关注自己';
						$result ['code'] 	= 0;
					}
					else{
		
						$followedMember = mysqld_select("SELECT openid FROM " . table('member') . " where openid=:openid", array(':openid' => $followed_openid));
							
						//被关注的用户不存在时
						if(empty($followedMember))
						{
							$result ['message'] = '被关注的用户不存在';
							$result ['code'] 	= 0;
						}
						else{
		
							$data = array ('followed_openid' 	=> $followed_openid,
											'follower_openid' 	=> $member ['openid'],
											'createtime'		=> time()
									);
								
							//新增关注用户信息
							if (mysqld_insert ( 'follow', $data )) {
									
								//设置相互关注
								setMutualAttention($followed_openid,$member['openid']);
		
								$result ['message'] = "关注用户成功。";
								$result ['code'] 	= 1;
							} else {
								$result ['message'] = "关注用户失败。";
								$result ['code'] 	= 0;
							}
						}
					}
		
					break;
		
				default :
		
					$result['message'] 	= '操作不合法';
					$result['code'] 	= 2;
		
					break;
			}
		} elseif ($member == 3) {
			$result ['message'] = "该账号已在别的设备上登录！";
			$result ['code'] = 3;
		} else {
			$result ['message'] = "用户还未登陆。";
			$result ['code'] = 2;
		}
	}
	
	echo apiReturn ( $result );
	exit ();
	
	
	/**
	 * 设置相互关注
	 * 
	 * @param $followed_openid:被关注的用户ID
	 * @param $openid:当前用户ID
	 * 
	 */
	function setMutualAttention($followed_openid,$openid)
	{
		$fans = mysqld_select("SELECT follow_id FROM " . table('follow') . " where follower_openid=:follower_openid and followed_openid=:followed_openid", array(':follower_openid' => $followed_openid,':followed_openid'=>$openid));
	
		//如果被关注者即是粉丝时，更新相互关注状态
		if($fans)
		{
			mysqld_update('follow', array('mutual_attention'=>1,'modifiedtime'=>time()),array('follower_openid' =>$followed_openid,'followed_openid'=>$openid));
			
			mysqld_update('follow', array('mutual_attention'=>1,'modifiedtime'=>time()),array('followed_openid' =>$followed_openid,'follower_openid'=>$openid));
		}
	}
	
	/**
	 * 他人关注列表及他人粉丝列表中获得相对于当前用户的关注信息
	 *
	 * @param $partner_openid:伙伴用户ID
	 * @param $current_openid:当前用户ID
	 *
	 * @return 0:未关注
	 *         1:已关注
	 *         2:粉丝
	 *         3:相互关注
	 */
	function getAttention($partner_openid,$current_openid)
	{
		$fans = mysqld_select("SELECT mutual_attention FROM " . table('follow') . " where follower_openid=:follower_openid and followed_openid=:followed_openid", array(':follower_openid' => $partner_openid,':followed_openid'=>$current_openid));
		
		//非粉丝时
		if(empty($fans))
		{
			if(mysqld_select("SELECT follow_id FROM " . table('follow') . " where follower_openid=:follower_openid and followed_openid=:followed_openid", array(':follower_openid' => $current_openid,':followed_openid'=>$partner_openid)))
			{
				return 1;
			}
		}
		elseif($fans['mutual_attention']==1)
		{
			return 3;
		}
		else{
			return 2;
		}
		
		return 0;
	}
			