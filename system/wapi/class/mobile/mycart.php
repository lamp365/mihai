<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace wapi\controller;

class mycart extends base
{
   public function index()
   {
       $_GP =  $this->request;
       $service  = new \service\wapi\mycartService();
       $cartlist = $service->cartlist();
       ajaxReturnData(1,'请求成功',$cartlist);
   }

    public function addCart()
    {
        $_GP =  $this->request;
        $dishid  = intval($_GP['id']);
        if(empty($dishid)){
            ajaxReturnData(0,'请选择商品');
        }
        $total   = intval($_GP['total']);
        $total   = empty($total) ? 1 : $total;

        $sql = "select ac_shop_dish,ac_dish_status from ".table('activity_dish');
        $sql .= " where ac_shop_dish={$dishid}";
        $find = mysqld_select($sql);
        if (empty($find)) {
            ajaxReturnData(0,'抱歉，该商品不存在或是已经被删除');
        }else if($find['ac_dish_status'] == 0){
            ajaxReturnData(0,'请等待上架！');
        }

        $service  = new \service\wapi\mycartService();
        $cartotal = $service->addCart($dishid,$total);
        if(!$cartotal){
           ajaxReturnData(0,$service->getError());
        }

        ajaxReturnData(1,'操作成功！',$cartotal);
    }

    public function updateCart()
    {
        $_GP    =  $this->request;
        $member = get_member_account();
        $openid = $member['openid'];
        $id  = intval($_GP['id']);
        $num = intval($_GP['num']);
        if(empty($id) || empty($num) || $num<0){
            ajaxReturnData(0,'参数有误！');
        }
        $find = mysqld_select("select * from ".table('shop_cart')." where id={$id} and openid='{$openid}'");
        if(empty($find)){
            ajaxReturnData(0,'该商品不存在');
        }
        mysqld_query("update " . table('shop_cart') . " set total={$num} where id=:id", array(
            ":id"     => $id
        ));
        ajaxReturnData(1,'操作成功！');
    }

    public function del()
    {
        $member = get_member_account();
        $openid = $member['openid'];
        $_GP = $this->request;
        $id  = intval($_GP['id']);
        mysqld_delete('shop_cart', array(
            'session_id' => $openid,
            'id' => $id
        ));
        ajaxReturnData(1,'已经移除！');
    }

    public function batdel()
    {
        $member = get_member_account();
        $openid = $member['openid'];
        $_GP = $this->request;
        if(empty($_GP['ids'])){
            ajaxReturnData(0,'参数有误！');
        }else{
            foreach($_GP['ids'] as $id){
                mysqld_delete('shop_cart', array(
                    'session_id' => $openid,
                    'id' => $id
                ));
            }
            ajaxReturnData(1,'删除成功！');
        }
    }

    public function clean()
    {
        $member = get_member_account();
        $openid = $member['openid'];
        mysqld_delete('shop_cart', array(
            'session_id' => $openid
        ));
        ajaxReturnData(1,'已全部移除！');
    }
    //所选择了哪些商品进行购买
    public function topay()
    {
        $_GP    = $this->request;
        $service  = new \service\wapi\mycartService();
        $res = $service->topay($_GP['ids']);
        if(!$res){
            ajaxReturnData(0,$service->getError());
        }else{
            ajaxReturnData(1,'操作成功！');
        }
    }
}