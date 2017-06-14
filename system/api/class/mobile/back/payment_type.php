<?php
/**
 * app获得支付方式接口
 *
 */

$result ['data']['payment_list']= getPayment (); 					// 支付方式
$result ['code'] 				= 1;

echo apiReturn ( $result );
exit ();