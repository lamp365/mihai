<?php
namespace service\wapi;

class mycartService extends  \service\publicService
{
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
