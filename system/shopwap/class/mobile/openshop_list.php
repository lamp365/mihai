<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/30 0030
 * Time: 16:49
 */
$saler_openid    = checkOpenshopAccessKey();       //验证地址accesskey
$accesskey       = $_GP['accesskey'];

$shopInfo = mysqld_select("select id,openid,shopname,shoppic,area,logo,qcodepic,level,mobile,notice,createtime from ". table('openshop') . " where openid=:openid",array(
    'openid' => $saler_openid
));

$shoperWebUrl = getShoperWebUrl($member['openid']);

$categoryResult = getGoodHaveCategory($saler_openid);   //获取商家的商品所对应的栏目分类
$children = $categoryResult['children'];
$category = $categoryResult['category'];


$pindex = max(1, intval($_GP["page"]));
$psize = 3;
$start = ($pindex -1) * $psize;
$limit = "limit {$start},{$psize}";

$sort     = empty($_GP['sort']) ? 'createtime' : $_GP['sort'];
$sorttype = empty($_GP['sorttype']) ? 'desc' : $_GP['sorttype'];
$orderStr = " order by b.{$sort} {$sorttype}";

$condition = "a.openid={$saler_openid} and a.status = 1";
if (!empty($_GP['p3'])) {
    $cid = intval($_GP['p3']);
    $condition .= " AND a.p3 = '{$cid}'";
} elseif (!empty($_GP['p2'])) {
    $cid = intval($_GP['p2']);
    $condition .= " AND a.p2 = '{$cid}'";
} elseif (!empty($_GP['p1'])) {
    $cid = intval($_GP['p1']);
    $condition .= " AND a.p1 = '{$cid}'";
}

if($sort == 'marketprice'){    //有的dish表中没有对应的价格
    $orderStr = " order by c.{$sort} {$sorttype}";

    $sql = "select a.goodid, a.openid,b.id,b.createtime as createtime, b.collect_num as collect_num,b.total as total, c.thumb as thumb, c.title,c.marketprice
          from  ". table('openshop_relation') ." as a left join ". table('shop_dish') ." as b on a.goodid=b.id left join ". table('shop_goods') ." as c on b.gid=c.id where {$condition} {$orderStr} {$limit}";

    $list = mysqld_selectall($sql);
    $total = 0;
    $pager = '';
    if(!empty($_POST['page'])){
        $html = postNextData($list,$accesskey,false);
        die($html);
    }
    if(!empty($list)){
        $total =  mysqld_selectcolumn("SELECT COUNT(a.id) as total FROM " . table('openshop_relation') ." as a left join ". table('shop_dish') ." as b on a.goodid=b.id
                left join ". table('shop_goods') ." as c on b.gid=c.id where {$condition}");
        $pager = pagination($total, $pindex, $psize,'.os_box_list');
    }

}else{
    $sql = "select a.goodid, a.openid, b.createtime as createtime, b.marketprice as marketprice, b.id, b.productprice as productprice,b.title as title, b.thumb as thumb, b.shoper_num as shoper_num
          from ". table('openshop_relation') . " as a left join ". table('shop_dish') ." as b on a.goodid=b.id where {$condition} {$orderStr} {$limit}";

    $list = mysqld_selectall($sql);
    $total   = 0;
    $pager   = '';
    if(!empty($_POST['page'])){
        $html = postNextData($list,$accesskey);
        die($html);
    }
    if(!empty($list)){
        foreach($list as &$data){
            $data = getEachGoodInfo($data,$accesskey);
        }
        $total =  mysqld_selectcolumn("SELECT COUNT(a.id) as total FROM " . table('openshop_relation') ." as a left join ". table('shop_dish') ." as b on a.goodid=b.id where {$condition}");
        $pager = pagination($total, $pindex, $psize,'.os_box_list');
    }
}

//加载wap滚动时下一页数据
function postNextData($list,$accesskey,$no_money=true){
    $html ='';
    if(!empty($list)){
        if($no_money){
            foreach($list as &$data){
                $data = getEachGoodInfo($data,$accesskey);
            }
        }
        foreach($list as $item){
            $html .= "<li>
                        <div class='item'>
                            <div class='pic'>
                                <a href='" .mobile_url('detail', array('id' => $item['id'],'accesskey'=>$accesskey)). "'><img  src='{$item['thumb']}' class='img-responsive'/></a>
                            </div>
                            <div class='txt'>{$item['title']}</div>
                            <div class='buy'><span class='text' style='font-size:12px;'><strong>
                                <em style='color:red;font-size:16px;'>{$item['marketprice']}</em>元
                            </strong></span></div>
                        </div>
                    </li>
            ";
        }
    }
    return $html;
}

include themePage('openshop_list');

