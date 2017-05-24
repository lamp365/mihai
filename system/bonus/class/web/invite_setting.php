<?php
/**
 * 邀请收益配置
 * @var unknown
 */

$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';

switch($operation){

	case 'post':		//提交

		$inviteSetting['direct_share_price']= intval(trim($_GP ['direct_share_price'] ));		//直接邀请收益
		$inviteSetting['order_share_price']	= intval(trim($_GP['order_share_price']));			//订单邀请收益
		$inviteSetting['direct_share_jifen']= intval(trim($_GP['direct_share_jifen']));			//订单邀请收益

		################## 保存 ##################
		$config_data = mysqld_selectcolumn('SELECT `name` FROM ' . table('config') . " where `name`=:name", array(":name" => 'invite_setting'));
		
		//没有数据时，新增
		if (empty($config_data)) {
			
			$data = array(	'name'  => 'invite_setting',
							'value' => serialize($inviteSetting));
			
			mysqld_insert('config', $data);
			
		}
		//有数据时，更新
		else {
			mysqld_update('config', array('value' => serialize($inviteSetting)), array('name' => 'invite_setting'));
		}
		
		message ( '邀请收益配置成功！', web_url ( 'invite_setting'), 'success' );
		
		break;

	default:			//列表页

		$inviteSetting 	= mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='invite_setting' " );
		$arrInviteSetting= unserialize($inviteSetting['value']);

		include page ( 'invite_setting' );

		break;
}