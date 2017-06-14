<?php
        //$delurl = $_GP['pic'];
		if ( !empty($_GP['ids']) ){
			mysqld_delete('shop_goods_piclist', array(
				'id' => $_GP['ids']
			));
			echo 1;
		}else{
            echo 0;
		}
		/*
        if (file_delete($delurl)) {
            $filename=basename(SYSTEM_WEBROOT . '/attachment/' . $delurl);
            echo 1;
        } else {
            echo 0;
        }
		*/