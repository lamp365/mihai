<?php
/**
 * app端专题banner管理
 * @var unknown
 */

$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';

switch($operation){

	case 'post':		//提交

		$banner_id 	= intval ( $_GP ['banner_id'] );
		$topic_id	= (int)$_GP['topic_id'];
		
		//显示页面为空
		if(empty($topic_id))
		{
			message ( '请选择显示页面！', web_url ( 'app_topic_banner'), 'error' );
		}

		$data = array('topic_id' 		=> $topic_id,
						'displayorder'	=> (int)$_GP['displayorder'],
						'link'			=> trim($_GP['link']),
						'enabled'		=> (int)$_GP['enabled'],
						'createtime' 	=> time(),
						'modifiedtime' 	=> time()
		);
		
		if (!empty($_FILES['thumb']['tmp_name'])) {
			$upload = file_upload($_FILES['thumb']);
			if (is_error($upload)) {
				message($upload['message'], '', 'error');
			}
			$data['thumb'] = $upload['path'];
		}
		elseif(empty ( $banner_id )){
			message ( '请上传banner图片！', web_url ( 'app_topic_banner'), 'error' );
		}

		if (! empty ( $banner_id )) {
				
			mysqld_update ( 'app_topic_banner', $data, array ('banner_id' => $banner_id) );
				
			message ( '更新banner成功！', web_url ( 'app_topic_banner'), 'success' );
				
		} else {
			mysqld_insert ( 'app_topic_banner', $data );
				
			message ( '新增banner成功！', web_url ( 'app_topic_banner'), 'success' );
		}

		break;

	case 'edit':		//编辑页
		
		$arrTopic = mysqld_selectall ( "SELECT * FROM " . table ( 'app_topic' ) . "  ORDER BY createtime DESC" );

		$appBanner = mysqld_select ( "SELECT * FROM " . table ( 'app_topic_banner' ) . " where banner_id=".(int)$_GP ['banner_id'] );

		include page ( 'app_topic_banner' );

		break;

	case 'new':			//新增页

		$arrTopic = mysqld_selectall ( "SELECT * FROM " . table ( 'app_topic' ) . "  ORDER BY createtime DESC" );
		
		include page ( 'app_topic_banner' );

		break;
		
	case 'delete':		//删除
		
		mysqld_delete ( 'app_topic_banner', array ('banner_id' => intval ( $_GP ['banner_id'] )) );
		
		message ( 'banner删除成功！', web_url ( 'app_topic_banner'), 'success' );
		
		break;

	default:			//列表页
		
		$pindex = max(1, intval($_GP['page']));		//页码
		$psize 	= 10;								//每页显示记录数
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS b.*,t.title FROM " . table('app_topic_banner')." b,".table('app_topic')." t ";
		$sql.= " where b.topic_id=t.topic_id ";
		$sql.= " ORDER BY t.displayorder DESC,b.displayorder desc ";
		$sql.= " limit ".($pindex - 1) * $psize . ',' . $psize;

		$list = mysqld_selectall ( $sql );
		
		$total = mysqld_select("SELECT FOUND_ROWS() as total;");
		$pager = pagination($total['total'], $pindex, $psize);

		include page ( 'app_topic_banner_list' );

		break;
}