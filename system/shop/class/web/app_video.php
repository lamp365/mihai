<?php
/**
 * app视频管理
 * @var unknown
 */

$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';

switch($operation){

	case 'post':		//提交

		$video_id 	= intval ( $_GP ['video_id'] );
		
		$data = array(	'enabled'		=> (int)$_GP['enabled'],
						'createtime' 	=> time(),
						'modifiedtime' 	=> time()
		);
	
		if (!empty($_FILES['video']['tmp_name'])) {
			$upload = file_upload($_FILES['video'],true, "350", "350",'music');
			if (is_error($upload)) {
				message($upload['message'], '', 'error');
			}
			$data['video_url'] = $upload['path'];
		}
		elseif($_FILES['video']['error']==1)
		{
			message ( '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值！', web_url ( 'app_video'), 'error' );
		}
		elseif($_FILES['video']['error']==2)
		{
			message ( '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值！', web_url ( 'app_video'), 'error' );
		}
		elseif(empty ( $video_id )){
			message ( '请上传视频文件！', web_url ( 'app_video'), 'error' );
		}

		if (! empty ( $video_id )) {
				
			mysqld_update ( 'app_video', $data, array ('video_id' => $video_id) );
				
			message ( '更新视频成功！', web_url ( 'app_video'), 'success' );
				
		} else {
			mysqld_insert ( 'app_video', $data );
				
			message ( '新增视频成功！', web_url ( 'app_video'), 'success' );
		}

		break;

	case 'edit':		//编辑页

		$videoInfo = mysqld_select ( "SELECT * FROM " . table ( 'app_video' ) . " where video_id=".(int)$_GP ['video_id'] );

		include page ( 'app_video' );

		break;

	case 'new':			//新增页

		include page ( 'app_video' );

		break;
		
	case 'delete':		//删除
		
		mysqld_delete ( 'app_video', array ('video_id' => intval ( $_GP ['video_id'] )) );
		
		message ( '视频删除成功！', web_url ( 'app_video'), 'success' );
		
		break;

	default:			//列表页

		$list = mysqld_selectall ( "SELECT * FROM " . table ( 'app_video' ) . "  ORDER BY createtime DESC" );

		include page ( 'app_video_list' );

		break;
}