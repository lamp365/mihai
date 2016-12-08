<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/22 0022
 * Time: 10:01
 */
/**
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_home&accesskey=n5F3q****zn**bpTr**41ekeR6oSFqJxOeg0&page=2  主页
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_home&accesskey=123asasajkzwuywx&act=review
 */


$openid    = checkOpenshopAccessKey();       //验证地址accesskey
$accesskey = $_GP['accesskey'];
countOpenshopView($openid);               //统计访问量

$shopInfo = mysqld_select("select * from ". table('openshop') . " where openid=:openid",array(
   'openid' => $openid
));

if(!empty($_GP['act'])){
    if($_GP['act'] == 'review'){
        //分配变量，加载模板

    }else{
        message('对不起，访问地址有误!','','error');
    }
}else{
    //显示主页
    $pindex = max(1, intval($_GP["page"]));
    $psize = 12;
    $start = ($pindex -1) * $psize;
    $limit = "limit {$start},{$psize}";
    $where  = " a.openid='{$openid}' and a.status = 1";
    $orderStr = " order by b.createtime desc";

    $sql = "select a.goodid, a.openid, b.createtime as createtime,b.marketprice as marketprice, b.id, b.productprice as productprice,b.title as title, b.thumb as thumb, b.shoper_num as shoper_num
 from ". table('openshop_relation') . " as a left join ". table('shop_dish') ." as b on a.goodid=b.id where {$where} {$orderStr} {$limit}";


    $list = mysqld_selectall($sql);

    if(!empty($_POST['page'])){  //wap端手机页面上会滚动加载数据
        // 处理异步数据
        $html = '';
        $theme = $_SESSION["theme"];
        if(!empty($list)) {
            foreach($list as &$data){
                $data = getEachGoodInfo($data);
            }
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
                                <p class='money'>￥{$item['marketprice']}<span class='pull-right salenum'></span></p>
                            </div>
                        </div>
                    </div>
                ";
        }}
        echo $html;
    }else{
        $is_data = 0;
        $total   = 0;
        if(!empty($list)){
            foreach($list as &$data){
                $data = getEachGoodInfo($data);
            }
            $is_data = 1;
            $total =  mysqld_selectcolumn("SELECT COUNT(a.id) as total FROM " . table('openshop_relation') ." as a left join ". table('shop_dish') ." as b on a.goodid=b.id where {$where}");
            $pager = pagination($total, $pindex, $psize,'.shoplist');
        }
        include themePage('openshop_home');
    }



}

