<?php

/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/1/25
 * Time: 15:46
 */
class WeixinTool
{
    public  function pop_text()
    {
        $unicode = $_SESSION[MOBILE_SESSION_ACCOUNT]['unionid'];
        $toUser  = $_SESSION[MOBILE_SESSION_ACCOUNT]['weixin_openid'];
//        $weixin  = mysqld_select("select weixin_openid from ".table('weixin_wxfans')." where unionid='{$unicode}'");

//        $toUser   = $weixin['weixin_openid'];

        $weixin_access_token = get_weixin_token();
        $url         = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$weixin_access_token}";
        $template_id = 'mJnqWMTB7HlkRA8QpQ7etz1IDqBdkYvHvebVE3J9l7Q';
        $data = array(
            'touser'      => $toUser,
            "template_id" => $template_id,
            "url"         => "",
            'data'        => array(
                        'first'=>array(
                            'value'=>'你，对了就是你',
                            'coloe'=>'#abcdef',
                        ),
                        'name'=>array(
                            'value'=>'http://7xiuw8.com1.z0.glb.clouddn.com/20150712103927_2015-07-12%2010:31:14%20%E7%9A%84%E5%B1%8F%E5%B9%95%E6%88%AA%E5%9B%BE.png',
                            'color'=>'red',
                        ),
            ),
        );
        $post_data = json_encode($data);
        $res       = http_post($url,$post_data);
        $res       = json_decode($res);
        if($res['errcode'] != 0){
            $msg  = "模板id({$template_id})发送失败：".$res['errmsg'];
            logRecord($msg,"weixin_pop_txt");
        }
    }

    public function get_weixin_erweima($parame,$isTemp=true){
        $weixin_access_token = get_weixin_token();
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$weixin_access_token}";
        if($isTemp){
            $post_data = array(
                'expire_seconds'  => 3600*24*6,
                'action_name'     => 'QR_SCENE',
                'action_info'     => array(
                    'scene'  => array(
                        'scene_id'  => $parame
                    )
                ),
            );
        }else{
            $post_data = array(
                'action_name'     => 'QR_LIMIT_SCENE',
                'action_info'     => array(
                    'scene'  => array(
                        'scene_id'  => $parame
                    )
                ),
            );
        }
        $post_data = json_encode($post_data);
        $result    = http_post($url,$post_data);
        $result    = json_decode($result, true);
        return urldecode($result['url']);
    }
}