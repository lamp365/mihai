<?php
	/**
	 * app 点赞操作
	 * @author WZW
	 * 
	 */

	$result = array();
	
	$op = $_GP['op'];
	$comment_id = $_GP['comment_id'];
	if (empty($op) or empty($comment_id)) {
		$result['message'] = "操作类型或评论ID为空!";
		$result['code']    = 0;
		echo apiReturn($result);
		exit;
	}

	$comment = mysqld_select("SELECT * FROM ".table('shop_goods_comment')." WHERE id=".$comment_id);
	if (empty($comment)) {
		$result['message'] = "评论不存在!";
		$result['code']    = 0;
	}else{
		if ($op == 'add') {
			mysqld_query("UPDATE ".table('shop_goods_comment')." SET praise_num=praise_num+1 WHERE id=".$comment_id);
			$result['message'] = "点赞成功!";
			$result['code']    = 1;
		}elseif ($op == 'del') {
				mysqld_query("UPDATE ".table('shop_goods_comment')." SET praise_num=if(praise_num>=1,praise_num-1,0) WHERE id=".$comment_id);
				$result['message'] = "取消赞成功!";
				$result['code']    = 1;
		}
	}

	echo apiReturn($result);
	exit;
