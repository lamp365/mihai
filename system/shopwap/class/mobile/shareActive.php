<?php
    $op     = empty($_GP['op'])? 'display' : $_GP['op'];
    $openid = checkIsLogin();
    if($op == 'display'){
        $openid = getOpenidFromWeixin($openid);
        //这里openid还可能是空，因为有些微信用户不一定绑定过用户信息
        $accesskey = getShareAccesskeyCookie();
        if(empty($accesskey)){
            if(empty($_GP['accesskey'])){
                //给自己openid加密后，得到accesskey用于分享
                $accesskey = encodeShareAccessKey($openid);
            }else{
                //获取当前
                $accesskey = $_GP['accesskey'];
            }

            $url       = mobile_url('shareActive',array('op'=>'display', 'accesskey'=>$accesskey));
            //把accesskey记入缓存，用于注册或者其他地方用到
            setShareAccesskeyCookie($accesskey);
            //再重新加载页面，是为了让cookie生效，也同时，让地址确保带上accesskey，便于分享
            header("location:".$url);
        }

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
        include themePage('shareActive');

    }else if($op == 'list'){
        $openid = getOpenidFromWeixin($openid);
        if(empty($openid)){
            //未登录则先登录
            $url = mobile_url('login');
            message('请先登录，再参与！',$url,'success');
        }
        //获取活动商品
        $psize  =  6;
        $pindex = max(1, intval($_GP["page"]));
        $limit  = ' limit '.($pindex-1)*$psize.','.$psize;
        $sql = "select a.*,d.title as dtitle from ".table('addon7_award')." as a left join ".table('shop_dish')." as d";
        $sql .= " on a.dishid=d.id order by a.id desc {$limit}";
        $shareActiveShop = mysqld_selectall($sql);
        if(!empty($shareActiveShop)){
            foreach($shareActiveShop as $key=>$item){
                //从产品库中获取缩略图
                $jindutiao  = ($item['amount']-$item['dicount'])*100/$item['amount'].'%';   //进度条
                $shareActiveShop[$key]['dthumb']    = getGoodsThumb($item['gid']);
                $shareActiveShop[$key]['jindutiao'] = $jindutiao;
            }
        }

        //获取分享者openid，
        $accesskey     = getShareAccesskeyCookie();
        $share_openid  = decodeShareAccessKey($accesskey);
        //再次确认是否已经在活动主表中添加过记录 并跟新当天的参与活动数值
        $shareActiveId = checkIsAddShareActive($share_openid);
        //检查accesskey是否是自己的
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
        $share_member = array_slice($share_member, 10);
        if(empty($share_member)){
            die(showAjaxMess(1002,$share_member));
        }else{
            die(showAjaxMess(200,$share_member));
        }
    }
