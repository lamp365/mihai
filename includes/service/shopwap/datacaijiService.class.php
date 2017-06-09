<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/7
 * Time: 18:29
 * demo
 * service 层 用于简化 我们的控制器，让控制器尽量再 简洁
 * 把一些业务提取出来，放在service层中去操作
$a = new \service\shop\goodsService();
if($a->todo()){
//操作成功 则继续业务
}else{
message($a->getError());
}
 */
namespace service\shopwap;
use QL\QueryList;

class datacaijiService extends \service\publicService
{
    /**
     * 母婴用品  行业属于 1 和 3
     * @param $data
     */
    public function getJindong($data)
    {
        $run_url    = $data['cat_url'];
        $caiji_rule = $data['caiji_rule'];
        $result =  QueryList::Query($run_url,$caiji_rule,'')->getData(function($item) use($run_url){
            foreach($item as $key => &$content){
                if($key == 'cat2_html'){
                    $cate2_info = QueryList::Query($content,array(
                        'cat2_name'  => array('a','text'),
                        'href2_link' => array('a','href')
                    ))->data;

                    foreach($cate2_info as $key2 => &$row2){
                        $row2['href2_link'] = "https:".$row2['href2_link'];
                    }
                    unset($item[$key]);
                    $item['son_cat'] = $cate2_info;
                }else{

                    $key_arr = explode('_',$key);
                    $link = array_pop($key_arr);
                    if($link == 'link'){
                        //对抓取的内容 带有链接的补全路径
                        /* if(!strstr($content,'http://') && !strstr($content,"https://")){
                             $url_arr = parse_url($run_url);
                             $url     = $url_arr['scheme'].'://'.$url_arr['host'];
                             $content = rtrim($url,'/').'/'.ltrim($content,'/');
                         }*/
                        $content = 'https:'.$content;
                    }else if($link == 'contlink'){
                        $content = 'https:'.$content;
                    }////link end if
                }///cat2_html   end else

            }// foreach end
            return $item;
        });
        return $result;
    }

    public function insert_cate($data,$industry_p1_id,$industry_p2_id)
    {
        foreach($data as $row){
            $data1['industry_p1_id'] = $industry_p1_id;
            $data1['industry_p2_id'] = $industry_p2_id;
            $data1['name'] = $row['cat1_name'];
            $data1['cate_url'] = $row['href1_link'];
            $data1['curent_page'] = 1;
            mysqld_insert('shop_category',$data1);
            $pid = mysqld_insertid();
            foreach($row['son_cat'] as $item){
                $data2['industry_p1_id'] = $industry_p1_id;
                $data2['industry_p2_id'] = $industry_p2_id;
                $data2['name'] = $item['cat2_name'];
                $data2['cate_url'] = $item['href2_link'];
                $data2['curent_page'] = 1;
                $data2['parentid'] = $pid;
                $data2['needcaiji'] = 1;
                $data2['markcaiji'] = 1;
                mysqld_insert('shop_category',$data2);
            }
        }
        return true;
    }
}