<?php

/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/1/25
 * Time: 15:46
 */
class WeixinTool
{
    /**
     * 主动推送模板消息
     * @param $data_arr  $data_arr 所需要的数据
     * @return string
     */
    public  function pop_text($toUser,$template_id,$data_arr)
    {
        //组装要推送的数据格式
        $data = getWeixinPopMsg($toUser,$template_id,$data_arr);
        if(empty($data)){
            return '';
        }
        $weixin_access_token = get_weixin_token();
        $url       = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$weixin_access_token}";
        $post_data = json_encode($data);
        $res       = http_post($url,$post_data);
        $res       = json_decode($res,true);
        if($res['errcode'] != 0){
            $msg  = "模板id({$data['template_id']})发送失败：".$res['errmsg'];
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
                        //临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
                        'scene_id'  => $parame
                    )
                ),
            );
        }else{
            $post_data = array(
                'action_name'     => 'QR_LIMIT_STR_SCENE',
//                'action_name'     => 'QR_LIMIT_SCENE',    //只能用id 不能 用 str
                'action_info'     => array(
                    'scene'  => array(
                        //字符串类型，长度限制为1到64，仅永久二维码支持此字段
                        'scene_str'  => $parame
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
        if(empty($data)){
            logRecord('推送客服消息为空','weixin_pop_msg');
            return '';
        }
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

    /**
     * 上传文件到媒体中 临时媒体存三天
     * @param $file 文件绝对路径
     * @param $type image  voice  video thumb
     */
    public function uploadTempMedia($file,$type='image'){
        $file = realpath($file);
        $access_token = get_weixin_token();
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type={$type}";

        if (class_exists('\CURLFile')) {
            $data = array(
                'media'=> new \CURLFile($file)
            );
        } else {
            $data = array(
                'media'=> "@{$file}"
            );
        }
        $res = http_post($url,$data);
        $res = json_decode($res,true);
        if(isset($res['media_id'])){
            return $res['media_id'];
        }else{
            return '';
        }
    }

    /**
     * 获取媒体最新的一条
     * @param $type image  news  video
     */
    public function medialist($type,$count=1){
        $access_token = get_weixin_token();
        $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token={$access_token}";
        $post_data = array(
            'type'   => $type,
            'offset' => '0',
            'count'  => $count,
        );
        $post_data = json_encode($post_data);
        $res = http_post($url,$post_data);
        $res = json_decode($res,true);
        switch($type){
            case 'image':
                //有需要的时候载补充
                break;
            case 'news':
                if(!empty($res['item'])){
                    //解析内容，组装成pop_custom_msg中的信息格式 用于推送客服消息
                    return $this->getMediaContentToPopMsg($res['item'],$type);
                }else{
                    return '';
                }
                break;
        }

    }

    /**
     * 根据或得到的媒体，进行组装数据 成要推送的数据  数据格式参照pop_custom_msg
     * @param $item
     * @param $type  image  news  video
     * @return array|string
     */
    public function getMediaContentToPopMsg($item,$type){
        $data  = '';
        switch($type){
            case 'news':
                $item = $item[0]['content']['news_item'];
                foreach($item as $one){
                    $article['title']       = $one['title'];
                    $article['description'] = '';
                    $article['url']         = $one['url'];
                    $article['picurl']      = $one['thumb_url'];
                    $data[] = $article;
                }
                break;

            case 'image':

                break;
        }

        return $data;
    }

    /**
     * 获取小程序二维码
     * type 1 只能是 $condition  跳转到某一个页面 不能带 ？参数  二维码数量有限
     * type 2 可以带入场景值 默认进入到首页  $condition 【用户member中的openid】就是对应的场景值  适合做推广型扫码 绑定用户上下级  数量无限
     * type 3 只能是 $condition  跳转到某一个页面 可以带 ？参数  二维码数量有限
     * @param $condition
     * @param $type
     * @return array
     */
    public function get_xcx_erweima($condition,$type)
    {
        if(empty($condition) || empty($type)){
            return array('errno'=>0,'message'=>'参数不能为空！');
        }

        if(in_array($_SERVER['HTTP_HOST'],array('www.sdudian.com','sdudian.com'))){
            //以免测试环境跟线上环境 二维码发生覆盖
            $dir     = 'xcxqrcode';
            $picname = md5($condition).".png";
        }else{
            $dir     = 'xcxqrcode';
            $picname = "dev_".md5($condition).".png";
        }
        $picfile = $dir."/".$picname;
        $res     = aliyunOSS::doesObjectExist($picfile);
        if($res){
            //如果存在  直接拼接二维码地址
            $erweima =  aliyunOSS::aliurl.'/'.$picfile;
            return array('errno'=>1,'message'=>$erweima);
        }

        //否则的上传到阿里云
        $seting = globaSetting();
        $appid  = $seting['xcx_appid'];
        $secret = $seting['xcx_appsecret'];

        $url     = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $content = http_get($url);

        if (empty($content)) {
            return array('errno'=>0,'message'=>'获取微信access token失败, 请稍后重试！');
        }
        $token = @json_decode($content, true);
        if (empty($token) || ! is_array($token)) {
            return array('errno'=>0,'message'=>'获取微信access token失败, 请稍后重试。');
        }
        if (empty($token['access_token']) || empty($token['expires_in'])) {
            return array('errno'=>0,'message'=>'解析微信公众号授权失败, 请稍后重试！');
        }

        $access_token = $token['access_token'];
        if($type == 1){
            //只能是 $condition  跳转到某一个页面 不能带 ？参数  二维码数量有限
            $url = "https://api.weixin.qq.com/wxa/getwxacode?access_token=".$access_token;
            $parame['path']  = $condition;
            $parame['width'] = 200;
        }else if($type == 2){
            //可以带入场景值 默认进入到首页  $condition 【用户member中的openid】就是对应的场景值  适合做推广型扫码 绑定用户上下级  数量无限
            $url = "http://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$access_token;
            $parame['scene']  = $condition;
            $parame['width'] = 200;
        }else if($type == 3){
            //只能是 $condition  跳转到某一个页面 可以带 ？参数  二维码数量有限
            $url = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=".$access_token;
            $parame['path']  = $condition;
            $parame['width'] = 200;
        }else{
            return array('errno'=>0,'message'=>'类型参数有误！');
        }

        $parame = json_encode($parame);
        $data   = http_post($url,$parame);
        $result = json_decode($data,true);
        if(is_array($result)){
            //说明报错了
            return array('errno'=>0,'message'=>$result['errmsg']);
        }

        //将二进制流 写入本地图片  把参数MD5 后 作为图片名，传到阿里云，下次同一个参数的请求二维码，直接从阿里拿
        $picfile = WEB_ROOT."/logs/".md5($condition).'.png';
        $res     = file_put_contents($picfile,$data);
        //再上传到阿里上
        if($res){
            //传到阿里服务器
            $info = aliyunOSS::putObject($picfile,$picname,$dir);
            //上传到阿里后 可以删除了
            @unlink($picfile);
            return array('errno'=>1,'message'=>$info['oss-request-url']);
        }else{
            return array('errno'=>0,'message'=>'网络原因,二维码生成失败！');
        }
    }
}