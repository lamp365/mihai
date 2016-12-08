<?php
	/**
	 * app 地址相关操作接口
	 * @var unknown
	 *
	 */

	$result = array();

	$member=get_member_account(true,true);
				
	if(!empty($member) AND $member != 3)
	{
		$op = $_GP['op'];
		
		switch($op)
		{
			case 'insert':			//新增地址
				
				if (empty($_GP['realname']) || empty($_GP['telephone']) || empty($_GP['province']) || empty($_GP['city']) || empty($_GP['area']) || empty($_GP['address'])) {
						
					$result['message'] 	= "请完善您的地址信息。";
					$result['code'] 	= 0;
				}
				else{
					mysqld_update('shop_address', array('isdefault' => 0), array( 'openid' => $member['openid']));
					
					$data = array('openid' 		=> $member['openid'],
									'realname' 	=> $_GP['realname'],
									'mobile' 	=> $_GP['telephone'],
									'province' 	=> $_GP['province'],
									'city' 		=> $_GP['city'],
									'area' 		=> $_GP['area'],
									'address' 	=> $_GP['address'],
									'isdefault'	=> 1);
				
					if(mysqld_insert('shop_address', $data))
					{
						$result['message'] 	= "地址新增成功。";
						$result['code'] 	= 1;
					}
					else{
						$result['message'] 	= "地址新增失败。";
						$result['code'] 	= 0;
					}
				}
				
				break;
				
				
			case 'update':			//地址更新
				
				$id = intval($_GP['id']);
				
				if (empty($_GP['realname']) || empty($_GP['telephone']) || empty($_GP['province']) || empty($_GP['city']) || empty($_GP['area']) || empty($_GP['address'])) {
				
					$result['message'] 	= "请完善您的地址信息。";
					$result['code'] 	= 0;
				}
				else{

					$data = array('realname' 	=> $_GP['realname'],
									'mobile' 	=> $_GP['telephone'],
									'province' 	=> $_GP['province'],
									'city' 		=> $_GP['city'],
									'area' 		=> $_GP['area'],
									'address' 	=> $_GP['address']);
					
					mysqld_update('shop_address', $data, array('openid' =>$member['openid'],'id'=>$id));
					
					$result['message'] 	= "地址更新成功。";
					$result['code'] 	= 1;
				}
				
				break;
				
			case 'set_default':		//设置默认地址
				
				$id = intval($_GP['id']);
				
				$addressInfo = mysqld_select("SELECT id FROM " . table('shop_address') . " WHERE deleted=0 and openid = :openid and id=:id", array(':openid' => $member['openid'],':id'=>$id));
					
				//地址信息不存在时
				if(empty($addressInfo))
				{
					$result['message'] 	= "地址信息不存在。";
					$result['code'] 	= 0;
				}
				else{
					mysqld_update('shop_address', array('isdefault' => 0), array('openid' =>$member['openid']));
					mysqld_update('shop_address', array('isdefault' => 1), array('id' => $id));
					
					$result['message'] 	= "设置默认地址成功。";
					$result['code'] 	= 1;
				}
				
				break;
				
				
			case 'default_address':		//获得默认地址
				
				$addressInfo = mysqld_select("SELECT id,realname,mobile,province,city,area,address,isdefault FROM " . table('shop_address') . " WHERE openid = :openid and isdefault=1", array(':openid' => $member['openid']));
				
				//地址信息不存在时
				if(empty($addressInfo))
				{
					$result['data']['address'] 	= array();
					$result['code'] 			= 0;
				}
				else{
					$result['data']['address'] 	= $addressInfo;
					$result['code'] 			= 1;
				}
						
				break;
				
			case 'remove':		//地址删除
				
				$id = intval($_GP['id']);
				
				$addressInfo = mysqld_select("SELECT id,isdefault FROM " . table('shop_address') . " WHERE deleted=0 and openid = :openid and id=:id", array(':openid' => $member['openid'],':id'=>$id));
						
				//地址信息不存在时
				if(empty($addressInfo))
				{
					$result['message'] 	= "地址信息不存在。";
					$result['code'] 	= 0;
				}
				else{
					mysqld_update('shop_address', array('deleted' => 1,"isdefault" => 0), array('id' => $id));
					
					if ($addressInfo['isdefault'] == 1) {
						//如果删除的是默认地址，则设置是新的为默认地址
						$maxid = mysqld_selectcolumn("select max(id) as maxid from " . table('shop_address') . " where  openid='".$member['openid']."' and deleted=0 limit 1 ");
						if (!empty($maxid)) {
							mysqld_update('shop_address', array('isdefault' => 1), array('id' => $maxid, 'openid' => $member['openid']));
						}
					}
					
					$result['message'] 	= "删除地址成功。";
					$result['code'] 	= 1;
				}
				
				break;
				
			case 'detail':		//单笔地址详情
				
				$id = intval($_GP['id']);
				
				$addressInfo = mysqld_select("SELECT id,realname,mobile,province,city,area,address,isdefault FROM " . table('shop_address') . " WHERE deleted=0 and openid = :openid and id=:id", array(':openid' => $member['openid'],':id'=>$id));
					
				//地址信息不存在时
				if(empty($addressInfo))
				{
					$result['data']['address'] 	= array();
					$result['code'] 			= 0;
				}
				else{
					$result['data']['address'] 	= $addressInfo;
					$result['code'] 			= 1;
				}
				
				break;
				
			default:			//地址列表
				
				$arrAddress = mysqld_selectall("SELECT id,realname,mobile,province,city,area,address,isdefault FROM " . table('shop_address') . " WHERE deleted=0 and openid = :openid order by isdefault desc,id desc", array(':openid' => $member['openid']));
				
				$result['data']['address'] 	= $arrAddress;
				$result['code'] 			= 1;
				
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
	
	echo apiReturn($result);
	exit;
			