<?php
defined('SYSTEM_IN') or exit('Access Denied');
		hasrule('alipay','alipay');
$settings=globaSetting(array(
               'alipay_name',
                'alipay_appId' ,
						  	'thirdlogin_alipay' ));
		
				$thirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE code = :code", array(':code' => 'alipay'));
				   		
 if (checksubmit()) {
            $cfg = array(
               'alipay_name' => $_GP['alipay_name'],
                'alipay_appId' => $_GP['alipay_appId'],
						  	'thirdlogin_alipay' => $_GP['thirdlogin_alipay']
            );
        
         
          refreshSetting($cfg);
 					  mysqld_delete('config', array('name'=>'alipay_access_token'));
         
        $settings=globaSetting(array(
               'alipay_name',
                'alipay_appId' ,
						  	'thirdlogin_alipay' ));
				   		  
				   		  
				   		      $thirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE code = :code", array(':code' => 'alipay'));
              	require WEB_ROOT.'/system/modules/plugin/thirdlogin/alipay/lang.php';
              
                 if (empty($thirdlogin['id'])) {
                 		 $data = array(
                    'code' => 'alipay',
                     'enabled' => intval($_GP['thirdlogin_alipay']),
                    'name' => $_LANG['thirdlogin_alipay_name']
                  );
									 mysqld_insert('thirdlogin', $data);
                } else {
	                		 $data = array(
	                		  'enabled' => intval($_GP['thirdlogin_alipay']),
	                    'name' => $_LANG['thirdlogin_alipay_name'],
	                  );
                    mysqld_update('thirdlogin',$data , array('code' =>'alipay'));
                }
				   		  
				   		  
            
            message('保存成功', 'refresh', 'success');
         
        }

					include page('setting');