<?php
		$settings=globaSetting();
		if (!empty($_CMS['account'])) {
			header("location:".create_url('site', array('name' => 'index','do' => 'main')));
		}
		include page('index');