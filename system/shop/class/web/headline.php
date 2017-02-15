<?php
/**
 * 觅海头条管理
 * @var unknown
 */

$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';
$admin = $_CMS['account']['id'];

switch($operation) {

	case 'post':		//提交

		$headline_id 	= intval ( $_GP ['headline_id'] );
		$isrecommand	= (int)$_GP['isrecommand'];
		$title = $_GP ['headtitle'];
		$video = $_GP ['video'];
		$description = $_REQUEST['description'];
		$preview = $_GP ['preview'];
		$ischeck = (int)$_GP['ischeck'];

		$headline = mysqld_select("SELECT * FROM ".table('headline')." WHERE headline_id=".$headline_id);

		if (empty($title) or empty($description)) {
			message ( '标题和内容不能为空！', refresh(), 'error' );
		}
		$contents =  htmlspecialchars_decode($_GP['description']);
		preg_match_all('<img.*?src=\"(.*?.*?)\".*?>',$contents,$match);
		$oldimg   =  $match[1];
		foreach ( $oldimg as $key=>$oldimg_value ){
			  // 将本地的图片上传到阿里云上
			  if ( strstr($oldimg_value, 'ueditor' )){
					$picurl   = rtrim(WEBSITE_ROOT,'/').$oldimg_value;
				    $newimg   = aliyunOSS::putObject($picurl);
				    $contents = str_replace($oldimg_value, $newimg['oss-request-url'],$contents);
				    //删除本地图片
				    $unlink_pic = ".".$oldimg_value;  //相对路径
				  	 @unlink ($unlink_pic);
			  }
		 }

		$data = array('isrecommand' 	=> $isrecommand,
						'modifiedtime' 	=> time(),
						'title'			=> $title,
						'description'	=> $contents,
						'preview'		=> $preview,
						'ischeck'		=> $ischeck
		);
		$pic = '';
		if (!empty($_GP['attachment'])) {
			foreach ($_GP['attachment'] as $atk => &$atv) {
				if (empty($atv)) {
					unset($_GP['attachment'][$atk]);
				}
			}
			unset($atv);
			$_GP['attachment'] = array_merge($_GP['attachment']);
			$pic .= implode(';',$_GP['attachment']);
		}
		if (!empty($_GP['attachment-new'])) {
			foreach ($_GP['attachment-new'] as $atnk => &$atnv) {
				if (empty($atnv)) {
					unset($_GP['attachment-new'][$atnk]);
				}
			}
			unset($atnv);
			$_GP['attachment-new'] = array_merge($_GP['attachment-new']);
			$pic .= ';';
			$pic .= implode(';',$_GP['attachment-new']);
		}
		$data['pic'] = $pic;
		if (!empty($_FILES['thumb']['tmp_name'])) {
            $upload = file_upload($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }

            $data['video_img'] = $upload['path'];
        }
        // $data['video'] = '';
        if (!empty($_FILES['video']['tmp_name'])) {
        	if ($_FILES['video']['type'] != "video/mp4") {
        		message ( '视频只能上传MP4格式！', refresh(), 'error' );
        		return;
        	}
            $upload = file_upload($_FILES['video'], true, '', '', 'other');
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }

            $data['video'] = $upload['path'];
        }
        // if (empty($data['pic']) AND empty($data['video'])) {
        // 	message ( '视频和图片不能都为空！', refresh(), 'error' );
        // }
        // if (!empty($data['pic']) AND !empty($data['video'])) {
        // 	message ( '视频和图片只能二选一！', refresh(), 'error' );
        // }
        // if (!empty($data['pic']) AND !empty($_GP ['hidvideo'])) {
        // 	message ( '视频和图片只能二选一！', refresh(), 'error' );
        // }
        // if (!empty($headline['pic']) AND !empty($headline['video'])) {
        // 	message ( '视频和图片只能二选一！', refresh(), 'error' );
        // }

		if (empty($headline_id)) {
			$data['createtime'] = time();
			$data['uid'] = $_CMS['account']['id'];
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
		$author = mysqld_select("SELECT * FROM ".table('user')." WHERE id=".$headline['uid']);

		include page ( 'headline' );

		break;

	case 'add':
		// 新增
		$author = mysqld_select("SELECT * FROM ".table('user')." WHERE id=".$admin);
		include page ( 'headline' );
		break;

	case 'delete':
		// 删除头条
		$id = intval ( $_GP ['id'] );
		mysqld_update ( 'headline', array('deleted'=>1), array ('headline_id' => $id) );
		message ( '删除成功', refresh(), 'success' );
		break;

	case 'delvideo':
		// 删除视频
		$id = intval($_GP ['vid']);
		mysqld_update ( 'headline', array('video'=>NULL), array ('headline_id' => $id) );
		message ( '视频删除成功', refresh(), 'success' );
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