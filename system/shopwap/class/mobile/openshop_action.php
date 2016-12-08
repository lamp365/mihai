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
    case 'shangjia':     //上架
        if(empty($_GP['dishid'])){
            echo showAjaxMess(1002,'操作有误，参数不对！');
        }else{
            if(!checkIsOpenshop()){
                $redirect = create_url('mobile',array('name'=>'shopwap', 'do'=>'openshop_xieyi'));
                message('您还没有开店，还不能代销商品！',$redirect,'error');
            }
            if(!empty($_GP['isAdd'])){   //首次添加
                //插入新的数据
                $goodid = $_GP['dishid'];
                $data   = array(
                    'goodid' => $goodid,
                    'p1'     => $_GP['p1'],
                    'p2'     => $_GP['p2'],
                    'p3'     => $_GP['p3'],
                    'openid' => $member['openid'],
                    'openshopid' => getOpenShopId($member['openid']),
                    'operatetime'=> time(),
                    'status'     => 1
                );

                mysqld_insert('openshop_relation',$data);

                //插入完毕，在更新该商品的有多少人再卖
                $dish = mysqld_select("SELECT id,shoper_num FROM ". table('shop_dish') . " where id=:dishid ",array(
                    ':dishid' => $_GP['dishid']
                ));
                $num  = $dish['shoper_num']+1;
                $data = array('shoper_num' => $num);
                mysqld_update('shop_dish', $data, array('id' => intval($dish['id'])));
            }else{  //添加过了，上架修改的是商家里面的状态
                $goodid = $_GP['dishid'];
                $data = array(
                    'openid' => $member['openid'],
                    'goodid' => $goodid,
                );
                mysqld_update('openshop_relation',array('status'=>1,'operatetime'=> time()),$data);
            }
            echo showAjaxMess(200,"该商品已经成功上架");
        }
        break;

    case 'xiajia':      //下架
        if(empty($_GP['dishid'])){
            echo showAjaxMess(1002,'操作有误，参数不对！');
        }else{
            $goodid = $_GP['dishid'];
            $data   = array(
                'openid' => $member['openid'],
                'goodid' => $goodid,
            );
            mysqld_update('openshop_relation',array('status'=>0,'operatetime'=> time()),$data);
             echo showAjaxMess(200,"该商品已经下架成功");

        }
        break;
    case 'tuijian':      //推荐置顶
        if(empty($_GP['dishid'])){
            echo showAjaxMess(1002,'操作有误，参数不对！');
        }else{
            $goodid = $_GP['dishid'];
            $data   = array(
                'openid' => $member['openid'],
                'goodid' => $goodid
            );
            mysqld_update('openshop_relation',array('is_top'=>$_GP['is_top']),$data);
            if($_GP['is_top'])
                echo showAjaxMess(200,"该商品已经推荐成功");
            else
                echo showAjaxMess(200,"该商品已经取消推荐");

        }
        break;

    case 'delete':      //删除
        if(empty($_GP['dishid'])){
            echo showAjaxMess(1002,'操作有误，参数不对！');
        }else{
            $goodid = $_GP['dishid'];
            $data   = array(
                'openid' => $member['openid'],
                'goodid' => $goodid,
            );
            mysqld_delete('openshop_relation',$data);
            //更新完毕，在更新该商品的有多少人再卖
            $dish = mysqld_select("SELECT id,shoper_num FROM ". table('shop_dish') . " where id=:dishid ",array(
                ':dishid' => $goodid
            ));
            $num  = $dish['shoper_num']-1;
            $data = array('shoper_num' => $num);
            mysqld_update('shop_dish', $data, array('id' => intval($dish['id'])));
            echo showAjaxMess(200,"该商品已经删除成功");

        }
        break;

    case 'openshop':  //同意协议进行开店
        if(empty($_GP['read_xieyi'])){
            message('请您确认开店协议！','','error');
        }
        if(checkIsOpenshop()){
            message('您已经开过店了,如有疑问请联系管理员');
        }
        $data = array(
            'openid'     => $member['openid'],
            'shopname'   => '店铺'.substr(uniqid(),0,4),
            'createtime' => time()
        );
        mysqld_insert('openshop', $data);   //返回影响行数
        if(mysqld_insertid()){
            //开店信息加入到mobile_account  session中
            $shopData   = mysqld_select("select id  from ".table('openshop')." where openid = ".$member['openid']);
            $_SESSION['mobile_account']['openshop_id'] = $shopData['id'];
            $url = create_url('mobile',array('name'=>'shopwap','do'=>'openshop_info'));
            message('开店成功，现在您可以代销商品！',$url);
        }else{
            message('开店失败,请联系管理员！','','error');
        }

        break;

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


    case 'collectshop':   //收藏店铺
        if(empty($_GP['openshop_id'])){
            echo showAjaxMess(1002,'操作有误，参数不对！');
        }else{
            //是否收藏过
            $result = mysqld_select("select id from ". table('goods_collection') ." where openshop_id={$_GP['openshop_id']} and openid={$member['openid']}");
            if(!empty($result)){
                echo showAjaxMess('200','该店铺之前收藏过！');
            }else{
                $data = array(
                    'openid'      => $member['openid'],
                    'openshop_id' => $_GP['openshop_id'],
                    'createtime'  => time()
                );
                mysqld_insert('goods_collection',$data);
                if(mysqld_insertid()){
                    //记录统计表当前商铺收藏++
                    $nowdate = strtotime(date("Y-m-d",time()));
                    $data = mysqld_select("select id,collect_num from ". table('openshop_viewreport') ." where seller_openid='{$member['openid']}' and time={$nowdate}");
                    if(empty($data)){
                        mysqld_insert('openshop_viewreport',array(
                            'collect_num'=>1,
                            'time'=>$nowdate,
                            'seller_openid'=>$member['openid']
                        ));
                    }else{
                        mysqld_update('openshop_viewreport',array('collect_num'=>$data['collect_num']+1),array('id'=>$data['id']));
                    }
                    echo showAjaxMess('200','该店铺收藏成功！');
                }
            }

        }
        break;


}