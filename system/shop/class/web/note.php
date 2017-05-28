<?php
/**
 * 图文笔记管理
 * @var unknown
 */

$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';

switch($operation){

	case 'post':		//提交

		$note_id 	= intval ( $_GP ['note_id'] );
		$title = $_GP['notetitle'];
		$isrecommand	= (int)$_GP['isrecommand'];
		$ischeck = (int)$_GP['ischeck'];
		$preview = $_GP ['preview'];
		$author = $_GP ['author'];
		
		
		$data = array('isrecommand' 	=> $isrecommand,
						'check'			=> $ischeck,
						'title'			=> $title,
						'description'	=> $preview
		);
		if (!empty($author)) {
			$data['openid'] = $author;
		}
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
		if ($pic[0] == ";") {
			$pic = substr($pic,1);
		}
		$data['pic'] = $pic;
		if (empty($note_id)) {
			$data['createtime'] = time();
			mysqld_insert('note', $data);
		}else{
			$data['modifiedtime'] = time();
			mysqld_update ( 'note', $data, array ('note_id' => $note_id) );
		}
		
		message ( '编辑图文笔记成功！', web_url ( 'note'), 'success' );

		break;

	case 'edit':		//编辑页

		$note = mysqld_select ( "SELECT * FROM " . table ( 'note' ) . " where note_id=".(int)$_GP ['note_id'] );
		// 获取虚拟用户
		$author = mysqld_selectall("SELECT * FROM ".table('member')." WHERE dummy=1 AND avatar<>'' AND realname<>''");
		$now_a = mysqld_select("SELECT dummy FROM ".table('member')." WHERE openid='".$note['openid']."'");
		$now_a = $now_a['dummy'];

		include page ( 'note' );

		break;

	case 'add':
		// 新增
		// 获取虚拟用户
		$author = mysqld_selectall("SELECT * FROM ".table('member')." WHERE dummy=1 AND avatar<>'' AND realname<>''");
		include page ( 'note' );
		break;

	case 'delete':
		// 删除
		$id = intval ( $_GP ['id'] );
		mysqld_update ( 'note', array('deleted'=>1), array ('note_id' => $id) );
		message ( '删除成功', refresh(), 'success' );
		break;
	case 'edit_auth':
		//修改作者
		if(empty($_GP['hide_noteid'])){
			message ( '参数有误', refresh(), 'error' );
		}
		mysqld_update('note',array('openid'=>$_GP['openid']),array('note_id'=>$_GP['hide_noteid']));
		message("修改成功！",refresh(), 'success' );
		break;
	default:			//列表页
		
		$pindex= max(1, intval($_GP['page']));
		$limit = 20;

		$list = mysqld_selectall ( "SELECT SQL_CALC_FOUND_ROWS * FROM " . table ( 'note' ) . " where deleted=0 ORDER BY createtime DESC limit ".(($pindex-1)*$limit).','.$limit);
		$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数

		foreach($list as &$one_list){
			$one_list['name'] = get_realname($one_list['openid']);
		}
		
		$pager = pagination($total['total'], $pindex,$limit);

		//获取虚拟用户，用于修改文章作者
		$dummy_member = mysqld_selectall("select openid,realname,mobile from ".table('member')." where dummy=1 and avatar !=''");
		foreach($dummy_member as &$one_member){
			//是否已经关联过 文章
			$check_note = mysqld_select("select openid from ".table('note')." where openid='{$one_member['openid']}'");
			if($check_note){
				$one_member['choose'] = "》》[已选过]";
			}else{
				$one_member['choose'] = "";
			}
		}
		include page ( 'note_list' );

		break;
}

function get_realname($openid) {
	$r_name = mysqld_select("SELECT * FROM ".table('member')." WHERE openid='".$openid."'");
	$name   = getNameByMemberInfo($r_name);
	return $name;
}