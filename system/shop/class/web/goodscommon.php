<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/12
 * Time: 16:47
 */
$op = $_GP['op'];
if($op == 'getCate'){
    //获取下一级分类
    if(empty($_GP['id'])){
        die(showAjaxMess(1002,'参数有误！'));
    }
    $all_category  = mysqld_selectall("SELECT * FROM " . table('shop_category') . "  where parentid={$_GP['id']} and  deleted=0  ORDER BY parentid ASC, displayorder ASC");
    if(empty($all_category)){
        die(showAjaxMess(1002,'无数据！'));
    }else{
        die(showAjaxMess(200,$all_category));
    }

}else if($op == 'getNextRegion'){
    //获取下一级分类
    if(empty($_GP['region_id'])){
        die(showAjaxMess(1002,'参数有误！'));
    }
    $all_region  = mysqld_selectall("SELECT * FROM " . table('region') . "  where parent_id={$_GP['region_id']}");
    if(empty($all_region)){
        die(showAjaxMess(1002,'无数据！'));
    }else{
        die(showAjaxMess(200,$all_region));
    }
}else if($op == 'getBrandByCate'){
    //通过下拉分类获取 商品模型 以及 品牌
    if(empty($_GP['p1'])){
        die(showAjaxMess(1002,'参数有误！'));
    }
    $brand = getBrandByCategory($_GP['p1'],$_GP['p2'],$_GP['p3']);
    $gtype = getGoodtypeByCategory($_GP['p1'],$_GP['p2'],$_GP['p3']);
    $data  = array(
        'brand'  => $brand,
        'gtype'  => $gtype,
    );
    die(showAjaxMess(200,$data));
}else if($op == 'addbrand'){
    //通过分类添加没有的品牌
    if(empty($_GP['brand'])){
        die(showAjaxMess('品牌名字不能为空！',1002));
    }
    if(empty($_GP['p1']) || empty($_GP['p2'])){
        die(showAjaxMess('分类不能为空！',1002));
    }

    $data = array(
        'icon' => $_GP['icon'],
        'p1'   => $_GP['p1'],
        'p2'   => $_GP['p2'],
        'p3'   => intval($_GP['p3']),
        'brand'=> $_GP['brand'],
    );
    $res = mysqld_insert('shop_brand',$data);
    if($res){
        die(showAjaxMess(200,mysqld_insertid()));
    }else{
        die(showAjaxMess(1002,'操作失败！'));
    }
}else if($op == 'goodget_attr'){
    if(empty($_GP['gtype_id'])){
        die(showAjaxMess(1002,'参数有误！'));
    }
    $goods_id      = $_GP['goods_id'];
    $gtype_id      = $_GP['gtype_id'];
    $goodsService  = new \service\shop\goodscommonService();
    $res = $goodsService->goodsAttrInput($gtype_id,$goods_id);
    if($res){
        die(showAjaxMess(200,$res));
    }else{
        die(showAjaxMess(1002,$goodsService->getError()));
    }
}else if($op == 'goodget_spec'){
    if(empty($_GP['gtype_id'])){
        die(showAjaxMess(1002,'参数有误！'));
    }
    $goods_id      = $_GP['goods_id'];
    $gtype_id      = $_GP['gtype_id'];

    $goodsService  = new \service\shop\goodscommonService();
    $res = $goodsService->goodsSpecInput($gtype_id,$goods_id);
    if($res){
        die(showAjaxMess(200,$res));
    }else{
        die(showAjaxMess(1002,$goodsService->getError()));
    }
}else if($op == 'goodspect_input'){
    if(empty($_GP['spec_arr'])){
        die(showAjaxMess(1002,'参数有误！'));
    }
    $goods_id      = $_GP['goods_id'];
    $spec_arr      = $_GP['spec_arr'];

    $goodsService  = new \service\shop\goodscommonService();
    $res = $goodsService->goodsSpecInput_info($spec_arr,$goods_id);
    if($res){
        die(showAjaxMess(200,$res));
    }else{
        die(showAjaxMess(1002,$goodsService->getError()));
    }
}
