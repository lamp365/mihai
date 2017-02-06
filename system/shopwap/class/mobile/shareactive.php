<?php
    $op     = empty($_GP['op'])? 'display' : $_GP['op'];
    $openid = checkIsLogin();
    if($op == 'display'){
        $openid    = getOpenidFromWeixin($openid);
        $accesskey = $_GP['accesskey'];

        //确认是否已经在活动主表中添加过记录 并跟新当天的参与活动数值
        $share_info  = checkIsAddShareActive($openid);

        //如果带有加密信息，说明是分享进来的，则进行设置缓存
        isReloadOrSetCache($openid,$accesskey);

        //记住当前地址
        tosaveloginfrom();

        //参与总人数
        $canyu_total = get_active_total_people();
        //获取6个为1 等待开奖的
        $toBeDraw    = get_active_goods(1,$openid);
        //获取1个推荐的
        $recommand   = get_active_goods(2,$openid);
        //获取礼品商品 心愿专区
        $activeGoods = get_active_goods(3,$openid);
        //获取即将进入心愿专区的礼品
        $toBeShow    = get_active_goods(4,$openid);
        //获取心愿晒单
        $shaidan     = get_active_shaidan();
        //是否满6个了是的话锁定
        $lock = false;
        if(count($toBeDraw) == 6){
            $lock     = true;
            $drawTime = $toBeDraw[0]['date'];
        }

        include themePage('shareactive');

    }else if($op == 'canyu_recorder'){  //参与记录
        $openid   = getOpenidFromWeixin($openid);
        if(empty($openid)){
            die(showAjaxMess(1002,'您还没登录！'));
        }
        $award_id = $_GP['award_id'];
        $recorder = mysqld_selectall("select createtime,star_num from ".table('addon7_request')." where award_id = {$award_id} and openid='{$openid}' order by id desc");
        if(empty($recorder)){
            die(showAjaxMess(1002,'您暂无参与记录'));
        }else{
            die(showAjaxMess(200,$recorder));
        }

    }else if($op == 'draw_info'){  //开奖情况 中奖人的信息
        $award_id = $_GP['award_id'];
        if(empty($award_id)){
            die(showAjaxMess(1002,'参数有误'));
        }else{
            //获取中奖人
            $drawer = mysqld_select("select openid from ".table('addon7_request')." where award_id = {$award_id} and status=1");
            if($drawer){
                $recorder = mysqld_selectall("select createtime,star_num from ".table('addon7_request')." where award_id = {$award_id} and openid='{$drawer['openid']}' order by id desc");
                die(showAjaxMess(200,$recorder));
            }else{
                die(showAjaxMess(1002,'参数有误'));
            }
        }
    }else if($op == 'wish'){  //进行许愿
        if(empty($_GP['openid'])){
            $openid   = getOpenidFromWeixin($openid);
        }else{
            $openid  = $_GP['openid'];
        }

        if(empty($openid)){
            die(showAjaxMess(1002,'您还没登录！'));
        }
        $award_id = $_GP['award_id'];
        if(empty($award_id)){
            die(showAjaxMess(1002,'参数有误！'));
        }
        //判断今天是否可以参与
        $share_info = checkIsAddShareActive($openid);
        if($share_info['total_num'] == 0){
            die(showAjaxMess(1002,'今天已上限，请明天再来'));
        }

        $award    = mysqld_select("select * from ".table('addon7_award')." where id={$award_id}");
        if($share_info['total_num'] < intval($award['credit_cost'])) {
            $num = $share_info['total_num'];
            die(showAjaxMess(1002,"您的幸运数只剩{$num}个"));
        }
        if($award['state']==2){
            die(showAjaxMess(1002,"该礼品许愿已满！"));
        }

        $star_num_arr = get_star_num($award_id);
        $data = array(
            'openid'         => $openid,
            'award_id'       => $award_id,
            'createtime'     => time(),
            'star_num'       => $star_num_arr['star_num'],
            'star_num_order' => $star_num_arr['star_num_order']
        );
        //商品的分数减去1  消耗个人的点数  并判断当前商品是否已经满人

        $res     = mysqld_insert('addon7_request',$data);
        $last_id = mysqld_insertid();
        if($last_id){
            $total_num = $share_info['total_num']-intval($award['credit_cost']);
            $dicount   = $award['dicount'] + 1;
            $up_data   = array('dicount'=>$dicount);
            if($award['amount'] == $dicount){
                //如果刚好等于总的份数 则已经满人了
                $up_data['state']        = 1;
                $up_data['confirm_time'] = time();
            }
            mysqld_update("share_active",array('total_num'=>$total_num), array('id'=>$share_info['id']));
            mysqld_update("addon7_award",$up_data, array('id'=>$award_id));
            $is_full = '';
            if($award['amount'] == $dicount){
                //新添加一个商品为满人，统计是否有6个商品为满人的
                $is_full = checkAwardGoodsIsFull();
            }
            //把参与总数计入缓存
            if(class_exists('Memcached')){
                $memcache = new Mcache();
                $total    = $memcache->get('shareActiveTotalPeople');
                if(!$total){
                    $total = mysqld_selectcolumn("select count(id) from ".table('addon7_request'));
                }
                $total = $total+1;
                $memcache->set('shareActiveTotalPeople',$total,time()+3600*2);
            }

            if($is_full){
                //刷新页面 的标识
                die(showAjaxMess(202,"恭喜您许愿成功!"));
            }
            die(showAjaxMess(200,"恭喜您许愿成功!"));
        }else{
            die(showAjaxMess(1002,'网络有误，稍后再试'));
        }

    }else if($op == 'result'){
        //把2和3 4的都取出来
        $psize  =  24;
        $pindex = max(1, intval($_GP["page"]));
        $limit  = ' limit '.($pindex-1)*$psize.','.$psize;
        $total  = $pager = '';

        $drawRecorder = mysqld_selectall("select * from ".table('addon7_award')." where state>=2 order by lock_time desc");
        if(!empty($drawRecorder)){
            //按照时间6个显示按照开奖时间
            $temp = array();
            foreach($drawRecorder as $item){
                $drawtime          = empty($item['date'])? 0 : $item['date'];
                $item['drawtime']  = $drawtime;
                $temp[$drawtime][] = $item;
            }
            $drawRecorder = $temp;

            $total  = mysqld_selectcolumn("select count(id) from ".table('addon7_award')." where state>=2");
            $pager  = pagination($total, $pindex, $psize);
        }

        include themePage('shareactive_result');

    }else if($op== 'clickzan') { //晒单点赞
        $id = $_GP['id'];
        if($id){
            $key    = "activeShaidan-".$id;
            $cookie = new LtCookie();
            $res    = $cookie->getCookie($key);
            if($res){
               die(showAjaxMess(1002,'你已经点赞过了'));
            }else{
                mysqld_query("update ".table('share_active_shaidan')." set `zan_num`=zan_num+1 where id={$id}");
                $cookie->setCookie($key,'yes',time()+3600*10);  //10个小时后可再次点击
                die(showAjaxMess(200,'点赞成功！'));
            }
        }
    }else if($op == 'canyu_total'){  //获取总的参与人数
        $canyu_total = get_active_total_people();
        die(showAjaxMess('200',$canyu_total));

    }else if($op == 'yaoqingma'){
        header('Access-Control-Allow-Origin:*');
        $unicode       = $_SESSION[MOBILE_SESSION_ACCOUNT]['unionid'];
        $weixin_openid = $_SESSION[MOBILE_SESSION_ACCOUNT]['weixin_openid'];
        //if(empty($unicode)){
            message('邀请码暂时不开放！','index.php','success');
        //}
        $unicode = 'olMgBwFlMMm46w90gzTT0ao3BHCY';
        $weixin  = mysqld_select("select * from ".table('weixin_wxfans')." where unionid='{$unicode}'");
        if(empty($weixin['openid'])){
            //记住当前地址
            tosaveloginfrom();
            $url = mobile_url('regedit');
            message("请您先注册，才能获取二维码",$url,'error');
        }
        //确认是否已经在活动主表中添加过记录 并跟新当天的参与活动数值
        $info         = checkIsAddShareActive($weixin['openid']);
        $erweimaUrl   = empty($info) ? '' : saveWeixinImgToLocal($weixin['weixin_openid'],$info['erweima']);
        $touxiangUrl  = empty($weixin['avatar']) ? '' : saveWeixinImgToLocal($weixin['weixin_openid'],$weixin['avatar'],false);
        include themePage('shareactive_yqm');
    }

