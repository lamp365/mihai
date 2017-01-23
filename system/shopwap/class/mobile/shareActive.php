<?php
    $op     = empty($_GP['op'])? 'display' : $_GP['op'];
    $openid = checkIsLogin();
    if($op == 'display'){
        if(empty($openid)){
            //未登录  获取微信表所存的openid
            //如果是用微信端打开可以获取到  如果用qq分享出去打开，则没有该信息
            $unionid = get_weixin_session_account('unionid');
            if(!empty($unionid)){
                $weixin_info = mysqld_select("select openid from ".table('weixin_wxfans')." where unionid={$unionid}");
                if(!empty($weixin_info)){
                    $openid = $weixin_info['openid'];
                }

            }
        }
        //这里openid还可能是空，因为有些微信用户不一定绑定过用户信息
        checkIsAddShareActive($openid);
        if(!empty($openid)){
            //获取今天已经分享给谁了

        }
        include themePage('shareActive');
    }else if($op == 'list'){

        include themePage('shareactive_list');
    }
