<?php

 $is_login = is_login_account();


  if($_GP['title']!='' && $_GP['address']!='' && $_GP['descript']!='')
  {      
      
  if (empty($_GP['title'])) {
        message("请输入菜品名称");
    }
    if (empty($_GP['address'])) {
        message("请输入您家乡的地址");
    }
    if (empty($_GP['descript'])) {
        message("请输入些您的意见建议");
    }
    
    if(!$is_login)
    {
        message("您还没有登录，请登录后操作");
    }
    
    //$strmess = unserialize($_COOKIE["mess"]);
    $messid=0;
    $member = get_member_account(false);
    $member = member_get($member['openid']);
    if (empty($member['openid'])) {
        $member = get_member_account(false);
        $member['createtime'] = date('Y-m-d H:i:s');
    }
   
    if($is_login)
    {
        $messid=$member['mess_id'];
    }
    else
    {         
        $openid = $member['openid'];
        $messid = mysqld_select("SELECT * FROM " . table('weixin_mess') . " WHERE openid = :openid", array(
            ':openid' => $openid));
        $messid =$messid["mess_id"];
    }
    
    $totalid= mysqld_selectall("SELECT * FROM " . table('user_advise') . " where openid=:openid", array(":openid"=>$member['openid'])); 
  
    
    
    if(is_array($totalid)&& sizeof($totalid)>3)
    {
        message("您已经提交过3个家乡菜了，请不要再次提交");  
        exit();
    }    
    $infopath ="";
    if (!empty($_FILES['thumb']['tmp_name'])) {
        $upload = file_upload($_FILES['thumb']);
        if (is_error($upload)) {
            message($upload['message'], '', 'error');
        }       
        $infopath = $upload['path'];       
    }  
    else
    {
        message("请上传您家乡菜的图片");
        exit();
        
    }
        
    
    $data = array(
        'title' => $_GP['title'],
        'descript' => $_GP['descript'],
        'address'=> $_GP['address'],
        'createtime' => time(),
        'openid'=>$openid,
        'thumb'=>$infopath,
        'mess_id' => $messid,     
    );
    
    mysqld_insert('user_advise', $data);
    $gourl =create_url('mobile', array(
        'name' => 'shopwap',
        'do' => 'index'
    ));
    message("你的家乡菜已经提交成功，感谢您的参与",$gourl,"success");  
  }


include themePage('need');