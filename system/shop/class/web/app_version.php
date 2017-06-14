<?php
$op = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';


switch($op){
	
	case 'post':		//提交

		$version_id = intval ( $_GP ['version_id'] );
		
		//版本号为空
		if(empty($_GP['version_no']))
		{
			message ( '请选择版本号！', web_url ( 'app_banner'), 'error' );
		}
		//安装包下载地址为空
		if(empty($_GP['url']))
		{
			message ( '请选择安装包下载地！', web_url ( 'app_banner'), 'error' );
		}
		
		$data = array('version_no' 	=> $_GP['version_no'],
					'app_type' 		=> (int)$_GP['app_type'],
					'force_update' 	=> (int)$_GP['force_update'],
					'url'			=> $_GP['url'],
					'comment'		=> $_GP['comment'],
					'modifiedtime' 	=> date('Y-m-d H:i:s')
		);
		
		if (! empty ( $version_id )) {
			
			mysqld_update ( 'app_version', $data, array ('version_id' => $version_id) );
			
			message ( '更新app版本设置成功！', web_url ( 'app_version'), 'success' );
			
		} else {
			$data['createtime'] = date('Y-m-d H:i:s');
			
			mysqld_insert ( 'app_version', $data );
			
			message ( '新增app版本设置成功！', web_url ( 'app_version'), 'success' );
		}
		
		break;
	
	case 'edit':		//编辑页
	
		$appVersion = mysqld_select ( "SELECT * FROM " . table ( 'app_version' ) . " where version_id=".(int)$_GP ['version_id'] );
	
		include page ( 'app_version' );
	
		break;
	
	case 'new':			//新增页
		
		include page ( 'app_version' );
		
		break;
	
	default:			//列表页
		$app_type_arr = array(0=>'安卓',1=>'IOS','2'=>'应用宝安卓',3=>'应用宝IOS');
		$list = mysqld_selectall ( "SELECT * FROM " . table ( 'app_version' ) . "  ORDER BY createtime DESC" );
		
		include page ( 'app_version_list' );
		
		break;
	
}

exit;