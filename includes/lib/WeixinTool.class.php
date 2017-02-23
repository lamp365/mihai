<?php

/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/1/25
 * Time: 15:46
 */
class WeixinTool
{
    public  function pop_text($toUser = '')
    {
        $weixin_access_token = get_weixin_token();
        $url         = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$weixin_access_token}";
        $template_id = 'mJnqWMTB7HlkRA8QpQ7etz1IDqBdkYvHvebVE3J9l7Q';
        $data = array(
            'touser'      => $toUser,
            "template_id" => $template_id,
            "url"         => "",  //可给也可以不给
            'data'        => array(
                        'first'=>array(
                            'value'=>urlencode('你，对了就是你'),
                            'coloe'=>'#abcdef',
                        ),
                        'name'=>array(
                            'value'=>urlencode('http://7xiuw8.com1.z0.glb.clouddn.com/20150712103927_2015-07-12%2010:31:14%20%E7%9A%84%E5%B1%8F%E5%B9%95%E6%88%AA%E5%9B%BE.png'),
                            'color'=>'red',
                        ),
            ),
        );
        $post_data = json_encode($data);
        $res       = http_post($url,$post_data);
        $res       = json_decode($res,true);
        if($res['errcode'] != 0){
            $msg  = "模板id({$template_id})发送失败：".$res['errmsg'];
            logRecord($msg,"weixin_pop_txt");
        }
    }

    /**
     * 生成带参数的二维码 带参数在关注的时候用来绑定用户关系
     * @param $parame
     * @param bool $isTemp  临时二维码寸6天  永久二维码的话，一个公众号只能10万张
     * @return string    返回带参数的二维码用于关注，拿到地址后，在自己去用js或者php生成二维码
     */
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
        $ticket    = $result['ticket'];
        $erweima   = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={$ticket}";
        return $erweima;
    }

    /**
     * 主动向用户推送客服消息 可以文本，图本，图片，视频等可扩展
     * @param $toUser
     * @param $data
     * @param $type
     * @return string
     */
    public function pop_custom_msg($toUser,$data,$type){
        $pop_data['touser']  = $toUser;
        $pop_data['msgtype'] = $type;
        switch($type){
            case 'text'://文本
                //$data是字符串信息
                $pop_data['text']['content'] = $data;
                break;
            case 'image'://图片
                //$data是一个媒体id
                $pop_data['image']['media_id'] = $data;
                break;
            case 'news'://图文
                //￥data是一个二维数组信息
                $article = array();
                foreach($data as $key =>$item){
                    $article['title']       = $item['title'];
                    $article['description'] = $item['description'];
                    $article['url']         = $item['url'];
                    $article['picurl']      = $item['picurl'];
                    $pop_data['news']['articles'][] = $article;
                }
                break;
            case 'video'://视频
                //$data是一个数组信息
                $pop_data['video']['media_id']       = $data['media_id'];
                $pop_data['video']['thumb_media_id'] = $data['thumb_media_id'];
                $pop_data['video']['title']          = $data['title'];
                $pop_data['video']['description']    = $data['description'];
                break;
            default:
                return '';

        }

        $access_token = get_weixin_token();
        $url   = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
        $post  = json_encode_ex($pop_data);
        $res   = http_post($url, $post);
        $res   = json_decode($res,true);
        if($res['errcode'] != 0){
            logRecord('weixin_tool中推送客服信息失败，当前类型是：'.$type,'pop_custom_msg');
        }
    }


}