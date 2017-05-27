<?php
			$settings=globaSetting();

			if (checksubmit("submit")) {
				$curt_url = $_SERVER['HTTP_HOST'];
				if($settings['open_shareactive'] != $_GP['open_shareactive'] && $curt_url=="www.hinrc.com"){
					//如果有修改则告警
					$mail = new MailService();
					$open = $_GP['open_shareactive'] == 1 ? '开启':'关闭';
					$message = "管理员{$_SESSION['account']['username']}，修改了心愿开关为{$open}";
					$mail->sendMail('459642586@qq.com','心愿开关异常变动',$message);
				}
            $cfg = array(
                'shop_openreg' => intval($_GP['shop_openreg']),
                 'shop_regcredit' => intval($_GP['shop_regcredit']),
				 'shop_keyword' => $_GP['shop_keyword'],
				   		  'shop_description' => $_GP['shop_description'],
				   		  'shop_title' => $_GP['shop_title'],
				   		    'shop_icp' => $_GP['shop_icp'],
				         'shop_regcredit'=>$_GP['shop_regcredit'],
				          'shop_tel'=>$_GP['shop_tel'],
				          'shop_address'=>$_GP['shop_address'],
				          'open_shareactive'=>$_GP['open_shareactive'],
				   		    'shop_kfcode' => htmlspecialchars_decode($_GP['shop_kfcode']),
				   		    'shop_tongjicode' => htmlspecialchars_decode($_GP['shop_tongjicode']),
				         'news'=> htmlspecialchars_decode($_GP['news']),
				   		  'help' =>   htmlspecialchars_decode($_GP['help'])
            );
      
          	if (!empty($_FILES['shop_logo']['tmp_name'])) {
                    $upload = file_upload($_FILES['shop_logo']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $shoplogo = $upload['path'];
                }
                if(!empty($shoplogo))
                {
                	$cfg['shop_logo']=$shoplogo;
                }
                
          	refreshSetting($cfg);
            message('保存成功', 'refresh', 'success');
        }

		$qq_info = '';
		if(!empty($settings['shop_kfcode'])){
			$qq_info = json_decode($settings['shop_kfcode'],true);
		}
		include page('setting');