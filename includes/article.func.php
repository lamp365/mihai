<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/12/1
 * Time: 17:35
 */
/**
 * @param $content
 * @return mixed
 * @content 将一些文章中带有占位符[shop]宝贝id[/shop]的进行替换成对应的html商品
 * 因为发布文章编辑器不能做到插入商品，所以用占位符，程序来处理。
 * 参数 $return_dishid为真 则会返回匹配到的商品id数组，如果没匹配到返回空
 */
function analyzeShopContent($content,$return_dishid = false){
    $content = htmlspecialchars_decode($content);
    $pattern = '/\[shop\](.*)\[\/shop\]/U';    // 这是匹配的正则表达式
    preg_match_all($pattern, $content, $matches);    //  开始匹配，该函数会把匹配结果放入 $matches
    $dishids = '';
    if($return_dishid){
        //如果只要返回 解析到的商品id 则返回商品id
        if(!empty($matches)){
            $dishids  = $matches[1];
        }
        return $dishids;
    }else{
        //否则解析内容，把占位符换成html
        //获取这样的占位符 代表要插入一个商品
        if(!empty($matches)){
            $place_shop_mark = $matches[0];
            $dishids         = $matches[1];
            foreach($dishids as $key=>$id){
                $where   = $shop_html = '';
                $where['table'] = 'shop_dish';
                $where['where'] = 'a.id='.$id;
                $dishinfo  = get_good($where);
                //取得商品把商品替换成对应的html
                if(!empty($dishinfo)){
                    $url = create_url('mobile', array('id' => $dishinfo['id'],'op'=>'dish','name'=>'shopwap','do'=>'detail'));
                    $img = download_pic($dishinfo['thumb'],120,120);
                    $shop_html ="<div class='item' data-id='{$dishinfo['id']}' data-url='{$url}'>
                                    <a target='_blank' href='javascript:;' class='item-pic'>
                                        <img src='{$img}'>
                                    </a>
                                   <div class='item-info'>
                                        <a class='item-title' href='javascript:;'>{$dishinfo['title']}</a>
                                        <div class='item-price'>
                                            <ins class='price-new'>¥<strong>{$dishinfo['marketprice']}</strong></ins>
                                            <del class='price-old'>¥{$dishinfo['productprice']}</del>
                                        </div>
                                        <div class='item-btn'>去购买</div>
                                    </div>
                                </div>";

                }
                $content = str_replace($place_shop_mark[$key],$shop_html,$content);
            }
        }
        return $content;
    }
}

function getArticleUrl($id,$openid = ''){
    $url = create_url('mobile', array('id' => $id,'name'=>'addon8','do'=>'article','is_app'=>1,'openid'=>$openid));
    return WEBSITE_ROOT.$url;
}

/**
 * 如果该用户不存在则，进行创建
 * 用于发布文章时，有几个固定用户的选择，如果开发环境没有该用户则创建
 */
function createTheUserForNotExist($openid){
    $user = member_get($openid);
    if(empty($user)){
        mysqld_insert('member',array(
            'realname' => '觅海小编'.mt_rand(100,999),
            'mobile'   => get_rand_mobile(),
            'pwd'      => md5('hinrc_123456'),
            'createtime' => time(),
            'openid'   => $openid
        ));
    }
}

/**
 * @param $item  比如是文章列表的一条数据 或者一条评论数据
 * @return mixed
 * 加入该文章的用户头像和名字
 */
function get_article_member($item){
    $item['nickname'] = '';
    $item['avatar']   = '';
    if(!empty($item['openid'])){
        $member = mysqld_select("select nickname,avatar,mobile from ".table('member')." where openid={$item['openid']}");
        $item['nickname'] = empty($member['nickname']) ? substr_cut($member['mobile']) : $member['nickname'];
        $item['avatar']   = empty($member['avatar']) ? '' : download_pic($member['avatar'],150);
    }
    return $item;
}

/**
 * @param $type  文章类型
 * @return array
 * 用于首页展示文章
 */
function getIndexArticle($type){
    switch($type){
        case 'healty':
            $sql = "SELECT * FROM ".table('addon8_article')." where state =6 and (iscommend = 1 or ishot = 1) order by displayorder desc,id desc limit 4";
            break;
        case 'note':
            $sql = "SELECT * FROM ".table('note')." order by isrecommand desc,note_id desc limit 8";
            break;
        case 'headline':
            $sql= "SELECT * FROM ".table('headline')."  order by isrecommand desc,headline_id desc limit 8";
            break;
    }
    $article = mysqld_selectall($sql);
    //获取用户头像
    if(!empty($article)){
        foreach($article as $key => $val){
            $article[$key] = get_article_member($val);
        }
    }
    return $article;
}
