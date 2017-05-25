<?php
/*
url组合方法
*/
//
function css_url($file_name, $dir_name='', $document = '', $template = '' ){
    $file_html = create_style($file_name, $dir_name, $document, 0, $template);
    echo $file_html;
}
function js_url($file_name, $dir_name='', $document = '', $template = ''){
    $file_html = create_style($file_name, $dir_name, $document, 1, $template);
    echo $file_html;
}
/*
    $type : 0 css 1 js
    $dir_name : 在 recouse 下的扩展目录
	$template : 模板目录
*/
function create_style($file_name, $dir_name='', $document='', $type= 0, $template=''){
    $template = '';
    if ( empty ( $file_name ) ){
        return false;
    }
    $det = ($type == 1)? 'js' : 'css';
    if ( !empty ( $template ) ){
        $find_dir = $template;
    }else{
        $find_dir = is_mobile_request() ? 'wap' : 'default' ;
    }
    if ( empty( $document ) ){
        $theme = array('themes', $find_dir, '__RESOURCE__', 'recouse');
    }else{
        $find_dir = $document;
        $theme = array( 'assets' , $find_dir );
    }
    if ( !empty ($dir_name) ) {
        $theme[] = $dir_name;
    }
    $theme[] = $det;
    $theme[] = $file_name;
    $theme_dir = implode('/', $theme);
    $file = SYSTEM_WEBROOT .'/'. $theme_dir . '.' . $det ;
    if ( is_file( $file )){
        $ver = filemtime($file);
        $style_html = WEBSITE_ROOT . $theme_dir . '.' . $det . '?ver=' . $ver;
        if ( $type == 1 ){
            return  '<script type="text/javascript" src="'.$style_html.'"></script>';
        }else{
            return  '<link type="text/css" rel="stylesheet" href="'.$style_html.'" />';
        }
    }
    return false;
}
function create_url($module, $params = array())
{
    global $_CMS;
    if (empty($params['name'])) {
        $params['name'] = strtolower($_CMS['module']);
    }
    $queryString = http_build_query($params, '', '&');
    $rewrite = 1;
    // 开启伪静态开关
    if ( $rewrite == 1 ){
        switch ( $module ){
            case 'mobile':
                $seo_url = array(
                    'public'=>'public',
                    'shop'=>'shop',
                    'shopwap'=>'shopwap',
                );
                $url = false;
                foreach( $seo_url as $key=>$value ){
                    if ( $key == $params['name'] ){
                        $url = true;
                    }
                }
                if ( $url ){
                    if(empty($params['op']))
                        $return_url =  WEBSITE_ROOT.$params['name'].'/'.$params['do'].'.html';
                    else
                        $return_url =  WEBSITE_ROOT.$params['name'].'/'.$params['do'].'/'.$params['op'].'.html';

                    //多余的参数作为？传参
                    unset($params['mod']); unset($params['name']);
                    unset($params['do']);  unset($params['op']);
                    $queryString    = empty($params) ? '' : http_build_query($params,'','&');
                    !empty($queryString) && $return_url .= "?".$queryString;
                    //多余参数最后是 http://dev-cbd.com/seller/product/goodstype.html?act=ss  暂时先这样
                    return $return_url;
                }else{
                    return WEBSITE_ROOT.'index.php?mod=' . $module . (empty($do) ? '' : '&do=' . $do) . '&' . $queryString;
                }
                break;
            default:
                return WEBSITE_ROOT.'index.php?mod=' . $module . (empty($do) ? '' : '&do=' . $do) . '&' . $queryString;
                break;
        }
    }else{
        return WEBSITE_ROOT.'index.php?mod=' . $module . (empty($do) ? '' : '&do=' . $do) . '&' . $queryString;
    }
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