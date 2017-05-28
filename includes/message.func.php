<?php
/*
message
*/

function message($msg, $redirect = '', $type = '', $successAutoNext = true)
{
    global $_CMS;
    if ($redirect == 'refresh') {
        $redirect = refresh();
    }
    if ($redirect == '') {
        $type = in_array($type, array(
            'success',
            'error',
			'order',
            'ajax'
        )) ? $type : 'error';
    } else {
        $type = in_array($type, array(
            'success',
            'error',
			'order',
            'ajax'
        )) ? $type : 'success';
    }
    if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || $type == 'ajax') {
        $vars = array();
        $vars['message'] = $msg;
        $vars['redirect'] = $redirect;
        $vars['type'] = $type;
        exit(json_encode($vars));
    }
    if (empty($msg) && ! empty($redirect)) {
        header('Location: ' . $redirect);
    }
	if ( $type == 'order' ){
        include themePage('noidentity');    
	}else{
        include page('message');
	}
    exit();
}