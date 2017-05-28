<?php

require_once WEB_ROOT . '/includes/TopSdk.php';

/**
 * 即时通讯类
 */
class OpenIm{
	
	public $appkey		='23465797';
	public $secretKey	='3ce73b0b159bff77a05fdc728c5dd69b';
	public $c;													//TopClient对象
	
	function __construct()
	{
		$this->c = new TopClient;
		$this->c->appkey 	= $this->appkey;
		$this->c->secretKey = $this->secretKey;
		$this->c->format 	= 'json';
	}

	/**
	 * 创建用户
	 * 
	 * @param array $userinfos:即时通讯用户信息数组
	 * 
	 */
	public function createUser($userinfos)
	{
		$req = new OpenimUsersAddRequest;
			
		$req->setUserinfos(json_encode($userinfos));
		$resp = $this->c->execute($req);

		//有新增失败信息时
		if(isset($resp->fail_msg->string)){
			
			return false;
		}
		else{
			
			$data['userid'] 		= $userinfos['userid'];
			$data['icon_url'] 		= $userinfos['icon_url'];
			$data['password'] 		= md5($userinfos['password']);
			$data['createtime'] 	= date('Y-m-d H:i:s');
			$data['modifiedtime'] 	= $data['createtime'];
			
			if(isset($userinfos['nick']))
			{
				$data['nick'] = $userinfos['nick'];
			}
			
			mysqld_insert('im_user', $data);
			
			return true;
		}
	}
	
	
	/**
	 * 更新用户信息
	 *
	 * @param array $userinfos:用户信息数组($userinfos['userid']:必填项)
	 * 
	 * 
	 */
	public function updateUser($userinfos)
	{
		$req = new OpenimUsersUpdateRequest;

		$req->setUserinfos(json_encode($userinfos));
		$resp = $this->c->execute($req);
		
		//有更新失败信息时
		if(isset($resp->fail_msg->string)){
				
			return false;
		}
		else{
			$data['password'] 		= md5($userinfos['password']);
			$data['modifiedtime'] 	= date('Y-m-d H:i:s');
			
			if(isset($userinfos['nick']))
			{
				$data['nick'] = $userinfos['nick'];
			}
			
			mysqld_update('im_user', $data,array('userid' =>$userinfos['userid']));
			
			return true;
		}
	}
	
	/**
	 * 删除用户信息
	 * 
	 * @param string $uid 用户ID
	 * 
	 * @return boolean
	 */
	function deleteUser($uid)
	{
		$req = new OpenimUsersDeleteRequest;
		$req->setUserids($uid);
		$resp = $this->c->execute($req);

		if(isset($resp->result->string[0]) && $resp->result->string[0]=='ok')
		{
			mysqld_delete('im_user', array('userid'=>$uid));
			
			return true;
		}
		else{
			
			return false;
		}
	}
	
	/**
	 * 获得用户信息
	 *
	 * @param string $userid 用户ID
	 * 
	 */
	function getUserInfo($userid)
	{
		$req = new OpenimUsersGetRequest;
		$req->setUserids($userid);
		$resp = $this->c->execute($req);
		
		return $resp;
	}
	
	
	/**
	 * 推送自定义openim消息
	 * 
	 * @param array $custmsg:消息内容数组(必填项参见阿里百川文档)
	 */
	function custMessagePush($custmsg)
	{
		if(!isset($custmsg['from_user']))
		{
			$custmsg['from_user'] = IM_FROM_USER;
		}
		
		$data['from_user'] 		= $custmsg['from_user'];
		$data['to_users'] 		= $custmsg['to_users'];
		$data['summary'] 		= $custmsg['summary'];
		$data['data'] 			= $custmsg['data'];
		$data['createtime'] 	= date('Y-m-d H:i:s');
		$data['modifiedtime'] 	= $data['createtime'];
		
		if(mysqld_insert('im_message', $data)){
			
			$req = new OpenimCustmsgPushRequest;
			
			$req->setCustmsg(json_encode($custmsg));
			$resp = $this->c->execute($req);
			
			return $resp;
		}
		else{
			return false;
		}
	}
	
	/**
	 * openim标准消息发送
	 * 
	 * @param array $immsg:消息内容数组
	 * 
	 */
	function imMessagePush($immsg)
	{
		if(!isset($immsg['from_user']))
		{
			$immsg['from_user'] = IM_FROM_USER;
		}
		
		$data['from_user'] 		= $immsg['from_user'];
		$data['to_users'] 		= $immsg['to_users'];
		$data['msg_type'] 		= isset($immsg['msg_type']) ? $immsg['msg_type'] : 0;
		$data['data'] 			= $immsg['context'];
		$data['createtime'] 	= date('Y-m-d H:i:s');
		$data['modifiedtime'] 	= $data['createtime'];
		
		logRecord('imMessagePush insert before:'.print_r($data,true),'im');
		
		if(mysqld_insert('im_message', $data)){
			$resp = 1;
			//为了不影响测试开发的工作，这里如果debug是0，才发送im消息。线上基本会设置为0，不影响线上。开发环境由于跟im不通会影响程序进行
			if(DEVELOPMENT == 0){
				$req = new OpenimImmsgPushRequest;
			
				$req->setImmsg(json_encode($immsg));
				
				$resp = $this->c->execute($req);
				
				logRecord('ImmsgPushRequest:'.print_r($resp,true),'im');
			}
			return $resp;
		}
		else{
			return false;
		}
	}
	
	/**
	 * openim聊天记录查询接口
	 * 
	 * @param string $uid1 用户1 id(聊天双方其中一方的id)
	 * @param string $uid2 用户2 id(聊天双方其中一方的id)
	 * @param int $starttime 查询开始时间
	 * @param int $endtime 查询结束时间
	 * @param int $count 查询条数
	 * 
	 */
	function getChatlogs($uid1,$uid2,$starttime,$endtime,$count)
	{
		$req = new OpenimChatlogsGetRequest;
		
		$user1['uid'] = $uid1;
		
		$req->setUser1(json_encode($user1));
		
		$user2['uid'] = $uid2;
		
		$req->setUser2(json_encode($user2));
		$req->setBegin($starttime);
		$req->setEnd($endtime);
		$req->setCount($count);
		
		$resp = $this->c->execute($req);

		if(isset($resp->result->messages->roaming_message))
		{
			return $resp->result->messages->roaming_message;
		}
		else{
			return false;
		}
	}
	
	/**
	 * 判断是否存在IM账号
	 *
	 * @param string $userid IM用户
	 * @return boolean
	 */
	function isImUser($userid)
	{
		$imUser = mysqld_select("SELECT * FROM " . table('im_user') . " WHERE userid = :userid", array(':userid' => $userid));
	
		if(!empty($imUser))
		{
			return true;
		}
		else{
			return false;
		}
	}
}