<?php
      $pindex = max(1, intval($_GP['page']));
      $psize = 30;
      $condition='';
      $conditiondata=array();
	  $mess_list = array();
	 /* $_mess    =  mysqld_selectall("SELECT * FROM " . table('shop_mess'));
	  if (!empty($_GP['mess'])){
		  $condition .= " AND mess_id = ".$_GP['mess'];
	  }*/
    if(!empty($_GP['timestart']) && !empty($_GP['timeend'])){
        $timestart = strtotime($_GP['timestart']);
        $timeend   = strtotime($_GP['timeend']);
        $condition .= " AND createtime >= {$timestart} And createtime<= {$timeend}";
    }
      if(!empty($_GP['realname']))
      {
      	
      	 $condition=$condition.' and realname like :realname';
      	 $conditiondata[':realname']='%'.$_GP['realname'].'%';
      }
         if(!empty($_GP['mobile']))
      {
      	
      	 $condition=$condition.' and mobile = :mobile';
      	 $conditiondata[':mobile']= $_GP['mobile'];
      }
	  $vc = isset($_GP['status'])? $_GP['status']: 1;
      switch ( $vc ){
			   // ÉÌ³Ç»áÔ±
                case 1:
					$condition=$condition.' and parent_roler_id = 0 and son_roler_id = 0 ';
					break;	 
				// ÇþµÀÉÌ»áÔ±
				case 2:
					$condition=$condition.' and parent_roler_id > 0 and son_roler_id > 0 ';
					break;
				default :
					break;
	  }
       if(!empty($_GP['weixinname']))
      {
      	
      	 $condition=$condition.' and openid in (select wxfans.openid from ' . table('weixin_wxfans').' wxfans where wxfans.nickname like :weixinname)';
      	 $conditiondata[':weixinname']='%'.$_GP['weixinname'].'%';
      }
           if(!empty($_GP['alipayname']))
      {
      	
      	 $condition=$condition.' and openid in (select alifans.openid from ' . table('alipay_alifans').' alifans where alifans.nickname like :alipayname)';
      	 $conditiondata[':alipayname']='%'.$_GP['alipayname'].'%';
      }
      $status=1;
          if(empty($_GP['showstatus'])||$_GP['showstatus']==1)
      {
      	
      	 $status=1;
      }
     
         if($_GP['showstatus']==-1)
      {
      	
      	 $status=0;
      }
      if(!empty($_GP['rank_level']))
      {
      $rank_model = mysqld_select("SELECT * FROM " . table('rank_model')."where rank_level=".intval($_GP['rank_level']) );
      if(!empty($rank_model['rank_level']))
      {
      			$condition=$condition." and experience>=".$rank_model['experience'];
      	 		 	$rank_model2 = mysqld_select("SELECT * FROM " . table('rank_model')."where rank_level>".$rank_model['rank_level'].' order  by rank_level limit 1' );
  								if(!empty($rank_model2['rank_level']))
  								{
  									if(intval($rank_model['experience'])<intval($rank_model2['experience']))
  									{
  									$condition=$condition." and experience<".$rank_model2['experience'];
  									}
  								}
  							}
      }

      $rank_model_list = mysqld_selectall("SELECT * FROM " . table('rank_model')." order by rank_level" );
	  // ²»¶Ô»áÔ±ÁÐ±í½øÐÐÉí·ÝÏÞÖÆ£¬±ÜÃâÎÞ·¨¶þ´Î²Ù×÷¡£Ó¦¸ÃÔÚÈ¨ÏÞÄÄÀï½øÐÐ¿ØÖÆ
			$list = mysqld_selectall('SELECT * FROM '.table('member')." where  dummy=0 and `istemplate`=0  and `status`={$status} {$condition} "." LIMIT " . ($pindex - 1) * $psize . ',' . $psize,$conditiondata);
	 		$total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('member')." where  dummy=0 and `istemplate`=0 {$condition} ",$conditiondata);
      $pager = pagination($total, $pindex, $psize);
      
      		foreach($list as  $index=>$item){
      			 $list[$index]['weixin']= mysqld_selectall("SELECT * FROM " . table('weixin_wxfans') . " WHERE openid = :openid", array(':openid' => $item['openid']));
                 $list[$index]['alipay'] = mysqld_selectall("SELECT * FROM " . table('alipay_alifans') . " WHERE openid = :openid", array(':openid' => $item['openid']));
                 $list[$index]['mess_name'] = mysqld_selectcolumn("SELECT title FROM " . table('shop_mess') . " WHERE id = :id", array(':id' => $item['mess_id']));
      		}

            //ÕÒ³öÒµÎñÔ±
            $rolers   = mysqld_select("select id,name,createtime from ".table('rolers')." where type=1 and isdelete=0");
            //ÒµÎñÔ±¶ÔÓ¦µÄ¹ÜÀíÔ±¶¼ÓÐÄÄÐ©
            $user_rolers  = '';
            if(!empty($rolers)){
                $sql = "select r.id,r.rolers_id,r.uid,u.username from ".table('rolers_relation')." as r ";
                $sql .= " left join ".table('user')." as u on u.id=r.uid where r.rolers_id={$rolers['id']}";
                $user_rolers = mysqld_selectall($sql);
            }

        
            $purchase = mysqld_selectall("select id,pid,name,createtime from ".table('rolers')." where type<>1 order by pid asc");
            if (! empty($purchase)) {
                $childrens = '';
                foreach ($purchase as $key => $item) {
                    if (! empty($item['pid'])) {
                        $childrens[$item['pid']][$item['id']] = $item;
                        unset($purchase[$key]);
                    }
                }
            }

			include page('list');