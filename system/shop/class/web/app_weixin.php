<?php
/**
 * 移动端微信设置
 * @var unknown
 */

$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';

switch($operation){

	case 'post':		//提交

		$weixin_mobile['weixin_mobile_appId'] 		= trim($_GP ['weixin_mobile_appId'] );
		$weixin_mobile['weixin_mobile_appSecret']	= trim($_GP['weixin_mobile_appSecret']);
		$weixin_mobile['weixin_mobile_mchId']		= trim($_GP['weixin_mobile_mchId']);
		$weixin_mobile['weixin_mobile_signKey']		= trim($_GP['weixin_mobile_signKey']);
		
		//移动端AppId为空
		if(empty($weixin_mobile['weixin_mobile_appId']))
		{
			message ( '请填写移动端AppId！', web_url ( 'app_weixin'), 'error' );
		}
		//移动端AppSecret为空
		elseif(empty($weixin_mobile['weixin_mobile_appSecret']))
		{
			message ( '请填写移动端AppSecret！', web_url ( 'app_weixin'), 'error' );
		}
		
		//移动端MchId为空
		elseif(empty($weixin_mobile['weixin_mobile_mchId']))
		{
			message ( '请填写移动端MchId！', web_url ( 'app_weixin'), 'error' );
		}
		//移动端signKey为空
		elseif(empty($weixin_mobile['weixin_mobile_signKey']))
		{
			message ( '请填写移动端支付密钥！', web_url ( 'app_weixin'), 'error' );
		}

		
		################## 保存 ##################
		$config_data = mysqld_selectcolumn('SELECT `name` FROM ' . table('config') . " where `name`=:name", array(":name" => 'weixin_mobile'));
		
		if (empty($config_data)) {
			
			$data = array(	'name'  => 'weixin_mobile',
							'value' => serialize($weixin_mobile));
			
			mysqld_insert('config', $data);
			
		} else {
			mysqld_update('config', array('value' => serialize($weixin_mobile)), array('name' => 'weixin_mobile'));
		}
		
		message ( '移动端微信设置保存成功！', web_url ( 'app_weixin'), 'success' );
		
		break;

	default:			//列表页

		$weixinMobile 	= mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='weixin_mobile' " );
		$arrWeixinMobile= unserialize($weixinMobile['value']);

		include page ( 'app_weixin' );

		break;
}