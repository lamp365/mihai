<?php
/*
url组合方法
*/

function create_url($module, $params = array())
{
    global $_CMS;
    if (empty($params['name'])) {
        $params['name'] = strtolower($_CMS['module']);
    }
    $queryString = http_build_query($params, '', '&');
    return 'index.php?mod=' . $module . (empty($do) ? '' : '&do=' . $do) . '&' . $queryString;
}

function web_url($do, $querystring = array())
{
    global $_CMS;
    if (empty($querystring['name'])) {
        $querystring['name'] = strtolower($_CMS['module']);
    }
    $querystring['do'] = $do;
    return create_url('site', $querystring);
}

function mobile_url($do, $querystring = array())
{
    global $_CMS;
    if (empty($querystring['name'])) {
        $querystring['name'] = strtolower($_CMS['module']);
    }
    $querystring['do'] = $do;
    return create_url('mobile', $querystring);
}


function get_wapshoper_url($openid){
    $accesskey = getOpenshopAccessKey($openid);
    $uri = create_url('mobile',array('name'=>'shopwap','do'=>'openshop_home','accesskey'=>$accesskey));
    return WEBSITE_ROOT .$uri;
}

function get_wapgoods_url($openid,$dishid){
    if(empty($openid)){
        $uri =  create_url('mobile',array('name'=>'shopwap','do'=>'detail','id'=>$dishid));
    }else{
        $accesskey = getOpenshopAccessKey($openid);
        $uri =  create_url('mobile',array('name'=>'shopwap','do'=>'detail','accesskey'=>$accesskey,'id'=>$dishid));
    }
     return WEBSITE_ROOT .$uri;
}