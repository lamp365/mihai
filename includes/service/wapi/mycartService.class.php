<?php
namespace service\wapi;

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
                $field = 'id,title,marketprice,history_lower_prcie,thumb,sts_id,store_count,status,isreason';
                $dish  = mysqld_select("select {$field} from ".table('shop_dish')." where id={$item['goodsid']}");
                $store_count = $dish['store_count'];
                $time_price  = $dish['marketprice'];
                $status      = $dish['status'];

                if(empty($dish) || $store_count ==0 || $status == 0){
                    $dish['cart_id']         =  $item['id'];
                    $out_gooslist[]          = $dish;
                    continue;
                }

                //判断商品是否属于活动中的商品
                $active = checkDishIsActive($dish['id'],$dish['store_count']);
                if(!empty($active)){
                    $store_count = $active['ac_dish_total'] ?: 0;
                    $time_price  = $active['ac_dish_price'] ?: $dish['marketprice'];
                    $status      = $active['ac_dish_status'] ?: 0;
                }

                $dish['store_count'] = $store_count;
                $dish['status']      = $status;

                if( $store_count ==0 || $status == 0){
                    //找不到 或者没有库存  已经下架的商品  表示该购物车已经过期了
                    $dish['cart_id']         =  $item['id'];
                    $out_gooslist[]          = $dish;
                    continue;
                }


                $store = member_store_getById($item['sts_id'],'sts_name,sts_id,sts_shop_type');

                $dish['time_price']        = FormatMoney($time_price,0);
                $dish['marketprice']       = FormatMoney($dish['marketprice'],0);
                $dish['history_lower_prcie']= FormatMoney($dish['history_lower_prcie'],0);
                $dish['buy_num']           = $item['total'];
                $dish['cart_id']           = $item['id'];
                $dish['to_pay']            = $item['to_pay'];
                $dish['ac_dish_id']        = intval($active['ac_dish_id']);


                if(!array_key_exists($item['sts_id'],$gooslist)){
                    $gooslist[$item['sts_id']]   = $store;
                    //获取店铺的运费，免邮等信息
                    if($get_express){
                        $this->get_express_fee($item['sts_id'],$gooslist);
                    }
                }
                //将产品并入到数组中去
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

        $redata = array(
            'goodslist'     => $gooslist,
            'out_gooslist'  => $out_gooslist,
            'totalprice'    => round($totalprice,2)
        );
        return $redata;
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
        $gooslist[$sts_id]['free_dispatch'] = FormatMoney($free_dispatch,0); //单位元
        $gooslist[$sts_id]['express_fee']   = FormatMoney($express_fee,0);  //单位元
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
            if($free_dispatch !=0 && $total_dish_price >= $free_dispatch){
                //商品价格  超过 满邮的条件   免邮
                $item['totalprice'] = round($total_dish_price,2);
                $item['send_free']  = 1;
            }else{
                //没有满邮，加上运费
                $totalprice +=  $express_fee;
                $item['totalprice'] = round($total_dish_price + $express_fee,2);
                $item['send_free']  = 0;
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
        if(empty($dishid) || $dish['deleted'] == 1){
            $this->error = '该商品不存在！';
            return false;
        }
        if( $dish['status'] == 0){
            $this->error = '请等待商品上架！';
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


        $row = mysqld_select("SELECT id,total FROM " . table('shop_cart') . " WHERE session_id = :session_id  AND goodsid = :goodsid ", array(
            ':session_id' =>  $member['openid'],
            ':goodsid'    =>  $dishid
        ));

        $buy_num = $total;
        $total   = $total + intval($row['total']);
        if($total > $store_count){
            $this->error = "该产品库存剩下{$store_count}件！";
            return false;
        }
        //每个用户最多能购买件数有限 库存少于30件，每人可以买一件。。否则每人可以买（库存*10%）
        if($store_count <= 30){
            $can_max_buy = 1;
        }else{
            $can_max_buy = floor($store_count*0.1);
        }
        if($total > $can_max_buy){
            $this->error = "该产品一次允许最大购买{$can_max_buy}件！";
            return false;
        }

        if (empty($row)) {
            // 不存在
            $data = array(
                'goodsid'       => $dishid,
                'ac_dish_id'    => intval($active['ac_dish_id']),
                'goodstype'     => 0,
                'session_id'    => $member['openid'],
                'sts_id'        => $dish['sts_id'],
                'to_pay'        => 1,  //默认是打钩状态的
                'total'         => $total
            );
            mysqld_insert('shop_cart', $data);
            if(mysqld_insertid()){
                //更新购物车的最新时间
                update_cart_record_time();
            }else{
                $this->error = '添加失败，请稍后操作！';
                return false;
            }
        } else {
            $data = array('total' => $total,'ac_dish_id' => intval($active['ac_dish_id']));
            mysqld_update('shop_cart', $data, array('id' => $row['id']));
        }

        //库存的操作减掉 卖出数量加1
        $ac_dish_id = intval($active['ac_dish_id']);
        operateStoreCount($dishid,$buy_num,$ac_dish_id,1);

        //返回总的购物车物物品总数量
        $carnum = getCartTotal(2);
        return $carnum;
    }

    public function lijiBuyCart($dishid,$total)
    {
        $member = get_member_account();

        $dish   = mysqld_select("select id,sts_id,deleted,status,store_count from ".table('shop_dish')." where id={$dishid}");
        if(empty($dishid) || $dish['deleted'] == 1){
            $this->error = '该商品不存在！';
            return false;
        }
        if($dish['status'] == 0){
            $this->error = '请等待商品上架！';
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

        if($total > $store_count){
            $this->error = "库存剩下{$store_count}个！";
            return false;
        }

        //每个用户最多能购买件数有限 库存少于30件，每人可以买一件。。否则每人可以买（库存*10%）
        if($store_count <= 30){
            $can_max_buy = 1;
        }else{
            $can_max_buy = floor($store_count*0.1);
        }
        if($total > $can_max_buy){
            $this->error = "该产品一次允许最大购买{$can_max_buy}件！";
            return false;
        }

        //移除掉所有商品的打钩状态
        mysqld_update("shop_cart",array('to_pay'=>0),array('session_id'=>$member['openid']));

        $row = mysqld_select("SELECT id, total FROM " . table('shop_cart') . " WHERE session_id = :session_id  AND goodsid = :goodsid ", array(
            ':session_id' =>  $member['openid'],
            ':goodsid'    =>  $dishid
        ));

        if(empty($row)){
            // 不存在
            $data = array(
                'goodsid'       => $dishid,
                'ac_dish_id'    => intval($active['ac_dish_id']),
                'goodstype'     => 0,
                'session_id'    => $member['openid'],
                'sts_id'        => $dish['sts_id'],
                'to_pay'        => 1,  //当前立即购买的设置打钩状态的
                'total'         =>  $total
            );
            mysqld_insert('shop_cart', $data);
        }else{
            $u_data = array('total' => $total,'to_pay'=>1,'ac_dish_id'=> intval($active['ac_dish_id']));
            mysqld_update('shop_cart', $u_data, array('id' => $row['id']));
        }
        return $total;
    }

    /**
     * @param $cart_id
     * @param $num  为更新所要购买的数量
     * @return bool
     */
    public function updateCart($cart_id,$buy_num)
    {
        $member = get_member_account();

        $cart   = mysqld_select("select * from ".table('shop_cart')." where id={$cart_id} and session_id='{$member['openid']}'");
        if(empty($cart)){
            $this->error = '抱歉，该商品已不存在！';
            return false;
        }

        $dish   = mysqld_select("select id,sts_id,deleted,status,store_count from ".table('shop_dish')." where id={$cart['goodsid']}");
        if(empty($dish) || $dish['deleted'] == 1){
            $this->error = '该商品不存在！';
            return false;
        }
        //库存
        $store_count = $dish['store_count'];
        //判断商品是否属于活动中的商品
        $active = checkDishIsActive($cart['goodsid'],$store_count);
        if(!empty($active)){
            $store_count = $active['ac_dish_total'];
            if($active['ac_dish_status'] == 0){
                $this->error = '请等待上架！';
                return false;
            }
        }

        if($buy_num > $store_count){
            $this->error = "库存剩下{$store_count}个！";
            return false;
        }
        //每个用户最多能购买件数有限 库存少于30件，每人可以买一件。。否则每人可以买（库存*10%）
        if($store_count <= 30){
            $can_max_buy = 1;
        }else{
            $can_max_buy = floor($store_count*0.1);
        }
        if($buy_num > $can_max_buy){
            $this->error = "该产品一次允许最大购买{$can_max_buy}件！";
            return false;
        }

        $data = array('total' => $buy_num,'ac_dish_id' => intval($active['ac_dish_id']));
        mysqld_update('shop_cart', $data, array('id' => $cart_id));

        //库存的操作减掉 卖出数量加1
        $ac_dish_id = intval($active['ac_dish_id']);
        $up_num     = abs($buy_num-$cart['total']);  //数量是添加还是减少
        $up_num !=0 &&  operateStoreCount($dish['id'],$up_num,$ac_dish_id,1);   //等于0的时候不执行
        return $data['total'];
    }

    /**
     * 删除单个物品
     * @param $carid
     * @return bool|string
     */
    public function delcart($carid)
    {
        $member = get_member_account();
        $openid = $member['openid'];
        $cart_info = mysqld_select("select * from ".table('shop_cart')." where id={$carid}");
        if(empty($cart_info)){
            return '';
        }
        mysqld_delete('shop_cart', array(
            'session_id' => $openid,
            'id' => $carid
        ));
        //释放库存
        operateStoreCount($cart_info['goodsid'],$cart_info['total'],$cart_info['ac_dish_id'],2);
        return true;
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
