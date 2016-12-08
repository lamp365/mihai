<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/22 0022
 * Time: 11:51
 */
//http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_xieyi
//http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_action&act=openshop

$member     = get_member_account(true,true);
$content    = "开店协议，从后台获取！";

include themePage('openshop_xieyi');




