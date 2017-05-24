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

/**
 * @param $openid
 * @param $dishid
 * @return string
 * 该方法是给APP调用的分享商品地址，会带有is_app参数，因为pc和app有点不一样，app分享出来的商品只能该商品有佣金，而pc的操作分享就是开店。
 */
function get_wapgoods_url($openid,$dishid){
    if(empty($openid)){
        $uri =  create_url('mobile',array('name'=>'shopwap','do'=>'detail','id'=>$dishid));
    }else{
        $accesskey = getOpenshopAccessKey($openid);
        $uri =  create_url('mobile',array('name'=>'shopwap','do'=>'detail','accesskey'=>$accesskey,'id'=>$dishid,'is_app'=>'1'));
    }
     return WEBSITE_ROOT .$uri;
}