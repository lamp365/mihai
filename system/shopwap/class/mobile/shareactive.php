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
        if(is_mobile_request()){
            //获取今天已经分享给谁了
            $shareMember = getHasShareMember($share_info);

        }else{
            //参与总人数
            $canyu_total = get_active_total_people();
            //获取6个为2 等待开奖的

            //获取1个推荐的

            //获取礼品商品

            //获取明天或者之后可以许愿的商品
        }
        include themePage('shareactive');

    }else if($op == 'list'){
        $openid = getOpenidFromWeixin($openid);
        if(empty($openid)){
            //未登录则先登录
            $url = mobile_url('login');
            message('请先登录，再参与！',$url,'success');
        }

        $share_info = checkIsAddShareActive($openid);
        //参与总人数
        $canyu_total = get_active_total_people();
        //获取活动正在进行的商品 endtime就是活动开始时间
        $now_time = time();
        $psize    =  6;
        $pindex   = max(1, intval($_GP["page"]));
        $limit    = ' limit '.($pindex-1)*$psize.','.$psize;
        $sql = "select * from ".table('addon7_award')." where state<=1 and endtime<={$now_time} order by id desc {$limit}";
        $shareActiveShop = mysqld_selectall($sql);
        if(!empty($shareActiveShop)){
            foreach($shareActiveShop as $key=>&$item){
                $diff_count        = $item['amount']-$item['dicount'];
                $jindutiao         = round($diff_count*100/$item['amount'],2).'%';   //进度条
                $item['jindutiao'] = $jindutiao;
                $item['wish_num']  = mysqld_selectcolumn("select count(id) from ".table('addon7_request')." where award_id={$item['id']} and openid='{$openid}'");
            }
        }

        //当手机端滑动的时候加载下一页
        if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
            if ( empty($shareActiveShop) ){
                die(showAjaxMess(1002,'查无数据！'));
            }else{
                die(showAjaxMess(200,$shareActiveShop));
            }
        }

        include themePage('shareactive_list');
    }else if($op == 'activity'){

        include themePage('shareactive_activity');
    }else if($op == 'ajax_moreMember'){
        $openid       = getOpenidFromWeixin($openid);
        $info         = checkIsAddShareActive($openid);
        //获取所有
        $share_member = getHasShareMember($info,false);
        //因为第一次加载的时候是10个头像，故这里去除前面10个
        $share_member = array_slice($share_member, 10);
        if(empty($share_member)){
            die(showAjaxMess(1002,$share_member));
        }else{
            die(showAjaxMess(200,$share_member));
        }
    }else if($op == 'canyu_recorder'){  //参与记录
        $openid   = getOpenidFromWeixin($openid);
        $award_id = $_GP['award_id'];
        $recorder = mysqld_selectall("select createtime,star_num from ".table('addon7_request')." where award_id = {$award_id} and openid='{$openid}'");
        if(empty($recorder)){
            die(showAjaxMess(1002,'暂无参与记录'));
        }else{
            die(showAjaxMess(200,$recorder));
        }

    }else if($op == 'wish'){  //进行许愿
        $openid   = getOpenidFromWeixin($openid);
        $award_id = $_GP['award_id'];
        //判断今天是否可以参与
        $share_info = checkIsAddShareActive($openid);
        if($share_info['total_num']>0){
            $data = array(
                'openid'     => $openid,
                'award_id'   => $award_id,
                'createtime' => time(),
                'star_num'   => '2564',
            );
            //商品的分数减去1  并判断当前商品是否已经满人
            $award = mysqld_select("select id,dicount from ".table('addon7_award')." where id={$award_id}");
            if($award['dicount']<1){
                die(showAjaxMess(1002,"该礼品许愿已满！"));
            }else{
                $res = mysqld_insert('addon7_request',$data);
                if($res){
                    $total_num = $share_info['total_num']-1;
                    $up_data   = array('dicount'=>$award['dicount'] - 1);
                    if($award['dicount'] == 1){
                        //如果刚好剩下一次许愿 则修改该商品 状态为1 表示已经满人
                        $up_data['state']        = 1;
                        $up_data['confirm_time'] = time();
                    }
                    mysqld_update("share_active",array('total_num'=>$total_num), array('id'=>$share_info['id']));
                    mysqld_update("addon7_award",$up_data, array('id'=>$award_id));
                    if($award['dicount'] == 1){
                        //新添加一个商品为满人，统计是否有6个商品为满人的
                        checkAwardGoodsIsFull();
                    }
                    die(showAjaxMess(200,"恭喜您许愿成功!"));
                }else{
                    die(showAjaxMess(1002,'操作失败，稍后再试'));
                }
            }

        }else{
            die(showAjaxMess(1002,'今天已上限，请明天再来'));
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

