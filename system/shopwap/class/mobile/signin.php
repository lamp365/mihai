<?php
$is_login = is_login_account();



if (!empty($_GP['section']) && $_GP['op'] == 'checkin') {
    
    if (!$is_login) {
         $result=array('result'=>"nologin","message"=>'您还未登录，请登录后签到');
        echo json_encode($result);
        exit();    
    }
    
    
    
    $member = get_member_account(false);
    $member = member_get($member['openid']);
    if (empty($member['openid'])) {
        $member = get_member_account(false);
        $member['createtime'] = time();
    }

    $result='';
    $messid = $member['mess_id'];
    date_default_timezone_set('Asia/Shanghai');
    $difftime = 0;
    $hour = date("H");
    $minute=date("i");
    
    if ($hour<7) {
        $difftime=1;
    }
    else if ($hour==7)
    {        
        if($minute<=30)
        {
            $difftime=1;
        }        
    }   
      
    
 if ($hour<9 && $hour>7) {
         if($hour==7)
         {   
            if($minute>30)
            {
                $difftime=2;
            }
         }
         else 
         {
             $difftime=2;
         }
    }
    else if ($hour==9)
    {
        if($minute<=30)
        {
            $difftime=2;
        }        
    }
    
    if ($hour<15 && $hour>9) {  
        if($hour==9)
        {
            if($minute>30)
            {          
                $difftime=3;
            }
        }
        else 
        {
            $difftime=3;
        }
    }
    else if ($hour==15)
    {
        if($minute<=30)
        {
            $difftime=3;
        }
    }
    
   
    
    if($difftime==0) 
    {
        $result=array('result'=>false,"message"=>'已经过了签到时间，请明日再签到');
        echo json_encode($result);
        exit();        
    }
    
    if($_GP['section']!=$difftime){
        $result=array('result'=>false,"message"=>'你当前已经签到或者不在签到时间，请下次再签到');  
        echo json_encode($result);
        exit();
    }

    $list = mysqld_selectall("select * from " . table('sign_list') . " where datediff(createtime,now())=0 and section=" . $difftime . " and mess_id=:mess_id and openid=:openid", array(
        ":openid" => $member['openid'],":mess_id"=>$messid
    ));

    if (is_array($list) && count($list) > 0) {

        $result=array('result'=>false,"message"=>'你当前已经签到过了，请下次再签到');
        echo json_encode($result);
        exit();
    } else {
        $data = array(
            'realname' => '',
            'createtime' => date('Y-m-d H:i:s'),
            'openid' => $member['openid'],
            'mess_id' => $messid,
            'section'=>$_GP['section']
        );

        mysqld_insert('sign_list', $data);
        $result=array('result'=>true,"message"=>'签到成功');
    }
    echo json_encode($result);
    exit();
}

include themePage('signin');

