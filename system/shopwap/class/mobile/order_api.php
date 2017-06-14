<?php
// 进行订单接口的处理
 $api = new OrderApi;
 $data = $api->QueryTradeByMTime();
 file_put_contents('api.txt', serialize($data));
 if ( !empty($data[0]) && is_array($data[0]) ){
     // 进行订单数据的处理
	 foreach( $data[0] as $data_value ){
         $api->check_order($data_value);   
	 }
 }