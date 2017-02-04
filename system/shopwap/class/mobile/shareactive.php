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

    }else if($op == 'wish'){  //进行许愿
        $openid   = getOpenidFromWeixin($openid);
        if(empty($openid)){
            die(showAjaxMess(1002,'您还没登录！'));
        }
        $award_id = $_GP['award_id'];

        //判断今天是否可以参与
        $share_info = checkIsAddShareActive($openid);
        if($share_info['total_num'] == 0){
            die(showAjaxMess(1002,'今天已上限，请明天再来'));
        }

        $award    = mysqld_select("select id,dicount from ".table('addon7_award')." where id={$award_id}");
        if($share_info['total_num']>=intval($award['credit_cost'])){
            $star_num_arr = get_star_num($award_id);
            $data = array(
                'openid'         => $openid,
                'award_id'       => $award_id,
                'createtime'     => time(),
                'star_num'       => $star_num_arr['star_num'],
                'star_num_order' => $star_num_arr['star_num_order']
            );
            //商品的分数减去1  消耗个人的点数  并判断当前商品是否已经满人
            if($award['state']==2){
                die(showAjaxMess(1002,"该礼品许愿已满！"));
            }else{
                $res = mysqld_insert('addon7_request',$data);
                if($res){
                    if($award['dicount'] == 0){
                        $total_num = $share_info['total_num']-intval($award['credit_cost']);
                        mysqld_update("share_active",array('total_num'=>$total_num), array('id'=>$share_info['id']));
                    }else{
                        $total_num = $share_info['total_num']-intval($award['credit_cost']);
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
                    }
                    die(showAjaxMess(200,"恭喜您许愿成功!"));
                }else{
                    die(showAjaxMess(1002,'网络有误，稍后再试'));
                }
            }

        }else{
            $num = $share_info['total_num'];
            die(showAjaxMess(1002,"您的点数只剩{$num}个"));
        }

    }else if($op == 'result'){

        include themePage('shareactive_result');
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

