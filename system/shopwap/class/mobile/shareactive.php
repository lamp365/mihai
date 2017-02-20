<?php
    $op           = empty($_GP['op'])? 'display' : $_GP['op'];
    $openid       = checkIsLogin();
    //获取微信分享的一些参数
    $weixin_share = get_share_js_parame();

    if($op == 'display'){
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
        //获取心愿晒单 手机访问不需要
        if(!is_mobile_request())
            $shaidan     = get_active_shaidan();

        //获取所有优惠券
        $allBonus   = get_all_changebonus($openid);
        //是否满6个了是的话锁定
        $lock = false;
        if(count($toBeDraw) == 6){
            $lock     = true;
            $drawTime = $toBeDraw[0]['date'];
        }

        //wap端关于我们
        if(is_mobile_request()){
            $use_about = getArticle(1,5);
            if ( !empty($use_about) ){
                $use_about = mobile_url('article',array('name'=>'addon8','id'=>$use_about[0]['id']));
            }else{
                $use_about =  'javascript:void(0)';
            }
        }
        //许愿总数
        $wish_total_num = 0;
        if($openid)
           $wish_total_num  = mysqld_selectcolumn("select count(id) from ".table('addon7_request')." where openid={$openid}");
        include themePage('shareactive');

    }else if($op == 'canyu_recorder'){  //参与记录

        if(empty($openid)){
            die(showAjaxMess(1000,'您还没登录！'));
        }
        $award_id = $_GP['award_id'];
        $recorder = mysqld_selectall("select createtime,star_num,star_num_order from ".table('addon7_request')." where award_id = {$award_id} and openid='{$openid}' order by id desc");
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
                $recorder = mysqld_selectall("select createtime,star_num,star_num_order from ".table('addon7_request')." where award_id = {$award_id} and openid='{$drawer['openid']}' order by id desc");
                die(showAjaxMess(200,$recorder));
            }else{
                die(showAjaxMess(1002,'参数有误'));
            }
        }
    }else if($op == 'wish'){  //进行许愿
        if(!empty($_GP['openid'])){
            //该分支预留作为刷单用，但是后台有开关，开关变动会报警。给相关人
            $openid   = $_GP['openid'];
            shuadan_checkActiveCishu($openid);
        }

        if(empty($openid)){
            die(showAjaxMess(1002,'您还没登录！'));
        }
        $award_id = $_GP['award_id'];

        //判断今天是否可以参与
        $share_info = checkIsAddShareActive($openid);
        if($share_info['total_num'] == 0){
            die(showAjaxMess(1002,'今天已上限，请明天再来'));
        }

        $award    = mysqld_select("select * from ".table('addon7_award')." where id={$award_id}");
        if($share_info['total_num'] < intval($award['credit_cost'])) {
            $num      = $share_info['total_num'];
            $need_num = intval($award['credit_cost']);
            $msg      = array('num'=>"您的心愿数只剩{$num}个",'need_num'=>"当前需要{$need_num}个");
            die(showAjaxMess(1004,$msg));
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
            if($star_num_arr['star_num_order'] == 1){
                $des = "您是第一个许愿者哦，棒棒的！";
            }else{
                $des = "许愿越多，实现几率越大哟~~";
            }
            die(showAjaxMess(200,array('tit'=>"获得心愿数字:{$star_num_arr['star_num_order']}","des"=>$des)));
        }else{
            die(showAjaxMess(1002,'网络有误，稍后再试'));
        }

    }else if($op == 'result'){
        //把2和3 4的都取出来
        $psize  =  24;
        $pindex = max(1, intval($_GP["page"]));
        $limit  = ' limit '.($pindex-1)*$psize.','.$psize;
        $total  = $pager = '';

        $drawRecorder = mysqld_selectall("select * from ".table('addon7_award')." where state>=2 order by lock_time desc {$limit}");
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

        //当手机端滑动的时候加载下一页
        if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
            if ( empty($drawRecorder) ){
                die(showAjaxMess(1002,'查无数据！'));
            }else{
                die(showAjaxMess(200,$drawRecorder));
            }
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

    }else if($op == 'get_bonuse'){//兑换优惠卷
        if(empty($openid)){
            die(showAjaxMess(1002,"对不起， 您还没登录！"));
        }
        $bonus_id = $_GP['bonus_id'];
        if(empty($bonus_id)){
            die(showAjaxMess(1002,"对不起，参数有误！"));
        }

        $res = toChangeBonus($openid,$bonus_id);
        die($res);

    }else if($op == 'recorderCount'){//当前参与的记录
        $award_id = $_GP['award_id'];
        if(empty($award_id)){
            die(showAjaxMess(200,"0"));
        }
        $total    = getRecorderCount($openid,$award_id);
        die(showAjaxMess(200,$total));

    }else if($op == 'rule'){
        //活动规则
        include themePage('shareactive_rule');

    }else if($op == 'getStandData'){
        //获取标准数据的截图
    	$lock_time = $_GP['lock_time'];
        if(empty($lock_time)){
            die(showAjaxMess(1002,'参数有误！'));
        }
        $res = mysqld_select("select thumb from ".table('addon7_point')." where lock_time={$lock_time}");
        if(empty($res)){
            die(showAjaxMess(1002,'查无信息'));
        }else{
            die(showAjaxMess(200,$res['thumb']));
        }
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

