<?php
				$member=get_member_account(true,true);
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
				      mysqld_update('member', $data,array('openid'=>$openid));
			           message('密码修改成功！', mobile_url('fansindex'), 'success');
				}
					   include themePage('member_pwd');