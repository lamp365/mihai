<?php
/**
 * 图文笔记管理
 * @var unknown
 */

$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';

switch($operation){

	case 'post':		//提交

		$note_id 	= intval ( $_GP ['note_id'] );
		$isrecommand	= (int)$_GP['isrecommand'];
		
		$data = array('isrecommand' 	=> $isrecommand,
						'modifiedtime' 	=> time()
		);
		
		mysqld_update ( 'note', $data, array ('note_id' => $note_id) );
		
		message ( '更新图文笔记成功！', web_url ( 'note'), 'success' );

		break;

	case 'edit':		//编辑页

		$note = mysqld_select ( "SELECT * FROM " . table ( 'note' ) . " where note_id=".(int)$_GP ['note_id'] );

		include page ( 'note' );

		break;

	default:			//列表页
		
		$pindex= max(1, intval($_GP['page']));
		$limit = 20;

		$list = mysqld_selectall ( "SELECT SQL_CALC_FOUND_ROWS * FROM " . table ( 'note' ) . " where deleted=0 ORDER BY createtime DESC limit ".(($pindex-1)*$limit).','.$limit);

		$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
		
		$pager = pagination($total['total'], $pindex,$limit);
		
		include page ( 'note_list' );

		break;
}