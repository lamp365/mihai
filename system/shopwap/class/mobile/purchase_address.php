<?php
		$member=get_vip_member_account(true,true);
		$user_a = get_user_identity($member['mobile']);
		$openid =$member['openid'] ;
		$from = $_GP['from'];
        $returnurl = urldecode($_GP['returnurl']);
        $operation = $_GP['op'];
		if (isset($_GP['ids'])){
			 $id = intval($_GP['ids']);
			 if ( empty( $id ) ){
                     $defaultAddress = mysqld_select("select * from " . table('shop_address') . " where isdefault=1 and deleted != 1 and openid='".$openid."' limit 1 ");
                     if ( $defaultAddress ){
                         die(json_encode(
							 array(
							 "result" => 1,
							 "id"=>$defaultAddress['id'],
							 "content"=> '<div class="addr-hd">'.$defaultAddress['province'].' '.$defaultAddress['city'].' '.$defaultAddress['area'].' '.$defaultAddress['address'].'</div><div class="addr-bd">'.$defaultAddress['realname'].', '.$defaultAddress['mobile'].'</div><a class="selected"></a>'
							 )
							 )
						  );
					 }
			 }else{
			 // 获取原来的默认地址
					 $defaultAddress = mysqld_select("select * from " . table('shop_address') . " where isdefault=1 and deleted != 1 and openid='".$openid."' limit 1 ");
					 if ($id != $defaultAddress['id']){
						 // 匹配传输过来的地址
						 die(json_encode(
							 array(
							 "result" => 1,
							 "id"=>$defaultAddress['id'],
							 "content"=> '<div class="addr-hd">'.$defaultAddress['province'].' '.$defaultAddress['city'].' '.$defaultAddress['area'].' '.$defaultAddress['address'].'</div><div class="addr-bd">'.$defaultAddress['realname'].', '.$defaultAddress['mobile'].'</div><a class="selected"></a>'
							 )
							 )
							 );
					 }else{
						 die(json_encode(array("result" => 0)));
					 }
			   }
		}
        if ($operation == 'post') {
            $id = intval($_GP['id']);
            $data = array(
                'openid' => $openid,
                'realname' => $_GP['realname'],
                'mobile' => $_GP['mobile'],
                'city' => $_GP['city'],
				'province'=>$_GP['province'],
				'idname'=>$_GP['idname'],
				'idnumber'=>$_GP['idnumber'],
                'area' => $_GP['area'],
                'address' => $_GP['address'],
            );
			$objValidator = new Validator();
            if (empty($_GP['realname']) || empty($_GP['mobile']) || empty($_GP['address'])) {
                die(showAjaxMess('1002','请输完善您的资料！'));
            }
            if(!empty($_GP['idnumber'])){
                if(empty($_GP['idname'])){
                    die(showAjaxMess('1002','请输入身份证姓名'));
                }
                if (!$objValidator->identityNumberValidator($_GP['idnumber'])) {
                    die(showAjaxMess('1002','身份证格式不正确！'));
                }
            }

            if (!empty($id)) {
                unset($data['openid']);
                mysqld_update('shop_address', $data, array('id' => $id));
                die(showAjaxMess('200',$id));
            } else {
                //修改其他地址为非默认，设置刚创建的为默认
                mysqld_update('shop_address',array('isdefault'=>0),array('openid'=>$openid));
                $data['isdefault'] = 1;
                mysqld_insert('shop_address', $data);
                $id = mysqld_insertid();
				if ( isset($_GP['ajax']) ){
                    die(showAjaxMess('200',$id));
				}else{
					if (!empty($id)) {
                        die(showAjaxMess('200',$id));
					} else {
                        die(showAjaxMess('1002','操作失败'));
					}
				}
            }
        } elseif ($operation == 'default') {
            $id = intval($_GP['id']);
            mysqld_update('shop_address', array('isdefault' => 0), array('openid' =>$openid));
            mysqld_update('shop_address', array('isdefault' => 1), array('id' => $id));
            message(1, '', 'ajax');
        } elseif ($operation == 'default_ajax') {
            $id = intval($_GP['id']);
            mysqld_update('shop_address', array('isdefault' => 0), array('openid' =>$openid));
            mysqld_update('shop_address', array('isdefault' => 1), array('id' => $id));
            die(json_encode(array("result" => 1, "id" => $id)));
        }elseif ($operation == 'detail') {
            $id = intval($_GP['id']);
            $row = mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE id = :id", array(':id' => $id));
            message($row, '', 'ajax');
        } elseif ($operation == 'remove') {
            $id = intval($_GP['id']);
            if (!empty($id)) {
                $address = mysqld_select("select isdefault from " . table('shop_address') . " where id='{$id}'  and openid='".$openid."' limit 1 ");
                if (!empty($address)) {
                    //修改成不直接删除，而设置deleted=1
                    mysqld_update("shop_address", array("deleted" => 1, "isdefault" => 0), array('id' => $id, 'openid' => $openid));
                    if ($address['isdefault'] == 1) {
                        //如果删除的是默认地址，则设置是新的为默认地址  
						$maxid = mysqld_selectcolumn("select max(id) as maxid from " . table('shop_address') . " where deleted = 0 and openid='".$openid."' limit 1 ");
                        if (!empty($maxid)) {
                            mysqld_update('shop_address', array('isdefault' => 1), array('id' => $maxid, 'openid' => $openid));
                            die(json_encode(array("result" => 1, "maxid" => $maxid,'message'=>'删除成功！')));
                        }else{
                            die(json_encode(array("result" => 1, "maxid" =>0,'message'=>'删除成功！')));
						}
                    }else{
						$maxid = mysqld_select("select id from " . table('shop_address') . " where isdefault =1 and deleted = 0 and openid='".$openid."' limit 1 ");
						if ( !empty($maxid) ){
                             die(json_encode(array("result" => 1, "maxid" => $maxid['id'],'message'=>'删除成功！')));
						}else{
                             die(json_encode(array("result" => 1, "maxid" => 0,'message'=>'删除成功！')));
						}
					}
                }
            }
            die(json_encode(array("result" => 0, "maxid" => $id,'message'=>'操作有误!')));
        } else {
			// 获取地址列表
            $address = mysqld_selectall("SELECT * FROM " . table('shop_address') . " WHERE deleted=0 and openid = :openid", array(':openid' => $openid));
			if ( $_GP['op'] == 'ajax' ){
                 include themePage('purchase_address2');
			}else{
                 include themePage('purchase_address');
			}
        }