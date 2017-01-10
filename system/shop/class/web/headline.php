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
		
		$data = array('isrecommand' 	=> $isrecommand,
						'modifiedtime' 	=> time()
		);
		
		mysqld_update ( 'headline', $data, array ('headline_id' => $headline_id) );
		
		message ( '觅海头条成功！', web_url ( 'headline'), 'success' );

		break;

	case 'edit':		//编辑页

		$headline = mysqld_select ( "SELECT * FROM " . table ( 'headline' ) . " where headline_id=".(int)$_GP ['headline_id'] );

		include page ( 'headline' );

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