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
