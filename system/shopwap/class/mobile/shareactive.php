<?php
    $op     = empty($_GP['op'])? 'display' : $_GP['op'];
    $openid = checkIsLogin();
    if($op == 'display'){
        $openid = getOpenidFromWeixin($openid);
        //这里openid还可能是空，因为有些微信用户不一定绑定过用户信息
        //是否需要重新载入页面 带上用户openid信息用于分享
        isReloadShareActivePage($openid);

        $isOpenByWeixin = false;
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $isOpenByWeixin = true;
        }
        //确认是否已经在活动主表中添加过记录 并跟新当天的参与活动数值
        checkIsAddShareActive($openid);
        //获取今天已经分享给谁了
        $shareMember = getHasShareMember();

        //检查accesskey是否是自己的
        $isSelf        = checkAccessKeyIsSelf($openid);
        //获取分享者微信信息
        $sharer_weixin = getSharerOfWeixin($openid);

        //记住当前地址
        tosaveloginfrom();
        include themePage('shareactive');

    }else if($op == 'list'){
        $openid = getOpenidFromWeixin($openid);
        if(empty($openid)){
            //未登录则先登录
            $url = mobile_url('login');
            message('请先登录，再参与！',$url,'success');
        }
        //获取活动正在进行的商品 endtime就是活动开始时间
        $now_time = time();
        $psize    =  6;
        $pindex   = max(1, intval($_GP["page"]));
        $limit    = ' limit '.($pindex-1)*$psize.','.$psize;
        $sql = "select a.*,d.title as dtitle from ".table('addon7_award')." as a left join ".table('shop_dish')." as d";
        $sql .= " on a.dishid=d.id where a.state=0 and a.endtime<={$now_time} order by a.id desc {$limit}";
        $shareActiveShop = mysqld_selectall($sql);
        if(!empty($shareActiveShop)){
            foreach($shareActiveShop as $key=>$item){
                //从产品库中获取缩略图
                $jindutiao  = round(($item['amount']-$item['dicount'])*100/$item['amount'],2).'%';   //进度条
                $shareActiveShop[$key]['dthumb']    = getGoodsThumb($item['gid']);
                $shareActiveShop[$key]['jindutiao'] = $jindutiao;
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


        //获取分享者openid，
        $accesskey     = getShareAccesskeyCookie();
        $share_openid  = decodeShareAccessKey($accesskey);
        //再次确认是否已经在活动主表中添加过记录 并跟新当天的参与活动数值
        $shareActiveId = checkIsAddShareActive($share_openid);
        //检查accesskey是否是自己的   z这里上面已经强制需要登录，所以openid是存在的
        $isSelf        = checkAccessKeyIsSelf($openid);
        if(!$isSelf){
            addShareActiveRecordMember($shareActiveId,$openid);
        }
        include themePage('shareactive_list');
    }else if($op == 'activity'){

        include themePage('shareactive_activity');
    }else if($op == 'ajax_moreMember'){
        //获取所有
        $share_member = getHasShareMember();
        //因为第一次加载的时候是10个头像，故这里去除前面10个
        $share_member = array_slice($share_member, 10);
        if(empty($share_member)){
            die(showAjaxMess(1002,$share_member));
        }else{
            die(showAjaxMess(200,$share_member));
        }
    }else if($op == 'yaoqingma'){
        $unicode       = $_SESSION[MOBILE_SESSION_ACCOUNT]['unionid'];
        $weixin_openid = $_SESSION[MOBILE_SESSION_ACCOUNT]['weixin_openid'];
        if(empty($unicode)){
            message('活动暂时关闭！','index.php','success');
        }
        $weixin  = mysqld_select("select * from ".table('weixin_wxfans')." where unionid='{$unicode}'");
        if(empty($weixin['openid'])){
            //记住当前地址
            tosaveloginfrom();
            message("请您先注册，才能获取二维码");
        }

        include themePage('shareactive_yqm');
    }

