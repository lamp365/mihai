<?php
namespace service\shopwap;

class mycartService extends  \service\publicService
{
    /**
     * 购物车列表  不需要获取邮费等信息
     * 但是清单列表页需要
     * 修改的时候注意，购物车与清单结算 都会调用这个方法
     * 会统计每个店铺总额 包括最后的总额 每个店铺下的产品
     * @param string $cart_where
     * @param int $get_express
     * @return array
     */
    public function cartlist($cart_where = '',$get_express = 0)
    {
        $member = get_member_account();
        $openid = $member['openid'];
        $where  = " session_id ='{$openid}'";
        if(!empty($cart_where)){
            $where .= " and {$cart_where}";
        }

        //找出购物车的商品
        $list   = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE  {$where}");
        $totalprice = 0;
        $gooslist   = $out_gooslist = array();
        if (! empty($list)) {
            //找出对应的商品 信息
            foreach($list as $item){
                $dish  = mysqld_select("select * from ".table('shop_dish')." where id={$item['goodsid']}");
                $dish['marketprice']   = get_limit_price($dish,$dish['marketprice']);
                $dish['spec_key']      = '';
                $dish['spec_key_name'] = '';
                $dish['cart_id']       = $item['id'];
                $dish['buy_num']       = $item['total'];
                $dish['to_pay']        = $item['to_pay'];

                //从规格项中找到对应的库存
                if($item['spec_key']){
                    $spec_info = mysqld_select("select * from ".table('dish_spec_price')." where dish_id={$item['goodsid']} and spec_key='{$item['spec_key']}'");
                    if(!empty($spec_info)){
                        $dish['total']         = $spec_info['total'];
                        $dish['marketprice']   = get_limit_price($dish,$spec_info['marketprice']);
                        $dish['spec_key']      = $spec_info['spec_key'];
                        $dish['spec_key_name'] = $spec_info['key_name'];
                    }
                }

                if(empty($dish) || $dish['total'] ==0 || $dish['status'] == 0){
                    //找不到 或者没有库存  已经下架的商品  表示该购物车已经过期了
                    $out_gooslist[]          = $dish;
                    continue;
                }

                //将产品并入到数组中去
                $gooslist[] = $dish;

                if($item['to_pay'] == 1)   //计算已经打钩的物品
                    $totalprice = $totalprice + $dish['marketprice']*$dish['buy_num'];
           }
            $gooslist = array_values($gooslist);
        }

        //获取运费，以及免邮等信息
        $express_fee = $free_dispatch = 0;
        if($get_express){
            $get_express_arr = $this->get_express_fee($gooslist);
            $express_fee     = $get_express_arr['express_fee'];    //运费
            $free_dispatch   = $get_express_arr['free_dispatch'];  //免邮条件
        }
        //总价扣掉运费
        $totalprice -= $express_fee;

        //根据中总的价格判断是否可以使用的优惠卷
        $bonuslist = array();
        if($get_express){
            $bonuslist = $this->countCanUseBonusByPrice($totalprice,$gooslist);
        }

        $redata = array(
            'goodslist'      => $gooslist,
            'out_gooslist'   => $out_gooslist,
            'totalprice'     => round($totalprice,2),
            'express_fee'    => round($express_fee,2),
            'free_dispatch'  => round($free_dispatch,2),
            'bonuslist'     => $bonuslist,
        );
        return $redata;
    }

    /**
     * 获取店铺最大的邮费 和免邮的金额
     * @param $sts_id
     * @param $gooslist
     */
    public function get_express_fee($gooslist)
    {
        $express_fee  = 0;  //运费
        $dish_price   = 0;
        foreach($gooslist as $one_list){
            $dish_price += $one_list['marketprice'];
            if($one_list['issendfree']){
                continue;
            }
            if(empty($one_list['transport_id'])){
                continue;
            }
            $yunfei = mysqld_select("select displayorder from ".table('dish_list')." where id={$one_list['transport_id']}");
            if(empty($yunfei)){
                continue;
            }
            $express_fee = max($express_fee,$yunfei['displayorder']);
        }

        //找到促销的免邮费用
        $time = time();
        $pormot = mysqld_select("select condition from ".table('shop_pormotions')." where promoteType=1 and endtime>{$time} and starttime<{$time}");
        $free_dispatch = floatval($pormot['condition']);  //免邮条件
        if($dish_price >= $free_dispatch){
            $express_fee = 0;
        }
        return array(
            'express_fee'    => $express_fee,
            'free_dispatch'  => $free_dispatch,
        );
    }

    public function countCanUseBonusByPrice($totalprice,$goodslist)
    {
        if(empty($gooslist))  return array();
        $meminfo    = get_member_account();
        $openid     = $meminfo['openid'];

        $bonus_sql = "select u.*,b.type_money,b.min_goods_amount,b.send_type,b.use_start_date.b.use_end_date from ".table('bonus_user')." as u left join ".table('bonus_type')." as b";
        $bonus_sql.= " on u.bonus_type_id=b.type_id where u.openid='{$openid}' and u.isuse=0  and b.min_goods_amount <= '{$totalprice}'";
        $bonus  = mysqld_selectall($bonus_sql);

        //去除时间还没开始的 或者已经过期的
        foreach($bonus as $key => &$item){
            //金额转为元
            if(time() < $item['use_start_date'] || time() > $item['use_end_date']){
                unset($bonus[$key]);
                continue;
            }
            //如果优惠卷针对的是单品，则判断是否在购买的列表中
            if($item['send_type'] == 1 ){
                $bonus_good_ids = array();
                $bonus_good     = mysqld_selectall("select good_id from ".table('bonus_good')." where bonus_type_id={$item['bonus_type_id']}");
                foreach($bonus_good as $one_bon){
                    $bonus_good_ids[] = $one_bon['good_id'];
                }

                $can_use_bonus = false;
                foreach($goodslist as $one_goodslist){
                    $dish_id = $one_goodslist['id'];
                    if(in_array($dish_id,$bonus_good_ids)){
                        $can_use_bonus = true;
                    }
                }
                if($can_use_bonus == false){
                    unset($bonus[$key]);
                    continue;
                }
            }
        }

        if(empty($bonus)){
            return array();
        }else{
            return $bonus;
        }
    }


    public function addCart($dishid,$spec_key,$total)
    {
        $member = get_member_account();
        $dish   = mysqld_select("select id,deleted,status,total from ".table('shop_dish')." where id={$dishid}");
        if(empty($dishid) || $dish['deleted'] == 1){
            $this->error = '该商品不存在！';
            return false;
        }
        if( $dish['status'] == 0){
            $this->error = '请等待商品上架！';
            return false;
        }
        //库存
        $store_count = $dish['total'];
        $spec_key    = $spec_key ?: 0;

        if($spec_key){
            //从规格项中找到对应的库存
            $spec_info = mysqld_select("select total from ".table('dish_spec_price')." where dish_id={$dishid} and spec_key='{$spec_key}'");
            if(!empty($spec_info)){
                $store_count = $spec_info['total'];
            }
        }
        $row = mysqld_select("SELECT id,total FROM " . table('shop_cart') . " WHERE session_id = :session_id  AND goodsid = :goodsid and spec_key=:spec_key", array(
            ':session_id'  =>  $member['openid'],
            ':goodsid'     =>  $dishid,
            ':spec_key'    =>  $spec_key
        ));

        if (empty($row)) {
            // 不存在
            $data = array(
                'goodsid'       => $dishid,
                'goodstype'     => 0,
                'session_id'    => $member['openid'],
                'to_pay'        => 1,  //默认是打钩状态的
                'total'         =>  $total,
                'spec_key'      =>  $spec_key
            );
            if($total > $store_count){
                $this->error = "库存剩下{$store_count}个！";
                return false;
            }
            mysqld_insert('shop_cart', $data);
        } else {
            // 累加最多限制购买数量
            $t_num = $total + $row['total'];
            if($t_num > $store_count){
                $this->error = "库存剩下{$store_count}个！";
                return false;
            }
            $data = array('total' => $t_num);
            mysqld_update('shop_cart', $data, array('id' => $row['id']));
        }
        //返回总的购物车物物品总数量
        $carnum = getCartTotal(2);
        return $carnum;
    }

    public function lijiBuyCart($dishid,$spec_key,$total)
    {
        $member = get_member_account();

        $dish   = mysqld_select("select id,deleted,status,total from ".table('shop_dish')." where id={$dishid}");
        if(empty($dishid) || $dish['deleted'] == 1){
            $this->error = '该商品不存在！';
            return false;
        }
        if($dish['status'] == 0){
            $this->error = '请等待商品上架！';
            return false;
        }
        //库存
        $store_count = $dish['total'];
        if($spec_key){
            //从规格项中找到对应的库存
            $spec_info = mysqld_select("select total from ".table('dish_spec_price')." where dish_id={$dishid} and spec_key='{$spec_key}'");
            if(!empty($spec_info)){
                $store_count = $spec_info['total'];
            }
        }
        if($total > $store_count){
            $this->error = "库存剩下{$store_count}个！";
            return false;
        }

        //移除掉所有商品的打钩状态
        mysqld_update("shop_cart",array('to_pay'=>0),array('session_id'=>$member['openid']));

        $row = mysqld_select("SELECT id, total FROM " . table('shop_cart') . " WHERE session_id = :session_id  AND goodsid = :goodsid and spec_key=:spec_key ", array(
            ':session_id'  =>  $member['openid'],
            ':goodsid'     =>  $dishid,
            ':spec_key'    =>  $spec_key
        ));

        if(empty($row)){
            // 不存在
            $data = array(
                'goodsid'       => $dishid,
                'goodstype'     => 0,
                'session_id'    => $member['openid'],
                'to_pay'        => 1,  //当前立即购买的设置打钩状态的
                'total'         =>  $total,
                'spec_key'      =>  $spec_key
            );
            mysqld_insert('shop_cart', $data);
        }else{
            $u_data = array('total' => $total,'to_pay'=>1);
            mysqld_update('shop_cart', $u_data, array('id' => $row['id']));
        }
        return true;
    }

    public function updateCart($cart_id,$buy_num)
    {
        $member = get_member_account();

        $cart   = mysqld_select("select * from ".table('shop_cart')." where id={$cart_id} and session_id='{$member['openid']}'");
        if(empty($cart)){
            $this->error = '抱歉，该商品已不存在！';
            return false;
        }

        $dish   = mysqld_select("select id,deleted,status,total from ".table('shop_dish')." where id={$cart['goodsid']}");
        //库存
        $store_count = $dish['total'];
        if($cart['spec_key']){
            //从规格项中找到对应的库存
            $spec_info = mysqld_select("select total from ".table('dish_spec_price')." where dish_id={$cart['goodsid']} and spec_key='{$cart['spec_key']}'");
            if(!empty($spec_info)){
                $store_count = $spec_info['total'];
            }
        }

        if($buy_num > $store_count){
            $this->error = "库存剩下{$store_count}个！";
            return false;
        }
        $data = array('total' => $buy_num,'to_pay'=>1);
        mysqld_update('shop_cart', $data, array('id' => $cart_id));

        return $data['total'];
    }
    /**
     * 选择了哪些商品进行购买 或者不买
     * @param $cart_ids
     * @return bool
     */
    public function topay($cart_ids,$type)
    {
        $member  = get_member_account();
        if (empty($cart_ids)) {
            $this->error = '对不起你没有选择商品！';
            return false;
        }
        if(!in_array($type,array('0','1'))){
            $this->error = '类型参数不对！';
            return false;
        }
        $cart_ids = explode(',',$cart_ids);
        foreach($cart_ids as $id){
            mysqld_update('shop_cart',array('to_pay'=>$type),array('id'=>$id,'session_id'=>$member['openid']));
        }
        return true;
    }

}
