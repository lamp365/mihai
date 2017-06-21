<?php
/**
购物车的service层
 */
namespace service\shopwap;
use \model\shop_cart_model;
use service\publicService;
class shopcartService extends publicService
{
    /***
     * 取出shop_cart表单条商品信息
     *   */
    public function getOneShopCart($where , $param="*"){
        if (!is_array($where) || empty($where)) return false;
        $shopCartModel = new shop_cart_model();
        return $shopCartModel->getOne($where,$param);
    }
    /***
     * 取出shop_cart表多条商品信息
     *   */
    public function getAllShopCart($where , $param="*" ,$orderby = false){
        if (!is_array($where) || empty($where)) return false;
        $shopCartModel = new shop_cart_model();
        return $shopCartModel->getAll($where,$param,$orderby);
    }
    /***
     * 取出shop_cart表多条商品信息,通过店铺id来分组
     *   */
    public function getAllShopCartQuery($where , $param="*" ,$orderby = "id desc"){
        $shopCartModel = new shop_cart_model();
        if (is_array($where)) $where = to_sqls($where);
        $sql = "SELECT {$param} FROM ".table($shopCartModel->table_name);
        $sql .= ($where) ? " WHERE $where" : '';
        $sql .= ' group by sts_id ';
        $sql .= ($orderby) ? " ORDER BY $orderby" : '';
        $info = $shopCartModel->fetchall($sql);
        var_dump($info);
        return $info;
    }
    /***
     * 往购物车表新增数据
     *   */
    public function insertCart($data = array()){
        $shopCartModel = new shop_cart_model();
        $res = $shopCartModel->insert($shopCartModel->table_name,$data);
        if ($res){
            return $shopCartModel->insertid();
        }
    }
    /**
     * 往购物车更新商品
     *   */
    public function updateCart($data = array(),$where = array()){
        if (empty($where)) return false;
        $shopCartModel = new shop_cart_model();
        $res = $shopCartModel->update($shopCartModel->table_name,$data,$where);
        return $res;
    }
    /**
     * 删除购物车商品
     *   */
    public function deleteCart($where = array()){
        if (empty($where)) return false;
        $shopCartModel = new shop_cart_model();
        $res = $shopCartModel->delete($shopCartModel->table_name,$where);
        return res;
    }
}