<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/23 0023
 * Time: 09:51
 */

/**
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_goodmange&op=list&isSale=1&order=3&page=2
 * isSale上架未上架   order类型为最新还是佣金最高还是收藏数最多
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_goodmange&op=keyword&keyword=水果   查询
 *
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_goodmange&op=shangjia  post提交批量的商品(openshop_relation)中的id
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_goodmange&op=xiajia  post提交批量的商品(openshop_relation)中的id
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_goodmange&op=delete  post提交批量的商品(openshop_relation)中的goodid也是dish表id
 */
$member     = get_member_account(true,true);
//$memberinfo = member_get($member['openid']);
if(!checkIsOpenshop()){
    $url = mobile_url("openshop_xieyi");
    message('您还不是商家用户，请您先开店。',$url,'error');
}
switch($_GP['op']){
    case 'list':
        $showOrderArr = array('createtime','sales','commision','productprice','collect_num');  //与前台的下拉排序要一致

        $isSale = empty($_GP['isSale']) ? 1 : $_GP['isSale'];     //是否上架
        $order  = empty($_GP['order'])? 'createtime' : $_GP['order'];   //默认是按照最新排序
        $where  = "a.openid={$member['openid']} and a.status = {$isSale}";
        $orderStr = " order by {$order} desc";

        $pindex = max(1, intval($_GP["page"]));
        $psize = 12;
        $start = ($pindex -1) * $psize;
        $limit = "limit {$start},{$psize}";

        $sql = "select a.id,a.goodid, a.openid, a.status, b.createtime as createtime, b.sales as sales, b.commision as commision, b.productprice as productprice, b.collect_num as collect_num,b.title as title, b.thumb as thumb,
b.timeprice as timeprice, b.total as total from ". table('openshop_relation') . " as a left join ". table('shop_dish') ." as b on a.goodid=b.id where {$where} {$orderStr} {$limit}";

        $list = mysqld_selectall($sql);
        $total = 0;
        $pager = '';
        if(!empty($list)){
            foreach($list as &$data){
                $data = getEachGoodInfo($data);
            }
            $total =  mysqld_selectcolumn("SELECT COUNT(a.id) as total FROM " . table('openshop_relation') ." as a left join ". table('shop_dish') ." as b on a.goodid=b.id where {$where}");
            $pager = pagination($total, $pindex, $psize,'.os_box_list');
        }

        break;

    case 'keyword':
        $where = "title like '%{$_GP['keyword']}%'";

        $psize = 12;
        $page  = empty($_GP['page'])? 1 : $_GP['page'];
        $pindex = ($page-1)*$psize;
        $limit  = " limit ". $pindex+1 ." ,{$psize}";

        $sql = "select a.id,a.goodid, a.openid, b.createtime as createtime, b.sales as sales, b.commision as commision, b.productprice as productprice, b.collect_num as collect_num,b.title as title, b.thumb as thumb,
b.timeprice as timeprice, b.total as total,b.status as status from ". table('openshop_relation') . " as a left join ". table('shop_dish') ." as b on a.goodid=b.id where {$where} {$limit}";

        $list = mysqld_selectall($sql);

        $total = mysqld_selectcolumn("SELECT COUNT(a.id) as total FROM " . table('openshop_relation') ." as a left join ". table('shop_dish') ." as b on a.goodid=b.id where {$where}");

        break;

    case 'shangjia':
        $idArr = $_GP['id'];
        foreach($idArr as $id){
            mysqld_update('openshop_relation',array('status'=>1),array('id'=>$id));
        }
        echo showAjaxMess('200','上架成功');
        break;

    case 'xiajia':
        $idArr = $_GP['id'];
        foreach($idArr as $id){
            mysqld_update('openshop_relation',array('status'=>0),array('id'=>$id));
        }
        echo showAjaxMess('200','下架成功');
        break;

    case 'delete':
        $dishidArr = $_GP['id'];
        foreach($dishidArr as $dishid){
            //删除掉该商品跟店铺的关系
            mysqld_delete('openshop_relation',array(
                'openid' => $member['openid'],
                'goodid' => $dishid
            ));
            //更新完毕，在更新该商品的有多少人再卖
            $dish = mysqld_select("SELECT id,shoper_num FROM ". table('shop_dish') . " where id=:dishid ",array(
                ':dishid' => $dishid
            ));
            $num  = $dish['shoper_num']-1;
            $data = array('shoper_num' => $num);
            mysqld_update('shop_dish', $data, array('id' => intval($dish['id'])));
        }

        echo showAjaxMess(200,"删除成功");

    default:
        message('对不起，访问有误!','','error');
}
