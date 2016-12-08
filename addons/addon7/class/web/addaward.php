<?php
  $category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " where deleted=0 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
		if (! empty($category)) {
			$childrens = '';
			foreach ($category as $cid => $cate) {
				if (! empty($cate['parentid'])) {
					$childrens[$cate['parentid']][$cate['id']] = array(
						$cate['id'],
						$cate['name']
					);
				}
			}
		}
  if (checksubmit("submit")) {
  	 $insert=array(
  	 	'names' => $_GP['names'],
		 'gid' => $_GP['c_goods'],
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
  	 $c_p = mysqld_select("SELECT * FROM ".table("shop_goods")." WHERE id = ".$_GP['c_goods']);
                $insert['p1'] = $c_p['pcate'];
				$insert['p2'] = $c_p['ccate'];
				$insert['p3'] = $c_p['ccate2'];
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