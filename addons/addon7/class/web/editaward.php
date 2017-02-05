<?php
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
		 if($endtime != $award['endtime']){
			 //如果已经开始了，时间不能修改
			 if($award['state'] >=1){
				 message('活动在进行中，不能修改时间',refresh(),'error');
			 }
		 }
  		 $update=array(
			'names' => $_GP['names'],
			'title' => $_GP['title'],
			'amount' => intval($_GP['amount']),
			'endtime' =>  $endtime,
			 'price' => $_GP['price'],
		   'gold'=> $_GP['gold'],
		   'awardtype'=> intval($_GP['awardtype']),
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
		}
  	 
		mysqld_update('addon7_award', $update,array("id"=>intval($_GP['id'])));
		message('保存成功', 'refresh', 'success');
	}
 	include addons_page('award');