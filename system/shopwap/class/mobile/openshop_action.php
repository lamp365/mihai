<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/20 0020
 * Time: 19:38
 */
/**********
  一些店铺过程中公共要操作的一些方法，比如上架下架，确认开店，收藏等
http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_action&act=xiajia&dishid=12&p1=12&p2=23&p3=16     下架
http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_action&act=shangjia&dishid=45&p1=12&p2=23&p3=16   上架
http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_action&act=openshop                开店
http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_action&act=collectgood&dishid=34             收藏商品
http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_action&act=collectshop&openshop_id=45             收藏店铺
 **********/
$member     = get_member_account(false);
$memberinfo = member_get($member['openid']);

if(empty($memberinfo)){
    die(showAjaxMess(1002,'请您先登录！'));
}

if(empty($_GP['act'])){
    die(showAjaxMess(1002,'操作有误，参数不对！'));
}

switch($_GP['act']){

    case 'collectgood':  //收藏商品
        if(empty($_GP['dishid'])){
            echo showAjaxMess(1002,'操作有误，参数不对！');
        }else{
            //是否收藏过
            $result = mysqld_select("select id from ". table('goods_collection') ." where dish_id={$_GP['dishid']} and openid={$member['openid']}");
            if(!empty($result)) {
                echo showAjaxMess('200', '该商品之前收藏过！');
            }else{
                $data = array(
                    'openid'  => $member['openid'],
                    'dish_id' => $_GP['dishid'],
                    'createtime'  => time()
                );
                if(!empty($_GP['openshop_id'])){
                        $data['openshop_id'] = $_GP['openshop_id'];
                }
                mysqld_insert('goods_collection',$data);
                //收藏成功后，商品的收藏数目++
                $result = mysqld_select("select id,collect_num from ".table('shop_dish'). " where id={$_GP['dishid']}");
                if(!empty($result))
                    mysqld_update('shop_dish',array('collect_num'=>$result['collect_num']+1),array('id'=>$result['id']));
                echo showAjaxMess('200','该商品收藏成功！');
            }

        }
        break;


}