<?php
    $op     = empty($_GP['op'])? 'display' : $_GP['op'];
    $openid = checkIsLogin();
    if($op == 'display'){
        $openid = getOpenidFromWeixin($openid);
        //这里openid还可能是空，因为有些微信用户不一定绑定过用户信息
        if(empty($_GP['accesskey'])){
            //给openid加密后，再带到地址后面，重加载页面，用于分享的时候，可以得到是谁分享的
            $accesskey = getOpenshopAccessKey($openid);
            $url       = mobile_url('shareActive',array('op'=>'display', 'accesskey'=>$accesskey));
            header("location:".$url);
        }

        checkIsAddShareActive($openid);
        //获取今天已经分享给谁了
        $shareMember = getHasShareMember($openid);

        //检查accesskey是否是自己的
        $isSelf        = checkAccessKeyIsSelf($openid,$_GP['accesskey']);
        $sharer_weixin = getSharerOfWeixin($_GP['accesskey'],$openid);
        //把accesskey记入缓存，用于注册或者其他地方得到
        setShareAccesskeyCookie($_GP['accesskey']);
        //记住当前地址
        tosaveloginfrom();
        include themePage('shareActive');

    }else if($op == 'list'){
        $openid = getOpenidFromWeixin($openid);
        if(empty($openid)){
            //未登录则先登录
            header("location:".mobile_url('login'));
        }
        //获取分享者openid，
        $accesskey     = getShareAccesskeyCookie();
        $share_openid  = decodeOpenshopAccessKey($accesskey);
        //再次确认是否已经在活动主表中添加过记录
        $shareActiveId = checkIsAddShareActive($share_openid);
        //检查accesskey是否是自己的
        $isSelf        = checkAccessKeyIsSelf($openid,$accesskey);
        if(!$isSelf){
            addShareActiveRecordMember($shareActiveId,$openid);
        }

        include themePage('shareactive_list');
    }
