<?php
$act = new Activity();
isset( $_GP['page'] ) && $act->setPage($_GP['page']);
if ( $_GP['op'] == 'list' ){
    $act_list = $act->getAct();
    include page("activity/activity_list");
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
    include page("activity/activity");
}else if ( $_GP['op'] == 'dish' ){
	$act_list = $act->getAct();
	if ( !empty($_GP['ac_list']) ){
         $act->actId = $_GP['ac_list'];
	}
	$list_op = array(
         '批量审核通过' =>1,
		 '批量审核失败' =>2
	);
	$in_list = $act->findIndustry();
	$category = $act->findCategory();
	$au_list = $act->getAuDish();
	$au_reason = $act->getAuReason();
	if ( !empty($_GP['id']) && !empty($_GP['value']) ){
		if ( !is_array($_GP['id']) ){
            $data = array( 'id'=>$_GP['id'], 'ac_dish_status'=>$_GP['value'] );
			if ( $_GP['value'] == 2 ){
                 $act->reason = $_GP['reason'];
			}
			$result = $act->setAuDish($data);
			if ($result){
                message('操作成功', 'refresh', 'success');
			}else{
				message( $act->errno, '' , 'error' );
			}
		}else{
             foreach( $_GP['id'] as $auId ){
                  $data = array( 'id'=>$auId, 'ac_dish_status'=>$_GP['value'] );
				  if ( $_GP['value'] == 2 ){
                       $act->reason = $_GP['reason'];
				  }
				  $result = $act->setAuDish($data);
			 }
			 exit;
		}
	}
	$pager = $act->pagination;
    include page("activity/activity_dish_check");
}else if ( $_GP['op'] == 'area_list' ){
    $area_list = $act->findArea();
    include page("activity/activity_area_list");
}else if ( $_GP['op'] == 'area' ){
	if ( !empty($_GP['ac_status']) && checksubmit('submit') ){
         $act->setDiffArea($_GP['ac_status']);
	}
    $area = $act->findDiffArea();
    include page("activity/activity_area");
}else if($_GP['op'] == 'showdish'){
	if(empty($_GP['ac_id'])){
		message('参数有误！');
	}
	$page   = max(1, $_GP['page']);
	$psize  = 25;
	$au_list = mysqld_selectall("SELECT a.*,b.* FROM ".table('activity_dish')." as a left join ".table('shop_dish')." as b on a.ac_shop_dish = b.id WHERE a.ac_dish_status = 1 and a.ac_action_id={$_GP['ac_id']} limit ".($page - 1) * $psize . ',' . $psize);
	$total   = mysqld_selectcolumn("SELECT count(a.ac_dish_id) FROM ".table('activity_dish')." as a left join ".table('shop_dish')." as b on a.ac_shop_dish = b.id WHERE a.ac_dish_status = 1 and a.ac_action_id={$_GP['ac_id']}");
	$pager   = pagination($total, $page, $psize);
	include page("activity/activity_dish_check");
}