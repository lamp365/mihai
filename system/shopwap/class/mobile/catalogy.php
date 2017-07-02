<?php

$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " WHERE deleted=0 and enabled=1 ORDER BY parentid ASC, displayorder DESC");
foreach ($category as $index => $row) {
    if (! empty($row['parentid'])) {
        $children[$row['parentid']][$row['id']] = $row;
        unset($category[$index]);
    }
}

$carttotal = getCartTotal(2);

$catid = intval($_GP['pcate']);
$list ='';
$condition ="";
$catalogyname="全部商品";
if(isset($catid) && $catid>0)
{
    $condition =" and pcate=".$catid;
    $items =mysqld_select("SELECT * FROM " . table('shop_category')." where enabled=1 and id=".$catid);
    $catalogyname =$items["name"];
    
}

$list = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  deleted=0 AND status = '1' ".$condition."  ORDER BY lists desc  ");
foreach ( $list as $key=>$value ) {
    $list[$key]['thumb'] = imgThumb($value['thumb'],72,72);
}
include themePage('catalogy');



/*
$pindex = max(1, intval($_GP["page"]));
$psize = 10;
$condition = '';
if (!empty($_GP['ccate'])) {
    $cid = intval($_GP['ccate']);
    $condition .= " AND ccate = '{$cid}'";
    $_GP['pcate'] = mysqld_selectcolumn("SELECT parentid FROM " . table('shop_category') . " WHERE id = :id", array(':id' => intval($_GP['ccate'])));
} elseif (!empty($_GP['pcate'])) {
    $cid = intval($_GP['pcate']);
    $condition .= " AND pcate = '{$cid}'";
}
if (!empty($_GP['keyword'])) {
    $condition .= " AND title LIKE '%{$_GP['keyword']}%'";
}
$sort = empty($_GP['sort']) ? 0 : $_GP['sort'];
$sortfield = "displayorder asc";



$sorturl = mobile_url('goodlist', array("pcate" => $_GP['pcate'], "ccate" => $_GP['ccate']));
if (!empty($_GP['isnew'])) {
    $condition .= " AND isnew = 1";
    $sorturl.="&isnew=1";
}

if (!empty($_GP['ishot'])) {
    $condition .= " AND ishot = 1";
    $sorturl.="&ishot=1";
}
if (!empty($_GP['isdiscount'])) {
    $condition .= " AND isdiscount = 1";
    $sorturl.="&isdiscount=1";
}
if (!empty($_GP['istime'])) {
    $condition .= " AND istime = 1 ";
    $sorturl.="&istime=1";
}

$children = array();



$category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " WHERE deleted=0 and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
foreach ($category as $index => $row) {
    if (!empty($row['parentid'])) {
        $children[$row['parentid']][$row['id']] = $row;
        unset($category[$index]);
    }
}
$list = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  deleted=0 AND status = '1' $condition ORDER BY $sortfield  ");

$total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_goods') . " WHERE  deleted=0  AND status = '1' $condition");
$pager = pagination($total, $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
 

$id = $profile['id'];
if($profile['status']==0)
{
    $profile['flag']=0;
}
*/

