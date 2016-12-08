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
 $award = mysqld_select("SELECT a.*,b.title as gname,b.id as gid,b.thumb as gthumb FROM " . table('addon7_award') . " AS a LEFT JOIN " . table('shop_goods') . " as b on a.gid = b.id WHERE a.id = :id", array(':id' => intval($_GP['id'])));
     if (checksubmit("submit")) {
  		 $update=array(
  	 	'names' => $_GP['names'],
			 'gid' => $_GP['c_goods'],
  	 		'amount' => intval($_GP['amount']),
  	 		'endtime' =>  strtotime($_GP['endtime']),
			
     	 'price' => $_GP['price'],
  	   'gold'=> $_GP['gold'],
  	   'awardtype'=> intval($_GP['awardtype']),
  	    'credit_cost' => intval($_GP['credit_cost']),
  	     'content' => htmlspecialchars_decode($_GP['content'])
  	 );
  	 $c_p = mysqld_select("SELECT * FROM ".table("shop_goods")." WHERE id = ".$_GP['c_goods']);
                $data['p1'] = $c_p['pcate'];
				$data['p2'] = $c_p['ccate'];
				$data['p3'] = $c_p['ccate2'];
  	   	if (!empty($_FILES['logo']['tmp_name'])) {
                    $upload = file_upload($_FILES['logo']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $logo = $upload['path'];
                }
                if(!empty($logo))
                {
                	$update['logo']=$logo;
                }
  	 
			  mysqld_update('addon7_award', $update,array("id"=>intval($_GP['id'])));	
			          message('保存成功', 'refresh', 'success');
	}
 include addons_page('award');