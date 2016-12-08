<?php
set_time_limit(1800000) ;
setlocale(LC_ALL, 'zh_CN');
$cfg = globaSetting();
require 'includes/hottpoic.func.php';

$operation = ! empty($_GP['op']) ? $_GP['op'] : 'display';

if ($operation == 'display') {
	getHottpoic(0);
	$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . "  where deleted=0 AND parentid=0 ORDER BY parentid ASC, displayorder ASC");
	// dump($category);
	$list = mysqld_selectall("SELECT * FROM " . table('shop_hottopic'));
	$list = ary_data($list);
	// dump($list);
	// $list = json_encode($list);
	include page('hottopic_list');
}elseif ($operation == 'add') {
	$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . "  where deleted=0 AND parentid=0 ORDER BY parentid ASC, displayorder ASC");
	if (checksubmit('submit')) {
		if ($_GP['classify'] == 'nil' or empty($_GP['description'])) {
			message('分类或热搜词为空!',refresh(),'error');
		}
		$data = array('classify_id' => $_GP['classify'], 'hottopic' => $_GP['description']);
		mysqld_insert('shop_hottopic', $data);
		message('增加成功！',web_url('hottopic'),'succes');
		return;
	}
	include page('hottopic_add');
}elseif ($operation == 'edit') {
	$id = $_GP['id'];
	$isEdit = true;
	$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . "  where deleted=0 AND parentid=0 ORDER BY parentid ASC, displayorder ASC");
	$this_hot = mysqld_select('SELECT * FROM '.table('shop_hottopic')." WHERE  id=:uid" , array(':uid'=> $id));
	// dump($this_hot);
	$this_classify = mysqld_select('SELECT * FROM '.table('shop_category')." WHERE  id=:uid" , array(':uid'=> $this_hot['classify_id']));

	if (checksubmit('submit')) {
		if ($_GP['classify'] == 'nil' or empty($_GP['description'])) {
			message('分类或热搜词为空!',refresh(),'error');
		}
		$data = array('classify_id' => $_GP['classify'], 'hottopic' => $_GP['description']);
		mysqld_update('shop_hottopic', $data, array('id'=> $_GP['id']));
		message('修改成功！',web_url('hottopic'),'succes');
		return;
	}
	include page('hottopic_add');
}elseif ($operation == 'delete') {
	mysqld_delete('shop_hottopic', array('id'=>$_GP['id']));
	message('删除成功',refresh(),'success');	
}

// 数组数据处理
function ary_data($ary) {
	for ($i=0; $i < count($ary); $i++) { 
		$token = strtok($ary[$i]['hottopic'], ";");
	    $u_ary = array();
	    while ($token !== false) {
	        array_push($u_ary, $token);
	        $token = strtok(";");
	    }
	    $ary[$i]['name'] = $u_ary;
	    $ary[$i]['url'] = array();
	    foreach ($u_ary as $u_v) {
	    	$url = build_url($u_v);
	    	array_push($ary[$i]['url'], $url);
	    }
	  //   $name = array();
	  //   $url = array();
	  //   foreach ($u_ary as $a_v) {
	  //   	$result = array(); 
			// preg_match_all("/(?:\[)(.*)(?:\])/i",$a_v, $result);
			// array_push($url, $result[1][0]);
			// $re2 = array();
			// preg_match_all("/^(.*)(?:\[)/i",$a_v, $re2);
			// array_push($name, $re2[1][0]);
			// // return $result[1][0]; 
	  //   }
	  //   $ary[$i]['name'] = $name;
	  //   $ary[$i]['url'] = $url;
	}

	return $ary;
}

// 生成链接
function build_url($hot) {
	return create_url('mobile', array('name' => 'shopwap','do' => 'goodlist','keyword'=>$hot));
}

// 获取指定分类Id数据
function get_classify_hot($id) {
	$data = mysqld_select('SELECT * FROM '.table('shop_hottopic')." WHERE classify_id=$id");
	return $data;
}