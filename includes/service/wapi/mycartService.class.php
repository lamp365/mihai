<?php
namespace service\wapi;

class mycartService extends  \service\publicService
{
    public function cartlist()
    {
        $member = get_member_account();
        $openid = $member['openid'];
        $list   = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE   session_id = '" . $openid . "'");
        $totalprice = 0;
        $gooslist   = array();
        if (! empty($list)) {
            //找出对应的商品 信息
            foreach($list as $item){
                $sql = "select ac_dish_price from ".table('activity_dish')." where ac_shop_dish={$item['goodsid']}";
                $act_dish = mysqld_select($sql);
                if(empty($act_dish) || $act_dish['ac_dish_status'] == 0 ||  $act_dish['ac_dish_total'] == 0){
                    //找不到 或者已经下架 没有库存 删除掉
                    mysqld_delete('shop_cart',array('id'=>$item['id']));
                    continue;
                }
                $totalprice += $act_dish['ac_dish_price'];
                $store = member_store_getById($item['sts_id'],'sts_name');
                $field = '*';
                $dish  = mysqld_select("select {$field} from ".table('shop_dish')." where id={$item['goodsid']}");

                if(!array_key_exists($item['sts_id'],$gooslist)){
                    $gooslist[$item['sts_id']]   = $store;
                }
                $gooslist[$item['sts_id']]['dishlist'][] = $dish;
           }
            $gooslist = array_values($gooslist);
        }

        $totalprice = FormatMoney($totalprice,0);
        return array(
            'goodslist'   => $gooslist,
            'totalprice'  => $totalprice
        );
    }

    public function addCart($dishid,$total)
    {
        $member = get_member_account();
        $dish   = mysqld_select("select id,sts_id,deleted,status from ".table('shop_dish')." where id={$dishid}");
        if(empty($dishid) || $dish['deleted'] == 1 || $dish['status'] == 0){
            $this->error = '该商品不存在！';
            return false;
        }

        $row = mysqld_select("SELECT id, total FROM " . table('shop_cart') . " WHERE session_id = :session_id  AND goodsid = :goodsid ", array(
            ':session_id' =>  $member['openid'],
            ':goodsid'    =>  $dishid
        ));

        if (empty($row)) {
            // 不存在
            $data = array(
                'goodsid'       => $dishid,
                'goodstype'     => 0,
                'session_id'    => $member['openid'],
                'sts_id'        => $dish['sts_id'],
                'total'         =>  $total
            );
            mysqld_insert('shop_cart', $data);
        } else {
            // 累加最多限制购买数量
            $t_num = $total + $row['total'];
            // 存在
            $data = array(
                'total'       => $t_num
            );
            mysqld_update('shop_cart', $data, array(
                'id' => $row['id']
            ));
        }
        return $data['total'];
    }

    /**
     * 选择了哪些商品进行购买
     * @param $cart_ids
     * @return bool
     */
    public function topay($cart_ids)
    {
        $member  = get_member_account();
        if (empty($cart_ids)) {
            $this->error = '对不起你没有选择商品！';
            return false;
        }

        //先全部置0
        mysqld_update('shop_cart',array('to_pay'=>0),array('openid'=>$member['openid']));
        foreach($cart_ids as $id){
            mysqld_update('shop_cart',array('to_pay'=>1),array('id'=>$id,'openid'=>$member['openid']));
        }
        return true;
    }
}
