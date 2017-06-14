<?php
    $member_type = $_SESSION[MOBILE_ACCOUNT]['member_type'];
    $_SESSION    = array();
    session_destroy(); 
    session_start(); 
    if($member_type == 2 ){
        header("location:" . WEBSITE_ROOT);
    }else{
        header("location:". create_url('site', array('name' => 'public','do' => 'index')));
    }
	