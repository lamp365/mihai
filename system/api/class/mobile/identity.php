<?php
	/**
	 * app 身份证相关操作接口
	 * @var unknown
	 *
	 */

	$result = array();

	$member=get_member_account(true,true);
	$openid =$member['openid'] ;
	
	$operation = $_GP ['op'];
	
	if(!empty($member) AND $member != 3)
	{
		switch ($operation)
		{
			case 'update':		//更新
				
				$message = validateIdentity($_GP['identity_number'],$_GP['identity_name']);
		
				if(empty($message))
				{
					$identity_id 	= (int)$_GP['identity_id'];
		
					$data = array(
							'identity_number' 	=> $_GP['identity_number'],
							'identity_name' 	=> $_GP['identity_name'],
							'modifiedtime' 		=> date('Y-m-d H:i:s')
					);
						
					//身份证正面
					if (isset($_FILES['front_image']) && $_FILES['front_image']['error']==0) {
							
						$upload = file_upload($_FILES['front_image'],false);
							
						if (is_error($upload)) {
							
							$message = $upload['message'];		//文件上传有错时
						}
						else{
							$data['identity_front_image'] = $upload['path'];
						}
					}
						
					//身份证反面
					if (isset($_FILES['back_image']) && $_FILES['back_image']['error']==0) {
							
						$upload = file_upload($_FILES['back_image'],false);
							
						if (is_error($upload)) {
							
							$message = $upload['message'];		//文件上传有错时
						}
						else{
							$data['identity_back_image'] = $upload['path'];
						}
					}
					
					//无错误信息时
					if(empty($message))
					{
						mysqld_update('member_identity', $data,array('openid' =>$openid,'identity_id'=>$identity_id,'status'=>0));
						
						$result['message'] 	= "身份证更新成功。";
						$result['code'] 	= 1;
					}
					else{
							
						$result['message'] 	= $message;
						$result['code'] 	= 0;
					}
					
				}
				else{
					
					$result['message'] 	= $message;
					$result['code'] 	= 0;
				}
					
				break;
		
		
			case 'insert':		//新增
				
				//信息验证
				$message = validateIdentity($_GP['identity_number'],$_GP['identity_name']);
				
				$identity_number= trim($_GP['identity_number']);
				$identity_name 	= trim($_GP['identity_name']);
				
				if(empty($message))
				{
					$data = array('openid' 			=> $openid,
								'identity_number' 	=> $identity_number,
								'identity_name' 	=> $identity_name,
								'isdefault'			=> 1,
								'createtime' 		=> date('Y-m-d H:i:s'),
								'modifiedtime' 		=> date('Y-m-d H:i:s'),
								'status'			=> 0
						);
						
					//身份证正面
					if (isset($_FILES['front_image']) && $_FILES['front_image']['error']==0) {
							
						$upload = file_upload($_FILES['front_image'],false);
							
						if (is_error($upload)) {
							
							$message = $upload['message'];		//文件上传有错时
						}
						else{
							$data['identity_front_image'] = $upload['path'];
						}
					}
						
					//身份证反面
					if (isset($_FILES['back_image']) && $_FILES['back_image']['error']==0) {
							
						$upload = file_upload($_FILES['back_image'],false);
							
						if (is_error($upload)) {
							
							$message = $upload['message'];		//文件上传有错时
						}
						else{
							$data['identity_back_image'] = $upload['path'];
						}
					}
					
					//无错误信息时
					if(empty($message))
					{
						//旧数据
						$identity = mysqld_select("SELECT identity_id,identity_number,identity_name,identity_front_image,identity_back_image,isdefault FROM " . table('member_identity') . " WHERE openid = :openid and identity_number=:identity_number and status=1", array(':openid' => $openid,':identity_number'=>$identity_number));
						
						//有旧记录时,更新
						if($identity)
						{
							if(mysqld_update('member_identity', $data,array('openid' =>$openid,'identity_id'=>$identity['identity_id'])))
							{
								//取消旧的默认身份证
								mysqld_query ( "update " . table ( 'member_identity' ) . "  SET isdefault=0 WHERE openid = '{$openid}' and identity_id!= " .$identity['identity_id'] );
								
								$result['message'] 	= "身份证新增成功。";
								$result['code'] 	= 1;
							}
							else{
								$result['message'] 	= "身份证新增失败。";
								$result['code'] 	= 0;
							}
						}
						//没有旧记录时,新增
						else{
							
							if(mysqld_insert('member_identity', $data))
							{
								$identity_id = mysqld_insertid();
							
								//取消旧的默认身份证
								mysqld_query ( "update " . table ( 'member_identity' ) . "  SET isdefault=0 WHERE openid = '{$openid}' and identity_id!= " .$identity_id );
							
								$result['message'] 	= "身份证新增成功。";
								$result['code'] 	= 1;
							}
							else{
								$result['message'] 	= "身份证新增失败。";
								$result['code'] 	= 0;
							}
						}
					}
					else{
						$result['message'] 	= $message;
						$result['code'] 	= 0;
					}
				}
				else{
					$result['message'] 	= $message;
					$result['code'] 	= 0;
				}
					
				break;
					
			case 'detail':		//详情
					
				$identity_id = (int)$_GP['identity_id'];
					
				$identity = mysqld_select("SELECT identity_id,identity_number,identity_name,identity_front_image,identity_back_image,isdefault FROM " . table('member_identity') . " WHERE openid = :openid and identity_id=:identity_id and status=0", array(':openid' => $openid,':identity_id'=>$identity_id));
					
				//身份证信息不存在时
				if(empty($identity))
				{
					$result['data']['identity'] = array();
					$result['code'] 			= 0;
				}
				else{
					
					if(!empty($identity['identity_front_image'])) $identity['identity_front_image']= $identity['identity_front_image'];
					if(!empty($identity['identity_back_image'])) $identity['identity_back_image']= $identity['identity_back_image'];
					
					$result['data']['identity'] = $identity;
					$result['code'] 			= 1;
				}
					
				break;
					
			case 'remove':		//删除
					
				$identity_id = (int)$_GP['identity_id'];
				
				if(empty($identity_id))
				{
					$result['message'] 	= '身份证ID不能为空';
					$result['code'] 	= 0;
				}
				else{
					$data = array('status' 		=> 1,
								'modifiedtime' 	=> date('Y-m-d H:i:s'));
					
					//只能删除非默认身份证
					if(mysqld_update('member_identity', $data,array('isdefault' => 0,'openid' =>$openid,'identity_id'=>$identity_id)))
					{
						$result['message'] 	= '身份证删除成功';
						$result['code'] 	= 1;
					}
					else{
						$result['message'] 	= '身份证删除失败';
						$result['code'] 	= 0;
					}
				}
				
				break;
				
			case 'set_default':		//设置默认身份证
						
				mysqld_update('member_identity', array('isdefault' => 0), array('openid' =>$openid,'status'=>0));
						
				$data = array('isdefault' 	=> 1,
							'modifiedtime' 	=> date('Y-m-d H:i:s'));
						
				if(mysqld_update('member_identity', $data,array('openid' =>$openid,'status'=>0,'identity_id'=>intval($_GP['identity_id']))))
				{
					$result['message'] = '默认身份证设置成功';
					$result['code'] 	= 1;
				}
				else{
					$result['message'] = '默认身份证设置失败';
					$result['code'] 	= 0;
				}
						
				break;
				
				
			case 'default_identity':		//获得默认身份证
				
				$identity = mysqld_select("SELECT identity_id,identity_number,identity_name,identity_front_image,identity_back_image,isdefault FROM " . table('member_identity') . " WHERE openid = :openid and isdefault=1 and status=0 ", array(':openid' => $openid));
					
				//默认身份证信息不存在时
				if(empty($identity))
				{
					$result['data']['identity'] = array();
					$result['code'] 			= 0;
				}
				else{
						
					if(!empty($identity['identity_front_image'])) $identity['identity_front_image']= $identity['identity_front_image'];
					if(!empty($identity['identity_back_image'])) $identity['identity_back_image']= $identity['identity_back_image'];
						
					$result['data']['identity'] = $identity;
					$result['code'] 			= 1;
				}
					
				break;
				
			default:			//身份证列表
				
				$arrIdentity = mysqld_selectall("SELECT identity_id,identity_number,identity_name,identity_front_image,identity_back_image,isdefault FROM " . table('member_identity') . " WHERE openid = :openid and status=0 ", array(':openid' => $openid));

				$result['data']['identity'] = $arrIdentity;
				$result['code'] 	= 1;
				
				break;
		}
	}elseif ($member == 3) {
		$result['message'] 	= "该账号已在别的设备上登录！";
		$result['code'] 	= 3;
	}else{
		$result['message'] 	= "用户还未登陆。";
		$result['code'] 	= 2;
	}
	
	
	echo apiReturn($result);
	exit;
	
	/**
	 * 表单验证
	 * 
	 * @param unknown $idNum  身份证号码
	 * @param $identity_name  身份证姓名
	 * 
	 * @return string 错误信息
	 */
	function validateIdentity($idNum,$identity_name) {
		
		$objValidator 	= new Validator();
		$message		= '';
		
		if($idNum=='')
		{
			$message = '请输入您的身份证号码！';
		}
		//身份证验证
		elseif(!$objValidator->identityNumberValidator($idNum))
		{
			$message = '身份证格式不正确！';
		}
		
		if($identity_name=='')
		{
			$message = '请输入您的身份证姓名！';
		}
		elseif (!preg_match('/^[\x{4e00}-\x{9fa5}]{2,20}$/u', $identity_name)) {
			
			$message = '身份证姓名必须为中文，2-20个字符！';
		}
		
		return $message;
	}
	