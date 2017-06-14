<?php
$award_info = array(
	1 => '自定义',
	2 => '优惠卷',
	3 => '自有商品'
);

    $config = mysqld_select("SELECT * FROM " . table('addon7_config') );
     $award = mysqld_select("SELECT * FROM " . table('addon7_award') . "  WHERE id = :id", array(':id' => intval($_GP['id'])));
     if (checksubmit("submit")) {
		 if($_GP['amount'] != $award['amount']){
			 if($award['state'] >=1){
				 message('已经满人了，不能修改份数大小',refresh(),'error');
			 }else{
				 //处于正在进行中，改大改小 最小不能小于参与人数
				 if($_GP['amount']<=$award['dicount']){
					 message("目前已经有{$award['dicount']}人在参与，不能改小",refresh(),'error');
				 }
			 }
		 }
		 $endtime = strtotime($_GP['endtime']);

		 //如果已经开始了，时间不能修改
		 $request = mysqld_select("select id from ".table('addon7_request')." where award_id={$_GP['id']}");
		 if(!empty($request)){
			 //当有人参与的时候，不能删除或者修改时间
			 if($endtime != $award['endtime']) {
				 message('活动在进行中，不能修改时间');
			 }
			 if($_GP['deleted'] != $award['deleted'] &&   $award['deleted'] == 1) {
				 message('活动在进行中，不能删除');
			 }
		 }



  		 $update=array(
			'names' => $_GP['names'],
			'title' => $_GP['title'],
			 'gid'   => $_GP['gid'],
			 'award_type' => $_GP['award_type'],
			 'deleted'=> $_GP['deleted'],
			'amount' => intval($_GP['amount']),
			'endtime' =>  $endtime,
			 'price' => $_GP['price'],
		   'gold'=> $_GP['gold'],
		  'isrecommand'=>intval($_GP['isrecommand']),
			'credit_cost' => intval($_GP['credit_cost']),
			 'content' => htmlspecialchars_decode($_GP['content'])
		 );
  	   	if (!empty($_FILES['logo']['tmp_name'])) {
				$upload = file_upload($_FILES['logo']);
				if (is_error($upload)) {
					message($upload['message'], '', 'error');
				}
				$logo = $upload['path'];
		}
		if(!empty($logo))
		{
			$update['logo']=$logo;
		}else if(!empty($_GP['choose_thumb'])){
			$update['logo']=$_GP['choose_thumb'];
		}
		 //是否开启了积分兑换
		 if($config['open_gift_change'] == 1){
			 $update['add_jifen_change'] = $_GP['add_jifen_change'];
			 $update['jifen_change']     = intval($_GP['jifen_change']);
		 }
		mysqld_update('addon7_award', $update,array("id"=>intval($_GP['id'])));
		message('保存成功', 'refresh', 'success');
	}
 	include addons_page('award');