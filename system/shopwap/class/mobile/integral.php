<?php
$op           = empty($_GP['op'])? 'display' : $_GP['op'];
$openid       = checkIsLogin();
if($op == 'display'){
    //查找出有兑换礼品的商品
    $config     = mysqld_select("SELECT * FROM " . table('addon7_config') );
    $goods_list = array();
    if($config['open_gift_change'] == 1){
        $goods_list  = mysqld_selectall("select * from ".table('addon7_award')." where add_jifen_change=1 order by id desc");
    }
//    ppd(get_weixin_token());
    include themePage('integral');
}
