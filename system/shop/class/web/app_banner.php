<?php
/**
 * app端banner管理
 * @var unknown
 */

$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';

switch($operation){

	case 'post':		//提交

		$banner_id 	= intval ( $_GP ['banner_id'] );
		$position	= (int)$_GP['position'];
		
		//显示页面为空
		if(empty($position))
		{
			message ( '请选择显示页面！', web_url ( 'app_banner'), 'error' );
		}

		$data = array('position' 		=> $position,
						'displayorder'	=> (int)$_GP['displayorder'],
						'enabled'		=> (int)$_GP['enabled'],
						'createtime' 	=> date('Y-m-d H:i:s'),
						'modifiedtime' 	=> date('Y-m-d H:i:s')
		);
		
		if (!empty($_FILES['thumb']['tmp_name'])) {
			$upload = file_upload($_FILES['thumb']);
			if (is_error($upload)) {
				message($upload['message'], '', 'error');
			}
			$data['thumb'] = $upload['path'];
		}
		elseif(empty ( $banner_id )){
			message ( '请上传banner图片！', web_url ( 'app_banner'), 'error' );
		}
		
		//首页顶部时
		if($position==1)
		{
			$data['link_type'] 	= (int)$_GP['link_type'];
			$data['link']		= trim($_GP['link']);
		}
		//觅海头条
		elseif($position==4)
		{
			$data['link']		= trim($_GP['link']);
		}
		//晒物笔记
		elseif($position==5)
		{
			$data['link']		= trim($_GP['link']);
		}
		//首页专题
		elseif($position==6)
		{
			$data['link']		= trim($_GP['link']);
		}

		if (! empty ( $banner_id )) {
				
			mysqld_update ( 'app_banner', $data, array ('banner_id' => $banner_id) );
				
			message ( '更新banner成功！', web_url ( 'app_banner'), 'success' );
				
		} else {
			mysqld_insert ( 'app_banner', $data );
				
			message ( '新增banner成功！', web_url ( 'app_banner'), 'success' );
		}

		break;

	case 'edit':		//编辑页

		$appBanner = mysqld_select ( "SELECT * FROM " . table ( 'app_banner' ) . " where banner_id=".(int)$_GP ['banner_id'] );

		include page ( 'app_banner' );

		break;

	case 'new':			//新增页

		include page ( 'app_banner' );

		break;
		
	case 'delete':		//删除
		
		mysqld_delete ( 'app_banner', array ('banner_id' => intval ( $_GP ['banner_id'] )) );
		
		message ( 'banner删除成功！', web_url ( 'app_banner'), 'success' );
		
		break;

	default:			//列表页

		$list = mysqld_selectall ( "SELECT * FROM " . table ( 'app_banner' ) . "  ORDER BY createtime DESC" );

		include page ( 'app_banner_list' );

		break;
}