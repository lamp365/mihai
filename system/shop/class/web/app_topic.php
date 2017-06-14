<?php
/**
 * app端专题管理
 * @var unknown
 */

$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';

switch($operation){

	case 'post':		//提交

		$topic_id 	= intval ( $_GP ['topic_id'] );
		
		/*
		//显示页面为空
		if(empty($position))
		{
			message ( '请选择显示页面！', web_url ( 'app_banner'), 'error' );
		}*/

		$data = array('displayorder'	=> (int)$_GP['displayorder'],
						'title'			=> trim($_GP['title']),
						'type'			=> (int)$_GP['type'],
						'enabled'		=> (int)$_GP['enabled'],
						'createtime' 	=> time(),
						'modifiedtime' 	=> time()
		);

		if (! empty ( $topic_id )) {
				
			mysqld_update ( 'app_topic', $data, array ('topic_id' => $topic_id) );
				
			message ( '更新专题成功！', web_url ( 'app_topic'), 'success' );
				
		} else {
			mysqld_insert ( 'app_topic', $data );
				
			message ( '新增专题成功！', web_url ( 'app_topic'), 'success' );
		}

		break;

	case 'edit':		//编辑页

		$appTopic = mysqld_select ( "SELECT * FROM " . table ( 'app_topic' ) . " where topic_id =".(int)$_GP ['topic_id'] );

		include page ( 'app_topic' );

		break;

	case 'new':			//新增页

		include page ( 'app_topic' );

		break;
	/*	
	case 'delete':		//删除
		
		mysqld_delete ( 'app_banner', array ('banner_id' => intval ( $_GP ['banner_id'] )) );
		
		message ( 'banner删除成功！', web_url ( 'app_banner'), 'success' );
		
		break;*/

	default:			//列表页

		$list = mysqld_selectall ( "SELECT * FROM " . table ( 'app_topic' ) . "  ORDER BY createtime DESC" );

		include page ( 'app_topic_list' );

		break;
}