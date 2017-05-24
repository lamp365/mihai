<?php
defined('SYSTEM_IN') or exit('Access Denied');
		hasrule('weixin','weixin');
			$ret = $this->menuDelete();
		if(is_error($ret)) {
			message($ret['message'], 'refresh');
		} else {
			message('ÒÑ¾­³É¹¦É¾³ý²Ëµ¥£¬ÇëÖØÐÂ´´½¨¡£', 'refresh');
		}