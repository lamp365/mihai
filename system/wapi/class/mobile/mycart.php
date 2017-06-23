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
    public function __construct()
    {
        parent::__construct();
        if(!checkIsLogin()){
            ajaxReturnData(0,'请授权登录！');
        }
    }

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
        $total   = intval($_GP['buy_num']);
        $total   = empty($total) ? 1 : $total;

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

        $id  = intval($_GP['id']);
        $num = intval($_GP['buy_num']);
        if(empty($id) || empty($num) || $num<0){
            ajaxReturnData(0,'参数有误！');
        }
        $service  = new \service\wapi\mycartService();
        $cartotal = $service->updateCart($id,$num);
        if(!$cartotal){
            ajaxReturnData(0,$service->getError());
        }

        $cartlist = $service->cartlist();
        ajaxReturnData(1,'操作成功',$cartlist);
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

        $service  = new \service\wapi\mycartService();
        $cartlist = $service->cartlist();
        ajaxReturnData(1,'已经移除!',$cartlist);
    }

    /**
     * 逗号分隔多个id
     */
    public function batdel()
    {
        $member = get_member_account();
        $openid = $member['openid'];
        $_GP = $this->request;
        if(empty($_GP['ids'])){
            ajaxReturnData(0,'参数有误！');
        }else{
            $ids = explode(',',$_GP['ids']);
            foreach($ids as $id){
                mysqld_delete('shop_cart', array(
                    'session_id' => $openid,
                    'id' => $id
                ));
            }

            $service  = new \service\wapi\mycartService();
            $cartlist = $service->cartlist();
            ajaxReturnData(1,'删除成功！',$cartlist);
        }
    }

    public function clean()
    {
        $member = get_member_account();
        $openid = $member['openid'];
        mysqld_delete('shop_cart', array(
            'session_id' => $openid
        ));
        $service  = new \service\wapi\mycartService();
        $cartlist = $service->cartlist();
        ajaxReturnData(1,'已全部移除！',$cartlist);
    }

    /**
     * 立即购买的时候，先调用该方法添加到购物车中
     */
    public function lijiBuy()
    {
        $_GP =  $this->request;
        $dishid  = intval($_GP['id']);
        if(empty($dishid)){
            ajaxReturnData(0,'请选择商品');
        }
        $total   = intval($_GP['buy_num']);
        $total   = empty($total) ? 1 : $total;

        $service  = new \service\wapi\mycartService();
        $cartotal = $service->lijiBuyCart($dishid,$total);
        if(!$cartotal){
            ajaxReturnData(0,$service->getError());
        }

        ajaxReturnData(1,'操作成功！',$cartotal);
    }

    //所选择了哪些商品进行购买  逗号分隔 多个id
    public function topay()
    {
        $_GP    = $this->request;
        $service  = new \service\wapi\mycartService();
        $res = $service->topay($_GP['ids'],$_GP['type']);
        if(!$res){
            ajaxReturnData(0,$service->getError());
        }else{
            ajaxReturnData(1,'操作成功！');
        }
    }
}