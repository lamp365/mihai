<?php
/**
 * 觅海头条管理
 * @var unknown
 */

$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';

switch($operation){

	case 'post':		//提交

		$headline_id 	= intval ( $_GP ['headline_id'] );
		$isrecommand	= (int)$_GP['isrecommand'];
		$title = $_GP ['headtitle'];
		$video = $_GP ['video'];
		$description = $_GP ['description'];

		// dump($_GP['attachment']);
		// dump($_GP['attachment-new']);
		// return;
		$data = array('isrecommand' 	=> $isrecommand,
						'modifiedtime' 	=> time(),
						'title'			=> $title,
						'description'	=> $description
		);
		$pic = '';
		if (!empty($_GP['attachment'])) {
			$pic .= implode(';',$_GP['attachment']);
		}
		if (!empty($_GP['attachment-new'])) {
			$pic .= ';';
			$pic .= implode(';',$_GP['attachment-new']);
		}
		if (!empty($pic)) {
			$data['pic'] = $pic;
		}
		if (!empty($_FILES['thumb']['tmp_name'])) {
            $upload = file_upload($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }

            $data['video_img'] = $upload['path'];
        }
        if (!empty($_FILES['video']['tmp_name'])) {
        	if ($_FILES['video']['type'] != "video/mp4") {
        		message ( '视频只能上传MP4格式！', refresh(), 'error' );
        		return;
        	}
            $upload = file_upload($_FILES['video']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }

            $data['video'] = $upload['path'];
        }

		if (empty($headline_id)) {
			$data['createtime'] = time();
			// 新增
			mysqld_insert('headline', $data);
		}else{
			// 更新
			mysqld_update ( 'headline', $data, array ('headline_id' => $headline_id) );
		}
		
		message ( '编辑觅海头条成功！', web_url ( 'headline'), 'success' );

		break;

	case 'edit':		//编辑页

		$headline = mysqld_select ( "SELECT * FROM " . table ( 'headline' ) . " where headline_id=".(int)$_GP ['headline_id'] );

		include page ( 'headline' );

		break;

	case 'add':
		// 新增
		include page ( 'headline' );
		break;

	case 'delete':
		// 删除头条
		$id = intval ( $_GP ['id'] );
		mysqld_update ( 'headline', array('deleted'=>1), array ('headline_id' => $id) );
		message ( '删除成功', refresh(), 'success' );
		break;

	default:			//列表页
		
		$pindex= max(1, intval($_GP['page']));
		$limit = 20;

		$list = mysqld_selectall ( "SELECT SQL_CALC_FOUND_ROWS * FROM " . table ( 'headline' ) . " where deleted=0 ORDER BY createtime DESC limit ".(($pindex-1)*$limit).','.$limit);

		$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
		
		$pager = pagination($total['total'], $pindex,$limit);
		
		include page ( 'headline_list' );

		break;
}