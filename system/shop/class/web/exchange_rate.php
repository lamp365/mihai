<?php
$op = $_GP['op'];
switch ( $op ){
   case 'set_exchange_rate':
	   if ( empty($_GP['exchange_rate_value']) ){
           die(showAjaxMess('1002','参数异常'));
       }else{
           $exchange_rate_value = floatval($_GP['exchange_rate_value']);
	   }
	   $exchange = mysqld_select("SELECT * FROM ".table('config')." WHERE name = 'exchange_rate' limit 1 ");
	   $data = array(
		   'value'=>$exchange_rate_value
	   );
       if ( $exchange ){
           mysqld_update('config', $data, array('name'=>'exchange_rate'));
	   }else{
		   $data['name'] = 'exchange_rate';
           mysqld_insert('config', $data);
	   }
	   die(showAjaxMess('200','修改完毕'));
	   exit;
	   break;
   default:
	   break;
}