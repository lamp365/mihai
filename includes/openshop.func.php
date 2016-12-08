<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/22 0022
 * Time: 11:18
 */

/**
 * 是否已经开店了
 */
function checkIsOpenshop()
{
    $member     = get_member_account(false);
    if(empty($member['openshop_id'])){
        $shopData   = mysqld_select("select id  from ".table('openshop')." where openid = ".$member['openid']);
        if(empty($shopData)){
            return false;
        }else{
            return true;
        }
    }else{
        return true;
    }
}

/**
 * 验证地址accesskey
 */
function checkOpenshopAccessKey()
{
    if(empty($_REQUEST['accesskey'])){
        message('对不起，非法访问！',refresh(),'error');
    }else{
        $openid = decodeOpenshopAccessKey($_REQUEST['accesskey']);
        if($openid){
            $user = mysqld_select("select id from ".table('openshop')." where openid='{$openid}'");
            if(empty($user))
                message("对不起该分享者还不是商家！",refresh(),'error');
            return $openid;
        }else{
            message('对不起，非法访问,参数有误！',refresh(),'error');
        }

    }
}

/**
 * 得到店铺卖家的openid
 * @return bool|int
 */
function getOpenshopSellerOpenid(){
    if(empty($_REQUEST['accesskey'])){
        return 0;
    }else{
        $openid = decodeOpenshopAccessKey($_REQUEST['accesskey']);
        if($openid){
            return $openid;
        }else{
            return 0;
        }
    }
}

/**
 * @param $openid 卖家id
 * @content 给每个商家统计当天访问量，相同ip只能统计一次，8小时后算为过期
 */
function countOpenshopView($openid)
{
    $cookie_key = 'view_key';
    $ip = md5(getip());

    $zeor_time = strtotime(date('Y-m-d'),time());  //今天凌晨时间戳
    $result = mysqld_select("select id,uv,pv,time from ". table('openshop_viewreport') ." where seller_openid='{$openid}' and time = {$zeor_time}");
    if(empty($result)){
        $data = array(
            'time' => $zeor_time,
            'pv' => 1,
            'uv' => 1,
            'seller_openid' => $openid
        );
        mysqld_insert("openshop_viewreport",$data);
    }else{
        $pv = $result['pv']+1;
        $uv = $result['uv'];   //实际ip
        if(empty($_COOKIE[$cookie_key])){
            $uv = $uv +1;
            setcookie($cookie_key,$ip,time()+3600*8);
        }else if($_COOKIE[$cookie_key] != $ip){
            $uv = $uv +1;
            setcookie($cookie_key,$ip,time()+3600*8);
        }
        $data = array('uv'=>$uv,'pv'=>$pv);
        mysqld_update('openshop_viewreport',$data,array('seller_openid'=>$openid));
    }

}


//从服务器获取访客ip
function getip(){
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_CDN_SRC_IP'])) {
        $ip = $_SERVER['HTTP_CDN_SRC_IP'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] as $xip) {
            if (! preg_match('#^(10|172\.16|192.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    }
    return $ip;
}

/**
 * @content  获取卖家所有商品中对应的 分类栏目
 * @param $saler_openid  卖家的openid
 * @return array
 */
function getGoodHaveCategory($saler_openid){
    $goods    = mysqld_selectall("select id,p1,p2,p3 from " .table('openshop_relation'). " where openid={$saler_openid} and status =1" );
    if(empty($goods)){
        $children = $category = array();
        return array('children'=>$children,'category'=>$category);
    }else{
        $catids = array();
        foreach($goods as $row){
            if(!in_array($row['p1'],$catids)){
                $catids[] = $row['p1'];
            }
            if(!in_array($row['p2'],$catids)){
                $catids[] = $row['p2'];
            }
            if(!in_array($row['p3'],$catids)){
                $catids[] = $row['p3'];
            }
        }

        //以上获取商品对应的分类id    接下来获取所有分类
        $category = mysqld_selectall("SELECT id,name,thumb,parentid FROM " . table('shop_category') . "  where deleted=0  ORDER BY parentid ASC, displayorder ASC");

        $newCategory = array();
        foreach($category as  $row){
            $newCategory[$row['id']] = $row;
        }

        //去除所有发类中  有些商品没有的
        foreach($newCategory as $cid => $val){
            if(!in_array($cid,$catids)){
                unset($newCategory[$cid]);
            }
        }

        $children = array();
        foreach ($newCategory as $index => $row) {
            if (!empty($row['parentid'])) {
                $children[$row['parentid']][] = $row;
                unset($newCategory[$index]);
            }
        }
        return array('children'=>$children,'category'=>$newCategory);
    }
}

/**
 * 通过商家添加商品和dish表关联后，有些商品可能没有价格标题，故需要再次查询从googs表获取
 * @param $list
 * @return mixed
 */
function getEachGoodInfo($list){
    $id = $list['id'];
    if(empty($list['title']) || empty($list['marketprice']) || empty($list['productprice']) || empty($list['thumb'])){
        $sql = "select a.id,b.title,b.description,b.marketprice,b.productprice,b.thumb from " .table('shop_dish') ." as a left join " .table('shop_goods'). " as b on a.gid=b.id where a.id={$id}";
        $info = mysqld_select($sql);
        if(empty($list['title']))                                        $list['title']        = $info['title'];
        if(empty($list['thumb']))                                         $list['thumb'] = $info['thumb'];
        if(empty($list['marketprice']) || $list['marketprice'] == 0)     $list['marketprice']  = $info['marketprice'];
        if(empty($list['productprice']) || $list['productprice']==0)     $list['productprice'] = $info['productprice'];
    }
    return $list;
}



function getGoodsShangjiaStatusBygoodId($goodid,$openid){
    $result = mysqld_select("select id,status from ".table('openshop_relation')." where goodid={$goodid} and openid='{$openid}'");
    if(empty($result)){
        $result = array('id'=>'','status'=>'');
    }
    return $result;
}

/**
 * 得到商家的主页url,用于分享出去
 * @param $openid
 * @return string
 */
function getShoperWebUrl($openid){
    $accesskey = getOpenshopAccessKey($openid);
    $host = 'http://'.$_SERVER['HTTP_HOST'];
    $url = mobile_url('openshop_home',array('accesskey'=>$accesskey));
    return rtrim($host,'/').'/'.$url;
}
