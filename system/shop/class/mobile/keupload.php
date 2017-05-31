<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小物社区 <QQ:119006873> <http://www.squdian.com>
// +----------------------------------------------------------------------
	$result = array(
		'url' => '',
		'message' => '',
		'error' => 1,
	);
	if (!empty($_FILES['imgFile']['name'])) {
		if ($_FILES['imgFile']['error'] != 0) {
			$result['message'] = '上传失败，请重试！';
			exit(json_encode($result));
		}
		$file = file_upload($_FILES['imgFile']);
		if (is_error($file)) {
			$result['message'] = $file['message'];
			exit(json_encode($result));
		}
			$result['error'] = 0;
		$result['url'] = $file['path'];
		$result['filename'] =$file['path'];
		$result['url']= $result['filename'];
		exit(json_encode($result));
	} else {
		$result['message'] = '请选择要上传的图片！';
				$result['error'] = 1;
		exit(json_encode($result));
	}