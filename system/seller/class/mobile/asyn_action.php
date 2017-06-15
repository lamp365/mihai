<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/6/13
 * Time: 19:03
 * 公用的一些异步推送操作，结合方法asyn_doRequest（）来使用
 * 注意不能继承base父类，推送过程中，丢失了用户的登录信息，故不能继承base,会被基类由于未登录而阻拦
 */

namespace seller\controller;

class asyn_action extends \common\controller\basecontroller
{
    /**
     * 异步批量操作 添加
     * @return string
     */
    public function batAddCategory()
    {
        ignore_user_abort (true);
        set_time_limit(0);
        /**
         * 参数 array('sys_p1'=>1,'sys_p2'=>2,'store_p1'=>23,'store_p2'=>23,'sts_id'=>1);
         */
        $_GP = $this->request;
        if(empty($_GP)) return '';
        foreach($_GP as $value){
            if(empty($value)) return '';
        }
        //根据系统分类获取 所有的商品  再导入到对应的店铺分类中
        $sql = "select g.*,p.id as picid,p.picurl,p.contentpicurl from ".table('shop_goods')." as g left join ".table('shop_goods_piclist')." as p ";
        $sql .= " on g.id=p.goodid where g.ccate={$_GP['sys_p2']}";
        $allGoods = mysqld_selectall($sql);

        $dish_sql = "select gid from ".table('shop_dish')." where store_p2={$_GP['store_p2']} and gid != 0";
        $allDish  = mysqld_selectall($dish_sql);

        $dishIds  = array();
        foreach($allDish as $item){
            $dishIds[] = $item['gid'];
        }

        foreach($allGoods as $good){
            if(in_array($good['id'],$dishIds)){
                continue;
            }

            $content_info              = changeWebImgToAli($good['content']);
            $content                   = $content_info['content'];
            $contPic                   = $content_info['img'];  //空或者数组
            $goodsData                 = array();
            $goodsData['gid']          = $good['id'];
            $goodsData['sts_id']       = $_GP['sts_id'];
            $goodsData['store_p1']     = $_GP['store_p1'];
            $goodsData['store_p2']     = $_GP['store_p2'];
            $goodsData['industry_p2_id']= $good['industry_p2_id'];
            $goodsData['status']       = 0;
            $goodsData['title']        = $good['title'];
            $goodsData['thumb']        = $good['thumb'];
            $goodsData['description']  = $good['description'];
            $goodsData['content']      = $content;
            $goodsData['marketprice']  = $good['marketprice'];
            $goodsData['productprice'] = $good['productprice'];
            $goodsData['goodssn']      = $good['goodssn'];
            $goodsData['store_count']  = $good['store_count'];
            $goodsData['createtime']   = time();
            $goodsData['updatetime']   = time();
            $goodsData['brand']        = $good['brand'];
            $goodsData['is_contentimg']= empty($contPic)? 0 : 1;
            mysqld_insert('shop_dish',$goodsData);
            if($lastid = mysqld_insertid()){
                //操作图片
                $contentpicurl = $this->reset_contpic($contPic,$good['contentpicurl']);
                $pic_data      = array();
                if(!empty($good['picid'])){//有细节图
                    $pic_data['picurl']        = $good['picurl'];
                    $pic_data['contentpicurl'] = $contentpicurl;
                    $pic_data['goodid']        = $lastid;
                    mysqld_insert('shop_dish_piclist',$pic_data);
                }else{
                    //没有细节图
                    if(!empty($contentpicurl)){
                        $pic_data['contentpicurl'] = $contentpicurl;
                        $pic_data['goodid']        = $lastid;
                        mysqld_insert('shop_dish_piclist',$pic_data);
                    }
                }

            }//lastid end
       }//allgood end
        die();
    }

    public function reset_contpic($contPic,$contentpicurl)
    {
        //$contPic 数据或者 空数组
        //$contentpicurl 字符串或者 空字符
        $temp_pic = '';
        if(!empty($contPic) && is_array($contPic)){
            $temp_pic = implode(',',$contPic);
        }
        $new_pic = empty($contentpicurl) ? $temp_pic : $contentpicurl.','.$temp_pic;
        $new_pic = rtrim($new_pic,',');
        return $new_pic;
    }

}