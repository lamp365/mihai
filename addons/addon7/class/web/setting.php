<?php
	 $setting = mysqld_select("SELECT * FROM " . table('addon7_config') );
 
  if (checksubmit("submit")) {
  	      $cfg = array(
                'title'            => $_GP['title'],
                'active_type'      => $_GP['active_type'],
                'open_gift_change' => $_GP['open_gift_change'],
            );
           if($cfg['active_type'] == 2){
               message('暂时积分许愿，不可用！',refresh(),'error');
           }
           mysqld_delete('addon7_config',array());
          	   mysqld_insert('addon7_config', $cfg);
             
            message('保存成功', 'refresh', 'success');
	}
 
 include addons_page('setting');