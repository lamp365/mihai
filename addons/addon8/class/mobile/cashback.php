<?php
//返现记录文章
$op = empty($_GP['op'])? 'content' : $_GP['op'];
if($op == 'list'){
    $psize =  10;
    $pindex = max(1, intval($_GP["page"]));
    $limit = ' limit '.($pindex-1)*$psize.','.$psize;
    $sql    = "SELECT * FROM".table('addon8_article')." where state =7  order by displayorder desc,id desc {$limit}";
    $sqlnum = "SELECT count(id) FROM".table('addon8_article')." where state =7";

    $article_list =  mysqld_selectall($sql);
    //当手机端滑动的时候加载下一页
    if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
        if ( empty($article_list) ){
            die(showAjaxMess(1002,'查无数据！'));
        }else{
            die(showAjaxMess(200,$article_list));
        }
    }
    include addons_page('wap_cashback_list');

}else if($op == 'content'){
    $article = mysqld_select("SELECT * FROM " . table('addon8_article')." where id=:id ",array(":id"=>intval($_GP['id'])) );
    include addons_page('wap_cashback');
}
