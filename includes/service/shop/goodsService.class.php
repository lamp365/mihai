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
namespace service\shop;

class goodsService extends \service\publicService
{
    //添加产品的时候 c操作图片
    public  function actGoodsPicture($id,$_GP)
    {
        $picurl_str = empty($_GP['attachment-new']) ? '' : implode(',',$_GP['attachment-new']);
        $find       = mysqld_select("select id from ".table('shop_goods_piclist')." where goodid={$id} ");
        if(empty($find)){
            if(empty($picurl_str)){
                return '';
            }else{
                //首次插入
                //新添加
                $data = array(
                    'goodid' => $id,
                    'picurl' => $picurl_str
                );
                mysqld_insert('shop_goods_piclist', $data);
            }
        }else{
            mysqld_update('shop_goods_piclist',array('picurl'=>$picurl_str),array('id'=>$find['id']));
        }
    }
    
    //添加dish到shop_goods
    public  function addDishToShopGoods($dish_id,$shop_cat_array=array('p1'=>'','p2'=>''))
    {
        $info = mysqld_select("select * from " . table('shop_dish') . " where id=:id  limit 1", array(":id" =>$dish_id));
        if(!$info){
            $this->error = "id匹配不到数据";
            return false;
        }
        if( $info['is_already_in_shop'] ){
            $this->error = "已经在产品库中";
            return false;
        }
        $data = array(
            'pcate'        => intval($shop_cat_array['p1']),
            'ccate'        => intval($shop_cat_array['p2']),
            'type'         => $info['type'],
            'brand'        => $info['brand'],
            'sort'         => intval($info['sort']),
            'title'        => $info['title']?$info['title']:'',
            'thumb'        => $info['thumb']?$info['thumb']:'',
            'description'  => $info['description']?$info['description']:'',
            'content'      => $info['content']?$info['content']:'',
            'marketprice'  => $info['marketprice'],
            'productprice' => $info['productprice'],
            'store_count'  => $info['store_count'],
            'totalcnf'     => $info['totalcnf'],
            'createtime'   => TIMESTAMP,
            'isnew'        => $info['isnew']?$info['isnew']:0,
            'issendfree'   => $info['issendfree']?$info['issendfree']:0,
            'ishot'        => $info['ishot']?$info['ishot']:0,
            'isfirst'      => $info['isfirst']?$info['isfirst']:0,
            'isjingping'   => $info['isjingping']?$info['isjingping']:0,
            'isdiscount'   => $info['isdiscount']?$info['isdiscount']:0,
            'isrecommand'  => $info['isrecommand']?$info['isrecommand']:0,
            'istime'       => $info['istime']?$info['istime']:0,
            'timestart'    => $info['timestart']?$info['timestart']:0,
            'timeend'      => $info['timeend']?$info['timeend']:0,
        );

        mysqld_insert('shop_goods',$data);
        $id = mysqld_insertid();
        if($id){
            $effect = mysqld_update('shop_dish',array('is_already_in_shop'=>$id), array('id' => $info['id']));
            
            //将图片导入产品库
            $sql_piclist = "select picurl,contentpicurl from " . table('shop_dish_piclist') . " where goodid = {$info['id']}";
            $rs_piclist  = mysqld_select($sql_piclist);

            $data_list = array();
            $data_list['goodid']        = $id;
            $data_list['picurl']        = $rs_piclist['picurl'];
            $data_list['contentpicurl'] = $rs_piclist['contentpicurl']; 
            $list_insert = mysqld_insert('shop_goods_piclist',$data_list);
        }
        return $id;
    }
    
    //批量添加dish到shop_goods
    /*
     * 返回成功添加数
     */
    public function addsDishToShopGoods($dish_id,$shop_cat_array=array('p1'=>'','p2'=>''))
    {
        $info = mysqld_selectall("select * from " . table('shop_dish') . " where id in ({$dish_id})");
        $i = 0;
        foreach($info as $v){
            if(!$v){
                $this->error = "id匹配不到数据";
                return false;
            }
            if( $info['is_already_in_shop'] ){
                $this->error = "已经在产品库中";
                return false;
            }
            $data = array(
                'pcate'        => intval($v['p1']),
                'ccate'        => intval($v['p2']),
                'type'         => $v['type'],
                'brand'        => $v['brand'],
                'sort'         => intval($v['sort']),
                'title'        => $v['title']?$v['title']:'',
                'thumb'        => $v['thumb']?$v['thumb']:'',
                'description'  => $v['description']?$v['description']:'',
                'content'      => $v['content']?$v['content']:'',
                'marketprice'  => $v['marketprice'],
                'productprice' => $v['productprice'],
                'store_count'  => $v['store_count'],
                'totalcnf'     => $v['totalcnf'],
                'createtime'   => TIMESTAMP,
                'isnew'        => $v['isnew']?$v['isnew']:0,
                'issendfree'   => $v['issendfree']?$v['issendfree']:0,
                'ishot'        => $v['ishot']?$v['ishot']:0,
                'isfirst'      => $v['isfirst']?$v['isfirst']:0,
                'isjingping'   => $v['isjingping']?$v['isjingping']:0,
                'isdiscount'   => $v['isdiscount']?$v['isdiscount']:0,
                'isrecommand'  => $v['isrecommand']?$v['isrecommand']:0,
                'istime'       => $v['istime']?$v['istime']:0,
                'timestart'    => $v['timestart']?$v['timestart']:0,
                'timeend'      => $v['timeend']?$v['timeend']:0,
            );

            mysqld_insert('shop_goods',$data);
            $id = mysqld_insertid();
            if($id){
                $effect = mysqld_update('shop_dish',array('is_already_in_shop'=>$id), array('id' => $v['id']));
                
                //将图片导入产品库
                $sql_piclist = "select picurl,contentpicurl from " . table('shop_dish_piclist') . " where goodid = {$v['id']}";
                $rs_piclist  = mysqld_select($sql_piclist);
                
                $data_list = array();
                $data_list['goodid']        = $id;
                $data_list['picurl']        = $rs_piclist['picurl'];
                $data_list['contentpicurl'] = $rs_piclist['contentpicurl']; 
                $list_insert = mysqld_insert('shop_goods_piclist',$data_list);

                $i = $i+1;
            }
        
        }
        return $i;
    }
    
    public function actGoodsAttr($id,$attritem)
    {
        if(empty($id))  return '';
        if(empty($attritem)){
            //清掉属性 属性可以请掉
            mysqld_delete("goods_attr",array('goods_id'=>$id));
            return '';
        }
        foreach($attritem as $attr_key => $item){
            //去除空的值，没有输入的属性项
            foreach($item as $key => $val){
                if(empty($val)){
                    unset($item[$key]);
                }
            }

            $attr_key_arr = explode('@',$attr_key);
            if(count($attr_key_arr) == 2){
                //则是修改
                $goods_attr_id = $attr_key_arr[0];
                $attr_id       = $attr_key_arr[1];
                if(empty($item)){
                    //删除 掉
                    mysqld_delete("goods_attr",array('goods_attr_id'=>$goods_attr_id));
                }else{
                    //更改
                    $attr_value   = implode(' / ',$item);
                    $up_data      = array('attr_value'  => $attr_value,);
                    mysqld_update('goods_attr',$up_data,array('goods_attr_id'=>$goods_attr_id));
                }
            }else{
                //则是新添加
                $attr_id      = $attr_key;
                if(!empty($item)){
                    $attr_value   = implode(' / ',$item);
                    $up_data          = array(
                        'goods_id'    => $id,
                        'attr_id'     => $attr_id,
                        'attr_value'  => $attr_value,
                    );
                    mysqld_insert('goods_attr',$up_data);
                }
            } //else  end
        }// foreach end
    }

    public function actGoodsSpec($id,$specitem)
    {
        if(empty($id)){
            return '';
        }
        mysqld_delete("goods_spec_price",array('goods_id'=>$id));

        if(empty($specitem)) return '';

        foreach($specitem as $spec_key => $item){
            $spec_key_str  = $spec_key;
            $insert_data  = array(
                'goods_id'    => $id,
                'spec_key'    => $spec_key_str,
                'key_name'    => $item['key_name'],
                'marketprice' => FormatMoney($item['marketprice']),
                'store_count' => intval($item['store_count']),
                'bar_code'    => $item['bar_code'],
                'sku'         => $item['sku'],
            );
            mysqld_insert('goods_spec_price',$insert_data);
        }//end foreach
    }
}