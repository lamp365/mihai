<?php

$op = empty($_GP['op'])? 'deleted':$_GP['op'];

if($op == 'deleted'){
	//软删除
	$award = mysqld_select("SELECT * FROM " . table('addon7_award') . "  WHERE id = :id", array(':id' => intval($_GP['id'])));

	$request = mysqld_select("select id from ".table('addon7_request')." where award_id={$_GP['id']}");
	if(!empty($request)){
		//当有人参与的时候，不能删除或者修改时间
		//开始中的礼品 不能进行删除操作
		message('该礼品在进行中，不能修改！', 'refresh', 'success');
	}
	mysqld_update('addon7_award',array("deleted"=>$_GP['deleted']),array("id"=>intval($_GP['id'])));
	message('操作成功！', 'refresh', 'success');
}else if($op == 'remove_jifen'){
	//移除积分兑换
	mysqld_update('addon7_award',array("add_jifen_change"=>0),array("id"=>intval($_GP['id'])));
	message('操作成功！', 'refresh', 'success');
}

