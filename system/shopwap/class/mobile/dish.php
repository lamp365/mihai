<?php
switch ($_GP['op']){
   case 'list':
        $sorturl = mobile_url('goodlist', array("keyword" => $_GP['keyword'], "pcate" => $_GP['pcate'], "ccate" => $_GP['ccate']));
        $children = array();
        $category = mysqld_selectall("SELECT * FROM " . table('shop_dish') . " WHERE  status=1 ORDER BY createtime DESC");
        foreach ($category as $index => $row) {
            if (!empty($row['parentid'])) {
                $children[$row['parentid']][$row['id']] = $row;
                unset($category[$index]);
            }
        }
		$messid=0;
        $member = get_member_account(false);
        $member = member_get($member['openid']);
		if (empty($member['openid'])) {
			$member = get_member_account(false);
			$member['createtime'] = time();
		}
		$is_login = is_login_account();
		$openid = $member['openid'];
		if($is_login)
		{
			$messid=$member['mess_id'];
		}
		else
		{         
			$messid = mysqld_select("SELECT * FROM " . table('weixin_mess') . " WHERE openid = :openid", array(
				':openid' => $openid));
			$messid =$messid["mess_id"];			
		}
		$date = getdate(time());
	    $date = $date['year'].$date['yday'];
	    $hadcomment = mysqld_select('SELECT dishid FROM '.table('shop_dish_comment')." WHERE messid=:mid and  createtime =:ts and openid =:oid" , array(':mid'=>$messid, ':ts'=>$date, ':oid'=>$openid));
        $list = mysqld_selectall("SELECT id,title,dishsn FROM " . table('shop_dish') . " WHERE   status = '1'  ORDER BY createtime DESC  ");
        foreach ( $list as $key=>$value ) {
			$dishsn = unserialize($value['dishsn']);
            if (is_array($dishsn)){
			if ( !in_array($messid, $dishsn)){
                  unset($list[$key]);
				  continue;
			}
			}else{
                  continue;
			}
			$count = mysqld_selectcolumn('SELECT count(*) FROM '.table('shop_dish_comment')." WHERE messid=:mid and dishid=:id " , array(':mid'=>$messid, ':id'=> $value['id']));
			$list[$key]['count'] = $count;
        }
		usort($list, function($a, $b) {
            $al = $a['count'];
            $bl = $b['count'];
            if ($al == $bl)
                return 0;
            return ($al > $bl) ? -1 : 1;
        });
	   include themePage('dishlist');
	   break;
   default:
	    $messid=0;
        $member = get_member_account(false);
        $member = member_get($member['openid']);
		if (empty($member['openid'])) {
			$member = get_member_account(false);
			$member['createtime'] = time();
		}
		$is_login = is_login_account();
		$openid = $member['openid'];
		if($is_login)
		{
			$messid=$member['mess_id'];
		}
		else
		{         
			$messid = mysqld_select("SELECT * FROM " . table('weixin_mess') . " WHERE openid = :openid", array(
				':openid' => $openid));
			$messid =$messid["mess_id"];			
		}
		$dates = getdate(time());
	    $date = $dates['year'].$dates['yday'];
		$hadcomment = mysqld_selectcolumn('SELECT count(*) FROM '.table('shop_dish_comment')." WHERE messid=:mid and createtime =:ts and openid =:oid" , array(':mid'=>$messid, ':ts'=>$date, ':oid'=>$openid));
		if ($hadcomment > 0){
              $result = 0;
		}else{
              $data= array('createtime'=> $date, 'openid'=> $openid, 'dishid'=>$_GP['id'], 'messid'=>$messid);
	          mysqld_insert('shop_dish_comment', $data);
			  $result = 2;
		}
		$count = mysqld_selectcolumn('SELECT count(id) FROM '.table('shop_dish_comment')." WHERE messid=:mid and dishid=:id " , array(':mid'=>$messid, ':id'=> $_GP['id']));
	    $result = array(
           'result' => $result,
           'total'  => $count
        );
	    die(json_encode($result));
	    break;
}