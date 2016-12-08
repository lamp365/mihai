<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/21 0020
 * Time: 14:19
 */

/**
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_mange&op=list&isSale=1&order=sales&page=2
 * isSale上架未上架   order类型为最新还是佣金最高还是收藏数最多
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_mange&op=qcode  二维码
 */

$member     = get_member_account(true,true);
if(!checkIsOpenshop()){
    $url = mobile_url("openshop_xieyi");
    message('您还不是商家用户，请您先开店。',$url,'error');
}
//$memberinfo = member_get($member['openid']);
$shoperWebUrl = getShoperWebUrl($member['openid']);

switch($_GP['op']){
    case 'list':
        $shopInfo = mysqld_select("select * from ". table('openshop') . " where openid=:openid",array(
            'openid' => $member['openid']
        ));
        $keyword = $_GP['keyword'];
        $showOrderArr = array('createtime','sales','commision','marketprice','collect_num');  //与前台的下拉排序要一致

        $isSale = ($_GP['isSale']=== NULL) ? 1 : $_GP['isSale'];     //是否上架
        $order  = empty($_GP['order'])? 'createtime' : $_GP['order'];   //默认是按照最新排序
        $where  = "a.openid='{$member['openid']}' and a.status = {$isSale}";


        $pindex = max(1, intval($_GP["page"]));
        $psize = 12;
        $start = ($pindex -1) * $psize;
        $limit = "limit {$start},{$psize}";
        $orderStr = " order by b.{$order} desc";

        if($order == 'marketprice' || !empty($keyword)){   //因为dish表可能没有价格和标题
            if($order == 'marketprice'){
                $orderStr = " order by c.{$order} desc";
            }
            if(!empty($keyword)){
                $where .= " and (b.title LIKE '%{$_GP['keyword']}%' or c.title LIKE '%{$_GP['keyword']}%')";
            }
            $sql = "select a.goodid, a.openid, a.status,a.is_top,
                          b.id,b.createtime as createtime, b.sales as sales, b.commision as commision, b.collect_num as collect_num,b.total as total,
                          c.thumb as thumb, c.title,c.marketprice
                          from  ". table('openshop_relation') ." as a left join ". table('shop_dish') ." as b on a.goodid=b.id left join ". table('shop_goods') ." as c on b.gid=c.id where {$where} {$orderStr} {$limit}";

            $list = mysqld_selectall($sql);

            $total = 0;
            $pager = '';
            if(!empty($list)){
                $total =  mysqld_selectcolumn("SELECT COUNT(a.id) as total FROM " . table('openshop_relation') ." as a left join ". table('shop_dish') ." as b on a.goodid=b.id
                left join ". table('shop_goods') ." as c on b.gid=c.id where {$where}");
                $pager = pagination($total, $pindex, $psize,'.os_box_list');
            }

        }else{
            $sql = "select a.goodid, a.openid, a.status,a.is_top, b.id, b.createtime as createtime, b.sales as sales, b.commision as commision, b.productprice as productprice, b.collect_num as collect_num,b.title as title, b.thumb as thumb,
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
        }


        include themePage('openshop_mange');
        break;


    case 'qcode':
        //先从数据库中获取，没有则生成
        $result = mysqld_select("select * from openshop where openid=:openid",array(
            'openid' => $member['openid']
        ));
        if(!empty($result['qcodepic'])){
            return $result['qcodepic'];
        }else{
            //地址分享出去后进行参数加密
            $accesskey = getOpenshopAccessKey($member['openid']);
            $webUrl    = WEBSITE_ROOT."index.php?mod=mobile&name=shopwap&do=openshop_home&accesskey={$accesskey}";

            $logoUrl = empty($result['logo']) ? '' : $result['logo'];
            $qcode   = new Qrcodeimg();
            $img     = $qcode->getImgQcode($webUrl,$logoUrl);

            //得到二维码图片地址后进行保存
            mysqld_update('openshop',array('qcodepic'=>$img),array('openid'=>$member['openid']));
            return $img;
        }
        break;
}

