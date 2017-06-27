<?php
namespace service\wapi;

class mycartService extends  \service\publicService
{
    /**
     * 购物车列表  不需要获取邮费等信息
     * 但是清单列表页需要
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
        //找出本次活动的场次
        $active = getCurrentAct();

        //找出购物车的商品
        $list   = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE  {$where}");
        $totalprice = 0;
        $gooslist   = $out_gooslist = array();
        if (! empty($list)) {
            //找出对应的商品 信息
            foreach($list as $item){
                $sql = "select ac_dish_price,ac_dish_status,ac_dish_total from ".table('activity_dish')." where ac_shop_dish={$item['goodsid']} and ac_action_id={$active['ac_id']}";
                $act_dish = mysqld_select($sql);

                $field = 'id,title,marketprice,thumb,sts_id';
                $dish  = mysqld_select("select {$field} from ".table('shop_dish')." where id={$item['goodsid']}");

                if(empty($dish) || empty($act_dish) || $act_dish['ac_dish_status'] == 0 || $act_dish['ac_dish_total'] == 0){
                    //找不到 或者没有库存  已经下架的商品  表示该购物车已经过期了
                    $dish['ac_dish_status']  = $act_dish['ac_dish_status'] ?: 0;
                    $dish['ac_dish_total']   = $act_dish['ac_dish_total'] ?: 0;
                    $dish['cart_id']         =  $item['id'];
                    $out_gooslist[]          = $dish;
                    continue;
                }


                $store = member_store_getById($item['sts_id'],'sts_name,sts_id,sts_shop_type');

                $dish['time_price']        = FormatMoney($act_dish['ac_dish_price'],0);
                $dish['marketprice']       = FormatMoney($dish['marketprice'],0);
                $dish['buy_num']           = $item['total'];
                $dish['ac_dish_status']    = $act_dish['ac_dish_status'];
                $dish['ac_dish_total']     = $act_dish['ac_dish_total'];
                $dish['cart_id']           = $item['id'];
                $dish['to_pay']            = $item['to_pay'];


                if(!array_key_exists($item['sts_id'],$gooslist)){
                    $gooslist[$item['sts_id']]   = $store;
                    //获取店铺的运费，免邮等信息
                    if($get_express){
                        $this->get_express_fee($item['sts_id'],$gooslist);
                    }
                }
                $gooslist[$item['sts_id']]['dishlist'][] = $dish;

                if($item['to_pay'] == 1)   //计算已经打钩的物品
                    $totalprice = $totalprice + $dish['time_price']*$dish['buy_num'];
           }
            $gooslist = array_values($gooslist);
        }


        //计算总的价格 加入店铺邮费 以及判断是否给予免邮费  以及判断是否可以使用的优惠卷
        if($get_express){
            $totalprice = $this->countStorePriceAndBonus($totalprice,$gooslist);
        }

        return array(
            'goodslist'     => $gooslist,
            'out_gooslist'  => $out_gooslist,
            'totalprice'    => number_format($totalprice,2)
        );
    }

    /**
     * 获取店铺邮费 和免邮的金额
     * @param $sts_id
     * @param $gooslist
     */
    public function get_express_fee($sts_id,&$gooslist)
    {
        $expressInfo   = mysqld_select("select free_dispatch,express_fee from ".table('store_extend_info')." where store_id={$sts_id}");
        $free_dispatch = $expressInfo['free_dispatch'];  //免邮
        $express_fee   = $expressInfo['express_fee'];    //运费
        $gooslist[$sts_id]['free_dispatch'] = FormatMoney($free_dispatch,0);
        $gooslist[$sts_id]['express_fee']   = FormatMoney($express_fee,0);
        $gooslist[$sts_id]['send_free']     = 0;
        $gooslist[$sts_id]['totalprice']    = 0;

    }

    public function countStorePriceAndBonus($totalprice,&$gooslist)
    {
        if(empty($gooslist))  return $totalprice;
        foreach($gooslist as &$item){
            $free_dispatch = $item['free_dispatch'];  //满多少免邮  单位元
            $express_fee   = $item['express_fee'];    //运费       单位元
            $dish_arr      = $item['dishlist'];
            $total_dish_price = 0;
            $dishid_arr       = array();
            foreach($dish_arr as $one){
                $total_dish_price += $one['time_price']*$one['buy_num'];
                $dishid_arr[]     =  $one['id'];
            }
            if($total_dish_price >= $free_dispatch){
                //商品价格  没有超过 满邮的条件   总价加上运费
                $totalprice +=  $express_fee;
                $item['totalprice'] = number_format($total_dish_price + $express_fee,2);
                $item['send_free']  = 1;
            }else{
                $item['totalprice'] = number_format($total_dish_price,2);
            }
            //根据店铺以及价格来选出 结算的时候 可以使用的优惠卷
            $item['bonuslist'] = getCouponByPriceOnPay($item['sts_id'],$total_dish_price,$dishid_arr);

        }
        return $totalprice;
    }


    public function addCart($dishid,$total)
    {
        $member = get_member_account();
        $dish   = mysqld_select("select id,sts_id,deleted,status,store_count from ".table('shop_dish')." where id={$dishid}");
        if(empty($dishid) || $dish['deleted'] == 1 || $dish['status'] == 0){
            $this->error = '该商品不存在！';
            return false;
        }
        //库存
        $store_count = $dish['store_count'];
        //判断商品是否属于活动中的商品
        $active = checkDishIsActive($dishid,$store_count);
        if(!empty($active)){
            $store_count = $active['ac_dish_total'];
            if($active['ac_dish_status'] == 0){
                $this->error = '请等待上架！';
                return false;
            }
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
                'to_pay'        => 1,  //默认是打钩状态的
                'total'         =>  $total
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
        //返回总的购物车物种
        $carnum = getCartTotal();
        return $carnum;
    }

    public function lijiBuyCart($dishid,$total)
    {
        $member = get_member_account();

        $dish   = mysqld_select("select id,sts_id,deleted,status from ".table('shop_dish')." where id={$dishid}");
        if(empty($dishid) || $dish['deleted'] == 1 || $dish['status'] == 0){
            $this->error = '该商品不存在！';
            return false;
        }
        //库存
        $store_count = $dish['store_count'];
        //判断商品是否属于活动中的商品
        $active = checkDishIsActive($dishid,$store_count);
        if(!empty($active)){
            $store_count = $active['ac_dish_total'];
            if($active['ac_dish_status'] == 0){
                $this->error = '请等待上架！';
                return false;
            }
        }

       //移除掉改用去其他商品的打钩状态
        mysqld_update("shop_cart",array('to_pay'=>0),array('session_id'=>$member['openid']));

        // 不存在
        $data = array(
            'goodsid'       => $dishid,
            'goodstype'     => 0,
            'session_id'    => $member['openid'],
            'sts_id'        => $dish['sts_id'],
            'to_pay'        => 1,  //当前立即购买的设置打钩状态的
            'total'         =>  $total
        );
        if($total > $store_count){
            $this->error = "库存剩下{$store_count}个！";
            return false;
        }
        mysqld_insert('shop_cart', $data);
        return $data['total'];
    }

    public function updateCart($cart_id,$num)
    {
        $member = get_member_account();

        $cart   = mysqld_select("select * from ".table('shop_cart')." where id={$cart_id} and session_id='{$member['openid']}'");
        if(empty($cart)){
            $this->error = '抱歉，该商品已不存在！';
            return false;
        }

        $sql = "select ac_shop_dish,ac_dish_status,ac_dish_total from ".table('activity_dish');
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

    /**
     * 从购物车中的店铺 得到对应的行业，如果有一个行业属于全球购的，那么本次结算页需要地址中带有身份证
     * @param $sts_id_arr
     * @return int
     */
    public function checkCarStoreIsNeedIdenty($sts_id_arr)
    {
        if(empty($sts_id_arr))  return 0;

        $need_identy = 0;
        foreach($sts_id_arr as $sts_id){
            $store = member_store_getById($sts_id,'sts_category_p1_id');
            if($store['sts_category_p1_id'] == 1){
                //行业1表示全球购，，全球购的商品，需要身份证
                $need_identy =1;
            }
        }
        return $need_identy;
    }
}
