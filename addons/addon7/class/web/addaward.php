<?php
  if (checksubmit("submit")) {
  	 $insert=array(
  	 	'names' => $_GP['names'],
		 'title' => $_GP['title'],
  	 	'amount' => intval($_GP['amount']),
		'dicount'=> intval($_GP['amount']),
		'isrecommand'=>intval($_GP['isrecommand']),
  	 	'endtime' => strtotime($_GP['endtime']),
  	    'price' => $_GP['price'],
  	     'gold'=> $_GP['gold'],
  	     'awardtype'=> intval($_GP['awardtype']),
  	     'credit_cost' => floatval($_GP['credit_cost']),
  	     'createtime' => time(),
  	     "deleted"=>0,
  	      'content' => htmlspecialchars_decode($_GP['content'])
  	 );
  		 	   	if (!empty($_FILES['logo']['tmp_name'])) {
                    $upload = file_upload($_FILES['logo']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $logo = $upload['path'];
                }
                if(!empty($logo))
                {
                	$insert['logo']=$logo;
                }
                
		   mysqld_insert('addon7_award', $insert);
			        message('保存成功', web_url('awardlist'), 'success');
	}

 include addons_page('award');