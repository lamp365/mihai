<?php
				$member=get_member_account(true);
				$openid =$member['openid'] ;
				$memberinfo=member_get($openid);
				if(empty($memberinfo['pwd'])){
					$hiddenoldpwd=true;
				}
				if (checksubmit("submit")) {
						if(empty($_GP['pwd']) || empty($_GP['repwd']))
						{
								message("请输入密码！");	
						}
						if($_GP['pwd'] != $_GP['repwd'])
						{
							   message("新密码与确认密码不一致");	
						}
						if($memberinfo['pwd']!=md5($_GP['oldpwd']))
						{
							   message("原始密码错误!");	
						}
				       $data = array('pwd' => md5($_GP['pwd']));
				       $res  = mysqld_update('member', $data,array('openid'=>$openid));
						if($res){
							//如果该用户也是后台管理员 业务员，，那么同时修改该业务员的密码
							mysqld_query("update ".table('user')." set `password`='{$data['pwd']}' where mobile='{$member['mobile']}' and password='{$member['pwd']}'");
							message('密码修改成功！', mobile_url('fansindex'), 'success');
						}else{
							message('密码修改失败！', refresh(),'error');
						}

				}
			    include themePage('member_pwd');