<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/20 0020
 * Time: 15:50
 */
/****
显示分类，以及按照分类查询对应的商品，和按模糊搜索也走这里。
http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_category&op=cat   分类展示
http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_category&op=list&p2=10&page=3  展示该分类下的产品
http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_category&op=list&p1=12&sort=2&sorttype=desc 展示时 排序
http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_category&op=list&keyword=手机  模糊查询
***/

$member     = get_member_account(true,true);
//$memberinfo = member_get($member['openid']);

$operation = empty($_GP['op']) ? 'cat' : $_GP['op'];
switch($operation){
    case 'cat':
        $children = array();
        $category = mysqld_selectall("SELECT id,name,thumb,parentid FROM " . table('shop_category') . "  where deleted=0  ORDER BY parentid ASC, displayorder ASC");

        foreach ($category as $index => $row) {
            if (!empty($row['parentid'])) {
                $children[$row['parentid']][] = $row;
                unset($category[$index]);
            }
        }

        //模板便利数据
       /* foreach($category as $row) {
            echo $row['name'] . '<br/>';
            if (is_array($children[$row['id']])) {
                foreach ($children[$row['id']] as $val) {
                        echo '-----'.$val['name'].$val['id'].'</br>';
                }
            }
        }*/
        include themePage('openshop_category');
        break;

    //模糊搜索和按照分类搜索都走这里
    case 'list':
        $accesskey = getOpenshopAccessKey($member['openid']);
        $pindex = max(1, intval($_GP["page"]));
        $psize = 12;
        $start = ($pindex -1) * $psize;
        $limit = "limit {$start},{$psize}";

        //是否是按照栏目分类进行搜索的
        if(!empty($_GP['p1'])){
            $condition = " a.p1={$_GP['p1']} and a.deleted=0";
        }else if(!empty($_GP['p2'])) {
            $condition = " a.p2={$_GP['p2']} and a.deleted=0";
        }else if(!empty($_GP['p3'])) {
            $condition = " a.p3={$_GP['p3']} and a.deleted=0";
        }else {
            $condition = "a.deleted=0";
        }

        //是否是模糊查询的
        $keyword = $_GP['keyword'];
        if(!empty($keyword)){
            $condition .= " and (a.title LIKE '%{$_GP['keyword']}%' or b.title LIKE '%{$_GP['keyword']}%')";
        }

        //排序
        if(!empty($_GP['sort'])){
            $sort = empty($_GP['sorttype'])? 'desc' : $_GP['sorttype'];
            switch($_GP['sort']){
                case 1:  //最新上架  卖的最多的  代销商最多的
                    $order =  " a.{$sort} desc";  break;
                case 2: //佣金排序
                    $order = ' a.commision '.$sort;  break;
                case 3: //价格【排序
                    $order =  ' b.marketprice '.$sort;  break;
            }
        }else{
            $order = ' a.createtime desc';   //默认是最新上架
        }

        $list = get_goods(array(
            'table'  => 'shop_dish',
            'where'  => $condition,
            'limit'  => $start.','.$psize,
            'order'  => $order
        ));

        if(!empty($_POST['page'])) {  //wap端手机页面上会滚动加载数据
            // 处理异步数据
            $html = '';
            $theme = $_SESSION["theme"];
            if(!empty($list)) {
                $i = 0;
                foreach($list as $item) {
                    $i++;
                    if($i % 2 == 1)
                        $class = 'pr_5';
                    else
                        $class = 'pl_5';
                    $html .="
                    <div class='col-sm-6 col-xs-6 pull-left {$class}'>
                        <div class='good'>
                            <div class='pic'>
                                <a href='". mobile_url('detail',array('id'=>$item['goodid'],'accesskey'=>$accesskey)) ."'><img src='{$item['thumb']}' class='img-responsive'/></a>
                            </div>
                            <div class='descript'>
                                <p class='title'><a href='".mobile_url('detail',array('id'=>$item['goodid'],'accesskey'=>$accesskey)) ."' title='{$item['title']}'>{$item['title']}</a></p>
                                <p class='yongjin'>佣金{$item['commision']}</p>
                                <p class='money'>￥{$item['marketprice']}<span class='pull-right salenum'>{$item['shoper_num']}人在卖</span></p>
                            </div>
                        </div>
                    </div>
                ";
                }}
            echo $html;

        }else{
            $total = 0;
            $pager = '';
            if(!empty($list)){
                $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_dish') . " as a left join ".table('shop_goods')." as b on a.gid = b.id WHERE $condition and a.deleted=0  AND a.status = '1' ");
                $pager = pagination($total, $pindex, $psize,'.shoplist');
            }
            include themePage('openshop_add_good');
        }

        break;

}
