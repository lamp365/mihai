<?php
namespace service\wapi;

class mycartService extends  \service\publicService
{
    public function cartlist($cart_where = '',$get_express = 0)
    {
        $member = get_member_account();
        $openid = $member['openid'];
        $where  = " session_id ='{$openid}'";
        if(!empty($cart_where)){
            $where .= " and {$cart_where}";
        }
        $list   = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE  {$where}");
        $totalprice = 0;
        $gooslist   = array();
        if (! empty($list)) {
            //找出对应的商品 信息
            foreach($list as $item){
                $sql = "select ac_dish_price,ac_dish_status,ac_dish_total from ".table('activity_dish')." where ac_shop_dish={$item['goodsid']}";
                $act_dish = mysqld_select($sql);
                if(empty($act_dish)){
                    //找不到
                    mysqld_delete('shop_cart',array('id'=>$item['id']));
                    continue;
                }
                $totalprice += $act_dish['ac_dish_price'];
                $store = member_store_getById($item['sts_id'],'sts_name');
                $field = 'title,thumb,sts_id';
                $dish  = mysqld_select("select {$field} from ".table('shop_dish')." where id={$item['goodsid']}");
                $dish['time_price']        = FormatMoney($act_dish['ac_dish_price'],0);
                $dish['buy_num']           = $item['total'];
                $dish['ac_dish_status']    = $act_dish['ac_dish_status'];
                $dish['ac_dish_total']     = $act_dish['ac_dish_total'];

                if(!array_key_exists($item['sts_id'],$gooslist)){
                    $gooslist[$item['sts_id']]   = $store;
                    //获取店铺的运费，免邮等信息
                    if($get_express){
                        $expressInfo = mysqld_select("select free_dispatch,express_fee from ".table('store_extend_info')." where store_id={$dish['sts_id']}");
                        $gooslist[$item['sts_id']]['free_dispatch'] = FormatMoney($expressInfo['free_dispatch'],0);
                        $gooslist[$item['sts_id']]['express_fee']   = FormatMoney($expressInfo['express_fee'],0);
                        $totalprice += $expressInfo['express_fee'];
                    }
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

        $sql = "select ac_shop_dish,ac_dish_status from ".table('activity_dish');
        $sql .= " where ac_shop_dish={$dishid}";
        $find = mysqld_select($sql);
        if (empty($find)) {
            $this->error = '抱歉，该商品已不存在！';
            return false;
        }else if($find['ac_dish_status'] == 0){
            $this->error = '请等待上架！';
            return false;
        }

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
            if($total > $find['ac_dish_total']){
                $this->error = "库存剩下{$find['ac_dish_total']}个！";
                return false;
            }
            mysqld_insert('shop_cart', $data);
        } else {
            // 累加最多限制购买数量
            $t_num = $total + $row['total'];
            if($t_num > $find['ac_dish_total']){
                $this->error = "库存剩下{$find['ac_dish_total']}个！";
                return false;
            }
            $data = array('total' => $t_num);
            mysqld_update('shop_cart', $data, array('id' => $row['id']));
        }
        return $data['total'];
    }

    public function updateCart($cart_id,$num)
    {
        $member = get_member_account();

        $cart   = mysqld_select("select * from ".table('shop_cart')." where id={$cart_id} and openid='{$member['openid']}'");
        if(empty($cart)){
            $this->error = '抱歉，该商品已不存在！';
            return false;
        }

        $sql = "select ac_shop_dish,ac_dish_status from ".table('activity_dish');
        $sql .= " where ac_shop_dish={$cart['goodsid']}";
        $find = mysqld_select($sql);
        if (empty($find)) {
            $this->error = '抱歉，该商品已不存在！';
            return false;
        }else if($find['ac_dish_status'] == 0){
            $this->error = '请等待上架！';
            return false;
        }

        // 累加最多限制购买数量
        $t_num = $num + $find['total'];
        if($t_num > $find['ac_dish_total']){
            $this->error = "库存剩下{$find['ac_dish_total']}个！";
            return false;
        }
        $data = array('total' => $t_num);
        mysqld_update('shop_cart', $data, array('id' => $cart_id));

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
