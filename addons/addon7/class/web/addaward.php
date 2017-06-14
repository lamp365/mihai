<?php
  $op = empty($_GP['op']) ? 'display' : $_GP['op'];
$award_info = array(
	1 => '自定义',
	2 => '优惠卷',
	3 => '自有商品'
);
 if($op == 'display'){
	 $config = mysqld_select("SELECT * FROM " . table('addon7_config') );
	 if (checksubmit("submit")) {
		 $insert=array(
			 'names' => $_GP['names'],
			 'title' => $_GP['title'],
			 'gid'   => $_GP['gid'],
			 'award_type' => $_GP['award_type'],
			 'amount' => intval($_GP['amount']),
			 'dicount'=> 0,
			 'isrecommand'=>intval($_GP['isrecommand']),
			 'endtime' => strtotime($_GP['endtime']),
			 'price' => $_GP['price'],
			 'gold'=> $_GP['gold'],
			 'deleted'=> $_GP['deleted'],
			 'credit_cost' => $_GP['credit_cost'],
			 'createtime' => time(),
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
			 $insert['logo']=$logo;
		 }else if(!empty($_GP['choose_thumb'])){
			 $insert['logo']=$_GP['choose_thumb'];
		 }

		 //是否开启了积分兑换
		 if($config['open_gift_change'] == 1){
			 $insert['add_jifen_change'] = $_GP['add_jifen_change'];
			 $insert['jifen_change']     = intval($_GP['jifen_change']);
		 }
		 mysqld_insert('addon7_award', $insert);
		 message('保存成功', web_url('awardlist'), 'success');
	 }

	 include addons_page('award');

 }else if($op == 'get_bonus'){
	 //获取优惠卷
	 $time = time();
	 if(empty($_GP['send_type'])){
		 $where = " where send_type = 0 and {$time}<send_end_date";
	 }else{
		 $where = " where send_type = {$_GP['send_type']} and {$time}<send_end_date";
	 }
	 $all_bonus = mysqld_selectall("select type_id,type_name,type_money,min_goods_amount,send_type,send_end_date from ".table('bonus_type')." {$where} ");
	 //获取优惠卷类型数组
	 $bonus_enum_arr = get_bonus_enum_arr();

	 if(!empty($_GP['showajax'])){
		 if(empty($all_bonus)){
			 die(showAjaxMess('1002','暂无数据！'));
		 }else{
			 die(showAjaxMess('200',$all_bonus));
		 }
	 }

	 include addons_page('ajaxload_get_bonus');

 }else if($op == 'get_goods'){
	//获取宝贝
	 if(empty($_GP['title'])){
		 die(showAjaxMess('1002','参数有误！'));
	 }

	 $condition['table'] = 'shop_dish';
	 $condition['limit'] = '15';
	 $condition['where'] = "a.title like '%{$_GP['title']}%'";
	 $data = get_goods($condition);
	 if(empty($data)){
		 die(showAjaxMess('1002','暂无数据！'));
	 }else{
		 die(showAjaxMess('200',$data));
	 }
 }
