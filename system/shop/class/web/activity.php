<?php
$act = new Activity();
isset( $_GP['page'] ) && $act->setPage($_GP['page']);
if ( $_GP['op'] == 'list' ){
    $act_list = $act->getAct();
    include page("activity_list");
}else if ( $_GP['op'] == 'add' ){
	if ( !empty($_GP['id']) ){
		$pro = $act->getAct($_GP['id']);
	}
	$area_list = $act->findArea();
	if (checksubmit('submit')) {
		$check = array('ac_title'=>'标题', 'ac_time_str'=>'开始时间', 'ac_time_end'=>'结束时间', 'ac_list_id' =>'区间', 'ac_status' => '状态');
		foreach( $_GP as $key=>$_GP_value ){
           if ( isset( $check[$key] ) && empty( $_GP_value ) ){
                message($check[$key]."不能为空");
		   }
		} 
		$data  = array(
            'ac_title'          => $_GP['ac_title'],
			'ac_time_str'      => strtotime($_GP['ac_time_str']),
			'ac_time_end'     => strtotime($_GP['ac_time_end']),
			'ac_area'         => $_GP['ac_list_id'],
			'ac_status'        => $_GP['ac_status']
		);
        $result = $act->setAct($data);
		if ( ! $result ){
			 message( $act->errno, '' , 'error' );
		}else{
             message('操作成功', 'refresh', 'success');
		}
	}
    include page("activity");
}else if ( $_GP['op'] == 'dish' ){
	$act_list = $act->getAct();
	if ( !empty($_GP['ac_list']) ){
         $act->actId = $_GP['ac_list'];
	}
	$in_list = $act->findIndustry();
	$category = $act->findCategory();
	$au_list = $act->getAuDish();
	if ( !empty($_GP['id']) && !empty($_GP['value']) ){
        $data = array( 'id'=>$_GP['id'], 'ac_dish_status'=>$_GP['value'] );
        $result = $act->setAuDish($data);
		if ($result){
            message('操作成功', 'refresh', 'success');
		}else{
            message( $act->errno, '' , 'error' );
		}
	}
	$pager = $act->pagination;
    include page("activity_dish_check");
}else if ( $_GP['op'] == 'area_list' ){
    $area_list = $act->findArea();
    include page("activity_area_list");
}else if ( $_GP['op'] == 'area' ){
	if ( !empty($_GP['ac_status']) && checksubmit('submit') ){
         $act->setDiffArea($_GP['ac_status']);
	}
    $area = $act->findDiffArea();
    include page("activity_area");
}